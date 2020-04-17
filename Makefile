node_modules: package.json yarn.lock
	#nvm use stable
	yarn install

vendor: composer.json composer.lock
	composer install

dev: vendor node_modules
	symfony local:server:start -d
	symfony run -d yarn encore dev --watch
	symfony server:log

dev-stop:
	symfony local:server:stop

prod: vendor node_modules
	sudo chown -R www-data:www-data var
	sudo -u www-data bin/console ca:cl --env=prod
	#sudo -u www-data
