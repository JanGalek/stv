.PHONY: phpstan cs csf

phpstan:
	vendor/bin/phpstan analyse ./app ./src
cs:
	vendor/bin/phpcs --standard=ruleset.xml app src tests
csf:
	vendor/bin/phpcbf --standard=ruleset.xml app src tests