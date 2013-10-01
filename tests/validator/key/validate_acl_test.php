<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_key_validate_acl_test extends phpbb_ext_official_translationvalidator_tests_validator_key_test_base
{
	static public function validate_acl_data()
	{
		return array(
			array('MissingCat', array('lang' => 'foo', 'cat' => 'bar'), array('lang' => 'foo'), array(
				array('type' => 'fail', 'message' => 'ACL_MISSING_CAT--MissingCat', 'source' => null, 'origin' => null),
			)),
			array('MissingLang', array('lang' => 'foo', 'cat' => 'bar'), array('cat' => 'bar'), array(
				array('type' => 'fail', 'message' => 'ACL_MISSING_LANG--MissingLang', 'source' => null, 'origin' => null),
			)),
			array('InvalidCat', array('lang' => 'foo', 'cat' => 'bar'), array('lang' => 'foo', 'cat' => 'notBar'), array(
				array('type' => 'fail', 'message' => 'ACL_INVALID_CAT--InvalidCat-bar-notBar', 'source' => null, 'origin' => null),
			)),
		);
	}

	/**
	* @dataProvider validate_acl_data
	*/
	public function test_validate_acl($key, $against_language, $validate_language, $expected)
	{
		$this->validator->validate_acl('', $key, $against_language, $validate_language);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
