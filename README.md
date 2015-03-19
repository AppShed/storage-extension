# AppShed Storage Extension

This extension allows an AppShed user to easily save and retrieve forms in AppShed.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/46def975-5394-4fab-8e7f-12938f9a9167/mini.png)](https://insight.sensiolabs.com/projects/46def975-5394-4fab-8e7f-12938f9a9167)

## Updating the server

As ever:

    npm install
    composer install
    ./app/console doctrine:schema:update
    rm -rf app/cache/prod
    ./app/console cache:clear --env=prod
    ./app/console assetic:dump --env=prod
    service apache2 restart

