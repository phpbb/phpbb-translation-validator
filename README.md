# phpBB Translation Validator Extension

## Requirement

This extension requires phpBB 3.1 to be installed.

## Installation

Clone into phpBB/ext/official/translationvalidator:

    git clone https://github.com/nickvergessen/phpbb3-translation-validator.git phpBB/ext/official/translationvalidator

Add to database by inserting a row into phpbb_ext

    INSERT INTO phpbb_ext (ext_name, ext_active, ext_state) VALUES ('official/translationvalidator', 0, '');

Go to "ACP" > "Customise" > "Extensions" and enable the "phpBB Translation Validator" extension.

##Tests and Continuous Intergration

[![Build Status](https://travis-ci.org/nickvergessen/phpbb3-translation-validator.png?branch=master)](https://travis-ci.org/nickvergessen/phpbb3-translation-validator)

We use Travis-CI as a continous intergtation server and phpunit for our unit testing. See more information on the [phpBB development wiki](https://wiki.phpbb.com/Unit_Tests).

## License

[GPLv2](license.txt)
