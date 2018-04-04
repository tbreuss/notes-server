NAME = notes-server
PORT ?= 9999

default: test

test: phptest
	echo "Codeception started"
	vendor/bin/codecept -v run

phptest:
	echo "PHP server started"
	ENV=test php -S localhost:$(PORT) -t public >/dev/null 2>&1 & echo $!
