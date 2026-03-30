# LelangFast

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Backend** | Laravel | 13.x |
| **PHP** | PHP (FrankenPHP) | 8.4 |
| **Frontend** | Vite + Tailwind CSS | 8.x / 4.x |
| **Real-time** | Laravel Reverb + Echo | 1.x / 2.x |
| **Queue** | Laravel Horizon (Redis) | 5.x |
| **Monitoring** | Laravel Pulse | 1.x |
| **Database** | MySQL | 8.4 |
| **Cache / Queue Driver** | Redis | 7.x |
| **Web Server** | serversideup/php (FrankenPHP) | 8.4-frankenphp |
| **Testing** | Pest | 4.x |

## Local Development Setup

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (includes Docker Compose)

No PHP, Node, or Composer needed on your machine.

### Getting Started

```bash
# 1. Clone the repo
git clone <repo-url> lelangfast
cd lelangfast

# 2. Create environment file
cp .env.example .env

# 3. Start everything
docker compose up --build
```

On first boot, the `php` container automatically runs migrations and creates the storage symlink. No manual setup required.

### Services

| Service | URL | Purpose |
|---------|-----|---------|
| **App** | http://localhost:8080 | FrankenPHP web server |
| **Horizon** | http://localhost:8080/horizon | Queue dashboard |
| **Pulse** | http://localhost:8080/pulse | App monitoring |
| **Reverb** | ws://localhost:9000 | WebSocket server |
| **Vite** | http://localhost:5173 | HMR dev server |
| **MySQL** | localhost:3306 | Database |
| **Redis** | localhost:6379 | Cache / Queue |

### How It Works

Docker Compose automatically merges `compose.yml` (base) with `compose.override.yml` (dev overrides). The dev setup:

- Bind-mounts your source code into containers for live editing
- Uses a named `vendor` volume (dependencies built inside Docker)
- Uses a named `node_modules` volume (npm install runs inside the `node` container)
- Disables config/route/view caching so changes reflect immediately
- Disables OPcache for instant PHP changes
- Runs Vite dev server with HMR in a separate Node container

### Common Commands

```bash
# Run artisan commands
docker compose exec php php artisan migrate
docker compose exec php php artisan tinker
docker compose exec php php artisan make:model Post -mfc

# Run tests
docker compose exec php php artisan test

# View logs
docker compose logs -f php        # App logs
docker compose logs -f horizon    # Queue logs
docker compose logs -f reverb     # WebSocket logs

# Rebuild after Dockerfile or dependency changes
docker compose up --build

# Stop everything
docker compose down

# Stop and wipe all data (DB, Redis, vendor volumes)
docker compose down --volumes
```

## Production Deployment

### Build Production Image

```bash
docker compose -f compose.yml up --build -d
```

Using only `compose.yml` (without the override) builds the `production` target:
- Dependencies installed without dev packages
- Frontend assets pre-built by Vite
- OPcache enabled
- `php artisan optimize` runs on startup (config, route, view, event caching)
- Migrations run automatically

### Required Environment Variables

Set these in your production `.env` or via your orchestrator:

```env
APP_NAME=LelangFast
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=lelangfast
DB_USERNAME=lelangfast
DB_PASSWORD=<secure-password>

REDIS_HOST=your-redis-host
REDIS_PASSWORD=<secure-password>

QUEUE_CONNECTION=redis
CACHE_STORE=redis
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=<random-id>
REVERB_APP_KEY=<random-key>
REVERB_APP_SECRET=<random-secret>
REVERB_HOST=your-domain.com
REVERB_PORT=443
REVERB_SCHEME=https
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=9000

PULSE_INGEST_DRIVER=redis
```

### Architecture Overview

```
                    ┌──────────────┐
                    │ Load Balancer│
                    │  (SSL term)  │
                    └──────┬───────┘
                           │
              ┌────────────┼────────────┐
              │            │            │
        ┌─────▼─────┐ ┌───▼───┐ ┌─────▼─────┐
        │    php     │ │reverb │ │    php     │
        │ FrankenPHP │ │  :9000│ │ (scaled)   │
        │   :8080    │ └───┬───┘ │   :8080    │
        └─────┬──────┘     │     └─────┬──────┘
              │            │           │
        ┌─────▼────────────▼───────────▼──┐
        │          Redis 7                │
        │  (queue, cache, broadcast)      │
        └─────────────┬──────────────────-┘
              ┌───────┴────────┐
              │                │
        ┌─────▼─────┐   ┌─────▼─────┐
        │  horizon   │   │   pulse   │
        │ (workers)  │   │ (ingest)  │
        └─────┬──────┘   └─────┬─────┘
              │                │
        ┌─────▼────────────────▼──┐
        │        MySQL 8.4        │
        └─────────────────────────┘
```

### Deployment Options

**Docker Compose** (single server):
```bash
docker compose -f compose.yml up --build -d
```

**Docker Swarm** (recommended for zero-downtime):
```bash
docker stack deploy -c compose.yml lelangfast
```

**Kubernetes**: Use the production Docker image with your own manifests or Helm chart.

### Health Checks

All services include built-in health checks:

| Service | Check |
|---------|-------|
| php | `curl http://localhost:8080/up` |
| horizon | `healthcheck-horizon` (built-in) |
| reverb | TCP socket on port 9000 |
| mysql | `mysqladmin ping` |
| redis | `redis-cli ping` |

## License

MIT
