const ACCOUNT_ID = process.env.CLOUDFLARE_ACCOUNT_ID;
const API_TOKEN = process.env.CLOUDFLARE_API_TOKEN;
const BUCKET_NAME = process.env.CLOUDFLARE_R2_BUCKET_NAME || 'orion-test-automation';

if (!ACCOUNT_ID || !API_TOKEN) {
  console.error('Missing CLOUDFLARE_ACCOUNT_ID or CLOUDFLARE_API_TOKEN');
  process.exit(1);
}

async function createBucket() {
  console.log(`Creating R2 bucket: ${BUCKET_NAME}...`);
  try {
    const response = await fetch(
      `https://api.cloudflare.com/client/v4/accounts/${ACCOUNT_ID}/r2/buckets`,
      {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${API_TOKEN}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name: BUCKET_NAME }),
      }
    );
    const data = await response.json();
    if (data.success) {
      console.log('Bucket created successfully.');
    } else if (data.errors && data.errors.some(e => e.code === 10003)) {
      console.log('Bucket already exists.');
    } else {
      console.error('Error creating bucket:', JSON.stringify(data.errors));
      process.exit(1);
    }
  } catch (error) {
    console.error('Failed to connect to Cloudflare API:', error);
    process.exit(1);
  }
}

async function deleteBucket() {
  console.log(`Deleting R2 bucket: ${BUCKET_NAME}...`);
  try {
    const response = await fetch(
      `https://api.cloudflare.com/client/v4/accounts/${ACCOUNT_ID}/r2/buckets/${BUCKET_NAME}`,
      {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${API_TOKEN}`,
        },
      }
    );
    const data = await response.json();
    if (data.success) {
      console.log('Bucket deleted successfully.');
    } else {
      console.error('Error deleting bucket:', JSON.stringify(data.errors));
      process.exit(1);
    }
  } catch (error) {
    console.error('Failed to connect to Cloudflare API:', error);
    process.exit(1);
  }
}

const action = process.argv[2];
if (action === 'create') {
  createBucket();
} else if (action === 'delete') {
  deleteBucket();
} else {
  console.log('Usage: node manage-r2.js [create|delete]');
}
