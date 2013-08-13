<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_key_validate_string_test extends phpbb_ext_official_translationvalidator_tests_validator_key_test_base
{
	static public function validate_string_data()
	{
		return array(
			array('Plain', 'foobar', 'foobar', array()),
			array('String', 'foobar %s', 'foo %s bar', array()),
			array('2 strings', 'foobar %s %s', 'foo %1$s %2$s bar', array()),
			array('2 strings reordered', 'foobar %s %s', 'foo %2$s %1$s bar', array()),
			array('Integer', 'foobar %d', 'foo %d bar', array()),
			array('2 integers', 'foobar %d %d', 'foo %1$d %2$d bar', array()),
			array('2 integers reordered', 'foobar %d %d', 'foo %2$d %1$d bar', array()),
			array('Mixed', 'foobar %d %s', 'foo %d %s bar', array()),
			array('Mixed reordered', 'foobar %d %s', 'foo %s %d bar', array()),
			array('Mixed reordered #$', 'foobar %d %s', 'foo %2$s %1$d bar', array()),
			array('Mixed missing int', 'foobar %d %s', 'foo %s bar', array(
				array('notice', 'INVALID_NUM_ARGUMENTS--Mixed missing int-integer-1-0'),
			)),
			array('Mixed missing string', 'foobar %d %s', 'foo %d bar', array(
				array('warning', 'INVALID_NUM_ARGUMENTS--Mixed missing string-string-1-0'),
			)),
			array('Mixed invalid int', 'foobar %d %s', 'foo %s %d %d bar', array(
				array('fail', 'INVALID_NUM_ARGUMENTS--Mixed invalid int-integer-1-2'),
			)),
			array('Mixed invalid string', 'foobar %d %s', 'foo %s %s %d bar', array(
				array('fail', 'INVALID_NUM_ARGUMENTS--Mixed invalid string-string-1-2'),
			)),
		);
	}

	/**
	* @dataProvider validate_string_data
	*/
	public function test_validate_string($key, $against_language, $validate_language, $expected)
	{
		$this->validator->validate_string('', $key, $against_language, $validate_language);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
