<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator;

class filelist_test extends \official\translationvalidator\tests\validator\file\test_base
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

		$this->validator = new \official\translationvalidator\validator\filelist($this->message_collection, $user, dirname(__FILE__) . '/fixtures/');
		$this->validator->set_upstream_language('original');
		$this->validator->set_origin_language('tovalidate');
	}

	protected $messages = array(
		'fail' => array(
			'MISSING_FILE-missing.php',
			'MISSING_FILE-missing.txt',
			'MISSING_FILE-subdir/missing.php',
			'ADDITIONAL_FILE-additional.php',
			'ADDITIONAL_FILE-subdir/additional.php',
		),
		'notice' => array(
			'ADDITIONAL_FILE-additional.txt',
		),
	);

	public function test_validate_filelist()
	{
		$this->validator->validate();
		$errors = $this->message_collection->get_messages();

		foreach ($this->messages['fail'] as $error)
		{
			$this->assertContains(array('type' => 'fail', 'message' => $error, 'source' => null, 'origin' => null), $errors, 'Missing expected error: ' . $error);
		}

		foreach ($this->messages['notice'] as $notice)
		{
			$this->assertContains(array('type' => 'notice', 'message' => $notice, 'source' => null, 'origin' => null), $errors, 'Missing expected notice: ' . $notice);
		}

		foreach ($errors as $error)
		{
			$this->assertContains($error['message'], $this->messages[$error['type']], 'Unexpected message: ' . $error);
		}
	}

	public function return_callback()
	{
		return implode('-', func_get_args());
	}
}
