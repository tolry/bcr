node_modules: package.json yarn.lock
	yarn install

vendor: composer.json composer.lock
	composer install

dev: vendor node_modules
	symfony local:server:start -d
	symfony run -d yarn encore dev --watch
	symfony server:log

dev-stop:
	symfony local:server:stop
