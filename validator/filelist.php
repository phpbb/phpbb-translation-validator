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

class filelist
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
	* Path to the folder where the languages are.
	* @var string
	*/
	protected $package_path;

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
	* List of files contained in $origin_language_dir
	* @var array
	*/
	protected $origin_file_list;

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
	* List of files contained in $upstream_language_dir
	* @var array
	*/
	protected $upstream_file_list;

	/**
	* Construct
	*
	* @param	\official\translationvalidator\message_collection 	$message_collection	Collection where we push our messages to
	* @param	\phpbb\user	$user		Current user object, only required for lang()
	* @param	string	$lang_path		Path to the folder where the languages are
	* @return	\official\translationvalidator\validator\filelist
	*/
	public function __construct(\official\translationvalidator\message_collection $message_collection, \phpbb\user $user, $lang_path)
	{
		$this->messages = $message_collection;
		$this->user = $user;
		$this->package_path = $lang_path;
	}

	/**
	* Set the iso of the language we validate
	*
	* @param	string	$language
	* @return	\official\translationvalidator\validator\filelist
	*/
	public function set_origin_language($language)
	{
		$this->origin_language = $language;
		$this->origin_language_dir = $this->package_path . $this->origin_language;

		if (!file_exists($this->origin_language_dir))
		{
			throw new \OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
		}

		return $this;
	}

	/**
	* Set the iso of the language we compare against
	*
	* @param	string	$language
	* @return	\official\translationvalidator\validator\filelist
	*/
	public function set_upstream_language($language)
	{
		$this->upstream_language = $language;
		$this->upstream_language_dir = $this->package_path . $this->upstream_language;

		if (!file_exists($this->upstream_language_dir))
		{
			throw new \OutOfBoundsException($this->user->lang('INVALID_LANGUAGE', $language));
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
		$this->upstream_file_list = $this->get_file_list($this->upstream_language_dir);
		// License file is required but missing from en/, so we add it here
		$this->upstream_file_list[] = 'language/' . $this->upstream_language . '/LICENSE';
		$this->origin_file_list = $this->get_file_list($this->origin_language_dir);

		$valid_files = array();
		foreach ($this->upstream_file_list as $upsteam_file)
		{
			$test_origin_file = str_replace('/' . $this->upstream_language . '/', '/' . $this->origin_language . '/', $upsteam_file);
			if (!in_array($test_origin_file, $this->origin_file_list))
			{
				$this->messages->push('fail', $this->user->lang('MISSING_FILE', $test_origin_file));
			}
			else if (substr($upsteam_file, -4) != '.gif' && substr($upsteam_file, -12) != 'imageset.cfg')
			{
				$valid_files[$upsteam_file] = $test_origin_file;
			}
		}

		foreach ($this->origin_file_list as $origin_file)
		{
			$test_upstream_file = str_replace('/' . $this->origin_language . '/', '/' . $this->upstream_language . '/', $origin_file);
			if (!in_array($test_upstream_file, $this->upstream_file_list))
			{
				if (in_array($origin_file, array(
						'language/' . $this->origin_language . '/AUTHORS',
						'language/' . $this->origin_language . '/CHANGELOG',
						'language/' . $this->origin_language . '/README',
						'language/' . $this->origin_language . '/VERSION',
					)))
				{
					$level = 'debug';
					$this->messages->push('debug', $this->user->lang('ADDITIONAL_FILE', $origin_file));
				}
				else
				{
					$level = (substr($origin_file, -4) == '.php') ? 'fail' : 'warning';
					$this->messages->push($level, $this->user->lang('ADDITIONAL_FILE', $origin_file));
				}
			}
		}

		return $valid_files;
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
			$iterator = new \DirectoryIterator($dir);
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
