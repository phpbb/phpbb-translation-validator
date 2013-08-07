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

class phpbb_ext_official_translationvalidator_validator_file
{
	protected $key_validator;
	protected $error_collection;
	protected $user;
	protected $package_path;

	protected $validate_files;
	protected $validate_language_dir;
	protected $validate_against_dir;

	public function __construct($key_validator, $error_collection, $user, $lang_path)
	{
		$this->key_validator = $key_validator;
		$this->error_collection = $error_collection;
		$this->user = $user;
		$this->package_path = $lang_path;
	}

	public function set_validate_language($language)
	{
		$this->validate_language_dir = $this->package_path . $language;

		if (!file_exists($this->validate_language_dir))
		{
			throw new OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
		}

		return $this;
	}

	public function set_validate_against($language)
	{
		$this->validate_against_dir = $this->package_path . $language;

		if (!file_exists($this->validate_against_dir))
		{
			throw new OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
		}

		return $this;
	}

	public function validate($file)
	{
		if (strpos($file, 'email/') === 0 && substr($file, -4) === '.txt')
		{
			$this->validate_email($file);
		}
		else if (strpos($file, 'help_') === 0 && substr($file, -4) === '.php')
		{
			$this->validate_help_file($file);
		}
		else if ($file == 'search_synonyms.php')
		{
			$this->validate_search_synonyms_file($file);
		}
		else if ($file == 'search_ignore_words.php')
		{
			$this->validate_search_ignore_words_file($file);
		}
		else if (substr($file, -4) === '.php')
		{
			$this->validate_lang_file($file);
		}
		else if (substr($file, -9) === 'index.htm')
		{
			$this->validate_index_file($file);
		}
		else if ($file === 'iso.txt')
		{
			$this->validate_iso_file($file);
		}
		else
		{
			$this->error_collection->push('debug', $this->user->lang('FILE_NOT_VALIDATED', $file));
		}
	}

	public function validate_lang_file($file)
	{
		$against = $validate = array();

		include($this->validate_against_dir . '/' . $file);
		$against = $lang;
		unset($lang);

		include($this->validate_language_dir . '/' . $file);
		$validate = $lang;
		unset($lang);

		foreach ($against as $against_lang_key => $against_language)
		{
			if (!isset($validate[$against_lang_key]))
			{
				$this->error_collection->push('fail', $this->user->lang('MISSING_KEY', $file, $against_lang_key));
				continue;
			}

			$this->key_validator->validate($file, $against_lang_key, $against_language, $validate[$against_lang_key]);
		}

		foreach ($validate as $validate_lang_key => $validate_language)
		{
			if (!isset($against[$validate_lang_key]))
			{
				$this->error_collection->push('warning', $this->user->lang('INVALID_KEY', $file, $validate_lang_key));
			}
		}
	}

	protected function validate_email($file)
	{
		$against_file = (string) file_get_contents($this->validate_against_dir . '/' . $file);
		$validate_file = (string) file_get_contents($this->validate_language_dir . '/' . $file);

		$against_file = explode("\n", $against_file);
		$validate_file = explode("\n", $validate_file);

		// One language contains a subject, the other one does not
		if (strpos($against_file[0], 'Subject: ') === 0 && strpos($validate_file[0], 'Subject: ') !== 0)
		{
			$this->error_collection->push('fail', $this->user->lang('EMAIL_MISSING_SUBJECT', $file));
		}
		else if (strpos($against_file[0], 'Subject: ') !== 0 && strpos($validate_file[0], 'Subject: ') === 0)
		{
			$this->error_collection->push('fail', $this->user->lang('EMAIL_INVALID_SUBJECT', $file));
		}

		// One language contains the signature, the other one does not
		if ((end($against_file) === '{EMAIL_SIG}' || prev($against_file) === '{EMAIL_SIG}')
			&& end($validate_file) !== '{EMAIL_SIG}' && prev($validate_file) !== '{EMAIL_SIG}')
		{
			$this->error_collection->push('fail', $this->user->lang('EMAIL_MISSING_SIG', $file));
		}
		else if ((end($validate_file) === '{EMAIL_SIG}' || prev($validate_file) === '{EMAIL_SIG}')
			&& end($against_file) !== '{EMAIL_SIG}' && prev($against_file) !== '{EMAIL_SIG}')
		{
			$this->error_collection->push('fail', $this->user->lang('EMAIL_INVALID_SIG', $file));
		}

		$validate_template_vars = $against_template_vars = array();
		preg_match_all('/{.+?}/', implode("\n", $validate_file), $validate_template_vars);
		preg_match_all('/{.+?}/', implode("\n", $against_file), $against_template_vars);

		$additional_against = array_diff($against_template_vars[0], $validate_template_vars[0]);
		$additional_validate = array_diff($validate_template_vars[0], $against_template_vars[0]);

		// Check the used template variables
		if (!empty($additional_validate))
		{
			$this->error_collection->push('warning', $this->user->lang('EMAIL_ADDITIONAL_VARS', $file, implode(', ', $additional_validate)));
		}

		if (!empty($additional_against))
		{
			$this->error_collection->push('warning', $this->user->lang('EMAIL_MISSING_VARS', $file, implode(', ', $additional_against)));
		}

		// Check for new liens at the end of the file
		if (end($validate_file) !== '')
		{
			$this->error_collection->push('notice', $this->user->lang('EMAIL_MISSING_NEWLINE', $file));
		}
	}

