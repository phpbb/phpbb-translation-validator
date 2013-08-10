<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_test extends phpbb_ext_official_translationvalidator_tests_validator_file_test_base
{
	static public function validate_index_data()
	{
		return array(
			array('index/empty_index.htm', array()),
			array('index/default_index.htm', array()),
			array('index/invalid_index.htm', array(array('fail', 'INVALID_INDEX_FILE-index/invalid_index.htm'))),
		);
	}

	/**
	* @dataProvider validate_index_data
	*/
	public function test_validate_index($file, $expected)
	{
		$this->validator->validate_index_file($file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
