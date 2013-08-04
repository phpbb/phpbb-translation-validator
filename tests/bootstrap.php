<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

define('IN_PHPBB', true);
$phpbb_root_path = '../../../';
$phpEx = 'php';
require_once $phpbb_root_path . 'includes/startup.' . $phpEx;

$table_prefix = 'phpbb_';
require_once $phpbb_root_path . 'includes/constants.' . $phpEx;
require_once $phpbb_root_path . 'includes/class_loader.' . $phpEx;

$phpbb_class_loader_mock = new phpbb_class_loader('phpbb_mock_', dirname(__FILE__) . '/mock/', $phpEx);
$phpbb_class_loader_mock->register();
$phpbb_class_loader_ext = new phpbb_class_loader('phpbb_ext_', $phpbb_root_path . 'ext/', $phpEx);
$phpbb_class_loader_ext->register();
$phpbb_class_loader = new phpbb_class_loader('phpbb_', $phpbb_root_path . 'includes/', $phpEx);
$phpbb_class_loader->register();

require_once $phpbb_root_path . '../tests/test_framework/phpbb_test_case_helpers.' . $phpEx;
require_once $phpbb_root_path . '../tests/test_framework/phpbb_test_case.' . $phpEx;
require_once $phpbb_root_path . '../tests/test_framework/phpbb_database_test_case.' . $phpEx;
require_once $phpbb_root_path . '../tests/test_framework/phpbb_database_test_connection_manager.' . $phpEx;
require_once $phpbb_root_path . '../tests/test_framework/phpbb_functional_test_case.' . $phpEx;
require_once 'test_framework/test_case_helpers.' . $phpEx;
require_once 'test_framework/test_case.' . $phpEx;
require_once 'test_framework/database_test_case.' . $phpEx;
require_once 'test_framework/database_test_connection_manager.' . $phpEx;
require_once 'test_framework/functional_test_case.' . $phpEx;
