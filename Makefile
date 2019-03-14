flushdb:
	php bin/console doctrine:database:drop --force
	php bin/console doctrine:database:create
	php bin/console doctrine:migration:migrate
	php bin/console doctrine:fixtures:load

cs:
	./vendor/bin/php-cs-fixer fix --verbose --show-progress dots

run
	php bin/bash server:run 127.0.0.1:80
