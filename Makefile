tests:
	php -v
	docker compose up -d
	docker compose exec php composer install
	docker compose exec vendor/bin/phpunit
