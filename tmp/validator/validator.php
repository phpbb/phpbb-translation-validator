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

class validator
{
	/**
	* @var \official\translationvalidator\validator\filelist
	*/
	protected $filelist_validator;

	/**
	* @var \official\translationvalidator\validator\file
	*/
	protected $file_validator;

	/**
	* @var \official\translationvalidator\message_collection
	*/
	protected $messages;

	/**
	* @var \phpbb\user
	*/
	protected $user;

	/**
	* Path to the folder where the language versions are.
	* @var string
	*/
	protected $package_path;

	/**
	* Path to the folder where the languages of a specific version are.
	* @var string
	*/
	protected $version_package_path;

	/**
	* phpBB Version we are validating (Should be '3.0' or '3.1' for now)
	* @var string
	*/
	protected $phpbb_version;

	/**
	* List of files to check for in both directories
	* @var array
	*/
	protected $validate_files;

	/**
	* Language to validate
	* @var string
	*/
	protected $origin_language;

	/**
	* Path to the folder where the language to validate is,
	* including $package_path.
	* @var string
	*/
	protected $origin_language_dir;

	/**
	* Language to compare against
	* @var string
	*/
	protected $upstream_language;

	/**
	* Path to the folder where the language to compare against is,
	* including $package_path.
	* @var string
	*/
	protected $upstream_language_dir;

	/**
	* Construct
	*
	* @param	\official\translationvalidator\validator\filelist	$filelist_validator		Validator for the file list
	* @param	\official\translationvalidator\validator\file		$file_validator			Validator for the language files
	* @param	\official\translationvalidator\message_collection 	$message_collection	Collection where we push our messages to
	* @param	\phpbb\user	$user		Current user object, only required for lang()
	* @param	string	$lang_path		Path to the folder where the languages are
	* @return	\official\translationvalidator\validator\validator
	*/
	public function __construct(\official\translationvalidator\validator\filelist $filelist_validator, \official\translationvalidator\validator\file $file_validator, \official\translationvalidator\message_collection $message_collection, \phpbb\user $user, $lang_path)
	{
		$this->filelist_validator = $filelist_validator;
		$this->file_validator = $file_validator;
		$this->messages = $message_collection;
		$this->user = $user;
		$this->package_path = $lang_path;
		$this->version_package_path = $lang_path;
		$this->phpbb_version = '3.0';
	}

	/**
	* Set the iso of the language we validate
	*
	* @param	string	$language
	* @return	\official\translationvalidator\validator\validator
	*/
	public function set_origin_language($language)
	{
		$this->origin_language = $language;
		$this->origin_language_dir = $this->version_package_path . $this->origin_language;

		if (!file_exists($this->origin_language_dir))
		{
			throw new \OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
		}

		$this->filelist_validator->set_origin_language($language);
		$this->file_validator->set_origin_language($language);

		return $this;
	}

	/**
	* Set the iso of the language we compare against
	*
	* @param	string	$language
	* @return	\official\translationvalidator\validator\validator
	*/
	public function set_upstream_language($language)
	{
		$this->upstream_language = $language;
		$this->upstream_language_dir = $this->version_package_path . $this->upstream_language;

		if (!file_exists($this->upstream_language_dir))
		{
			throw new \OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
		}

		$this->filelist_validator->set_upstream_language($language);
		$this->file_validator->set_upstream_language($language);

		return $this;
	}

	/**
	* Set the phpbb version we validate
	*
	* @param	string	$version	Should be 3.0 or 3.1 for now
	* @return	\official\translationvalidator\validator\validator
	*/
	public function set_version($version)
	{
		$this->phpbb_version = $version;
		$this->version_package_path = $this->package_path . $this->phpbb_version . '/';

		if (!file_exists($this->version_package_path))
		{
			throw new \OutOfBoundsException($this->user->lang('INVALID_VERSION', $version));
		}

		$this->filelist_validator->set_version($version);
		$this->file_validator->set_version($version);

		return $this;
	}

	public function validate()
	{
		$this->validate_files = $this->filelist_validator->validate();

		if (empty($this->validate_files))
		{
			return $this->messages;
		}

		foreach ($this->validate_files as $upstream_file => $origin_file)
		{
			$this->file_validator->validate($upstream_file, $origin_file);
		}

		return $this->messages;
	}
}
