FPM_CONTAINER=php74
DC := docker compose
DC_EXEC := $(DC) exec $(FPM_CONTAINER)

ifeq (, $(shell which docker))
WRAPPER_EXEC =
else
WRAPPER_EXEC = $(DC_EXEC)
endif

start: ## start project
start:
	@$(DC) up -d

stop: ## stop project
stop:
	@$(DC) stop

install: ## install project
install: start
	@$(WRAPPER_EXEC) composer install

bash: ## bash inside container
	@$(WRAPPER_EXEC) bash

phpunit: ## Run phpunit
phpunit:
	@$(WRAPPER_EXEC) php vendor/bin/codecept run $(_suite)

analyse: ## Run phpstan analyse
analyse:
	@$(WRAPPER_EXEC) vendor/bin/phpstan analyse -c phpstan.neon

grumphp: ## Run grumPHP
grumphp:
	@$(WRAPPER_EXEC) vendor/bin/grumphp run

cs-fixer: ## Run PHP CS Fixer
cs-fixer:
	@$(WRAPPER_EXEC) vendor/bin/php-cs-fixer fix

clear-cache: ## Run cache clear for dev and test
clear-cache:
	@$(WRAPPER_EXEC) php bin/console cache:clear --env=dev
	@$(WRAPPER_EXEC) php bin/console cache:clear --env=test
