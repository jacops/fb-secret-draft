.PHONY: apps/frontend infra

setup: setup/env apps/db/setup

setup/env:
	cd apps && cp -n .env.dist .env || true

all: apps/build apps/frontend/push deploy/sandbox/frontend

apps/build:
	cd apps && docker compose build

apps/frontend/run:
	cd apps && docker compose up frontend

apps/db/setup:
	cd apps && docker compose up -d db
	cd apps && docker compose exec -T db mysql -uroot -pexample mysql < ./frontend/sql/db-migration.sql

apps/frontend/push:
	cd apps && docker compose push frontend

apps/phpmyadmin:
	cd apps && docker compose up phpmyadmin

infra/sandbox/base/plan:
	STACK_PATH=environments/sandbox/base docker-compose -f infra/docker-compose.yml run --rm terragrunt terragrunt plan -out tfplan

infra/sandbox/base/apply:
	STACK_PATH=environments/sandbox/base docker-compose -f infra/docker-compose.yml run --rm terragrunt terragrunt apply tfplan

infra/sandbox/frontend/plan:
	STACK_PATH=environments/sandbox/frontend docker-compose -f infra/docker-compose.yml run --rm terragrunt terragrunt run-all plan

infra/sandbox/frontend/apply:
	STACK_PATH=environments/sandbox/frontend docker-compose -f infra/docker-compose.yml run --rm terragrunt terragrunt run-all apply

deploy/sandbox: deploy/sandbox/frontend

deploy/sandbox/frontend:
	scp apps/Caddyfile ubuntu@18.195.150.32:/home/ubuntu
	scp apps/docker-compose.yml ubuntu@18.195.150.32:/home/ubuntu
	scp infra/environments/sandbox/frontend/.env ubuntu@18.195.150.32:/home/ubuntu
	ssh ubuntu@18.195.150.32 docker compose pull
	ssh ubuntu@18.195.150.32 docker compose up frontend caddy --force-recreate -d
	ssh ubuntu@18.195.150.32 docker compose exec -T db mysql -uroot -pexample mysql < ./apps/frontend/sql/db-migration.sql
