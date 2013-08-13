<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_key_validate_test extends phpbb_ext_official_translationvalidator_tests_validator_key_test_base
{
	static public function validate_data()
	{
		return array(
			array('String-Array', 'foobar', array(), array(array('fail', 'INVALID_TYPE--String-Array-string-array'))),
			array('Array-String', array(), 'foobar', array(array('fail', 'INVALID_TYPE--Array-String-array-string'))),
			array('String-Int', 'foobar', 0, array(array('fail', 'INVALID_TYPE--String-Int-string-integer'))),
			array('Int-String', 0, 'foobar', array(array('fail', 'INVALID_TYPE--Int-String-integer-string'))),
			array('Array-Int', array(), 0, array(array('fail', 'INVALID_TYPE--Array-Int-array-integer'))),
			array('Int-Array', 0, array(), array(array('fail', 'INVALID_TYPE--Int-Array-integer-array'))),
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
