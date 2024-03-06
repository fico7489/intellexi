# Intellexi projekt - Filip Horvat - 6.3.2024.

[Zadatak](/docs/assignment.docx)

## Set up project

- git clone https://github.com/fico7489/intellexi
- cd intellexi
- cp .env.example .env
- docker compose up -d
- docker compose exec php
- composer install
- php artisan migrate
- php artisan db:seed
- vendor/bin/phpunit
- curl --location --request POST 'http://localhost:5005/api/auth/login?email=administrator@example.com'

### With Makefile

- Set up local app: make install_local_app
- Run tests:  make install_tests
- Run tests with mysql:  make install_tests_mysql


## Notes

I did not work with Laravel for 1-2 years, so it took me a little more time.

Until now, I had not worked with the CQRS pattern; I only knew the basic concept.
In this project, I created a mix using resources that I found, and I probably did not hit 100% of all the points of CQRS. 
I looked into dozens of resources about CQRS, and in each of them, there were different notes on how to implement these patterns.

For this example, I used one questionable package, but it will serve the purpose for this test assignment.

The API is created as raw because there were no instructions to implement JSONAPI or something similar.

Most of the functionalities are covered with tests.


Pay attention to the following:
- Use composer - DONE!
- Use JWT tokens - DONE! For this testing occasion, I added logging with just an email because the user model in the requirements does not have a password.
- Pay attention to separation of concerns principle - DONE! I guess it refers to CQRS
- Error handling - DONE! I created a test for all kinds of errors; it could be extended to cover even more of them.
- API design - DONE! There were no special instructions, so I am not sure what I should pay attention to.
- Logging - DONE! Exceptions are logged by default. There were no special instructions, so I am not sure what I should pay attention to.
- Configuration management - DONE!
- Tests - DONE!
- Docker - DONE!

