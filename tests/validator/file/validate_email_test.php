<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\file;

class validate_email_test extends \official\translationvalidator\tests\validator\file\test_base
{
	static public function validate_email_data()
	{
		return array(
			array('3.0', 'email/email.txt', array(
				array('type' => 'fail', 'message' => 'EMAIL_MISSING_SUBJECT-email/email.txt'),
				array('type' => 'fail', 'message' => 'EMAIL_MISSING_SIG-email/email.txt'),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_VARS-email/email.txt-{TEMPLATE_VAR_DOES_NOT_EXIST}, {U_ACTIVATE*NOT_USING_NORMAL_VAR*}'),
				array('type' => 'warning', 'message' => 'EMAIL_MISSING_VARS-email/email.txt-{U_ACTIVATE}'),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_HTML-email/email.txt-&lt;a href=&quot;localhost&quot;&gt;'),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_HTML-email/email.txt-&lt;/a&gt;'),
				array('type' => 'debug', 'message' => 'EMAIL_MISSING_NEWLINE-email/email.txt'),
			)),
			array('3.1', 'email/email.txt', array(
				array('type' => 'fail', 'message' => 'EMAIL_MISSING_SUBJECT-email/email.txt'),
				array('type' => 'fail', 'message' => 'EMAIL_MISSING_SIG-email/email.txt'),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_VARS-email/email.txt-{TEMPLATE_VAR_DOES_NOT_EXIST}, {U_ACTIVATE*NOT_USING_NORMAL_VAR*}'),
				array('type' => 'warning', 'message' => 'EMAIL_MISSING_VARS-email/email.txt-{U_ACTIVATE}'),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_HTML-email/email.txt-&lt;a href=&quot;localhost&quot;&gt;'),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_HTML-email/email.txt-&lt;/a&gt;'),
				array('type' => 'warning', 'message' => 'EMAIL_MISSING_NEWLINE-email/email.txt'),
			)),
			array('3.0', 'email/invalid_sig.txt', array(
				array('type' => 'fail', 'message' => 'EMAIL_UTF8-email/invalid_sig.txt'),
				array('type' => 'fail', 'message' => 'EMAIL_INVALID_SIG-email/invalid_sig.txt'),
				array('type' => 'fail', 'message' => 'EMAIL_ADDITIONAL_VARS-email/invalid_sig.txt-{YEHAA}'),
			)),
		);
	}

	/**
	* @dataProvider validate_email_data
	*/
	public function test_validate_email($phpbb_version, $file, $expected)
	{
		$this->validator->set_version($phpbb_version);
		$this->validator->validate_email($file, $file);
		$errors = $this->message_collection->get_messages();

		$clean_errors = array();
		foreach ($errors as $error)
		{
			unset($error['source']);
			unset($error['origin']);
			$clean_errors[] = $error;
		}
		$this->assertEquals($expected, $clean_errors);
	}
}
