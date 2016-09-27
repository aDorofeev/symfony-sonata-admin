all: ready

ready: 
	@echo "update composer to last version"
	php composer.phar self-update

install:
	@echo "install vendors"
	php composer.phar install
	@echo "create assets dump"
	php app/console assets:install --symlink
	@echo "create database schema"
	php app/console doctrine:database:create
	@echo "load default fixtures"
	php app/console doctrine:fixtures:load
	
upgrade:
	php composer.phar update