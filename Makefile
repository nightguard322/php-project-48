autoload:
	composer dump-autoload
install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
gendiff:
	./bin/gendiff bin/file1.json bin/file2.json
validate:
	composer validate
rec:
	asciinema rec
gendiffTest:
	composer exec --verbose phpunit tests
coverage:
	composer exec --verbose phpunit tests -- --coverage-text --coverage-html report --coverage-clover clover.xml