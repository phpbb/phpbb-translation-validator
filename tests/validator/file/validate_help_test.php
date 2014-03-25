<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\file;

class validate_help_test extends \official\translationvalidator\tests\validator\file\test_base
{
	static public function validate_help_data()
	{
		return array(
			array('help/valid.php', array()),
			array('help/no_help.php', array(
				array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-help/no_help.php-help', 'source' => null, 'origin' => null),
			)),
			array('help/invalid_help_var.php', array(
				array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-help/invalid_help_var.php-help', 'source' => null, 'origin' => null),
			)),
			array('help/additional_variable.php', array(
				array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-help/additional_variable.php-help', 'source' => null, 'origin' => null),
			)),
			array('help/invalid_help.php', array(
				array('type' => 'fail', 'message' => 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-a:1:{i:0;s:2:&quot;--&quot;;}', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-a:2:{i:0;s:2:&quot;--&quot;;i:2;s:2:&quot;--&quot;;}', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-a:1:{s:3:&quot;lol&quot;;s:3:&quot;bar&quot;;}', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-s:3:&quot;foo&quot;;', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'FILE_HELP_ONE_BREAK-help/invalid_help.php', 'source' => null, 'origin' => null),
			)),
		);
	}

	/**
	* @dataProvider validate_help_data
	*/
	public function test_validate_help($file, $expected)
	{
		$this->validator->validate_help_file($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
