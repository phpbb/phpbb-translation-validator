<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\key;

class validate_plural_keys_test extends \official\translationvalidator\tests\validator\key\test_base
{
	static public function validate_plural_keys_data()
	{
		return array(
			array('EmptyArray', 0, array(), array(
				array('type' => 'fail', 'message' => 'LANG_PLURAL_EMPTY--EmptyArray', 'source' => null, 'origin' => null),
			)),
			array('EmptyZeroArray', 0, array(0 => 'None'), array(
				array('type' => 'fail', 'message' => 'LANG_PLURAL_EMPTY--EmptyZeroArray', 'source' => null, 'origin' => null),
			)),
			array('FullArray', 0, array(
					1 => 'Default',
			), array()),
			array('FullZeroArray', 0, array(
					0 => 'Zero',
					1 => 'Default',
			), array()),
			array('MissingArray', 1, array(
					1 => 'Default',
					//2 => 'Default2 Missing',
				), array(
				array('type' => 'debug', 'message' => 'LANG_PLURAL_MISSING--MissingArray-2', 'source' => null, 'origin' => null),
			)),
			array('MissingZeroArray', 1, array(
					0 => 'Zero',
					1 => 'Default1',
					//2 => 'Default2 Missing',
				), array(
				array('type' => 'debug', 'message' => 'LANG_PLURAL_MISSING--MissingZeroArray-2', 'source' => null, 'origin' => null),
			)),
			array('AdditionalArray', 0, array(
					1 => 'Default',
					2 => 'Additional',
				), array(
				array('type' => 'fail', 'message' => 'LANG_PLURAL_ADDITIONAL--AdditionalArray-2', 'source' => null, 'origin' => null),
			)),
			array('AdditionalZeroArray', 0, array(
					0 => 'Zero',
					1 => 'Default',
					2 => 'Additional',
				), array(
				array('type' => 'fail', 'message' => 'LANG_PLURAL_ADDITIONAL--AdditionalZeroArray-2', 'source' => null, 'origin' => null),
			)),
		);
	}

	/**
	* @dataProvider validate_plural_keys_data
	*/
	public function test_validate_plural_keys($key, $plural_rule, $validate_language, $expected)
	{
		$this->validator->set_plural_rule($plural_rule)->validate_plural_keys('', $key, array(), $validate_language);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
