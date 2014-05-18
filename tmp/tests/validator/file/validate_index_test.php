<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\file;

class validate_index_test extends \official\translationvalidator\tests\validator\file\test_base
{
	static public function validate_index_data()
	{
		return array(
			array('index/empty_index.htm', array()),
			array('index/default_index.htm', array()),
			array('index/invalid_index.htm', array(array('type' => 'fail', 'message' => 'INVALID_INDEX_FILE-index/invalid_index.htm', 'source' => null, 'origin' => null))),
		);
	}

	/**
	* @dataProvider validate_index_data
	*/
	public function test_validate_index($file, $expected)
	{
		$this->validator->validate_index_file($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
