# CodeIgniter 4 Framework

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds the distributable version of the framework.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Contributing

We welcome contributions from the community.

Please read the [*Contributing to CodeIgniter*](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the development repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library


## Excel Import Module Requirements

This application includes an **Excel Import** feature for bulk asset data input.  
To enable this functionality, an additional PHP library is required.

---

### Required Package

The system requires the following Composer package:

```
PhpOffice PhpSpreadsheet
```

This library is used to read and process Excel files in `.xls` and `.xlsx` format.

---

### Installation

Run the following command in the root directory of the project:

```bash
composer require phpoffice/phpspreadsheet
```

This command will generate the following required files and folders:

```
/vendor
/vendor/autoload.php
/vendor/phpoffice/phpspreadsheet
```

These files are required for the Excel import controller to function properly.

---

### Shared Hosting (No Composer Support)

If the production server does not support Composer:

1. Run the installation on your local machine:
   ```bash
   composer require phpoffice/phpspreadsheet
   ```

2. Upload the following files and folders to the server:
   ```
   /vendor
   composer.json
   composer.lock
   ```

3. Make sure this file exists on the server:
   ```
   /vendor/autoload.php
   ```

---

### Why This Is Required

The Excel Import feature uses the following classes:

```php
PhpOffice\PhpSpreadsheet\IOFactory
PhpOffice\PhpSpreadsheet\Shared\Date
```

Without the **PhpSpreadsheet** library, the system will not be able to read Excel files and the import process will fail.

---

### Summary

To use the Excel Import feature, the following requirements must be met:

- PHP **8.3+**
- CodeIgniter **4**
- **PhpOffice PhpSpreadsheet** installed via Composer

These requirements apply to both **development** and **production** environments.
