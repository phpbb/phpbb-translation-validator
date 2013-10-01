<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_iso_test extends phpbb_ext_official_translationvalidator_tests_validator_file_test_base
{
	static public function validate_iso_data()
	{
		return array(
			array('iso/valid_iso.txt', array()),
			array('iso/more_iso.txt', array(array('type' => 'fail', 'message' => 'INVALID_ISO_FILE-iso/more_iso.txt', 'source' => null, 'origin' => null))),
			array('iso/fewer_iso.txt', array(array('type' => 'fail', 'message' => 'INVALID_ISO_FILE-iso/fewer_iso.txt', 'source' => null, 'origin' => null))),
		);
	}

	/**
	* @dataProvider validate_iso_data
	*/
	public function test_validate_iso($file, $expected)
	{
		$this->validator->validate_iso_file($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
