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

class phpbb_ext_official_translationvalidator_validator_key
{
	protected $error_collection;
	protected $user;

	protected $validate_files;
	protected $validate_language_dir;
	protected $validate_against_dir;

	public function __construct($error_collection, $user)
	{
		$this->error_collection = $error_collection;
		$this->user = $user;
	}

	public function validate($file, $key, $against_language, $validate_language)
	{
		if (gettype($against_language) !== gettype($validate_language))
		{
			$this->error_collection->push('fail', $this->user->lang('INVALID_TYPE', $file, $key, gettype($against_language), gettype($validate_language)));
			return;
		}

		if (gettype($against_language) === 'string')
		{
			$this->validate_lang($file, $key, $against_language, $validate_language);
		}
		else
		{
			$this->validate_array($file, $key, $against_language, $validate_language);
		}
	}

	// Arrays (Plurals, permissions and more)
	protected function validate_array($file, $key, $against_language, $validate_language)
	{
		if ($key === 'dateformats')
		{
			if (empty($validate_language))
			{
				$this->error_collection->push('fail', $this->user->lang('LANG_ARRAY_EMPTY', $file, $key));
			}
		}
		// Remove for 3.1
		else if (strpos($key, 'acl_') === 0)
		{
			$this->validate_acl($file, $key, $against_language, $validate_language);
		}
		// Remove for 3.1
		else if ($key === 'permission_cat' || $key === 'permission_type')
		{
			$this->validate_array_key($file, $key, $against_language, $validate_language);
		}
		// Remove for 3.1
		else if ($key === 'tz' || $key === 'tz_zones')
		{
			$this->validate_array_key($file, $key, $against_language, $validate_language);
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
					$this->error_collection->push('debug', $this->user->lang('LANG_ARRAY_UNSUPPORTED', $file, $key));
					$this->validate_array_key($file, $key, $against_language, $validate_language);
					return;
				}
			}
			else
			{
				$this->error_collection->push('debug', $this->user->lang('LANG_ARRAY_MIXED', $file, $key, implode(', ', array_keys($key_types))));
			}
		}

	}

	protected function validate_acl($file, $key, $against_language, $validate_language)
	{
		if (!isset($validate_language['cat']))
		{
			$this->error_collection->push('fail', $this->user->lang('ACL_MISSING_CAT', $file, $key));
		}
		else if ($validate_language['cat'] !== $against_language['cat'])
		{
			$this->error_collection->push('fail', $this->user->lang('ACL_INVALID_CAT', $file, $key, $against_language['cat'], $validate_language['cat']));
		}

		if (!isset($validate_language['lang']))
		{
			$this->error_collection->push('fail', $this->user->lang('ACL_MISSING_LANG', $file, $key));
		}
		else
		{
			$this->validate_lang($file, $key, $against_language['lang'], $validate_language['lang']);
		}
	}

	protected function validate_array_key($file, $key, $against_language, $validate_language)
	{
		$cat_validate_keys = array_keys($validate_language);
		$cat_against_keys = array_keys($against_language);
		$missing_keys = array_diff($cat_against_keys, $cat_validate_keys);
		$invalid_keys = array_diff($cat_validate_keys, $cat_against_keys);

		if (!empty($missing_keys))
		{
			$this->error_collection->push('fail', $this->user->lang('LANG_ARRAY_MISSING', $file, $key, implode(', ', $missing_keys)));
		}

		foreach ($against_language as $array_key => $lang)
		{
			if (!isset($validate_language[$array_key]))
			{
				// Key missing, but we displayed a fail before...
				continue;
			}

			if (is_string($lang))
			{
				$this->validate_lang($file, $key . '.' . $array_key, $lang, $validate_language[$array_key]);
			}
			else
			{
				$this->validate_array_key($file, $key . '.' . $array_key, $lang, $validate_language[$array_key]);
			}
		}

		if (!empty($invalid_keys))
		{
			// Strangly used plural?
			$this->error_collection->push('warning', $this->user->lang('LANG_ARRAY_INVALID', $file, $key, implode(', ', $invalid_keys)));
		}
	}

	protected function validate_lang($file, $key, $against_language, $validate_language)
	{
		$this->validate_string($file, $key, $against_language, $validate_language);
	}

	protected function validate_string($file, $key, $against_language, $validate_language)
	{
		// @todo: Reusing a replacement should be allowed: "we <s> do xyz on <s>" vs "we <s> do xyz here"
		// @todo: Ommiting a replacement should be allowed: "one post" vs "<i> post"
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
			$this->error_collection->push('warning', $this->user->lang('INVALID_NUM_ARGUMENTS', $file, $key, 'string', $against_strings, $validate_strings));
			return;
		}

		if ($against_integers - $validate_integers !== 0)
		{
			$this->error_collection->push('notice', $this->user->lang('INVALID_NUM_ARGUMENTS', $file, $key, 'integer', $against_integers, $validate_integers));
			return;
		}
	}
}