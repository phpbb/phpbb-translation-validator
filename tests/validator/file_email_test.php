<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_email_test extends phpbb_ext_official_translationvalidator_tests_validator_test_base
{
	static public function validate_email_data()
	{
		return array(
			array('email/email.txt', array(
				array('fail', 'EMAIL_MISSING_SUBJECT-email/email.txt'),
				array('fail', 'EMAIL_MISSING_SIG-email/email.txt'),
				array('warning', 'EMAIL_ADDITIONAL_VARS-email/email.txt-{TEMPLATE_VAR_DOES_NOT_EXIST}, {U_ACTIVATE*NOT_USING_NORMAL_VAR*}'),
				array('warning', 'EMAIL_MISSING_VARS-email/email.txt-{U_ACTIVATE}'),
				array('fail', 'EMAIL_ADDITIONAL_HTML-email/email.txt-&lt;a href=&quot;localhost&quot;&gt;, &lt;/a&gt;'),
				array('notice', 'EMAIL_MISSING_NEWLINE-email/email.txt'),
			)),
			array('email/invalid_sig.txt', array(
				array('fail', 'EMAIL_INVALID_SIG-email/invalid_sig.txt'),
				array('warning', 'EMAIL_ADDITIONAL_VARS-email/invalid_sig.txt-{EMAIL_SIG}'),
			)),
		);
	}

	/**
	* @dataProvider validate_email_data
	*/
	public function test_validate_email($iso_file, $expected)
	{
		$this->validator->validate_email($iso_file);
		$this->assertEquals($expected, $this->error_collection->get_messages());
	}
}
