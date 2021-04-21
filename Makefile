DOCKER_COMPOSE  			= docker-compose
DOCKER_COMPOSE_INSIDE_PHP  	= docker-compose exec php
EXEC_PHP        			= $(DOCKER_COMPOSE_INSIDE_PHP) php
SYMFONY         			= $(EXEC_PHP) bin/console
COMPOSER        			= $(DOCKER_COMPOSE_INSIDE_PHP) composer

PROJECT_NAME = sezane-technical-test

COM_COLOR   = \033[0;34m
OBJ_COLOR   = \033[0;36m
OK_COLOR    = \033[0;32m
ERROR_COLOR = \033[0;31m
WARN_COLOR  = \033[0;33m
NO_COLOR    = \033[m

##
##	Project
##-------

build:
	@printf "$(WARN_COLOR)Build docker services$(NO_COLOR)\n"
	@printf "$(WARN_COLOR)---------------------$(NO_COLOR)\n"
	@$(DOCKER_COMPOSE) pull --quiet --ignore-pull-failures 2> /dev/null
	$(DOCKER_COMPOSE) build --pull
	@printf "\n"

kill: stop
	@printf "$(WARN_COLOR)Kill docker services$(NO_COLOR)\n"
	@printf "$(WARN_COLOR)--------------------$(NO_COLOR)\n"
	$(DOCKER_COMPOSE) kill
	@printf "\n"

install: ## Install and start the project
install: build start db fix-permissions

update: ## Update and start the project
update: start ci migrate elastic fix-permissions

fix-permissions:
	$(DOCKER_COMPOSE_INSIDE_PHP) chmod -R 777 var/
	@printf "\n"

reset: ## Stop and start a fresh install of the project
reset: kill install

start: ## Start the project
	@printf "$(WARN_COLOR)Start docker services$(NO_COLOR)\n"
	@printf "$(WARN_COLOR)---------------------$(NO_COLOR)\n"
	$(DOCKER_COMPOSE) up -d --force-recreate --renew-anon-volumes
	@printf "\n"

stop: ## Stop the project
	@printf "$(WARN_COLOR)Stop docker services$(NO_COLOR)\n"
	@printf "$(WARN_COLOR)---------------------$(NO_COLOR)\n"
	$(DOCKER_COMPOSE) down --volumes
	@printf "\n"

clean: ## Stop the project and remove generated files
clean: kill
	rm -rf vendor var/cache/* var/log/*
	@printf "\n"

.PHONY: build kill install update reset start stop clean

##
##Utils
##-----

db: ## Reset the database and load fixtures
db: ci wait-for-db wait-for-es
	@printf "$(WARN_COLOR)Reset the database and load fixtures$(NO_COLOR)\n"
	@printf "$(WARN_COLOR)------------------------------------$(NO_COLOR)\n"
	$(SYMFONY) doctrine:database:drop --if-exists --force
	$(SYMFONY) doctrine:database:create --if-not-exists
	$(SYMFONY) doctrine:schema:update --force --no-interaction
	$(SYMFONY) doctrine:migrations:version --add --all --no-interaction
	$(SYMFONY) doctrine:fixtures:load --no-interaction
	$(SYMFONY) app:elastic:populate
	@printf "\n"

clear-cache: ## Clear cache
clear-cache: fix-permissions
	$(SYMFONY) cache:clear --env=prod --no-debug
	$(SYMFONY) cache:warmup --env=prod --no-debug
	@printf "\n"

elastic: ## Populate Elasticsearch index
elastic: ci wait-for-es
	$(SYMFONY) app:elastic:populate
	@printf "\n"

migration: ## Generate a new doctrine migration
migration: ci wait-for-db
	$(SYMFONY) doctrine:migrations:diff
	@printf "\n"

migrate: ## Generate a new doctrine migration
migrate: ci wait-for-db
	$(SYMFONY) doctrine:migrations:migrate -n
	@printf "\n"

db-validate-schema: ## Validate the doctrine ORM mapping
db-validate-schema: ci wait-for-db
	$(SYMFONY) doctrine:schema:validate
	@printf "\n"

wait-for-es:
	@./docker/wait-for-healthy-container.sh sezane_elasticsearch

wait-for-db:
	@./docker/wait-for-healthy-container.sh sezane_db

.PHONY: db migration elastic wait-for-es wait-for-db

##
##Tests
##-----

test: ## Run unit and functional tests
test: tu tf

tu: ## Run unit tests
tu: ci
	$(EXEC_PHP) bin/phpunit --exclude-group functional
	@printf "\n"

tf: ## Run functional tests
tf: ci wait-for-es wait-for-db
	$(SYMFONY) cache:clear --env=test
	$(SYMFONY) doctrine:database:drop --if-exists --force --env=test
	$(SYMFONY) doctrine:database:create --if-not-exists --env=test
	$(SYMFONY) doctrine:schema:update --force --no-interaction --env=test
	$(SYMFONY) doctrine:migrations:version --add --all --no-interaction --env=test
	$(SYMFONY) doctrine:fixtures:load --no-interaction --env=test
	$(SYMFONY) app:elastic:populate --env=test
	$(EXEC_PHP) bin/phpunit --group functional
	@printf "\n"

.PHONY: test tu tf

##
##Vendor
##------

cu: ## Update dependencies
	@printf "$(WARN_COLOR)Update dependencies$(NO_COLOR)\n"
	@printf "$(WARN_COLOR)-------------------$(NO_COLOR)\n"
	$(COMPOSER) update --no-interaction -o
	@printf "\n"

ci: ## Install dependencies
	@printf "$(WARN_COLOR)Install dependencies$(NO_COLOR)\n"
	@printf "$(WARN_COLOR)--------------------$(NO_COLOR)\n"
	$(COMPOSER) install --no-interaction -o
	@printf "\n"

.PHONY: ci cu

##
##Quality assurance
##-----------------

lint: ## Lints twig and yaml files
lint: ly php-cs-fixer phpstan

ly: ## lint yaml
ly: ci
	$(SYMFONY) lint:yaml config

phpstan: ## Run phpstan
phpstan: ci
	$(DOCKER_COMPOSE_INSIDE_PHP) ./vendor/bin/phpstan analyse

php-cs-fixer: ## Run php-cs-fixer
php-cs-fixer: ci
	$(DOCKER_COMPOSE_INSIDE_PHP) ./vendor/bin/php-cs-fixer fix

.PHONY: lint lt ly security php-cs-fixer phpstan

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
