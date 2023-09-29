#-----------------------------------------------------------------------------------------------------------------------
# Command line
#-----------------------------------------------------------------------------------------------------------------------

install:
	@echo "Starting install"
	@${compose} up --build -d
	@${exec} -c "composer install;"
.PHONY: install

start:
	@echo "Starting project"
	@${compose} up -d
.PHONY: start

stop:
	@echo "Stopping project"
	@${compose} stop
.PHONY: stop

bash:
	@${exec}

phpstan:
	@${exec} -c "vendor/bin/phpstan analyse -l 9 src tests"
.PHONY: phpstan

cs-fix:
	@${exec} -c "vendor/bin/php-cs-fixer fix"
.PHONY: cs-fix

cs-show:
	@${exec} -c "vendor/bin/php-cs-fixer fix --diff --dry-run"
.PHONY: cs-show

tests:
	@${exec} -c "vendor/bin/phpunit"
.PHONY: tests

test-filter:
	@${exec} -c "vendor/bin/phpunit --filter $(method) $(path)"
.PHONY: test-filter

test-path:
	@${exec} -c "vendor/bin/phpunit $(path)"
.PHONY: test-path

test-coverage:
	$(exec) -c  "XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html var/coverage"
.PHONY: test-coverage

spotify:
	$(exec) -c "php bin/console get-spotify-plays"
.PHONY: spotify

cache:
	$(exec) -c "php bin/console cache:clear"
.PHONY: cache

migration:
	$(exec) -c "php bin/console make:migration"
.PHONY: migration

migrate:
	$(exec) -c "php bin/console doctrine:migrations:migrate"
.PHONY: migrate

redis-bash:
	docker exec -it redis_spotify redis-cli
.PHONY: redis-bash

rabbit-bash:
	docker exec -it rabbit_spotify bash
.PHONY: rabbit-bash

run-messenger:
	$(exec) -c "php bin/console messenger:consume async -vv"
.PHONY: run-messenger
#-----------------------------------------------------------------------------------------------------------------------
# Variables
#-----------------------------------------------------------------------------------------------------------------------

compose = docker-compose --env-file ./app/.env -f ./docker/docker-compose.yml
exec = docker exec -it php_spotify bash
build = ./docker/docker-compose up
