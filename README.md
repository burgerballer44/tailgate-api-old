# Tailgate API

## Composer

Install the dependencies by running `composer install`. Run `composer install --no-dev` for production environments.

## Environment

Create a .env file by copying and the .env.example file. Update the values accordingly in the new .env file.

## Phinx Migration

Create a mysql database called tailgate. Update the .env accordingly.  
Migrate the database by running `./vendor/bin/phinx migrate`. The database can be rolled back by running `./vendor/bin/phinx rollback -t 0`.

## Deployment

`var` directory needs to be writable.  
Remove container cache file at `var/cache/container`.  