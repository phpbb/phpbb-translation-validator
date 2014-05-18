<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\file;

class validate_defined_in_phpbb_test extends \official\translationvalidator\tests\validator\file\test_base
{
	static public function validate_defined_in_phpbb_data()
	{
		return array(
			array('in_phpbb/valid.php', array()),
			array('in_phpbb/invalid.php', array(array('type' => 'fail', 'message' => 'FILE_MISSING_IN_PHPBB-in_phpbb/invalid.php', 'source' => null, 'origin' => null))),
		);
	}

	/**
	* @dataProvider validate_defined_in_phpbb_data
	*/
	public function test_validate_defined_in_phpbb($file, $expected)
	{
		$this->validator->validate_defined_in_phpbb($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
