const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');

const PROJECT_ROOT = path.resolve(__dirname, '..', '..', '..');

function getSeedFiles(includeSampleData = false) {
  const files = [
    path.join(PROJECT_ROOT, 'orion_structure.sql'),
    path.join(PROJECT_ROOT, 'orion_basicdata.sql'),
    path.join(PROJECT_ROOT, 'tests', 'playwright', 'fixtures', 'test_users.sql'),
  ];

  if (includeSampleData) {
    files.push(path.join(PROJECT_ROOT, 'orion_sampledata.sql'));
  }

  return files;
}

async function getConnection() {
  return mysql.createConnection({
    host: process.env.DB_HOST || 'db-test',
    user: process.env.DB_USER || 'orion_user',
    password: process.env.DB_PASS || 'orion_pass',
    database: process.env.DB_NAME || 'orion_test',
    multipleStatements: true,
  });
}

async function resetDatabase(options = {}) {
  const includeSampleData = !!options.includeSampleData;
  const connection = await getConnection();

  try {
    const [tables] = await connection.query('SHOW TABLES');
    const tableNames = tables.map((row) => Object.values(row)[0]);

    await connection.query('SET FOREIGN_KEY_CHECKS = 0');
    for (const tableName of tableNames) {
      await connection.query(`DROP TABLE IF EXISTS \`${tableName}\``);
    }
    await connection.query('SET FOREIGN_KEY_CHECKS = 1');

    const executedFiles = [];
    for (const filePath of getSeedFiles(includeSampleData)) {
      const sql = fs.readFileSync(filePath, 'utf8');
      await connection.query(sql);
      executedFiles.push(path.basename(filePath));
    }

    return {
      status: 200,
      message: 'Base de datos de pruebas reiniciada correctamente.',
      executedFiles,
    };
  } finally {
    await connection.end();
  }
}

module.exports = {
  resetDatabase,
};
