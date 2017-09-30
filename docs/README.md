# Readme

This is basic overview of system requirements, installation etc.
For more detailed developer documentation see [DEVELOPING.md](DEVELOPING.md) file.

For information about deployment see [DEPLOYMENT.md](DEPLOYMENT.md) file.

## Scope

This project consist of one custom-made website with administration usable
by instructed individuals, some knowledge of HTML can be useful as the decision
made at the beginning was to avoid WYSIWYG editors for output unpredactibility.

## Requirements

These are the requirements for hosting:

  - SSH access,
  - Git executable,
  - PHP executable from command line,
  - Apache 2.0+ (with activated `mod_rewrite` module)
  - PHP 5.6 (higher versions should be ok, though some warnings are expected)
  - MySQL database
  - Nette capable hosting (see https://doc.nette.org/cs/2.3/requirements)

The application doesn't any extra treatment to achieve desired performance as
the is is quite simple.

## Deployment

- Checkout the project do desired directory
- Create `/project/config/config.local.neon` where you put database configuration
  and `maintenance.key` which maybe needed for deleting cache on production (when created by `www-data` user).
- Set `0777` permissions to `log` and `temp` recursively.
- Ensure that `app/config/*`, `temp`, `log` are not available from browser as sensitive data could leak here.
- Run `php composer.phar install` to install necessary dependencies.
- Install database (`app/updatedb.php` or manually by executing `resources/migrations` in hinted order).
- For local development run `php -S localhost:8090` and then go to browser `http://localhost:8090`.

## Authors

- Jan Drábek <me@jandrabek.cz>
- Martin Ukrop <mukrop@mail.muni.cz>