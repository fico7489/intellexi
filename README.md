# FH

## Set up project

- git clone https://github.com/fico7489/intellexi
- cd intellexi
- cp .env.example .env
- docker compose up -d
- docker compose exec php sh
- composer install
- php artisan migrate
- php artisan db:seed
- vendor/bin/phpunit


