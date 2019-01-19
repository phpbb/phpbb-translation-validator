# phpBB Translation Validator Extension

Allows to validate **phpBB** language packages.

## Requirement

This extension requires PHP 5.6 to 7.1 to be set up.

## Installation for validating a language package locally

*Note:* the validator only works when you have the source language pack in your directory (default source language is `en`).

1. [Download](https://github.com/phpbb/phpbb-translation-validator/archive/master.zip) the package.
2. Run `php composer.phar install` to download the dependencies
3. Run `php src/Phpbb/TranslationValidator/PhpbbTranslationValidator.php validate --help` to get information how to run the validator

### Example

1. Create a directory called `3.2` in the root
2. Download the British English (`en`) language pack from phpBB.com as this is the default source language. Upload the `en` directory into `3.2`.
3. With either your own language, or another language pack, upload it into the `3.2` directory as well.
4. If the second language was French (`fr`) for example, you would then run: `php src/Phpbb/TranslationValidator/PhpbbTranslationValidator.php validate fr --phpbb-version=3.2` to begin the validation.

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

## License

[GPLv2](license.txt)
