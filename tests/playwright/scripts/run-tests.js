const { spawn } = require('child_process');
const { listNamespaces, resolveNamespace } = require('./namespaces');

function runCommand(args) {
  return new Promise((resolve, reject) => {
    const child = spawn('npx', ['playwright', 'test', ...args], {
      cwd: process.cwd(),
      stdio: 'inherit',
      shell: process.platform === 'win32',
    });

    child.on('error', reject);
    child.on('close', (code) => {
      if (code === 0) {
        resolve();
        return;
      }
      reject(new Error(`Playwright exited with code ${code}`));
    });
  });
}

async function main() {
  const action = process.argv[2] || 'all';

  if (action === 'list') {
    console.log(JSON.stringify(listNamespaces(), null, 2));
    return;
  }

  if (action === 'all') {
    await runCommand([]);
    return;
  }

  if (action === 'namespace') {
    const namespace = process.argv[3] || process.env.TEST_NAMESPACE;
    if (!namespace) {
      throw new Error('Namespace requerido. Usa: node scripts/run-tests.js namespace <nombre>');
    }

    const namespacePath = resolveNamespace(namespace);
    if (!namespacePath) {
      throw new Error(`Namespace no encontrado: ${namespace}`);
    }

    await runCommand([namespacePath]);
    return;
  }

  throw new Error(`Acción no soportada: ${action}`);
}

main().catch((error) => {
  console.error(error.message || error);
  process.exit(1);
});
