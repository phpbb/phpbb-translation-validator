<?php
/**
 *
 * @package LanguagePackValidator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\LanguagePackValidator\Tests\Validator;

use Phpbb\LanguagePackValidator\Output\Output;

class FilelistTest extends \Phpbb\LanguagePackValidator\Tests\TestBase
{
	public function validateFilelistData()
	{
		return array(
			array(
				'3.0',
				array(
					Output::FATAL . '-Missing required file-missing.php-',
					Output::FATAL . '-Missing required file-missing.txt-',
					Output::FATAL . '-Missing required file-subdir/missing.php-',
					Output::FATAL . '-Missing required file-language/origin/LICENSE-',
					Output::FATAL . '-Found additional file-additional.php-',
					Output::FATAL . '-Found additional file-subdir/additional.php-',
					Output::FATAL . '-Found additional file-additional.txt-',

					Output::ERROR . '-Found additional file-language/origin/AUTHORS.md-',
					Output::ERROR . '-Found additional file-language/origin/CHANGELOG.md-',
					Output::ERROR . '-Found additional file-language/origin/README.md-',
					Output::ERROR . '-Found additional file-language/origin/VERSION.md-',

					Output::NOTICE . '-Found additional file-language/origin/AUTHORS-',
					Output::NOTICE . '-Found additional file-language/origin/CHANGELOG-',
					Output::NOTICE . '-Found additional file-language/origin/README-',
					Output::NOTICE . '-Found additional file-language/origin/VERSION-',
				),
			),
			array(
				'3.1',
				array(
					Output::FATAL . '-Missing required file-missing.php-',
					Output::FATAL . '-Missing required file-missing.txt-',
					Output::FATAL . '-Missing required file-subdir/missing.php-',
					Output::FATAL . '-Missing required file-language/origin/LICENSE-',
					Output::FATAL . '-Found additional file-additional.php-',
					Output::FATAL . '-Found additional file-subdir/additional.php-',
					Output::FATAL . '-Found additional file-additional.txt-',

					Output::NOTICE . '-Found additional file-language/origin/AUTHORS.md-',
					Output::NOTICE . '-Found additional file-language/origin/CHANGELOG.md-',
					Output::NOTICE . '-Found additional file-language/origin/README.md-',
					Output::NOTICE . '-Found additional file-language/origin/VERSION.md-',
					Output::NOTICE . '-Found additional file-language/origin/AUTHORS-',
					Output::NOTICE . '-Found additional file-language/origin/CHANGELOG-',
					Output::NOTICE . '-Found additional file-language/origin/README-',
					Output::NOTICE . '-Found additional file-language/origin/VERSION-',
				),
			),
		);
	}

	/**
	* @dataProvider validateFilelistData
	*/
	public function testValidateFilelist($phpbbVersion, $expected)
	{
		$validator = new \Phpbb\LanguagePackValidator\Validator\FilelistValidator($this->getMock('Symfony\Component\Console\Input\InputInterface'), $this->output, 'origin', 'source', dirname(__FILE__) . '/fixtures/', $phpbbVersion, false);
		$validator->validate();
		$this->assertOutputMessages($expected);
	}
}
