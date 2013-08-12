<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_key_test_base extends phpbb_ext_test_case
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

		$this->validator = new phpbb_ext_official_translationvalidator_validator_key($this->message_collection, $user);
	}

	public function return_callback()
	{
		return implode('-', func_get_args());
	}
}
