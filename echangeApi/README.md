# SETUP

## SETUP LARAVEL

- `copy .env.localhost to .env`
- `update settings if needed`

- migrate database : `php artisan migrate` (maybe necessary to wipe database first `php artisan db:wipe`)
- create oauth2 passport clients : `php artisan passport:install`
- seed database `php artisan db:seed`
- sync permissions `php artisan app:permissions:sync`
- full command : `php artisan db:wipe && php artisan migrate && php artisan db:seed && php artisan passport:install && php artisan app:permissions:sync && php artisan app:options:sync`


- launch phpstan debug :  ./vendor/bin/phpstan analyse --debug
- launch cs fixer :  ./vendor/bin/pint

- Check all :  ./vendor/bin/phpstan analyse --debug && ./vendor/bin/pint
