<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_help_test extends phpbb_ext_official_translationvalidator_tests_validator_test_base
{
	static public function validate_help_data()
	{
		return array(
			array('help/valid.php', array()),
			array('help/no_help.php', array(array('fail', 'FILE_INVALID_VARS-help/no_help.php-help'))),
			array('help/invalid_help_var.php', array(array('fail', 'FILE_INVALID_VARS-help/invalid_help_var.php-help'))),
			array('help/additional_variable.php', array(array('fail', 'FILE_INVALID_VARS-help/additional_variable.php-help'))),
			array('help/invalid_help.php', array(
				array('fail', 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-a:1:{i:0;s:2:&quot;--&quot;;}'),
				array('fail', 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-a:2:{i:0;s:2:&quot;--&quot;;i:2;s:2:&quot;--&quot;;}'),
				array('fail', 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-a:1:{s:3:&quot;lol&quot;;s:3:&quot;bar&quot;;}'),
				array('fail', 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-s:3:&quot;foo&quot;;'),
				array('fail', 'FILE_HELP_ONE_BREAK-help/invalid_help.php'),
			)),
		);
	}

	/**
	* @dataProvider validate_help_data
	*/
	public function test_validate_help($iso_file, $expected)
	{
		$this->validator->validate_help_file($iso_file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
