# phpBB Translation Validator Extension

Allows to validate **phpBB** language packages.

## Requirement

This extension requires PHP 5.3 or above.

### Installation

1. `git clone https://github.com/phpbb/phpbb-translation-validator.git` 
2. Create a directory called `3.2` in the root
3. Download the British English (`en`) language pack from phpBB.com as this is the default source language. Upload the `en` directory into `3.2`.
4. With either your own language, or another language pack, upload it into the `3.2` directory as well.
5. If the second language was French (`fr`) for example, you would then run: `php translation.php validate fr --phpbb-version=3.2` to begin the validation.

Append the `--safe-mode` flag to run on a web server. This option will cause files to be parsed instead of included.

## Installation for validating a language package on TravisCI

1. Add the TranslationValidator as a dependency:

		{
			"require-dev": {
				"phpbb/translation-validator": "1.5.*"
			}
		}

2. Add the `php vendor/bin/PhpbbTranslationValidator.php` call you run locally to your `.travis.yml`

## Tests and Continuous Intergration

[![Build Status](https://travis-ci.org/phpbb/phpbb-translation-validator.png?branch=master)](https://travis-ci.org/phpbb/phpbb-translation-validator)

We use Travis-CI as a continous intergtation server and phpunit for our unit testing. See more information on the [phpBB development wiki](https://wiki.phpbb.com/Unit_Tests).

To run the unit tests locally, use this command:

     php vendor/phpunit/phpunit/phpunit src/Phpbb/TranslationValidator/Tests/

## License

[GPLv2](license.txt)
