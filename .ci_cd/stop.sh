#!/bin/bash

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"

echo "Encerrando containers..."

if [ "$1" == "-v" ]; then
    echo "Removendo volumes..."
    docker compose --env-file .ci_cd/.docker.env -f .ci_cd/docker-compose.yml down -v
else
    docker compose --env-file .ci_cd/.docker.env -f .ci_cd/docker-compose.yml down
fi

echo "Containers desligados com sucesso."