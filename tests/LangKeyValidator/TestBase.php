<?php
/**
 *
 * @package phpBB Translation Validator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\TranslationValidator\Tests\LangKeyValidator;

class TestBase extends \Phpbb\TranslationValidator\Tests\TestBase
{
	/** @var \Phpbb\TranslationValidator\Validator\LangKeyValidator */
	protected $validator;

	public function setUp(): void
	{
		parent::setUp();

		$this->validator = new \Phpbb\TranslationValidator\Validator\LangKeyValidator($this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')->getMock(), $this->output);
		$this->validator->setOrigin('origin', dirname(__FILE__) . '/fixtures/origin', 'language/origin/')
			->setSource('source', dirname(__FILE__) . '/fixtures/source', 'language/source/')
			->setPhpbbVersion('3.2')
			->setPluralRule(1);
	}
}
