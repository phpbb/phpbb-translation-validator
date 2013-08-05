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

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'TRANSLATION_VALIDATOR'		=> 'Translation Validator',
	'INVALID_LANGUAGE'			=> 'Language package “%s” not found',

	'ADDITIONAL_FILE'			=> 'Additional file “%s”',
	'MISSING_FILE'				=> 'Missing file “%s”',
));
