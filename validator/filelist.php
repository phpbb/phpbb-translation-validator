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

class phpbb_ext_official_translationvalidator_validator_filelist
{
	protected $error_collection;
	protected $user;
	protected $package_path;

	protected $language_file_list;
	protected $validate_language_dir;

	protected $against_file_list;
	protected $validate_against_dir;

	public function __construct($error_collection, $user, $lang_path)
	{
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

	public function validate()
	{
		$this->against_file_list = $this->get_file_list($this->validate_against_dir);
		$this->language_file_list = $this->get_file_list($this->validate_language_dir);

		$missing_files = array_diff($this->against_file_list, $this->language_file_list);
		if (!empty($missing_files))
		{
			foreach ($missing_files as $missing_file)
			{
				$this->error_collection->push('fail', $this->user->lang('MISSING_FILE', $missing_file));
			}
		}

		$additional_files = array_diff($this->language_file_list, $this->against_file_list);
		if (!empty($additional_files))
		{
			foreach ($additional_files as $additional_file)
			{
				$this->error_collection->push('notice', $this->user->lang('ADDITIONAL_FILE', $additional_file));
			}
		}

		// We only further try to validate files that exist in both languages.
		return array_intersect($this->against_file_list, $this->language_file_list);
	}

	protected function get_file_list($dir)
	{
		try 
		{
			$iterator = new DirectoryIterator($dir);
		}
		catch (Exception $e)
		{
			return array();
		}

		$files = array();
		foreach ($iterator as $fileInfo)
		{
			if ($fileInfo->isDot())
			{
				continue;
			}

			if ($fileInfo->isDir())
			{
				$sub_dir = $this->get_file_list($fileInfo->getPath() . '/' . $fileInfo->getFilename());
				foreach ($sub_dir as $file)
				{
					$files[] = $fileInfo->getFilename() . '/' . $file;
				}
			}
			else
			{
				$files[] = $fileInfo->getFilename();
			}
		}

		return $files;
	}
}
