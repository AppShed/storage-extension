# AppShed Storage Extension

This extension allows an AppShed user to easily save and retrieve forms in AppShed.

## Updating the server

As ever:

    npm install
    composer install
    ./app/console doctrine:schema:update
    rm -rf app/cache/prod
    ./app/console cache:clear --env=prod
    ./app/console assetic:dump --env=prod
    service apache2 restart

