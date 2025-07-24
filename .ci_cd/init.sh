#!/bin/bash

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"

if [ ! -f "$PROJECT_ROOT/.ci_cd/.docker.env" ]; then
    cp "$PROJECT_ROOT/.ci_cd/.docker.env.example" "$PROJECT_ROOT/.ci_cd/.docker.env"
    echo ".docker.env criado a partir do .docker.env.example"
fi

cd "$PROJECT_ROOT"

echo "Iniciando o Docker Compose..."

docker compose --env-file .ci_cd/.docker.env -f .ci_cd/docker-compose.yml up --build -d