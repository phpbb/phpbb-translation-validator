<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\key;

class validate_test extends \official\translationvalidator\tests\validator\key\test_base
{
	static public function validate_data()
	{
		return array(
			array('String-Array', 'foobar', array(), array(array('type' => 'fail', 'message' => 'INVALID_TYPE--String-Array-string-array', 'source' => null, 'origin' => null))),
			array('Array-String', array(), 'foobar', array(array('type' => 'fail', 'message' => 'INVALID_TYPE--Array-String-array-string', 'source' => null, 'origin' => null))),
			array('String-Int', 'foobar', 0, array(array('type' => 'fail', 'message' => 'INVALID_TYPE--String-Int-string-integer', 'source' => null, 'origin' => null))),
			array('Int-String', 0, 'foobar', array(array('type' => 'fail', 'message' => 'INVALID_TYPE--Int-String-integer-string', 'source' => null, 'origin' => null))),
			array('Array-Int', array(), 0, array(array('type' => 'fail', 'message' => 'INVALID_TYPE--Array-Int-array-integer', 'source' => null, 'origin' => null))),
			array('Int-Array', 0, array(), array(array('type' => 'fail', 'message' => 'INVALID_TYPE--Int-Array-integer-array', 'source' => null, 'origin' => null))),
		);
	}

	/**
	* @dataProvider validate_data
	*/
	public function test_validate($key, $against_language, $validate_language, $expected)
	{
		$this->validator->validate('', $key, $against_language, $validate_language);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
