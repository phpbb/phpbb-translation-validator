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

class phpbb_ext_official_translationvalidator_message_collection
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

	public function push($message_type, $message)
	{
		$this->messages[] = array($message_type, $message);
	}

	public function get_messages()
	{
		return $this->messages;
	}
}
