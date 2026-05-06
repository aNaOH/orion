const http = require('http');
const fs = require('fs');
const path = require('path');
const { spawn } = require('child_process');
const { listNamespaces, resolveNamespace } = require('../scripts/namespaces');
const { resetDatabase } = require('../scripts/db');
const r2 = require('../manage-r2');

const PORT = Number(process.env.TEST_RUNNER_PORT || 3000);
const ROOT_DIR = path.resolve(__dirname, '..');
const REPORT_DIR = path.join(ROOT_DIR, 'playwright-report');
const DATA_DIR = path.join(ROOT_DIR, 'runner-data');
const LOG_DIR = path.join(DATA_DIR, 'logs');
const HISTORY_FILE = path.join(DATA_DIR, 'history.json');
const INDEX_FILE = path.join(__dirname, 'index.html');

fs.mkdirSync(LOG_DIR, { recursive: true });

function loadHistory() {
  if (!fs.existsSync(HISTORY_FILE)) {
    return [];
  }

  try {
    return JSON.parse(fs.readFileSync(HISTORY_FILE, 'utf8'));
  } catch (error) {
    return [];
  }
}

function saveHistory(history) {
  fs.writeFileSync(HISTORY_FILE, JSON.stringify(history.slice(0, 20), null, 2));
}

const state = {
  currentJob: null,
  history: loadHistory(),
};

function sendJson(res, statusCode, payload) {
  res.writeHead(statusCode, { 'Content-Type': 'application/json; charset=utf-8' });
  res.end(JSON.stringify(payload));
}

function sendText(res, statusCode, text, contentType = 'text/plain; charset=utf-8') {
  res.writeHead(statusCode, { 'Content-Type': contentType });
  res.end(text);
}

async function readJsonBody(req) {
  const chunks = [];
  for await (const chunk of req) {
    chunks.push(chunk);
  }

  if (chunks.length === 0) {
    return {};
  }

  const raw = Buffer.concat(chunks).toString('utf8');
  return raw ? JSON.parse(raw) : {};
}

function getCurrentLogContent() {
  if (!state.currentJob?.logFile || !fs.existsSync(state.currentJob.logFile)) {
    return '';
  }

  return fs.readFileSync(state.currentJob.logFile, 'utf8');
}

function createJobSummary(job) {
  return {
    id: job.id,
    namespace: job.namespace,
    mode: job.mode,
    status: job.status,
    startedAt: job.startedAt,
    finishedAt: job.finishedAt || null,
    durationMs: job.durationMs || null,
    exitCode: job.exitCode ?? null,
    reportUrl: fs.existsSync(REPORT_DIR) ? '/playwright-report/index.html' : null,
    logUrl: `/api/jobs/${job.id}/logs`,
    options: job.options,
  };
}

async function runPlaywrightJob({ namespace, resetDb, recreateR2 }) {
  if (state.currentJob) {
    throw new Error('Ya hay una ejecución en curso.');
  }

  const id = `job-${Date.now()}`;
  const logFile = path.join(LOG_DIR, `${id}.log`);
  const job = {
    id,
    namespace: namespace || null,
    mode: namespace ? 'namespace' : 'all',
    status: 'preparing',
    startedAt: new Date().toISOString(),
    finishedAt: null,
    durationMs: null,
    exitCode: null,
    logFile,
    options: {
      resetDb: !!resetDb,
      recreateR2: !!recreateR2,
    },
  };

  fs.writeFileSync(logFile, '');
  state.currentJob = job;

  const appendLog = (text) => {
    fs.appendFileSync(logFile, text);
  };

  try {
    if (resetDb) {
      appendLog('[db] Reiniciando base de datos de pruebas...\n');
      const result = await resetDatabase({ includeSampleData: false });
      appendLog(`${JSON.stringify(result, null, 2)}\n`);
    }

    if (recreateR2) {
      appendLog('[r2] Recreando bucket de pruebas...\n');
      const result = await r2.recreate();
      appendLog(`${JSON.stringify(result, null, 2)}\n`);
    }

    job.status = 'running';
    const args = ['playwright', 'test'];
    if (namespace) {
      const namespacePath = resolveNamespace(namespace);
      if (!namespacePath) {
        throw new Error(`Namespace no encontrado: ${namespace}`);
      }
      args.push(namespacePath);
    }

    await new Promise((resolve, reject) => {
      const child = spawn('npx', args, {
        cwd: ROOT_DIR,
        shell: process.platform === 'win32',
      });

      child.stdout.on('data', (chunk) => appendLog(chunk.toString()));
      child.stderr.on('data', (chunk) => appendLog(chunk.toString()));
      child.on('error', reject);
      child.on('close', (code) => {
        job.exitCode = code ?? 1;
        if (code === 0) {
          resolve();
          return;
        }
        reject(new Error(`Playwright exited with code ${code}`));
      });
    });

    job.status = 'passed';
  } catch (error) {
    job.status = 'failed';
    appendLog(`\n[error] ${error.message || error}\n`);
  } finally {
    job.finishedAt = new Date().toISOString();
    job.durationMs = new Date(job.finishedAt).getTime() - new Date(job.startedAt).getTime();
    state.history.unshift(createJobSummary(job));
    saveHistory(state.history);
    state.currentJob = null;
  }

  return createJobSummary(job);
}

