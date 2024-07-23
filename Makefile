.PHONY: phpstan cs csf start stop restart in composer install logs

phpstan:
	vendor/bin/phpstan analyse ./app ./src
cs:
	vendor/bin/phpcs --standard=ruleset.xml app src tests
csf:
	vendor/bin/phpcbf --standard=ruleset.xml app src tests
start:
	docker compose up -d
stop:
	docker compose stop
restart:
	$(MAKE) stop
	$(MAKE) start
in:
	docker compose exec php83-fpm /bin/bash
composer:
	docker compose exec php83-fpm composer $(filter-out $@,$(MAKECMDGOALS))
install:
	docker compose exec php83-fpm composer install
logs:
	docker compose logs --follow

%:
	@: