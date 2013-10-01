<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class message_collection
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

	public function push($message_type, $message, $source_language = null, $origin_language = null)
	{
		$this->messages[] = array(
			'type'		=> $message_type,
			'message'	=> $message,
			'source'	=> $source_language,
			'origin'	=> $origin_language,
		);
	}

	public function get_messages()
	{
		return $this->messages;
	}
}
