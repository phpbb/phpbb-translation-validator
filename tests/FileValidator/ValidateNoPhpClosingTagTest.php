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

class ValidateNoPhpClosingTagTest extends TestBase
{
	public function validateNoPhpClosingTagData()
	{
		return array(
			array('4.0', 'nophpclosingtag/shortarraysyntax.php', array()),
		);
	}

	/**
	* @dataProvider validateNoPhpClosingTagData
	*/
	public function testvalidateNoPhpClosingTag($phpbbVersion, $file, $expected)
	{
		$this->validator->setPhpbbVersion($phpbbVersion);
		$this->validator->validateNoPhpClosingTag($file);
		$this->assertOutputMessages($expected);
	}
}
