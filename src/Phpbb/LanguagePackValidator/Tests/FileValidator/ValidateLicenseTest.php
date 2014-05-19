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

class ValidateLicenseTest extends TestBase
{
	public function validateLicenseFileData()
	{
		return array(
			array('license/valid_gnu_gplv2.txt', array()),
			array('license/invalid1.txt', array(
				Output::FATAL . '-License must be: GNU GENERAL PUBLIC LICENSE Version 2, June 1991-license/invalid1.txt-',
			)),
		);
	}

	/**
	* @dataProvider validateLicenseFileData
	*/
	public function testValidateLicenseFile($file, $expected)
	{
		$this->validator->validateLicenseFile($file);
		$this->assertOutputMessages($expected);
	}
}
