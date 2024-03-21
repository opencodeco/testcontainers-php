.PHONY: install
install:
	@composer install

.PHONY: test
test:
	@vendor/bin/phpunit

.PHONY: lint
lint:
	@vendor/bin/php-cs-fixer fix .
