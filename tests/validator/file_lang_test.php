<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_lang_test extends phpbb_ext_official_translationvalidator_tests_validator_file_test_base
{
	static public function validate_lang_data()
	{
		return array(
			array('language/lang.php', array(
				array('fail', 'FILE_INVALID_VARS-language/lang.php-lang'),
				array('fail', 'LANG_OUTPUT-language/lang.php-' . "\n\n"),
				array('fail', 'MISSING_KEY-language/lang.php-7_DAYS'),
				array('fail', 'INVALID_KEY-language/lang.php-8_DAYS'),
			)),
			array('language/lang2.php', array(
				array('fail', 'FILE_INVALID_VARS-language/lang2.php-lang'),
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
