<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\file;

class validate_lang_test extends \official\translationvalidator\tests\validator\file\test_base
{
	static public function validate_lang_data()
	{
		return array(
			array('language/lang.php', array(
				array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-language/lang.php-lang', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'LANG_OUTPUT-language/lang.php-' . "\n\n", 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'MISSING_KEY-language/lang.php-7_DAYS', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'INVALID_KEY-language/lang.php-8_DAYS', 'source' => null, 'origin' => null),
			)),
			array('language/lang2.php', array(
				array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-language/lang2.php-lang', 'source' => null, 'origin' => null),
			)),
		);
	}

	/**
	* @dataProvider validate_lang_data
	*/
	public function test_validate_lang($file, $expected)
	{
		$this->validator->validate_lang_file($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
