<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\key;

class validate_html_test extends \official\translationvalidator\tests\validator\key\test_base
{
	static public function validate_html_data()
	{
		return array(
			array('None', 'foobar', 'foobar', array()),
			array('Same', '<em>foobar</em>', '<em>foobar</em>', array()),
			array('Different html', '<em>foobar</em>', '<strong>foobar</strong>', array(
				array('type' => 'notice', 'message' => 'LANG_ADDITIONAL_HTML--Different html-&lt;strong&gt;', 'source' => '<em>foobar</em>', 'origin' => '<strong>foobar</strong>'),
				array('type' => 'notice', 'message' => 'LANG_ADDITIONAL_HTML--Different html-&lt;/strong&gt;', 'source' => '<em>foobar</em>', 'origin' => '<strong>foobar</strong>'),
			)),
			array('Additional html', '<em>foobar</em>', '<em>foobar</em> <strong>foobar</strong> <invalid>foobar</invalid>', array(
				array('type' => 'notice', 'message' => 'LANG_ADDITIONAL_HTML--Additional html-&lt;strong&gt;', 'source' => '<em>foobar</em>', 'origin' => '<em>foobar</em> <strong>foobar</strong> <invalid>foobar</invalid>'),
				array('type' => 'notice', 'message' => 'LANG_ADDITIONAL_HTML--Additional html-&lt;/strong&gt;', 'source' => '<em>foobar</em>', 'origin' => '<em>foobar</em> <strong>foobar</strong> <invalid>foobar</invalid>'),
				array('type' => 'fail', 'message' => 'LANG_ADDITIONAL_HTML--Additional html-&lt;invalid&gt;', 'source' => '<em>foobar</em>', 'origin' => '<em>foobar</em> <strong>foobar</strong> <invalid>foobar</invalid>'),
				array('type' => 'fail', 'message' => 'LANG_ADDITIONAL_HTML--Additional html-&lt;/invalid&gt;', 'source' => '<em>foobar</em>', 'origin' => '<em>foobar</em> <strong>foobar</strong> <invalid>foobar</invalid>'),
			)),
			array('Additional unclosed html', '<em>foobar</em>', '<em>foobar</em> <strong>foobar', array(
				array('type' => 'notice', 'message' => 'LANG_ADDITIONAL_HTML--Additional unclosed html-&lt;strong&gt;', 'source' => '<em>foobar</em>', 'origin' => '<em>foobar</em> <strong>foobar'),
				array('type' => 'fail', 'message' => 'LANG_UNCLOSED_HTML--Additional unclosed html-strong', 'source' => '<em>foobar</em>', 'origin' => '<em>foobar</em> <strong>foobar'),
			)),
			array('Invalid html', '<em>foobar</em>', '<em>foobar</em><em>foobar</e m>', array(
				array('type' => 'fail', 'message' => 'LANG_INVALID_HTML--Invalid html-&lt;/e m&gt;', 'source' => '<em>foobar</em>', 'origin' => '<em>foobar</em><em>foobar</e m>'),
				array('type' => 'fail', 'message' => 'LANG_UNCLOSED_HTML--Invalid html-em', 'source' => '<em>foobar</em>', 'origin' => '<em>foobar</em><em>foobar</e m>'),
			)),
			array('Unclosed html', '<em>foobar</em>', '<em>foo<em>foobar</em>bar', array(
				array('type' => 'fail', 'message' => 'LANG_UNCLOSED_HTML--Unclosed html-em', 'source' => '<em>foobar</em>', 'origin' => '<em>foo<em>foobar</em>bar'),
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
