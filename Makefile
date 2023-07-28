autoload:
	composer dump-autoload
install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
gendiff:
	gendiff bin/file1.json bin/file2.json
validate:
	composer validate
rec:
	asciinema rec
gendiffTest:
	composer exec --verbose phpunit tests