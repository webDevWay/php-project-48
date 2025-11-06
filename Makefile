install:
	composer install

validate:
	composer validate

autoload:
	composer dump-autoload

require:
	composer require
	
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests

test:
	composer test

lint-fix:
	composer lint-fix