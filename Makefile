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

lint-fix:
	composer lint-fix

test:
	composer exec --verbose phpunit tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover=build/logs/clover.xml

test-coverage-text:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text

test1:
	php ./bin/gendiff ./tests/fixtures/file1.yaml ./tests/fixtures/file2.yaml
test2:
	php ./bin/gendiff ./tests/fixtures/file1.json ./tests/fixtures/file2.json
test3:
	php ./bin/gendiff ./tests/fixtures/file1.json ./tests/fixtures/file2.json --format plain
test4:
	php ./bin/gendiff ./tests/fixtures/file1.yaml ./tests/fixtures/file2.yaml --format plain
test5:
	php ./bin/gendiff ./tests/fixtures/file1.yaml ./tests/fixtures/file2.yaml --format json
test6:
	php ./bin/gendiff ./tests/fixtures/file1.json ./tests/fixtures/file2.json --format json