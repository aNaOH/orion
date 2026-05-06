const { resetDatabase } = require('./db');

async function main() {
  const includeSampleData = process.argv.includes('--include-sampledata');
  const result = await resetDatabase({ includeSampleData });
  console.log(JSON.stringify(result, null, 2));
}

main().catch((error) => {
  console.error(error.message || error);
  process.exit(1);
});
