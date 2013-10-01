<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_search_test extends phpbb_ext_official_translationvalidator_tests_validator_file_test_base
{
	static public function validate_search_synonyms_data()
	{
		return array(
			array('search_synonyms/valid.php', array()),
			array('search_synonyms/no_synonyms.php', array(array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-search_synonyms/no_synonyms.php-synonyms', 'source' => null, 'origin' => null))),
			array('search_synonyms/invalid_synonyms.php', array(array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-search_synonyms/invalid_synonyms.php-synonyms', 'source' => null, 'origin' => null))),
			array('search_synonyms/additional_variable.php', array(array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-search_synonyms/additional_variable.php-synonyms', 'source' => null, 'origin' => null))),
			array('search_synonyms/invalid_synonym.php', array(
				array('type' => 'fail', 'message' => 'FILE_SEARCH_INVALID_TYPES-search_synonyms/invalid_synonym.php-s:3:&quot;bar&quot;;-i:1;', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'FILE_SEARCH_INVALID_TYPES-search_synonyms/invalid_synonym.php-i:0;-s:4:&quot;this&quot;;', 'source' => null, 'origin' => null),
				array('type' => 'fail', 'message' => 'FILE_SEARCH_INVALID_TYPES-search_synonyms/invalid_synonym.php-i:1;-i:2;', 'source' => null, 'origin' => null),
			)),
		);
	}

	/**
	* @dataProvider validate_search_synonyms_data
	*/
	public function test_validate_search_synonyms($file, $expected)
	{
		$this->validator->validate_search_synonyms_file($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}

	static public function validate_search_ignore_words_data()
	{
		return array(
			array('search_ignore_words/valid.php', array()),
			array('search_ignore_words/no_words.php', array(array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-search_ignore_words/no_words.php-words', 'source' => null, 'origin' => null))),
			array('search_ignore_words/invalid_words.php', array(array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-search_ignore_words/invalid_words.php-words', 'source' => null, 'origin' => null))),
			array('search_ignore_words/additional_variable.php', array(array('type' => 'fail', 'message' => 'FILE_INVALID_VARS-search_ignore_words/additional_variable.php-words', 'source' => null, 'origin' => null))),
			array('search_ignore_words/invalid_word.php', array(array('type' => 'fail', 'message' => 'FILE_SEARCH_INVALID_TYPE-search_ignore_words/invalid_word.php-i:0;', 'source' => null, 'origin' => null))),
		);
	}

	/**
	* @dataProvider validate_search_ignore_words_data
	*/
	public function test_validate_search_ignore_words($file, $expected)
	{
		$this->validator->validate_search_ignore_words_file($file, $file);
		$this->assertEquals($expected, $this->message_collection->get_messages());
	}
}
