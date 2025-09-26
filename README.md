# phpBB Translation Validator - master-dev 

This console application allows you to validate [phpBB](https://www.phpbb.com) language packages.

## Requirements

This tool requires PHP 8.1 or above.

### Installation

Firstly, download the latest British English (`en`) [language pack from phpBB.com](http://www.phpbb.com/customise/db/translation/british_english/) as this is the default source language. Then clone this repository and download the dependencies:

    git clone https://github.com/phpbb/phpbb-translation-validator.git
    composer.phar install

For the easiest results, create a directory called `4.0` in the root of the Translation Validator. Upload the `en` language page into this directory, along with the languages you wish to test. Which leads e.g. to:

    phpbb-translation-validator/4.0/en/
    phpbb-translation-validator/4.0/de/
    phpbb-translation-validator/translation.php

The simplest way to validate is to then run this command (the final argument is the language you wish to test and that has already been uploaded to the `4.0` directory; eg. `fr` for French):

     php translation.php validate fr

There are more arguments that can be supplied. For example, suppose you wanted to have your `4.x` directory in a different location, you wanted to explicitly specify phpBB version 4.x (default validation is against 4.0), you wanted to run in safe mode and you wanted to see all notices displayed - you would run this command:

     php translation.php validate fr 
        --package-dir=/path/to/your/4.0 
        --phpbb-version=4.0 
        --safe-mode 
        --display-notices

The `--safe-mode` flag indicates that you want to parse files instead of directly including them. This is useful if you want to run validations on a web server.

If you are missing the English language files for the official Viglink extension, they can be easily donwloaded using this command:

    php translation.php download --files=phpbb-extensions/viglink --phpbb-version=4.0

## Tests

![GitHub Actions CI](https://github.com/phpbb/phpbb-translation-validator/actions/workflows/phpunit.yaml/badge.svg?branch=master)

In your project you can add phpBB Translation Validator as a dependency:

		{
			"require-dev": {
				"phpbb/translation-validator": "2.0.*"
			}
		}

Then add a `php vendor/bin/translation.php` call to your workflow.

We use GitHub Actions as a continuous integration server and phpunit for our unit testing.

To run the unit tests locally, use this command:

     php vendor/phpunit/phpunit/phpunit tests/

## Contributing

If you notice any problems with this application, please raise an issue at https://github.com/phpbb/phpbb-translation-validator/issues.

To submit your own code contributions, please fork the project and submit a pull request at https://github.com/phpbb/phpbb-translation-validator/pulls.

When a new version is released, the version number will be updated in `composer.json` and `translation.php`. A new tag will be created and the package will become available at https://packagist.org/packages/phpbb/translation-validator.

## License

[GPLv2](license.txt)
