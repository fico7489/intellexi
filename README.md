# Intellexi projekt - Filip Horvat - 6.3.2024.

## Set up project

git clone https://github.com/fico7489/intellexi
cd intellexi
cp .env.example .env
docker compose up -d
docker compose exec php
composer install
php artisan migrate
php artisan db:seed
vendor/bin/phpunit
curl --location --request POST 'http://localhost:5005/api/auth/login?email=administrator@example.com'

### With Makefile

Set up local app: make install_local_app
Run tests:  make install_tests
Run tests with mysql:  make install_tests_mysql


## Notes

Nisam već 1-2 godine radio u laravelu pa mi je trebalo malo duže.

Do sad nisam radio sa CQRS patternom, znao sam samo površno što je to, pa sam u ovome projektu složio neki mix što sam proučio u dva dana 
tako da vjerojatno nisam 100% pogodio. Pogledao sam desetak članaka o CQRS te se u svakome se radi drugačije i koliko vidim nema baš straightforward uputa.
Uzeo sam neki "upitan" paket za to ali poslužit će svrsi za ovaj testni zadatak.

API je složen sirov, vraća čiste podatke, nije implementiran JSONAPI ili nešto slično, nije bilo uputa.

Testovima je pokrivena sva authentifikacija (tko šta može).

Pay attention to the following:
- Use composer - DONE!
- Use JWT tokens - DONE! Za ovaj testni primjer je napravljeno da se user ulogira samo preko maila jer entiteti po specifikaciji nemaju password
- Pay attention to separation of concerns principle - DONE! to se valjda misli na CQRS
- Error handling - DONE! Napisao sam test za sve api errore, to se još može proširiti na potrebne testove
- API design - DONE! nije bilo nekakvih posebbnih uputa tako da ne znam na što sam morao obračati pažnju
- Logging - DONE! Exceptioni se logaju po defaultu, ne znam što je ovdje točno bio request da se složi
- Configuration management - DONE!
- Tests - DONE!
- Docker - DONE!

