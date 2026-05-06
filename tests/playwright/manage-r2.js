const {
  S3Client,
  HeadBucketCommand,
  CreateBucketCommand,
  DeleteBucketCommand,
  ListObjectsV2Command,
  DeleteObjectsCommand,
} = require('@aws-sdk/client-s3');

function getClient() {
  const accountId = process.env.CLOUDFLARE_ACCOUNT_ID;
  const accessKeyId = process.env.CLOUDFLARE_R2_TOKEN;
  const secretAccessKey = process.env.CLOUDFLARE_R2_TOKEN_SECRET;

  if (!accountId || !accessKeyId || !secretAccessKey) {
    throw new Error('Missing CLOUDFLARE_ACCOUNT_ID, CLOUDFLARE_R2_TOKEN or CLOUDFLARE_R2_TOKEN_SECRET');
  }

  return new S3Client({
    region: 'auto',
    endpoint: `https://${accountId}.r2.cloudflarestorage.com`,
    credentials: {
      accessKeyId,
      secretAccessKey,
    },
  });
}

function getBucketName() {
  return process.env.CLOUDFLARE_R2_BUCKET_NAME || 'orion-test-automation';
}

async function bucketExists() {
  try {
    await getClient().send(new HeadBucketCommand({ Bucket: getBucketName() }));
    return true;
  } catch (error) {
    return false;
  }
}

async function status() {
  const exists = await bucketExists();
  const objectCount = exists ? await countObjects() : 0;

  return {
    status: 200,
    bucket: getBucketName(),
    exists,
    objectCount,
  };
}

async function create() {
  if (await bucketExists()) {
    return {
      status: 200,
      bucket: getBucketName(),
      message: 'El bucket de pruebas ya existe.',
    };
  }

  await getClient().send(new CreateBucketCommand({ Bucket: getBucketName() }));
  return {
    status: 200,
    bucket: getBucketName(),
    message: 'Bucket de pruebas creado correctamente.',
  };
}

async function countObjects() {
  const client = getClient();
  let continuationToken;
  let count = 0;

  do {
    const response = await client.send(new ListObjectsV2Command({
      Bucket: getBucketName(),
      ContinuationToken: continuationToken,
    }));

    count += (response.Contents || []).length;
    continuationToken = response.NextContinuationToken;
  } while (continuationToken);

  return count;
}

async function empty() {
  if (!(await bucketExists())) {
    return {
      status: 404,
      bucket: getBucketName(),
      message: 'El bucket de pruebas no existe.',
      deletedObjects: 0,
    };
  }

  const client = getClient();
  let continuationToken;
  let deletedObjects = 0;

  do {
    const response = await client.send(new ListObjectsV2Command({
      Bucket: getBucketName(),
      ContinuationToken: continuationToken,
    }));

    const objects = (response.Contents || []).map((item) => ({ Key: item.Key }));
    if (objects.length > 0) {
      await client.send(new DeleteObjectsCommand({
        Bucket: getBucketName(),
        Delete: {
          Objects: objects,
          Quiet: true,
        },
      }));
      deletedObjects += objects.length;
    }

    continuationToken = response.NextContinuationToken;
  } while (continuationToken);

  return {
    status: 200,
    bucket: getBucketName(),
    message: 'Bucket de pruebas vaciado correctamente.',
    deletedObjects,
  };
}

async function remove() {
  if (!(await bucketExists())) {
    return {
      status: 200,
      bucket: getBucketName(),
      message: 'El bucket de pruebas ya no existe.',
    };
  }

  const objectCount = await countObjects();
  if (objectCount > 0) {
    const error = new Error('El bucket contiene objetos. Vacíalo antes de borrarlo.');
    error.status = 409;
    throw error;
  }

  await getClient().send(new DeleteBucketCommand({ Bucket: getBucketName() }));
  return {
    status: 200,
    bucket: getBucketName(),
    message: 'Bucket de pruebas eliminado correctamente.',
  };
}

async function recreate() {
  if (await bucketExists()) {
    await empty();
    await getClient().send(new DeleteBucketCommand({ Bucket: getBucketName() }));
  }

  await getClient().send(new CreateBucketCommand({ Bucket: getBucketName() }));
  return {
    status: 200,
    bucket: getBucketName(),
    message: 'Bucket de pruebas recreado correctamente.',
  };
}

const actions = {
  status,
  create,
  empty,
  delete: remove,
  recreate,
};

async function runAction(action) {
  const handler = actions[action];
  if (!handler) {
    throw new Error('Usage: node manage-r2.js [status|create|empty|delete|recreate]');
  }

  const result = await handler();
  console.log(JSON.stringify(result, null, 2));
  return result;
}

if (require.main === module) {
  runAction(process.argv[2]).catch((error) => {
    console.error(error.message || error);
    process.exit(error.status || 1);
  });
}

module.exports = {
  status,
  create,
  empty,
  delete: remove,
  recreate,
  runAction,
};
