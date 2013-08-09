<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_iso_test extends phpbb_ext_official_translationvalidator_tests_validator_test_base
{
	static public function validate_iso_data()
	{
		return array(
			array('iso/valid_iso.txt', array()),
			array('iso/more_iso.txt', array(array('fail', 'INVALID_ISO_FILE-iso/more_iso.txt'))),
			array('iso/fewer_iso.txt', array(array('fail', 'INVALID_ISO_FILE-iso/fewer_iso.txt'))),
		);
	}

	/**
	* @dataProvider validate_iso_data
	*/
	public function test_validate_iso($iso_file, $expected)
	{
		$this->validator->validate_iso_file($iso_file);
		$this->assertEquals($expected, $this->error_collection->get_messages());
	}
}
