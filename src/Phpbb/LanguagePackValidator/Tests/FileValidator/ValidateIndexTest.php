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

class ValidateIndexTest extends TestBase
{
	public function validateIndexFileData()
	{
		return array(
			array('index/empty_index.htm', array()),
			array('index/default_index.htm', array()),
			array('index/invalid_index.htm', array(
				Output::FATAL . '-File must be empty-index/invalid_index.htm-',
			)),
		);
	}

	/**
	* @dataProvider validateIndexFileData
	*/
	public function testValidateIndexFile($file, $expected)
	{
		$this->validator->validateIndexFile($file);
		$this->assertOutputMessages($expected);
	}
}
