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

class ValidateNewEndLineTest extends TestBase
{
	public function validateNewEndLineData()
	{
		return array(
			array('new_end_line/valid.php', array()),
			array('new_end_line/invalid.php', array(
				Output::FATAL . '-Must terminate with a new empty line-new_end_line/invalid.php-',
			)),
		);
	}

	/**
	* @dataProvider validateNewEndLineData
	*/
	public function testNewEndLineTag($file, $expected)
	{
		$this->validator->validateNewEndLine($file);
		$this->assertOutputMessages($expected);
	}
}
