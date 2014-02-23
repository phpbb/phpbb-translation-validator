<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\validator;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class key
{
	/**
	* @var \official\translationvalidator\message_collection
	*/
	protected $messages;

	/**
	* @var \phpbb\user
	*/
	protected $user;

	/**
	* Language to validate
	* @var string
	*/
	protected $origin_language;

	/**
	* Language to compare against
	* @var string
	*/
	protected $upstream_language;

	/**
	* phpBB Version we are validating (Should be '3.0' or '3.1' for now)
	* @var string
	*/
	protected $phpbb_version;

	/**
	* Construct
	*
	* @param	\official\translationvalidator\message_collection 	$message_collection	Collection where we push our messages to
	* @param	\phpbb\user	$user		Current user object, only required for lang()
	*/
	public function __construct(\official\translationvalidator\message_collection $message_collection, \phpbb\user $user)
	{
		$this->messages = $message_collection;
		$this->user = $user;
		$this->phpbb_version = '3.0';
	}

	/**
	* Set the iso of the language we validate
	*
	* @param	string	$language
	* @return	\official\translationvalidator\validator\key
	*/
	public function set_origin_language($language)
	{
		$this->origin_language = $language;

		return $this;
	}

	/**
	* Set the iso of the language we compare against
	*
	* @param	string	$language
	* @return	\official\translationvalidator\validator\key
	*/
	public function set_upstream_language($language)
	{
		$this->upstream_language = $language;

		return $this;
	}

	/**
	* Set the phpbb version we validate
	*
	* @param	string	$version	Should be 3.0 or 3.1 for now
	* @return	\official\translationvalidator\validator\key
	*/
	public function set_version($version)
	{
		$this->phpbb_version = $version;

		return $this;
	}

	/**
	* Validates type of the language and decides on further validation
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	mixed	$against_language		Original language
	* @param	mixed	$validate_language		Translated language
	* @return	null
	*/
	public function validate($file, $key, $against_language, $validate_language)
	{
		if (gettype($against_language) !== gettype($validate_language))
		{
			$this->messages->push('fail', $this->user->lang('INVALID_TYPE', $file, $key, gettype($against_language), gettype($validate_language)));
			return;
		}

		if ($this->phpbb_version !== '3.0' && $key === 'PLURAL_RULE')
		{
			if ($validate_language < 0 || $validate_language > 15)
			{
				$this->messages->push('fail', $this->user->lang('INVALID_PLURAL_RULE', $file, $validate_language));
				return;
			}
		}
		else if ($key === 'DIRECTION')
		{
			if (!in_array($validate_language, array('ltr', 'rtl')))
			{
				$this->messages->push('fail', $this->user->lang('INVALID_DIRECTION', $file, $validate_language));
				return;
			}
		}
		else if ($key === 'USER_LANG')
		{
			if (str_replace('_', '-', $this->origin_language) !== $validate_language && strpos($validate_language, $this->origin_language . '-') !== 0)
			{
				$this->messages->push('fail', $this->user->lang('INVALID_USER_LANG', $file, $validate_language));
				return;
			}
		}
		else if (gettype($against_language) === 'string')
		{
			$this->validate_string($file, $key, $against_language, $validate_language);
		}
		else
		{
			$this->validate_array($file, $key, $against_language, $validate_language);
		}
	}

	/**
	* Decides which array validation function should be used, based on the key
	*
	* Supports:
	*	- Dateformats
	*	- Datetime
	*	- Timezones
	*	- BBCode Tokens
	*	- Report Reasons
	*	- Plurals
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	array	$against_language		Original language
	* @param	array	$validate_language		Translated language
	* @return	null
	*/
	public function validate_array($file, $key, $against_language, $validate_language)
	{
		//var_dump($key, $against_language); echo '<br /><br />';
		if ($key === 'dateformats')
		{
			$this->validate_dateformats($file, $key, $against_language, $validate_language);
		}
		else if ($key === 'datetime')
		{
			#$this->validate_dateformats($file, $key, $against_language, $validate_language);
		}
		else if ($key === 'timezones')
		{
			#$this->validate_dateformats($file, $key, $against_language, $validate_language);
		}
		else if ($key === 'tokens')
		{
			#$this->validate_array_key($file, $key, $against_language, $validate_language);
		}
		else if ($key === 'report_reasons')
		{
			#$this->validate_array_key($file, $key, $against_language, $validate_language);
		}
		else if ($key === 'PM_ACTION')
		{
			#$this->validate_array_key($file, $key, $against_language, $validate_language);
		}
		else if ($key === 'PM_CHECK')
		{
			#$this->validate_array_key($file, $key, $against_language, $validate_language);
		}
		else if ($key === 'PM_RULE')
		{
			#$this->validate_array_key($file, $key, $against_language, $validate_language);
		}
		// Fix for 3.1 - Plurals are normal there...
		else if ($key === 'NUM_POSTS_IN_QUEUE' || $key === 'USER_LAST_REMINDED')
		{
			$this->validate_array_key($file, $key, $against_language, $validate_language);
		}
		else
		{
			$against_keys = array_keys($against_language);
			$key_types = array();
			foreach ($against_keys as $against_key)
			{
				$type = gettype($against_key);
				if (!isset($key_types[$type]))
				{
					$key_types[$type] = 0;
				}
				$key_types[$type]++;
			}

			if (sizeof($key_types) == 1)
			{
				if (isset($key_types['string']))
				{
					$this->validate_array_key($file, $key, $against_language, $validate_language);
				}
				else if (isset($key_types['integer']))
				{
					// Plurals?!
					$this->messages->push('debug', $this->user->lang('LANG_ARRAY_UNSUPPORTED', $file, $key));
					$this->validate_array_key($file, $key, $against_language, $validate_language);
					return;
				}
			}
			else
			{
				$this->messages->push('debug', $this->user->lang('LANG_ARRAY_MIXED', $file, $key, implode(', ', array_keys($key_types))));
			}
		}
	}

	/**
	* Validates the dateformats
	*
	* Should not be empty
	* Keys and Descriptions should not contain HTML
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	array	$against_language		Original language
	* @param	array	$validate_language		Translated language
	* @return	null
	*/
	public function validate_dateformats($file, $key, $against_language, $validate_language)
	{
		if (empty($validate_language))
		{
			$this->messages->push('fail', $this->user->lang('LANG_ARRAY_EMPTY', $file, $key));
			return;
		}

		foreach ($validate_language as $dateformat => $example_time)
		{
			$this->validate_string($file, $key . '.' . $dateformat, '', $dateformat);
			$this->validate_string($file, $key . '.' . $dateformat, '', $example_time);
		}
	}

	/**
	* Validates a permission entry
	*
	* Should have a cat and lang key
	* cat should be the same as in origin language
	* lang should compare like a string to origin lang
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	array	$against_language		Original language
	* @param	array	$validate_language		Translated language
	* @return	null
	*/
	public function validate_acl($file, $key, $against_language, $validate_language)
	{
		if (!isset($validate_language['cat']))
		{
			$this->messages->push('fail', $this->user->lang('ACL_MISSING_CAT', $file, $key));
		}
		else if ($validate_language['cat'] !== $against_language['cat'])
		{
			$this->messages->push('fail', $this->user->lang('ACL_INVALID_CAT', $file, $key, $against_language['cat'], $validate_language['cat']));
		}

		if (!isset($validate_language['lang']))
		{
			$this->messages->push('fail', $this->user->lang('ACL_MISSING_LANG', $file, $key));
		}
		else
		{
			$this->validate_string($file, $key, $against_language['lang'], $validate_language['lang']);
		}
	}

	/**
	* Validates an array entry
	*
	* Arrays that have strings as key, must have the same keys in the foreign language
	* Arrays that have integers as keys, might have different ones (plurals)
	* Additional keys can not be further validated
	*
	* Function works recursive
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	array	$against_language		Original language
	* @param	array	$validate_language		Translated language
	* @return	null
	*/
	public function validate_array_key($file, $key, $against_language, $validate_language)
	{
		if (gettype($against_language) !== gettype($validate_language))
		{
			$this->messages->push('fail', $this->user->lang('INVALID_TYPE', $file, $key, gettype($against_language), gettype($validate_language)));
			return;
		}

		$cat_validate_keys = array_keys($validate_language);
		$cat_against_keys = array_keys($against_language);
		$missing_keys = array_diff($cat_against_keys, $cat_validate_keys);
		$invalid_keys = array_diff($cat_validate_keys, $cat_against_keys);

		foreach ($against_language as $array_key => $lang)
		{
			// Only error for string keys, plurals might force different keys
			if (!isset($validate_language[$array_key]))
			{
				if (gettype($array_key) == 'string')
				{
					$this->messages->push('fail', $this->user->lang('LANG_ARRAY_MISSING', $file, $key, $array_key));
				}
				continue;
			}

			if (is_string($lang))
			{
				$this->validate_string($file, $key . '.' . $array_key, $lang, $validate_language[$array_key]);
			}
			else
			{
				$this->validate_array($file, $key . '.' . $array_key, $lang, $validate_language[$array_key]);
			}
		}

		if (!empty($invalid_keys))
		{
			foreach ($invalid_keys as $array_key)
			{
				if (gettype($array_key) == 'string')
				{
					$this->messages->push('fail', $this->user->lang('LANG_ARRAY_INVALID', $file, $key, $array_key));
				}
				else
				{
					// Strangly used plural?
					$this->messages->push('warning', $this->user->lang('LANG_ARRAY_INVALID', $file, $key, $array_key));
				}
				$this->messages->push('warning', $this->user->lang('KEY_NOT_VALIDATED', $file, $key . '.' . $array_key));
			}
		}
	}

	/**
	* Validates a string
	*
	* Checks whether replacements %d and %s are used correctly
	* Checks for HTML
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	string	$against_language		Original language string
	* @param	string	$validate_language		Translated language string
	* @return	null
	*/
	public function validate_string($file, $key, $against_language, $validate_language)
	{
		if (gettype($against_language) !== gettype($validate_language))
		{
			$this->messages->push('fail', $this->user->lang('INVALID_TYPE', $file, $key, gettype($against_language), gettype($validate_language)));
			return;
		}

		$against_strings = substr_count($against_language, '%s');
		$against_integers = substr_count($against_language, '%d');
		$validate_strings = substr_count($validate_language, '%s');
		$validate_integers = substr_count($validate_language, '%d');
		for ($i = 1; $i < 10; $i++)
		{
			if ($looping_count = substr_count($against_language, '%' . $i . '$s'))
			{
				$against_strings++;
			}
			if ($looping_count = substr_count($against_language, '%' . $i . '$d'))
			{
				$against_integers++;
			}
			if ($looping_count = substr_count($validate_language, '%' . $i . '$s'))
			{
				$validate_strings++;
			}
			if ($looping_count = substr_count($validate_language, '%' . $i . '$d'))
			{
				$validate_integers++;
			}
		}

		if ($against_strings - $validate_strings !== 0)
		{
			$this->messages->push('fail', $this->user->lang('INVALID_NUM_ARGUMENTS', $file, $key, 'string', $against_strings, $validate_strings), $against_language, $validate_language);
		}

		if ($against_integers - $validate_integers !== 0)
		{
			$level = ($against_integers == 1 && $validate_integers == 0) ? 'warning' : 'fail';
			$this->messages->push($level, $this->user->lang('INVALID_NUM_ARGUMENTS', $file, $key, 'integer', $against_integers, $validate_integers), $against_language, $validate_language);
		}

		$this->validate_html($file, $key, $against_language, $validate_language);
	}

	/**
	* List of additional html we found
	*
	* This will allow to not display the same error multiple times for the same string
	* Structure: file -> key -> html-tag
	*
	* @var array
	*/
	protected $additional_html_found = array();

	/**
	* Validates the html usage in a string
	*
	* Checks whether the used HTML tags are also used in the original language.
	* Omitting tags is okay, as long as both (start and end) are omitted.
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	string	$against_language		Original language string
	* @param	string	$validate_language		Translated language string
	* @return	null
	*/
	public function validate_html($file, $key, $against_language, $validate_language)
	{
		if (substr($file, -12) == '/install.php' && in_array($key, array(
			'INSTALL_CONGRATS_EXPLAIN',
			'INSTALL_INTRO_BODY',
			'SUPPORT_BODY',
			'UPDATE_INSTALLATION_EXPLAIN',
			'OVERVIEW_BODY',
		)) || substr($file, -8) == '/ucp.php' && in_array($key, array(
			'TERMS_OF_USE_CONTENT',
			'PRIVACY_POLICY',
		)))
		{
			$against_language = '<p>' . $against_language . '</p>';
			$validate_language = '<p>' . $validate_language . '</p>';
		}

		$against_html = $validate_html = $open_tags = array();
		preg_match_all('/\<.+?\>/', $against_language, $against_html);
		preg_match_all('/\<.+?\>/', $validate_language, $validate_html);

		if (!empty($validate_html) && !empty($validate_html[0]))
		{
			$failed_unclosed = false;
			foreach ($validate_html[0] as $possible_html)
			{
				$opening_tag = $possible_html[1] !== '/';
				$ignore_additional = false;

				// The closing tag contains a space
				if (!$opening_tag && strpos($possible_html, ' ') !== false)
				{
					$this->messages->push('fail', $this->user->lang('LANG_INVALID_HTML', $file, $key, htmlspecialchars($possible_html)), $against_language, $validate_language);
					$ignore_additional = true;
				}

				$tag = (strpos($possible_html, ' ') !== false) ? substr($possible_html, 1, strpos($possible_html, ' ') - 1) : substr($possible_html, 1, strpos($possible_html, '>') - 1);
				$tag = ($opening_tag) ? $tag : substr($tag, 1);

				if ($opening_tag)
				{
					if (in_array($tag, $open_tags))
					{
						if (!$failed_unclosed)
						{
							$this->messages->push('fail', $this->user->lang('LANG_UNCLOSED_HTML', $file, $key, $tag), $against_language, $validate_language);
						}
						$failed_unclosed = true;
					}
					else if (substr($possible_html, -3) !== ' />')
					{
						$open_tags[] = $tag;
					}
				}
				else if (empty($open_tags))
				{
					if (!$failed_unclosed)
					{
						$this->messages->push('fail', $this->user->lang('LANG_CLOSING_UNOPENED_HTML', $file, $key, $tag), $against_language, $validate_language);
					}
					$failed_unclosed = true;
				}
				else if (end($open_tags) != $tag)
				{
					if (!$failed_unclosed)
					{
						$this->messages->push('fail', $this->user->lang('LANG_UNCLOSED_HTML', $file, $key, end($open_tags)), $against_language, $validate_language);
					}
					$failed_unclosed = true;
				}
				else
				{
					array_pop($open_tags);
				}

				$possible_html_specialchars = htmlspecialchars($possible_html);
				// HTML tag is not used in original language
				if (!$ignore_additional && !in_array($possible_html, $against_html[0]) && !isset($this->additional_html_found[$file][$key][$possible_html_specialchars]))
				{
					$this->additional_html_found[$file][$key][$possible_html_specialchars] = true;
					$level = (in_array($possible_html_specialchars, array(
						'&lt;i&gt;',
						'&lt;/i&gt;',
						'&lt;b&gt;',
						'&lt;/b&gt;',
					))) ? 'warning' : ((in_array($possible_html_specialchars, array(
						'&lt;em&gt;',
						'&lt;/em&gt;',
						'&lt;strong&gt;',
						'&lt;/strong&gt;',
						'&lt;u&gt;',
						'&lt;/u&gt;',
						'&lt;/a&gt;',
						'&lt;br /&gt;',
					))) ? 'notice' : 'fail');
					$this->messages->push($level, $this->user->lang('LANG_ADDITIONAL_HTML', $file, $key, $possible_html_specialchars), $against_language, $validate_language);
				}

			}

			if (!empty($open_tags))
			{
				if (!$failed_unclosed)
				{
					$this->messages->push('fail', $this->user->lang('LANG_UNCLOSED_HTML', $file, $key, $open_tags[0]), $against_language, $validate_language);
				}
			}
		}
	}
}
