<?php
/**
 *
 * @package LanguagePackValidator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\LanguagePackValidator\Tests\FileValidator;

use Phpbb\LanguagePackValidator\Output\Output;

class ValidateLineEndingsTest extends TestBase
{
	public function validateLineEndingsData()
	{
		return array(
			array('line_endings/valid.php', array()),
			array('line_endings/invalid.php', array(
				Output::FATAL . '-Not using Linux line endings (LF)-line_endings/invalid.php-',
			)),
		);
	}

	/**
	* @dataProvider validateLineEndingsData
	*/
	public function testValidateLineEndings($file, $expected)
	{
		$this->validator->validateLineEndings($file);
		$this->assertOutputMessages($expected);
	}
}
