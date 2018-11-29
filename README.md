# uct

## Universal Code Translation

### Prepare packages

    composer update -a

### Init

    ./uct app:init

This will ask for your database credentials and admin password to store into `.env`.

### Bootstrap database

    ./uct sql:bootstrap <primary language>

Define, which language your primary or native language is.

It can be one of `en`, `de` or `fr` at the moment.

### Seed database with additional codes

Check for available SQLs with:

    ./uct sql:list

and load with:

    ./uct sql:load <file name>
