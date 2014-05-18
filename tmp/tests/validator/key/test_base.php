<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace official\translationvalidator\tests\validator\key;

class test_base extends \phpbb_ext_test_case
{
	protected $validator;
	protected $message_collection;

	public function setUp()
	{
		parent::setUp();

		$this->message_collection = new \official\translationvalidator\message_collection();
		$user = $this->getMock('\phpbb\user', array('lang'));
		$user->expects($this->any())
			->method('lang')
			->will($this->returnCallback(array($this, 'return_callback')));

		$this->validator = new \official\translationvalidator\validator\key($this->message_collection, $user);
	}

	public function return_callback()
	{
		return implode('-', func_get_args());
	}
}