function serveStaticFile(res, filePath, contentType) {
  if (!fs.existsSync(filePath)) {
    sendText(res, 404, 'Not found');
    return;
  }

  sendText(res, 200, fs.readFileSync(filePath), contentType);
}

async function handleApi(req, res, url) {
  if (req.method === 'GET' && url.pathname === '/api/namespaces') {
    return sendJson(res, 200, { namespaces: listNamespaces() });
  }

  if (req.method === 'GET' && url.pathname === '/api/jobs/current') {
    return sendJson(res, 200, {
      currentJob: state.currentJob ? createJobSummary(state.currentJob) : null,
      log: getCurrentLogContent(),
    });
  }

  if (req.method === 'GET' && url.pathname === '/api/jobs/history') {
    return sendJson(res, 200, { history: state.history });
  }

  if (req.method === 'GET' && url.pathname.startsWith('/api/jobs/') && url.pathname.endsWith('/logs')) {
    const jobId = url.pathname.split('/')[3];
    const historyItem = state.history.find((item) => item.id === jobId);
    if (!historyItem) {
      return sendJson(res, 404, { message: 'Job no encontrado.' });
    }

    const logFile = path.join(LOG_DIR, `${jobId}.log`);
    return sendText(res, 200, fs.existsSync(logFile) ? fs.readFileSync(logFile, 'utf8') : '');
  }

  if (req.method === 'POST' && url.pathname === '/api/run') {
    try {
      const body = await readJsonBody(req);
      const namespace = body.namespace || null;
      if (namespace && !resolveNamespace(namespace)) {
        return sendJson(res, 400, { message: `Namespace no encontrado: ${namespace}` });
      }

      runPlaywrightJob({
        namespace,
        resetDb: body.resetDb !== false,
        recreateR2: body.recreateR2 !== false,
      }).catch(() => {});

      return sendJson(res, 202, { message: 'Ejecución iniciada.' });
    } catch (error) {
      return sendJson(res, 409, { message: error.message || 'No se pudo iniciar la ejecución.' });
    }
  }

  if (req.method === 'POST' && url.pathname === '/api/db/reset') {
    try {
      return sendJson(res, 200, await resetDatabase({ includeSampleData: false }));
    } catch (error) {
      return sendJson(res, 500, { message: error.message || 'No se pudo reiniciar la base de datos.' });
    }
  }

  if (req.method === 'GET' && url.pathname === '/api/r2/status') {
    try {
      return sendJson(res, 200, await r2.status());
    } catch (error) {
      return sendJson(res, error.status || 500, { message: error.message || 'No se pudo consultar R2.' });
    }
  }

  const r2Actions = {
    '/api/r2/create': r2.create,
    '/api/r2/empty': r2.empty,
    '/api/r2/delete': r2.delete,
    '/api/r2/recreate': r2.recreate,
  };

  if (req.method === 'POST' && r2Actions[url.pathname]) {
    try {
      return sendJson(res, 200, await r2Actions[url.pathname]());
    } catch (error) {
      return sendJson(res, error.status || 500, { message: error.message || 'No se pudo completar la operación de R2.' });
    }
  }

  return sendJson(res, 404, { message: 'Endpoint no encontrado.' });
}

const server = http.createServer(async (req, res) => {
  const url = new URL(req.url, `http://${req.headers.host}`);

  if (url.pathname.startsWith('/api/')) {
    return handleApi(req, res, url);
  }

  if (url.pathname === '/' || url.pathname === '/index.html') {
    return serveStaticFile(res, INDEX_FILE, 'text/html; charset=utf-8');
  }

  if (url.pathname.startsWith('/playwright-report/')) {
    const relativePath = url.pathname.replace('/playwright-report/', '');
    const filePath = path.join(REPORT_DIR, relativePath);
    const contentType = filePath.endsWith('.html') ? 'text/html; charset=utf-8' : 'application/octet-stream';
    return serveStaticFile(res, filePath, contentType);
  }

  return sendText(res, 404, 'Not found');
});

server.listen(PORT, () => {
  console.log(`Orion test runner listening on http://0.0.0.0:${PORT}`);
});
