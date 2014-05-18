<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\tests\validator\file;

class validate_license_test extends \official\translationvalidator\tests\validator\file\test_base
{
	static public function validate_license_data()
	{
		return array(
			array('license/valid_gnu_gplv2.txt', array()),
			array('license/invalid1.txt', array(array('type' => 'fail', 'message' => 'INVALID_LICENSE_FILE-license/invalid1.txt', 'source' => null, 'origin' => null))),
		);
	}

	/**
	* @dataProvider validate_license_data
	*/
	public function test_validate_license($file, $expected)
	{
		$this->validator->validate_license_file($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
