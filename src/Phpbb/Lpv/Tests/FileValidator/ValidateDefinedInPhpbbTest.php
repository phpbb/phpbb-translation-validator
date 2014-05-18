<?php
/**
 *
 * @package LPV
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\Lpv\Tests\FileValidator;

use Phpbb\Lpv\Output\Output;

class validate_defined_in_phpbb_test extends TestBase
{
	public function validateDefinedInPhpbbData()
	{
		return array(
			array('in_phpbb/valid.php', array()),
			array('in_phpbb/invalid.php', array(
				Output::FATAL . '-Must check whether IN_PHPBB is defined-in_phpbb/invalid.php-',
			)),
		);
	}

	/**
	* @dataProvider validateDefinedInPhpbbData
	*/
	public function testValidateDefinedInPhpbb($file, $expected)
	{
		$this->validator->validateDefinedInPhpbb($file, $file);
		$this->assertOutputMessages($expected);
	}
}
