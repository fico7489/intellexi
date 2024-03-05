# Test

commands:
php artisan migrate
php artisan db:wipe
php artisan make:migration User
php artisan db:seed --class=UserSeeder
php artisan db:seed
php artisan jwt:secret
php artisan route:list

http://localhost:5005/api/race



- vendor/bin/php-cs-fixer fix
- vendor/bin/phpstan analyse
- vendor/bin/phpunit





