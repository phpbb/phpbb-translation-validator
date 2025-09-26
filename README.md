# phpBB Translation Validator

With the help of this command line application you are able 
to validate [phpBB](https://www.phpbb.com) language packs. 
This application runs on your local machine and can be integrated
into a [GitHub](https://www.github.com) repository. 

## 📋Requirements

This tool requires PHP 8.1 or above. In addition it needs several
symfony and other packages, which need to be downloaded and installed with [Composer](https://getcomposer.org).


## 🏗️ Installation

Clone this repository:

    git clone https://github.com/phpbb/phpbb-translation-validator.git
  
Install the dependencies with Composer: 

    composer.phar install

Create a directory called `4.0` in the root of the Translation Validator. Afterwards download 
the [British English language pack](http://www.phpbb.com/customise/db/translation/british_english/) 
and put its content into ``4.0/en/``. Do the same with the languages you wish to test. Which leads e.g. to:

    phpbb-translation-validator/4.0/en/
    phpbb-translation-validator/4.0/de/
    phpbb-translation-validator/4.0/fr/
    phpbb-translation-validator/translation.php

## ⚗️ Validate language packs

The simplest way to validate this language packages, 
is to open a command line tool in the validator directory. 
Then run this command (the final argument is the language you wish to test and that has already been stored to the `4.0` directory; e.g. `fr` for French):

     php translation.php validate fr

There are more arguments that can be supplied. For example, suppose you wanted to have your `4.x` directory in a different location, you wanted to explicitly specify phpBB version 4.x (default validation is against 4.0), you wanted to run in safe mode and you wanted to see all notices displayed - you would run this command:

     php translation.php validate fr 
        --package-dir=/path/to/your/4.0 
        --phpbb-version=4.0 
        --safe-mode 
        --display-notices

The `--safe-mode` flag indicates that you want to parse files instead of directly including them. 
This is useful if you want to run validations on a web server.

If you are missing the English language files for the official Viglink extension, 
they can be easily donwloaded using this command:

    php translation.php download --files=phpbb-extensions/viglink --phpbb-version=4.0

## 🛠️ Integration to your Repository

In your project you can add phpBB Translation Validator as a dependency:

		{
			"require-dev": {
				"phpbb/translation-validator": "2.0.*"
			}
		}

Then add a `php vendor/bin/translation.php` call to your workflow.

We use GitHub Actions as a continuous integration server and phpunit for our unit testing.

### 🏠 Local phpunit execution

To run the unit tests locally, use this command:

     php vendor/phpunit/phpunit/phpunit tests/

## 🤖 Tests

![GitHub Actions CI](https://github.com/phpbb/phpbb-translation-validator/actions/workflows/phpunit.yaml/badge.svg?branch=master)

## 🧑‍💻 Contributing

If you notice any problems with this application, please raise an issue at the [Github-Repository](https://github.com/phpbb/phpbb-translation-validator/issues).

To submit your own code contributions, please fork the project and submit a pull request at [Github-Repository](https://github.com/phpbb/phpbb-translation-validator/pulls).

When a new version is released, the version number will be updated in `composer.json` and `translation.php`. A new tag will be created and the package will become available at [Packagist](https://packagist.org/packages/phpbb/translation-validator).

## 📜 License

[GNU General Public License v2](license.txt)
