<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_email_test extends phpbb_ext_official_translationvalidator_tests_validator_file_test_base
{
	static public function validate_email_data()
	{
		return array(
			array('email/email.txt', array(
				array('type' => 'fail', 'message' => 'EMAIL_MISSING_SUBJECT-email/email.txt', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'EMAIL_MISSING_SIG-email/email.txt', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_VARS-email/email.txt-{TEMPLATE_VAR_DOES_NOT_EXIST}, {U_ACTIVATE*NOT_USING_NORMAL_VAR*}', 'source' => null, 'origin' => null),
				array('type' => 'warning', 'message' => 'EMAIL_MISSING_VARS-email/email.txt-{U_ACTIVATE}', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_HTML-email/email.txt-&lt;a href=&quot;localhost&quot;&gt;', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_HTML-email/email.txt-&lt;/a&gt;', 'source' => null, 'origin' => null),
				array('type' => 'debug', 'message' => 'EMAIL_MISSING_NEWLINE-email/email.txt', 'source' => null, 'origin' => null),
			)),
			array('email/invalid_sig.txt', array(
				array('type' => 'fail', 'message' => 'EMAIL_INVALID_SIG-email/invalid_sig.txt', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_VARS-email/invalid_sig.txt-{YEHAA}', 'source' => null, 'origin' => null),
			)),
		);
	}

	/**
	* @dataProvider validate_email_data
	*/
	public function test_validate_email($file, $expected)
	{
		$this->validator->validate_email($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
