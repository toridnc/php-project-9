PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public

lint:
	phpcs --standard=PSR12 /path/to/code-directory

install:
	composer install

composer:
	composer validate
	composer dump-autoload
	composer update