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
- vendor/bin/phpunit


Notes:

Do sad nisam radio sa CQRS patternom, znao sam samo površno što je to, pa sam u ovome projektu složio neki mix što sam proučio u dva dana 
tako da vjerojatno nisam 100% pogodio. Pogledao sam desetak članaka o CQRS te se u svakome se radi drugačije i koliko vidim nema baš straightforward uputa.

API je složen sirov, vraća sirove podatke nije implementiran JSONAPI ili nešto slično.

Testovima je pokrivena sva authentifikacija (tko šta može).


Pay attention to the following:
- Use composer - DONE!
- Use JWT tokens - DONE! Za ovaj testni primjer je napravljeno da se user ulogira samo preko maila jer entiteti po specifikaciji nemaju password
- Pay attention to separation of concerns principle - DONE! to se valjda misli na CQRS
- Error handling - DONE! Napisao sam test za sve api errore, to se još može proširiti na potrebne testove
- API design - DONE! nije bilo nekakvih posebbnih uputa tako da ne znam na što sam morao obračati pažnju
- Logging - DONE! Exceptioni se logaju po defaultu, ne znam što je ovdje točno trebalo, ja složim ali ne znam što
- Configuration management - DONE!
- Tests - DONE!
- Docker - DONE!






