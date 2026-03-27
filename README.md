# Minimal native PHP API (Docker + Apache + MySQL)

**Architecture & technical choices (FR):** see [TECHNICAL.md](TECHNICAL.md).

## Prerequisites

- Docker + Docker Compose

## Quickstart

1) Create your env file:

```bash
cp .env.example .env
```

2) Start the stack:

```bash
docker compose up --build
```

3) Try the endpoints:

```bash
curl -sS http://localhost:8080/health
curl -sS http://localhost:8080/
curl -sS http://localhost:8080/db/ping
```

## Endpoints

- `GET /` -> `{ "service": "api" }`
- `GET /health` -> `{ "status": "ok" }`
- `GET /db/ping` -> `{ "db": "ok" }` (or a 500 error JSON if DB is not reachable)

## Troubleshooting

- If port `8080` is already used, change the host port in `docker-compose.yml`.
- If you want a fresh database volume:

```bash
docker compose down -v
```

