# phpBB Translation Validator Extension

## Requirement

This extension requires phpBB 3.1 to be installed.

## Installation Translation Validator for validating a language package

[Download](https://github.com/nickvergessen/phpbb3-translation-validator/archive/master.zip) the package, unzip it and move the content to `phpBB/ext/official/translationvalidator`, so that the file `phpBB/ext/official/translationvalidator/ext.php` exists.

Go to "ACP" > "Customise" > "Extensions" and enable the "phpBB Translation Validator" extension.

## Validating a language package

In order to validate your language package, you need to put your language package into the following folder (unzipped) (replace {your-iso} with e.g. **en** ):

    phpBB/store/language-packages/{your-iso}/

The folders `phpBB/store/language-packages/{your-iso}/language/` and `phpBB/store/language-packages/{your-iso}/styles/` should exist. Now open

	phpBB/app.php/validate/{your-iso}

with your browser.

## Installation for Validation-Tool Development

Clone into `phpBB/ext/official/translationvalidator`:

    git clone https://github.com/nickvergessen/phpbb3-translation-validator.git phpBB/ext/official/translationvalidator

Go to "ACP" > "Customise" > "Extensions" and enable the "phpBB Translation Validator" extension.

##Tests and Continuous Intergration

[![Build Status](https://travis-ci.org/nickvergessen/phpbb-translation-validator.png?branch=master)](https://travis-ci.org/nickvergessen/phpbb-translation-validator)

We use Travis-CI as a continous intergtation server and phpunit for our unit testing. See more information on the [phpBB development wiki](https://wiki.phpbb.com/Unit_Tests).

## License

[GPLv2](license.txt)
