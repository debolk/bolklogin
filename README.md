# BolkLogin
OAuth2 authorization server for Bolk-accounts

## Deployment
1. Install on a VPS.
1. Ensure Apache 2, PHP5 and MySQL are present.
1. Create database and create the tables from database.sql.
1. Copy `web/.htaccess.example` to `web/.htaccess` and fill in the details
1. Install php8.3-ldap
1. Install dependencies by running `composer install`
1. Point webroot of apache to `web`

## License
Copyright 2012-2014,2016 Jakob Buis and Max Maton.
