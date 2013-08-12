<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_line_endings_test extends phpbb_ext_official_translationvalidator_tests_validator_file_test_base
{
	static public function validate_line_endings_data()
	{
		return array(
			array('line_endings/valid.php', array()),
			array('line_endings/invalid.php', array(array('fail', 'FILE_UNIX_ENDINGS-line_endings/invalid.php'))),
		);
	}

	/**
	* @dataProvider validate_line_endings_data
	*/
	public function test_validate_line_endings($file, $expected)
	{
		$this->validator->validate_line_endings($file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
