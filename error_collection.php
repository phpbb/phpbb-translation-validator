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
	protected $notices;
	protected $warnings;
	protected $fails;

	public function push($error_type, $error_msg)
	{
		switch ($error_type)
		{
			case 'notice':
				$this->notices[] = $error_msg;
			break;
			case 'warning':
				$this->warnings[] = $error_msg;
			break;
			case 'fail':
				$this->fails[] = $error_msg;
			break;
		}
	}
}
