<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_key_validate_dateformats_test extends phpbb_ext_official_translationvalidator_tests_validator_key_test_base
{
	static public function validate_dateformats_data()
	{
		return array(
			array('EmptyArray', 'foobar', array(), array(
				array('fail', 'LANG_ARRAY_EMPTY--EmptyArray'),
			)),
			array('InvalidArray', 'foobar', array('Array' => array()), array(
				array('fail', 'INVALID_TYPE--InvalidArray.Array-string-array'),
			)),
			array('InvalidInteger', 'foobar', array('Integer' => 0), array(
				array('fail', 'INVALID_TYPE--InvalidInteger.Integer-string-integer'),
			)),
			array('ValidString', 'foobar', array('String' => 'foobar'), array()),
			array('UsingHTML', 'foobar', array('String' => 'foo<em>bar</em>'), array(
				array('fail', 'LANG_ADDITIONAL_HTML--UsingHTML.String-&lt;em&gt;'),
				array('fail', 'LANG_ADDITIONAL_HTML--UsingHTML.String-&lt;/em&gt;'),
			)),
			array('UsingHTMLKey', 'foobar', array('Str<em>i</em>ng' => 'foo'), array(
				array('fail', 'LANG_ADDITIONAL_HTML--UsingHTMLKey.Str<em>i</em>ng-&lt;em&gt;'),
				array('fail', 'LANG_ADDITIONAL_HTML--UsingHTMLKey.Str<em>i</em>ng-&lt;/em&gt;'),
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
