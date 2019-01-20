# phpBB Translation Validator

This console application allows you to validate [phpBB](https://www.phpbb.com) language packages.

## Requirements

This extension requires PHP 5.3.3 or above.

### Installation

Firstly, download the British English (`en`) language pack from phpBB.com as this is the default source language. Then clone this repository:

    git clone https://github.com/phpbb/phpbb-translation-validator.git
    
For the easiest results, create a directory called `3.2` in the root of the Translation Validator. Upload the `en` language page into this directory, along with the languages you wish to test.

The simplest way to validate is to then run this command (the final argument is the language you wish to test and that has already been uploaded to the `3.2` directory; eg. `fr` for French):

     php translation.php validate fr

There are more arguments that can be supplied. For example, suppose you wanted to have your `3.2` directory in a different location, you wanted to explicitly specify phpBB version 3.2, you wanted to run in safe mode and you wanted to see all notices displayed - you would run this command:

     php translation.php validate fr 
        --package-dir=/home/vagrant/phpbb/phpBB/array_parser/3.2 
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

Then add the `php translation.php` call you run locally to your `.travis.yml` file.

We use Travis-CI as a continous intergtation server and phpunit for our unit testing. See more information on the [phpBB development wiki](https://wiki.phpbb.com/Unit_Tests).

To run the unit tests locally, use this command:

     php vendor/phpunit/phpunit/phpunit tests/

## License

[GPLv2](license.txt)
