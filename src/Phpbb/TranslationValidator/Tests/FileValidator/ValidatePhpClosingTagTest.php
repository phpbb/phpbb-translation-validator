<?php
/**
 *
 * @package phpBB Translation Validator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\TranslationValidator\Tests\FileValidator;

use Phpbb\TranslationValidator\Output\Output;

class ValidatePhpClosingTagTest extends TestBase
{
	public function validatePhpClosingTagData()
	{
		return array(
			array('php_closing_tag/valid.php', array()),
			array('php_closing_tag/invalid.php', array(
				Output::FATAL . '-Must not contain PHP closing tag-php_closing_tag/invalid.php-',
			)),
		);
	}

	/**
	* @dataProvider validatePhpClosingTagData
	*/
	public function testValidatePhpClosingTag($file, $expected)
	{
		$this->validator->validatePhpClosingTag($file);
		$this->assertOutputMessages($expected);
	}
}
