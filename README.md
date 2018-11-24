# uct

## Universal Code Translation

### Prepare packages

    composer update -a

### Init

    ./uct app:init

### Bootstrap database

    ./uct sql:bootstrap

### Seed database with some add. codes

Check for available SQLs with

    ./uct sql:list

Load with

    ./uct sql:load <file name>
