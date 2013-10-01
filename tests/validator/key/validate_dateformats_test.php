<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\key;

class validate_dateformats_test extends \official\translationvalidator\tests\validator\key\test_base
{
	static public function validate_dateformats_data()
	{
		return array(
			array('EmptyArray', 'foobar', array(), array(
				array('type' => 'fail', 'message' => 'LANG_ARRAY_EMPTY--EmptyArray', 'source' => null, 'origin' => null),
			)),
			array('InvalidArray', 'foobar', array('Array' => array()), array(
				array('type' => 'fail', 'message' => 'INVALID_TYPE--InvalidArray.Array-string-array', 'source' => null, 'origin' => null),
			)),
			array('InvalidInteger', 'foobar', array('Integer' => 0), array(
				array('type' => 'fail', 'message' => 'INVALID_TYPE--InvalidInteger.Integer-string-integer', 'source' => null, 'origin' => null),
			)),
			array('ValidString', 'foobar', array('String' => 'foobar'), array()),
			array('UsingHTML', 'foobar', array('String' => 'foo<em>bar</em>'), array(
				array('type' => 'notice', 'message' => 'LANG_ADDITIONAL_HTML--UsingHTML.String-&lt;em&gt;', 'source' => '', 'origin' => 'foo<em>bar</em>'),
				array('type' => 'notice', 'message' => 'LANG_ADDITIONAL_HTML--UsingHTML.String-&lt;/em&gt;', 'source' => '', 'origin' => 'foo<em>bar</em>'),
			)),
			array('UsingHTMLKey', 'foobar', array('Str<em>i</em>ng' => 'foo'), array(
				array('type' => 'notice', 'message' => 'LANG_ADDITIONAL_HTML--UsingHTMLKey.Str<em>i</em>ng-&lt;em&gt;', 'source' => '', 'origin' => 'Str<em>i</em>ng'),
				array('type' => 'notice', 'message' => 'LANG_ADDITIONAL_HTML--UsingHTMLKey.Str<em>i</em>ng-&lt;/em&gt;', 'source' => '', 'origin' => 'Str<em>i</em>ng'),
			)),
		);
	}

	/**
	* @dataProvider validate_dateformats_data
	*/
	public function test_validate_dateformats($key, $against_language, $validate_language, $expected)
	{
		$this->validator->validate_dateformats('', $key, $against_language, $validate_language);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
