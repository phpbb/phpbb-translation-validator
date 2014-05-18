<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\file;

class validate_line_endings_test extends \official\translationvalidator\tests\validator\file\test_base
{
	static public function validate_line_endings_data()
	{
		return array(
			array('line_endings/valid.php', array()),
			array('line_endings/invalid.php', array(array('type' => 'fail', 'message' => 'FILE_UNIX_ENDINGS-line_endings/invalid.php', 'source' => null, 'origin' => null))),
		);
	}

	/**
	* @dataProvider validate_line_endings_data
	*/
	public function test_validate_line_endings($file, $expected)
	{
		$this->validator->validate_line_endings($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
