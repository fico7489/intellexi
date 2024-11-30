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


### Maxwell

https://redis.io/docs/latest/develop/data-types/lists/
https://maxwells-daemon.io/config/
https://redis.io/glossary/redis-queue/

docker exec -it intellexi_maxwell sh
bin/maxwell --user='root' --password='root' --host='mysql' --producer=stdout
bin/maxwell  --user='root' --password='root' --host='mysql' --producer=file --output_file="/app/test.txt"
bin/maxwell  --user='root' --password='root' --host='mysql' --producer=redis --redis_host=redis --redis_database=0
bin/maxwell  --user='root' --password='root' --host='mysql' --producer=redis --redis_host=redis --redis_database=0

### Redis

redis-cli
KEYS *
SET title:1 "The Hobbit"
GET title:1
CONFIG GET databases
redis-cli -n 2 -> selects db 2
SUBSCRIBE maxwell




