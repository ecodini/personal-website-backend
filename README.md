# php-backend-template
simple php backend template without any framework

## requirements
- install [PHP 8.2](https://www.php.net/manual/en/install.php) and [Composer](https://getcomposer.org/doc/00-intro.md)
- setup [PHP-FPM](https://www.php.net/manual/en/install.fpm.php) on your web server

## setup
- run `composer update` in the root directory. this installs all needed dependencies.
- run `composer dump-autoload` to update php autoload.
- run `composer run start-dev` to start the project on port `8080`.

you can set up an apache server with the PHP module enable using the `.htaccess` file provided.
