<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @codeCoverageIgnore
*/
abstract class phpbb_ext_database_test_case extends phpbb_database_test_case
{
	protected $db;

	public function setUp()
	{
		parent::setUp();

		global $db;
		$db = $this->db = $this->new_dbal();
	}

	protected function create_connection_manager($config)
	{
		return new phpbb_ext_database_test_connection_manager($config);
	}

	public function get_database_config()
	{
		$config = phpbb_ext_test_case_helpers::get_test_config();

		if (!isset($config['dbms']))
		{
			$this->markTestSkipped('Missing test_config.php: See first error.');
		}

		return $config;
	}

	public function get_test_case_helpers()
	{
		if (!$this->test_case_helpers)
		{
			$this->test_case_helpers = new phpbb_ext_test_case_helpers($this);
		}

		return $this->test_case_helpers;
	}
}
