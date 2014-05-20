<?php
/**
 *
 * @package LanguagePackValidator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\LanguagePackValidator\Tests\FileValidator;

class TestBase extends \Phpbb\LanguagePackValidator\Tests\TestBase
{
	/** @var \Phpbb\LanguagePackValidator\Validator\FileValidator */
	protected $validator;

	public function setUp()
	{
		parent::setUp();

		if (!defined('IN_PHPBB'))
		{
			// Need to set this, otherwise we can not load the language files
			define('IN_PHPBB', true);
		}

		$this->validator = new \Phpbb\LanguagePackValidator\Validator\FileValidator($this->getMock('Symfony\Component\Console\Input\InputInterface'), $this->output);
		$this->validator->setOrigin('origin', dirname(__FILE__) . '/fixtures/origin', 'language/origin/')
			->setSource('source', dirname(__FILE__) . '/fixtures/source', 'language/source/')
			->setPhpbbVersion('3.0');
	}
}
