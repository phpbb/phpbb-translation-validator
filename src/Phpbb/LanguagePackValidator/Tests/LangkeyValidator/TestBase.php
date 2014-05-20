<?php
/**
 *
 * @package LanguagePackValidator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\LanguagePackValidator\Tests\LangKeyValidator;

class TestBase extends \Phpbb\LanguagePackValidator\Tests\TestBase
{
	/** @var \Phpbb\LanguagePackValidator\Validator\LangKeyValidator */
	protected $validator;

	public function setUp()
	{
		parent::setUp();

		$this->validator = new \Phpbb\LanguagePackValidator\Validator\LangKeyValidator($this->getMock('Symfony\Component\Console\Input\InputInterface'), $this->output);
		$this->validator->setOrigin('origin', dirname(__FILE__) . '/fixtures/origin', 'language/origin/')
			->setSource('source', dirname(__FILE__) . '/fixtures/source', 'language/source/')
			->setPhpbbVersion('3.0')
			->setPluralRule(1);
	}
}
