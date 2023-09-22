<?php
/**
 *
 * @package phpBB Translation Validator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\TranslationValidator\Tests\FileListValidator;

use Phpbb\TranslationValidator\Output\Output;

class FileListTest extends \Phpbb\TranslationValidator\Tests\TestBase
{
	/** @var \Phpbb\TranslationValidator\Validator\FileListValidator */
	protected $validator;

	public function setUp(): void
	{
		parent::setUp();

		$this->validator = new \Phpbb\TranslationValidator\Validator\FileListValidator($this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')->getMock(), $this->output);
	}

	public function validateFileListData()
	{
		return array(
			array(
				'3.2',
				array(
					Output::FATAL . '-Missing required file-missing.php-',
					Output::FATAL . '-Missing required file-missing.txt-',
					Output::FATAL . '-Missing required file-subdir/missing.php-',
					Output::FATAL . '-Missing required file-language/origin/LICENSE-',
					Output::FATAL . '-Found additional file-additional.php-',
					Output::FATAL . '-Found additional file-subdir/additional.php-',
					Output::FATAL . '-Found additional file-additional.txt-',
					Output::FATAL . '-Found additional file-language/origin/AUTHORS-',
					Output::FATAL . '-Found additional file-language/origin/CHANGELOG-',
					Output::FATAL . '-Found additional file-language/origin/README-',
					Output::FATAL . '-Found additional file-language/origin/VERSION-',

					Output::NOTICE . '-Found additional file-language/origin/AUTHORS.md-',
					Output::NOTICE . '-Found additional file-language/origin/CHANGELOG.md-',
					Output::NOTICE . '-Found additional file-language/origin/README.md-',
					Output::NOTICE . '-Found additional file-language/origin/VERSION.md-',
					Output::NOTICE . '-Found additional file-language/origin/index.htm-',
				),
			),
		);
	}

	/**
	* @dataProvider validateFileListData
	*/
	public function testValidateFileList($phpbbVersion, $expected)
	{
		$this->validator->setOrigin('origin', dirname(__FILE__) . '/fixtures/'. $phpbbVersion . '/origin', 'language/origin/')
			->setSource('source', dirname(__FILE__) . '/fixtures/'. $phpbbVersion . '/source', 'language/source/');

		$this->validator
			->setPhpbbVersion($phpbbVersion)
			->validate();
		$this->assertOutputMessages($expected);
	}
}
