<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_key_validate_array_key_test extends phpbb_ext_official_translationvalidator_tests_validator_key_test_base
{
	static public function validate_array_key_data()
	{
		return array(
			// Invalid types
			array('String-Array', 'foobar', array(), array(
				array('type' => 'fail', 'message' => 'INVALID_TYPE--String-Array-string-array', 'source' => null, 'origin' => null),
			)),
			array('Array-String', array(), 'foobar', array(
				array('type' => 'fail', 'message' => 'INVALID_TYPE--Array-String-array-string', 'source' => null, 'origin' => null),
			)),
			array('String-Int', 'foobar', 0, array(
				array('type' => 'fail', 'message' => 'INVALID_TYPE--String-Int-string-integer', 'source' => null, 'origin' => null),
			)),
			array('Int-String', 0, 'foobar', array(
				array('type' => 'fail', 'message' => 'INVALID_TYPE--Int-String-integer-string', 'source' => null, 'origin' => null),
			)),
			array('Array-Int', array(), 0, array(
				array('type' => 'fail', 'message' => 'INVALID_TYPE--Array-Int-array-integer', 'source' => null, 'origin' => null),
			)),
			array('Int-Array', 0, array(), array(
				array('type' => 'fail', 'message' => 'INVALID_TYPE--Int-Array-integer-array', 'source' => null, 'origin' => null),
			)),

			// Missing keys
			array('MissingKey', array('foo' => 'bar'), array('bar' => 'bar2'), array(
				array('type' => 'fail', 'message' => 'LANG_ARRAY_MISSING--MissingKey-foo', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'LANG_ARRAY_INVALID--MissingKey-bar', 'source' => null, 'origin' => null),
				array('type' => 'warning', 'message' => 'KEY_NOT_VALIDATED--MissingKey.bar', 'source' => null, 'origin' => null),
			)),
			array('MissingIntKey', array(1 => 'bar', 2 => 'bars'), array(1 => 'bar/s'), array(
			)),
		);
	}

	/**
	* @dataProvider validate_array_key_data
	*/
	public function test_validate_array_key($key, $against_language, $validate_language, $expected)
	{
		$this->validator->validate_array_key('', $key, $against_language, $validate_language);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
