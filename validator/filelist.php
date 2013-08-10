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
	/**
	* @var phpbb_ext_official_translationvalidator_message_collection
	*/
	protected $messages;

	/**
	* @var phpbb_user
	*/
	protected $user;

	/**
	* Path to the folder where the languages are.
	* @var string
	*/
	protected $package_path;

	/**
	* Path to the folder where the language to validate is,
	* including $package_path.
	* @var string
	*/
	protected $validate_language_dir;

	/**
	* Path to the folder where the language to compare against is,
	* including $package_path.
	* @var string
	*/
	protected $validate_against_dir;

	/**
	* List of files contained in $validate_language_dir
	* @var array
	*/
	protected $language_file_list;

	/**
	* List of files contained in $validate_against_dir
	* @var array
	*/
	protected $against_file_list;

	/**
	* Construct
	*
	* @param	phpbb_ext_official_translationvalidator_message_collection	$message_collection	Collection where we push our messages to
	* @param	phpbb_user	$user		Current user object, only required for lang()
	* @param	string	$lang_path		Path to the folder where the languages are
	* @return	phpbb_ext_official_translationvalidator_validator_filelist
	*/
	public function __construct($message_collection, $user, $lang_path)
	{
		$this->messages = $message_collection;
		$this->user = $user;
		$this->package_path = $lang_path;
	}

	/**
	* Set the iso of the language we validate
	*
	* @param	string	$language
	* @return	phpbb_ext_official_translationvalidator_validator_file
	*/
	public function set_validate_language($language)
	{
		$this->validate_language_dir = $this->package_path . $language;

		if (!file_exists($this->validate_language_dir))
		{
			throw new OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
		}

		return $this;
	}

	/**
	* Set the iso of the language we compare against
	*
	* @param	string	$language
	* @return	phpbb_ext_official_translationvalidator_validator_file
	*/
	public function set_validate_against($language)
	{
		$this->validate_against_dir = $this->package_path . $language;

		if (!file_exists($this->validate_against_dir))
		{
			throw new OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
		}

		return $this;
	}

	/**
	* Validates the directories
	*
	* Directories should not miss any files.
	* Directories must not contain additional php files.
	* Directories should not contain additional files.
	*
	* @return	array	List of files we can continue to validate.
	*/
	public function validate()
	{
		$this->against_file_list = $this->get_file_list($this->validate_against_dir);
		$this->language_file_list = $this->get_file_list($this->validate_language_dir);

		$missing_files = array_diff($this->against_file_list, $this->language_file_list);
		if (!empty($missing_files))
		{
			foreach ($missing_files as $missing_file)
			{
				$this->messages->push('fail', $this->user->lang('MISSING_FILE', $missing_file));
			}
		}

		$additional_files = array_diff($this->language_file_list, $this->against_file_list);
		if (!empty($additional_files))
		{
			foreach ($additional_files as $additional_file)
			{
				$level = (substr($additional_file, -4) == '.php') ? 'fail' : 'notice';
				$this->messages->push($level, $this->user->lang('ADDITIONAL_FILE', $additional_file));
			}
		}

		// We only further try to validate files that exist in both languages.
		return array_intersect($this->against_file_list, $this->language_file_list);
	}

	/**
	* Returns a list of files in that directory
	*
	* Works recursive with any depth
	*
	* @param	string	$dir	Directory to go through
	* @return	array	List of files (including directories from within $dir
	*/
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
