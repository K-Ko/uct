# uct
## Universal Code Translation

### Prepare packages

    composer update -a

### Init

    app/console app:init

### Bootstrap database

    app/console sql:bootstrap

### Seed database with some add. codes

Check for available SQLs with

    app/console sql:list

Load with

    app/console sql:load <file name>
