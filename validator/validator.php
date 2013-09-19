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

	public function __construct($filelist_validator, $file_validator, $emessage_collection, $user, $lang_path)
	{
		$this->filelist_validator = $filelist_validator;
		$this->file_validator = $file_validator;
		$this->messages = $emessage_collection;
		$this->user = $user;
		$this->package_path = $lang_path;
	}

	public function set_origin_language($language)
	{
		$this->origin_language = $language;
		$this->origin_language_dir = $this->package_path . $this->origin_language;

		if (!file_exists($this->origin_language_dir))
		{
			throw new OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
		}

		$this->filelist_validator->set_origin_language($language);
		$this->file_validator->set_origin_language($language);

		return $this;
	}

	public function set_upstream_language($language)
	{
		$this->upstream_language = $language;
		$this->upstream_language_dir = $this->package_path . $this->upstream_language;

		if (!file_exists($this->upstream_language_dir))
		{
			throw new OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
		}

		$this->filelist_validator->set_upstream_language($language);
		$this->file_validator->set_upstream_language($language);

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
