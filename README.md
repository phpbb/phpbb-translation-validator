# phpBB Translation Validator

This console application allows you to validate [phpBB](https://www.phpbb.com) language packages.

## Requirements

This extension requires PHP 5.5.9 or above.

### Installation

Firstly, download the latest British English (`en`) [language pack from phpBB.com](http://www.phpbb.com/customise/db/translation/british_english/) as this is the default source language. Then clone this repository and download the dependencies:

    git clone https://github.com/phpbb/phpbb-translation-validator.git
    composer.phar install

For the easiest results, create a directory called `3.2` or `3.3` in the root of the Translation Validator. Upload the `en` language page into this directory, along with the languages you wish to test. Which leads e.g. to:

    phpbb-translation-validator/3.2/en/
    phpbb-translation-validator/3.2/fr/
    phpbb-translation-validator/3.3/en/
    phpbb-translation-validator/3.3/fr/
    phpbb-translation-validator/translation.php

The simplest way to validate is to then run this command (the final argument is the language you wish to test and that has already been uploaded to the `3.2` directory; eg. `fr` for French):

     php translation.php validate fr

There are more arguments that can be supplied. For example, suppose you wanted to have your `3.2` directory in a different location, you wanted to explicitly specify phpBB version 3.2 (default validation is against 3.3), you wanted to run in safe mode and you wanted to see all notices displayed - you would run this command:

     php translation.php validate fr 
        --package-dir=/path/to/your/3.2 
        --phpbb-version=3.2 
        --safe-mode 
        --display-notices

The `--safe-mode` flag indicates that you want to parse files instead of directly including them. This is useful if you want to run validations on a web server.

## Tests

[![Build Status](https://travis-ci.org/phpbb/phpbb-translation-validator.png?branch=master)](https://travis-ci.org/phpbb/phpbb-translation-validator)

Add the TranslationValidator as a dependency:

		{
			"require-dev": {
				"phpbb/translation-validator": "1.5.*"
			}
		}

Then add a `php vendor/bin/translation.php` call to your `.travis.yml` file.

We use Travis-CI as a continuous integration server and phpunit for our unit testing. See more information on the [phpBB development wiki](https://wiki.phpbb.com/Unit_Tests).

To run the unit tests locally, use this command:

     php vendor/phpunit/phpunit/phpunit tests/

## Contributing

If you notice any problems with this application, please raise an issue at https://github.com/phpbb/phpbb-translation-validator/issues.

To submit your own code contributions, please fork the project and submit a pull request at https://github.com/phpbb/phpbb-translation-validator/pulls.

When a new version is released, the version number will be updated in `composer.json` and `translation.php`. A new tag will be created and the package will become available at https://packagist.org/packages/phpbb/translation-validator.

## License

[GPLv2](license.txt)
