<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\key;

class validate_string_test extends \official\translationvalidator\tests\validator\key\test_base
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
				array('type' => 'fail', 'message' => 'INVALID_NUM_ARGUMENTS--Mixed missing int-integer-1-0', 'source' =>  'foobar %d %s', 'origin' => 'foo %s bar'),
			)),
			array('Mixed missing string', 'foobar %d %s', 'foo %d bar', array(
				array('type' => 'fail', 'message' => 'INVALID_NUM_ARGUMENTS--Mixed missing string-string-1-0', 'source' =>  'foobar %d %s', 'origin' => 'foo %d bar'),
			)),
			array('Mixed invalid int', 'foobar %d %s', 'foo %s %d %d bar', array(
				array('type' => 'fail', 'message' => 'INVALID_NUM_ARGUMENTS--Mixed invalid int-integer-1-2', 'source' =>  'foobar %d %s', 'origin' => 'foo %s %d %d bar'),
			)),
			array('Mixed invalid string', 'foobar %d %s', 'foo %s %s %d bar', array(
				array('type' => 'fail', 'message' => 'INVALID_NUM_ARGUMENTS--Mixed invalid string-string-1-2', 'source' =>  'foobar %d %s', 'origin' => 'foo %s %s %d bar'),
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

	static public function validate_string_plurals_data()
	{
		return array(
			array('Integer', 'foobar %d', 'foo %d bar', array()),
			array('MissingInt', 'foobar %d', 'foo bar', array()),
			array('2Integers', 'foobar %d %d', 'foo %d %d bar', array()),
			array('2IntegersMissingInt', 'foobar %d %d', 'foo %d bar', array(
				array('type' => 'fail', 'message' => 'INVALID_NUM_ARGUMENTS--2IntegersMissingInt-integer-2-1', 'source' =>  'foobar %d %d', 'origin' => 'foo %d bar'),
			)),
			array('2IntegersNum', 'foobar %1$d %2$d', 'foo %1$d %2$d bar', array()),
			array('2IntegersNumMissingInt1', 'foobar %1$d %2$d', 'foo %2$d bar', array(
				array('type' => 'fail', 'message' => 'INVALID_NUM_ARGUMENTS--2IntegersNumMissingInt1-integer-2-1', 'source' =>  'foobar %1$d %2$d', 'origin' => 'foo %2$d bar'),
			)),
			array('2IntegersNumMissingInt2', 'foobar %1$d %2$d', 'foo %1$d bar', array()),
			array('2IntegersNumMAdditionalInt1', 'foobar %2$d', 'foo %1$d %2$d bar', array()),
			array('2IntegersNumMAdditionalInt2', 'foobar %1$d', 'foo %1$d %2$d bar', array(
				array('type' => 'fail', 'message' => 'INVALID_NUM_ARGUMENTS--2IntegersNumMAdditionalInt2-integer-1-2', 'source' =>  'foobar %1$d', 'origin' => 'foo %1$d %2$d bar'),
			)),
		);
	}

	/**
	* @dataProvider validate_string_plurals_data
	*/
	public function test_validate_string_plurals($key, $against_language, $validate_language, $expected)
	{
		$this->validator->validate_string('', $key, $against_language, $validate_language, true);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
