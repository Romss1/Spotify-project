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
	@${exec} -c "vendor/bin/phpstan analyse -l 9 src"
.PHONY: phpstan

cs-fix:
	@${exec} -c "vendor/bin/php-cs-fixer fix"
.PHONY: cs-fix

cs-show:
	@${exec} -c "vendor/bin/php-cs-fixer fix --diff --dry-run"
.PHONY: cs-show

phpunit:
	@${exec} -c "vendor/bin/phpunit tests"
.PHONY: phpunit

test-coverage:
	$(exec) -c  "XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html var/coverage"
.PHONY: test-coverage

spotify:
	$(exec) -c "php bin/console get-spotify-plays"
.PHONY: spotify
#-----------------------------------------------------------------------------------------------------------------------
# Variables
#-----------------------------------------------------------------------------------------------------------------------

compose = docker-compose --env-file ./app/.env -f ./docker/docker-compose.yml
exec = docker exec -it php_spotify bash
build = ./docker/docker-compose up
