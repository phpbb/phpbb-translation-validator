<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_filelist_test extends phpbb_ext_test_case
{
	protected $validator;
	protected $message_collection;

	public function setUp()
	{
		parent::setUp();

		$this->message_collection = new phpbb_ext_official_translationvalidator_message_collection();
		$user = $this->getMock('phpbb_user', array('lang'));
		$user->expects($this->any())
			->method('lang')
			->will($this->returnCallback(array($this, 'return_callback')));

		$this->validator = new phpbb_ext_official_translationvalidator_validator_filelist($this->message_collection, $user, dirname(__FILE__) . '/fixtures/filelist/');
		$this->validator->set_validate_against('original');
		$this->validator->set_validate_language('tovalidate');
	}

	public function test_validate_filelist()
	{
		$this->validator->validate();
		$this->assertEquals(array(
			array('fail', 'MISSING_FILE-missing.php'),
			array('fail', 'MISSING_FILE-missing.txt'),
			array('fail', 'MISSING_FILE-subdir/missing.php'),
			array('fail', 'ADDITIONAL_FILE-additional.php'),
			array('notice', 'ADDITIONAL_FILE-additional.txt'),
			array('fail', 'ADDITIONAL_FILE-subdir/additional.php'),
		), $this->message_collection->get_messages());
	}

	public function return_callback()
	{
		return implode('-', func_get_args());
	}
}
