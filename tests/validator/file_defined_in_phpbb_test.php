<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_defined_in_phpbb_test extends phpbb_ext_official_translationvalidator_tests_validator_file_test_base
{
	static public function validate_defined_in_phpbb_data()
	{
		return array(
			array('in_phpbb/valid.php', array()),
			array('in_phpbb/invalid.php', array(array('fail', 'FILE_MISSING_IN_PHPBB-in_phpbb/invalid.php'))),
		);
	}

	/**
	* @dataProvider validate_defined_in_phpbb_data
	*/
	public function test_validate_defined_in_phpbb($file, $expected)
	{
		$this->validator->validate_defined_in_phpbb($file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
