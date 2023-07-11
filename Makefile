autoload:
	composer dump-autoload
install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
gendiff:
	./bin/gendiff
validate:
	composer validate
rec:
	asciinema rec