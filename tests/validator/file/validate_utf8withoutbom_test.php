<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\file;

class validate_utf8withoutbom_test extends \official\translationvalidator\tests\validator\file\test_base
{
	static public function validate_utf8withoutbom_data()
	{
		return array(
			array('utf8withoutbom/without.php', array()),
			array('utf8withoutbom/with.php', array(array('type' => 'fail', 'message' => 'FILE_SAVED_UTF8-utf8withoutbom/with.php', 'source' => null, 'origin' => null))),
		);
	}

	/**
	* @dataProvider validate_utf8withoutbom_data
	*/
	public function test_validate_utf8withoutbom($file, $expected)
	{
		$this->validator->validate_utf8withoutbom($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
