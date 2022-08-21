apps/frontend/build:
	docker-compose -f apps/front/docker-compose.yml run --rm build

apps/frontend/deploy:
	docker-compose -f apps/front/docker-compose.yml run --rm deploy

infra/sandbox/base/plan:
	STACK_PATH=environments/sandbox/base docker-compose -f terragrunt/docker-compose.yml run --rm terragrunt terragrunt plan -out tfplan

infra/sandbox/base/apply:
	STACK_PATH=environments/sandbox/base docker-compose -f terragrunt/docker-compose.yml run --rm terragrunt terragrunt apply tfplan

infra/sandbox/frontend/plan:
	STACK_PATH=environments/sandbox/base docker-compose -f terragrunt/docker-compose.yml run --rm terragrunt terragrunt run-all plan

infra/sandbox/frontend/apply:
	STACK_PATH=environments/sandbox/base docker-compose -f terragrunt/docker-compose.yml run --rm terragrunt terragrunt run-all apply