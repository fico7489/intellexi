install_tests:
	docker compose up -d
	docker compose exec php composer install
	docker compose exec php vendor/bin/phpunit

install_tests_mysql:
	docker compose up -d
	docker compose exec php composer install
	docker compose exec --env=DB_CONNECTION=mysql --env=DB_HOST=mysql --env=DB_PORT=3306 --env=DB_DATABASE=intellexi_testing --env=DB_USERNAME=root --env=DB_PASSWORD=root php vendor/bin/phpunit

install_local_app:
	cp .env.example .env
	docker compose up -d
	docker compose exec php composer install
	docker compose exec php php artisan migrate
	docker compose exec php php artisan db:seed
	curl --location --request POST 'http://localhost:5005/api/auth/login?email=administrator@example.com'
