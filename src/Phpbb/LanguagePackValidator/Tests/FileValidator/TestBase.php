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

		$this->validator = new \Phpbb\LanguagePackValidator\Validator\FileValidator($this->getMock('Symfony\Component\Console\Input\InputInterface'), $this->output, 'origin', 'source', dirname(__FILE__) . '/fixtures/', '3.0', false);
	}
}