<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class phpbb_ext_official_translationvalidator_error_collection
{
	protected $messages;

	public function __construct()
	{
		$this->reset();
	}

	public function reset()
	{
		$this->messages = array();
	}

	public function push($error_type, $error_msg)
	{
		$this->messages[] = array($error_type, $error_msg);
	}

	public function get_messages()
	{
		return $this->messages;
	}
}
