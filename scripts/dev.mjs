import { spawn } from 'node:child_process'
import { fileURLToPath } from 'node:url'

const isWindows = process.platform === 'win32'

const services = [
  {
    name: 'api',
    command: 'php',
    args: ['artisan', 'serve', '--host=0.0.0.0', '--port=8000'],
    cwd: fileURLToPath(new URL('../backend', import.meta.url)),
    color: '\x1b[36m',
  },
  {
    name: 'web',
    command: isWindows ? 'cmd.exe' : 'npm',
    args: isWindows
      ? ['/d', '/s', '/c', 'npm run dev -- --host 0.0.0.0']
      : ['run', 'dev', '--', '--host', '0.0.0.0'],
    cwd: fileURLToPath(new URL('../frontend', import.meta.url)),
    color: '\x1b[35m',
  },
]

const children = []
let stopping = false

function write(service, chunk, stream) {
  const reset = '\x1b[0m'
  const lines = chunk.toString().split(/\r?\n/).filter(Boolean)
  for (const line of lines) {
    stream.write(`${service.color}[${service.name}]${reset} ${line}\n`)
  }
}

function stop(child) {
  if (child.exitCode !== null || child.signalCode !== null) return
  if (isWindows) {
    // npm spawns a child process (Vite); kill the whole tree on Windows.
    spawn('taskkill', ['/pid', String(child.pid), '/T', '/F'], { stdio: 'ignore' })
  } else {
    child.kill('SIGTERM')
  }
}

function shutdown(code = 0) {
  if (stopping) return
  stopping = true
  children.forEach(stop)
  process.exit(code)
}

console.log('Hospital EMR is starting...')
console.log('  Web:   http://localhost:3000')
console.log('  API:   http://localhost:8000/api/v1')
console.log('  Login: admin@hospital.bo / password')
console.log('  (press Ctrl+C to stop)\n')

for (const service of services) {
  const child = spawn(service.command, service.args, {
    cwd: service.cwd,
    stdio: ['ignore', 'pipe', 'pipe'],
  })

  child.stdout.on('data', (chunk) => write(service, chunk, process.stdout))
  child.stderr.on('data', (chunk) => write(service, chunk, process.stderr))
  child.on('error', (error) => {
    console.error(`[${service.name}] Failed to start: ${error.message}`)
    shutdown(1)
  })
  child.on('exit', (code) => {
    if (!stopping) {
      console.error(`[${service.name}] exited with code ${code}`)
      shutdown(code ?? 1)
    }
  })

  children.push(child)
}

process.on('SIGINT', () => shutdown(0))
process.on('SIGTERM', () => shutdown(0))