	public function validate_help_file($file)
	{
		include($this->validate_language_dir . '/' . $file);

		$defined_variables = get_defined_vars();
		if (sizeof($defined_variables) != 2 || !isset($defined_variables['help']) || gettype($defined_variables['help']) != 'array')
		{
			$this->error_collection->push('fail', $this->user->lang('FILE_INVALID_VARS', $file, 'help'));
			return;
		}

		$column_breaks = 0;
		foreach ($help as $help)
		{
			if (gettype($help) != 'array' || sizeof($help) != 2 || !isset($help[0]) || !isset($help[1]))
			{
				$this->error_collection->push('fail', $this->user->lang('FILE_HELP_INVALID_ENTRY', $file, htmlspecialchars(serialize($help))));
			}
			else if ($help[0] == '--' && $help[1] == '--')
			{
				$column_breaks++;
			}
		}
		if ($column_breaks != 1)
		{
			$this->error_collection->push('fail', $this->user->lang('FILE_HELP_ONE_BREAK', $file));
		}
	}

	public function validate_search_synonyms_file($file)
	{
		include($this->validate_language_dir . '/' . $file);

		$defined_variables = get_defined_vars();
		if (sizeof($defined_variables) != 2 || !isset($defined_variables['synonyms']) || gettype($defined_variables['synonyms']) != 'array')
		{
			$this->error_collection->push('fail', $this->user->lang('FILE_INVALID_VARS', $file, 'synonyms'));
			return;
		}

		foreach ($synonyms as $synonym1 => $synonym2)
		{
			if (gettype($synonym1) != 'string' || gettype($synonym2) != 'string')
			{
				$this->error_collection->push('fail', $this->user->lang('FILE_SEARCH_INVALID_TYPES', $file, htmlspecialchars(serialize($synonym1)), htmlspecialchars(serialize($synonym2))));
			}
		}
	}

	public function validate_search_ignore_words_file($file)
	{
		include($this->validate_language_dir . '/' . $file);

		$defined_variables = get_defined_vars();
		if (sizeof($defined_variables) != 2 || !isset($defined_variables['words']) || gettype($defined_variables['words']) != 'array')
		{
			$this->error_collection->push('fail', $this->user->lang('FILE_INVALID_VARS', $file, 'words'));
			return;
		}

		foreach ($words as $word)
		{
			if (gettype($word) != 'string')
			{
				$this->error_collection->push('fail', $this->user->lang('FILE_SEARCH_INVALID_TYPE', $file, htmlspecialchars(serialize($word))));
			}
		}
	}

	public function validate_index_file($file)
	{
		$validate_file = (string) file_get_contents($this->validate_language_dir . '/' . $file);

		// Empty index.htm file or one that displayes an empty white page
		if ($validate_file !== '' && md5($validate_file) != '16703867d439efbd7c373dc2269e25a7')
		{
			$this->error_collection->push('fail', $this->user->lang('INVALID_INDEX_FILE', $file));
		}
	}

	public function validate_iso_file($file)
	{
		$iso_file = (string) file_get_contents($this->validate_language_dir . '/' . $file);
		$iso_file = explode("\n", $iso_file);

		if (sizeof($iso_file) != 3)
		{
			$this->error_collection->push('fail', $this->user->lang('INVALID_ISO_FILE', $file));
		}
	}
}
