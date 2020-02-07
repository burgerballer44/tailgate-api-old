# Tailgate API

## Composer

Install the dependencies by running `composer install`. Run `composer install --no-dev` for production environments.

## Environment

Create a .env file by copying and the .env.example file. Update the values accordingly in the new .env file.  
Create a phinx.php file by copying and the phinx.php.example file. Update the values accordingly in the new phinx.php file.

## Phinx Migration

Create a mysql database called tailgate. Update the .env accordingly.  
Migrate the database by running `./vendor/bin/phinx migrate`. The database can be rolled back by running `./vendor/bin/phinx rollback -t 0`.

Insert a client id and client secret into the oauth_clients table. The client_id can be whatever. The client_secret should be output by `password_hash("your_password", PASSWORD_BCRYPT)` or the hashing interface you choose to implement.

## Deployment

`var` directory needs to be writable.  
Remove container cache file at `var/cache/container`.  