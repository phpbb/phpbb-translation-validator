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
	/** @var \official\translationvalidator\validator\filelist */
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

	public static function validate_filelist_data()
	{
		return array(
			array(
				'3.0',
				array(
					'fail' => array(
						'MISSING_FILE-missing.php',
						'MISSING_FILE-missing.txt',
						'MISSING_FILE-subdir/missing.php',
						'MISSING_FILE-language/tovalidate/LICENSE',
						'ADDITIONAL_FILE-additional.php',
						'ADDITIONAL_FILE-subdir/additional.php',
					),
					'warning' => array(
						'ADDITIONAL_FILE-additional.txt',
						'ADDITIONAL_FILE-language/tovalidate/AUTHORS.md',
						'ADDITIONAL_FILE-language/tovalidate/CHANGELOG.md',
						'ADDITIONAL_FILE-language/tovalidate/README.md',
						'ADDITIONAL_FILE-language/tovalidate/VERSION.md',
					),
					'debug' => array(
						'ADDITIONAL_FILE-language/tovalidate/AUTHORS',
						'ADDITIONAL_FILE-language/tovalidate/CHANGELOG',
						'ADDITIONAL_FILE-language/tovalidate/README',
						'ADDITIONAL_FILE-language/tovalidate/VERSION',
					),
				),
			),
			array(
				'3.1',
				array(
					'fail' => array(
						'MISSING_FILE-missing.php',
						'MISSING_FILE-missing.txt',
						'MISSING_FILE-subdir/missing.php',
						'MISSING_FILE-language/tovalidate/LICENSE',
						'ADDITIONAL_FILE-additional.php',
						'ADDITIONAL_FILE-subdir/additional.php',
					),
					'warning' => array(
						'ADDITIONAL_FILE-additional.txt',
					),
					'debug' => array(
						'ADDITIONAL_FILE-language/tovalidate/AUTHORS',
						'ADDITIONAL_FILE-language/tovalidate/AUTHORS.md',
						'ADDITIONAL_FILE-language/tovalidate/CHANGELOG',
						'ADDITIONAL_FILE-language/tovalidate/CHANGELOG.md',
						'ADDITIONAL_FILE-language/tovalidate/README',
						'ADDITIONAL_FILE-language/tovalidate/README.md',
						'ADDITIONAL_FILE-language/tovalidate/VERSION',
						'ADDITIONAL_FILE-language/tovalidate/VERSION.md',
					),
				),
			),
		);
	}

	/**
	* @dataProvider validate_filelist_data
	*/
	public function test_validate_filelist($phpbb_version, $messages)
	{
		$this->validator->set_version($phpbb_version);
		$this->validator->validate();
		$errors = $this->message_collection->get_messages();

		foreach ($messages['fail'] as $error)
		{
			$this->assertContains(array('type' => 'fail', 'message' => $error, 'source' => null, 'origin' => null), $errors, 'Missing expected error: ' . $error);
		}

		foreach ($messages['warning'] as $warning)
		{
			$this->assertContains(array('type' => 'warning', 'message' => $warning, 'source' => null, 'origin' => null), $errors, 'Missing expected warning: ' . $notice);
		}

		foreach ($messages['debug'] as $debug)
		{
			$this->assertContains(array('type' => 'debug', 'message' => $debug, 'source' => null, 'origin' => null), $errors, 'Missing expected debug: ' . $notice);
		}

		foreach ($errors as $error)
		{
			$this->assertContains($error['message'], $messages[$error['type']], 'Unexpected message: ' . $error['message']);
		}
	}

	public function return_callback()
	{
		return implode('-', func_get_args());
	}
}
