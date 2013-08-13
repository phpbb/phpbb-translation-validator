<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_key_validate_html_test extends phpbb_ext_official_translationvalidator_tests_validator_key_test_base
{
	static public function validate_html_data()
	{
		return array(
			array('None', 'foobar', 'foobar', array()),
			array('Same', '<em>foobar</em>', '<em>foobar</em>', array()),
			array('Different html', '<em>foobar</em>', '<strong>foobar</strong>', array(
				array('fail', 'LANG_ADDITIONAL_HTML--Different html-&lt;strong&gt;'),
				array('fail', 'LANG_ADDITIONAL_HTML--Different html-&lt;/strong&gt;'),
			)),
			array('Additional html', '<em>foobar</em>', '<em>foobar</em> <strong>foobar</strong>', array(
				array('fail', 'LANG_ADDITIONAL_HTML--Additional html-&lt;strong&gt;'),
				array('fail', 'LANG_ADDITIONAL_HTML--Additional html-&lt;/strong&gt;'),
			)),
			array('Additional unclosed html', '<em>foobar</em>', '<em>foobar</em> <strong>foobar', array(
				array('fail', 'LANG_ADDITIONAL_HTML--Additional unclosed html-&lt;strong&gt;'),
				array('fail', 'LANG_UNCLOSED_HTML--Additional unclosed html-strong'),
			)),
			array('Invalid html', '<em>foobar</em>', '<em>foobar</em><em>foobar</e m>', array(
				array('fail', 'LANG_INVALID_HTML--Invalid html-&lt;/e m&gt;'),
				array('fail', 'LANG_UNCLOSED_HTML--Invalid html-em'),
			)),
			array('Unclosed html', '<em>foobar</em>', '<em>foo<em>foobar</em>bar', array(
				array('fail', 'LANG_UNCLOSED_HTML--Unclosed html-em'),
			)),
		);
	}

	/**
	* @dataProvider validate_html_data
	*/
	public function test_validate_html($key, $against_language, $validate_language, $expected)
	{
		$this->validator->validate_html('', $key, $against_language, $validate_language);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
