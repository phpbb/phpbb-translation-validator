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

class phpbb_ext_official_translationvalidator_validator
{
	protected $filelist_validator;
	protected $file_validator;
	protected $error_collection;
	protected $user;
	protected $package_path;

	protected $validate_files;
	protected $validate_language_dir;
	protected $validate_against_dir;

	public function __construct($filelist_validator, $file_validator, $error_collection, $user, $lang_path)
	{
		$this->filelist_validator = $filelist_validator;
		$this->file_validator = $file_validator;
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

		$this->filelist_validator->set_validate_language($language);
		$this->file_validator->set_validate_language($language);

		return $this;
	}

	public function set_validate_against($language)
	{
		$this->validate_against_dir = $this->package_path . $language;

		if (!file_exists($this->validate_against_dir))
		{
			throw new OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
		}

		$this->filelist_validator->set_validate_against($language);
		$this->file_validator->set_validate_against($language);

		return $this;
	}

	public function validate()
	{
		$this->validate_files = $this->filelist_validator->validate();

		if (empty($this->validate_files))
		{
			return false;
		}

		foreach ($this->validate_files as $file)
		{
			$this->file_validator->validate($file);
		}

		return $this->error_collection;
	}
}
