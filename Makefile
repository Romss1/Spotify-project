#-----------------------------------------------------------------------------------------------------------------------
# Command line
#-----------------------------------------------------------------------------------------------------------------------

install:
	@echo "Starting install"
	@${compose} up -d
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
#-----------------------------------------------------------------------------------------------------------------------
# Variables
#-----------------------------------------------------------------------------------------------------------------------

compose = docker-compose -f ./docker/docker-compose.yml
exec = docker exec -it php_spotify bash
