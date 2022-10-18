# Test task for Knowledge City

### Requirements

* PHP 7.4 with PDO extension

### How to set up it locally

1. Create database in MySQL
2. Create tables in the database using SQLs from database/sql
3. Change config for database connection in config/database.php
4. Install dependencies using `composer install`
5. Seed data using `php database/seed.php`
6. Set up nginx (or other proxy) to proxy requests to public/index.php

### Tests

Run `composer test` to run tests
