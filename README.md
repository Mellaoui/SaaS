# Task Management Platform

## Stack
- Laravel
- OpenAPI (for documentation & design)
- Database(MySQL / mariaDB)


## Steps for setup

- Setup your  app
- run `composer install` (to install all the packages )
- run `cp .env.example .env` (to create .env file )
- run `php artisan key:generate` (to generate app key)
- run `php artisan migrate` (to migrate the database)
- run `npm insatll && npm run dev` 


## Testing

- We're using PHPUNit for running tests, so run `php artisan test` to run the full suite

## Updating routes & models

- First, to check the database structure check `database/migrations`
- To check the relationship check `app/models/*`
- All routes are to be kept in `routes`.
