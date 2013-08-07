<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_official_translationvalidator_tests_validator_file_test extends phpbb_ext_test_case
{
	protected $validator;
	protected $error_collection;

	public function setUp()
	{
		parent::setUp();

		$this->error_collection = new phpbb_ext_official_translationvalidator_error_collection();
		$user = $this->getMock('phpbb_user', array('lang'));
		$user->expects($this->any())
			->method('lang')
			->will($this->returnCallback(array($this, 'return_callback')));

		$key_validator = new phpbb_ext_official_translationvalidator_validator_key($this->error_collection, $user);
		$this->validator = new phpbb_ext_official_translationvalidator_validator_file($key_validator, $this->error_collection, $user, dirname(__FILE__) . '/fixtures/');
		$this->validator->set_validate_language('original');
		$this->validator->set_validate_language('tovalidate');
	}

	static public function validate_help_data()
	{
		return array(
			array('help/valid.php', array()),
			array('help/no_help.php', array(array('fail', 'FILE_INVALID_VARS-help/no_help.php-help'))),
			array('help/invalid_help_var.php', array(array('fail', 'FILE_INVALID_VARS-help/invalid_help_var.php-help'))),
			array('help/additional_variable.php', array(array('fail', 'FILE_INVALID_VARS-help/additional_variable.php-help'))),
			array('help/invalid_help.php', array(
				array('fail', 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-a:1:{i:0;s:2:&quot;--&quot;;}'),
				array('fail', 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-a:2:{i:0;s:2:&quot;--&quot;;i:2;s:2:&quot;--&quot;;}'),
				array('fail', 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-a:1:{s:3:&quot;lol&quot;;s:3:&quot;bar&quot;;}'),
				array('fail', 'FILE_HELP_INVALID_ENTRY-help/invalid_help.php-s:3:&quot;foo&quot;;'),
				array('fail', 'FILE_HELP_ONE_BREAK-help/invalid_help.php'),
			)),
		);
	}

	/**
	* @dataProvider validate_help_data
	*/
	public function test_validate_help($iso_file, $expected)
	{
		$this->validator->validate_help_file($iso_file);
		$this->assertEquals($expected, $this->error_collection->get_messages());
	}

	static public function validate_search_synonyms_data()
	{
		return array(
			array('search_synonyms/valid.php', array()),
			array('search_synonyms/no_synonyms.php', array(array('fail', 'FILE_INVALID_VARS-search_synonyms/no_synonyms.php-synonyms'))),
			array('search_synonyms/invalid_synonyms.php', array(array('fail', 'FILE_INVALID_VARS-search_synonyms/invalid_synonyms.php-synonyms'))),
			array('search_synonyms/additional_variable.php', array(array('fail', 'FILE_INVALID_VARS-search_synonyms/additional_variable.php-synonyms'))),
			array('search_synonyms/invalid_synonym.php', array(
				array('fail', 'FILE_SEARCH_INVALID_TYPES-search_synonyms/invalid_synonym.php-s:3:&quot;bar&quot;;-i:1;'),
				array('fail', 'FILE_SEARCH_INVALID_TYPES-search_synonyms/invalid_synonym.php-i:0;-s:4:&quot;this&quot;;'),
				array('fail', 'FILE_SEARCH_INVALID_TYPES-search_synonyms/invalid_synonym.php-i:1;-i:2;'),
			)),
		);
	}

	/**
	* @dataProvider validate_search_synonyms_data
	*/
	public function test_validate_search_synonyms($iso_file, $expected)
	{
		$this->validator->validate_search_synonyms_file($iso_file);
		$this->assertEquals($expected, $this->error_collection->get_messages());
	}

	static public function validate_search_ignore_words_data()
	{
		return array(
			array('search_ignore_words/valid.php', array()),
			array('search_ignore_words/no_words.php', array(array('fail', 'FILE_INVALID_VARS-search_ignore_words/no_words.php-words'))),
			array('search_ignore_words/invalid_words.php', array(array('fail', 'FILE_INVALID_VARS-search_ignore_words/invalid_words.php-words'))),
			array('search_ignore_words/additional_variable.php', array(array('fail', 'FILE_INVALID_VARS-search_ignore_words/additional_variable.php-words'))),
			array('search_ignore_words/invalid_word.php', array(array('fail', 'FILE_SEARCH_INVALID_TYPE-search_ignore_words/invalid_word.php-i:0;'))),
		);
	}

	/**
	* @dataProvider validate_search_ignore_words_data
	*/
	public function test_validate_search_ignore_words($iso_file, $expected)
	{
		$this->validator->validate_search_ignore_words_file($iso_file);
		$this->assertEquals($expected, $this->error_collection->get_messages());
	}

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
	public function test_validate_index($iso_file, $expected)
	{
		$this->validator->validate_index_file($iso_file);
		$this->assertEquals($expected, $this->error_collection->get_messages());
	}

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

	public function return_callback()
	{
		return implode('-', func_get_args());
	}
}
