const fs = require('fs');
const path = require('path');

const TESTS_ROOT = path.resolve(__dirname, '..', 'tests');

function hasSpecFiles(dirPath) {
  if (!fs.existsSync(dirPath)) {
    return false;
  }

  const entries = fs.readdirSync(dirPath, { withFileTypes: true });
  for (const entry of entries) {
    const fullPath = path.join(dirPath, entry.name);
    if (entry.isDirectory() && hasSpecFiles(fullPath)) {
      return true;
    }

    if (entry.isFile() && /\.spec\.(ts|js)$/.test(entry.name)) {
      return true;
    }
  }

  return false;
}

function listNamespaces() {
  if (!fs.existsSync(TESTS_ROOT)) {
    return [];
  }

  return fs
    .readdirSync(TESTS_ROOT, { withFileTypes: true })
    .filter((entry) => entry.isDirectory())
    .map((entry) => entry.name)
    .filter((name) => hasSpecFiles(path.join(TESTS_ROOT, name)))
    .sort();
}

function resolveNamespace(namespace) {
  const namespaces = listNamespaces();
  if (!namespaces.includes(namespace)) {
    return null;
  }

  return path.join(TESTS_ROOT, namespace);
}

module.exports = {
  TESTS_ROOT,
  listNamespaces,
  resolveNamespace,
};
