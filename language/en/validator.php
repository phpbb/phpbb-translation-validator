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
	'TRANSLATION_VALIDATOR'			=> 'Translation Validator',
	'TRANSLATION_VALIDATE_AGAINST'	=> 'Validate “%1$s” against “%2$s”',

	'INVALID_LANGUAGE'			=> 'Language package “%s” not found',

	'ACL_INVALID_CAT'			=> 'Permission “%2$s” in file “%1$s” should have cat “%3$s” but has “%4$s”',
	'ACL_MISSING_CAT'			=> 'Permission “%2$s” in file “%1$s” is missing the “cat” key',
	'ACL_MISSING_LANG'			=> 'Permission “%2$s” in file “%1$s” is missing the “lang” key',
	'LANG_ARRAY_EMPTY'			=> 'Array “%2$s” in file “%1$s” should not be empty',
	'LANG_ARRAY_INVALID'		=> 'Array “%2$s” in file “%1$s” has invalid keys: “%3$s”',
	'LANG_ARRAY_MISSING'		=> 'Array “%2$s” in file “%1$s” is missing keys: “%3$s”',
	'LANG_ARRAY_MIXED'			=> 'Array “%2$s” in file “%1$s” has mixed types: %3$s',
	'LANG_ARRAY_UNSUPPORTED'	=> 'Array “%2$s” in file “%1$s” has unsupported type integer',
	'ADDITIONAL_FILE'			=> 'Additional file “%s”',
	'INVALID_INDEX_FILE'		=> '“%s” should be empty',
	'INVALID_ISO_FILE'			=> '“%s” should only contain 3 lines',
	'INVALID_KEY'				=> 'Invalid key “%2$s” in file “%1$s”',
	'INVALID_NUM_ARGUMENTS'		=> 'Key “%2$s” in file “%1$s” should have “%4$s” %3$s arguments, but has “%5$s”',
	'INVALID_TYPE'				=> 'Key “%2$s” in file “%1$s” should be type “%3$s” but is type “%4$s”',
	'MISSING_FILE'				=> 'Missing file “%s”',
	'MISSING_KEY'				=> 'Missing key “%2$s” in file “%1$s”',
	'EMAIL_MISSING_SUBJECT'		=> 'The email template “%1$s” is missing a subject-line',
	'EMAIL_INVALID_SUBJECT'		=> 'The email template “%1$s” should not have a subject-line',
	'EMAIL_MISSING_SIG'			=> 'The email template “%1$s” is missing the signature',
	'EMAIL_INVALID_SIG'			=> 'The email template “%1$s” should not have the signature appended',
	'EMAIL_MISSING_NEWLINE'		=> 'The email template “%1$s” does not have a new line at the end of the file',
	'EMAIL_ADDITIONAL_VARS'		=> 'The email template “%1$s” is using additional variables “%2$s”',
	'EMAIL_MISSING_VARS'		=> 'The email template “%1$s” is not using variables “%2$s”',

	'KEY_NOT_VALIDATED'			=> 'Key “%2$s” in file “%1$s” was not validated',
	'FILE_NOT_VALIDATED'		=> 'File “%s” was not validated',
	'FILE_HELP_INVALID'			=> 'The help file “%s” is invalid',
	'FILE_HELP_INVALID_ENTRY'	=> 'The help file “%1$s” has an invalid entry: “%2$s”',
	'FILE_HELP_ONE_BREAK'		=> "The help file “%s” has should have exactly one column break entry: “array( 0 => '--', 1 => '--'),”",
	'FILE_INVALID_VARS'			=> '“%1$s” should only contain the “$%2$s” array',
	'FILE_SEARCH_INVALID_TYPE'	=> 'The search file “%1$s” should only contain entries of type “string”',
	'FILE_SEARCH_INVALID_TYPES'	=> 'The search file “%1$s” should only contain entries of type “string => string”',

	'FAILS'					=> 'Fails',
	'NOTICES'				=> 'Notices',
	'WARNINGS'				=> 'Warnings',

	'NO_FAILS'				=> 'No fails',
	'NO_NOTICES'			=> 'No notices',
	'NO_WARNINGS'			=> 'No warnings',
));
