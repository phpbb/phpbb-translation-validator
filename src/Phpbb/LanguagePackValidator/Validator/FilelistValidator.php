<?php
/**
 *
 * @package LanguagePackValidator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\LanguagePackValidator\Validator;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Phpbb\LanguagePackValidator\Output\Output;
use Phpbb\LanguagePackValidator\Output\OutputInterface;

class FilelistValidator
{
	/** @var string */
	protected $originIso;
	/** @var string */
	protected $originPath;
	/** @var string */
	protected $sourceIso;
	/** @var string */
	protected $sourcePath;
	/** @var string */
	protected $phpbbVersion;

	/** @var bool */
	protected $debug;

	/** @var \Symfony\Component\Console\Input\InputInterface */
	protected $input;
	/** @var \Phpbb\LanguagePackValidator\Output\OutputInterface */
	protected $output;

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	public function __construct(InputInterface $input, OutputInterface $output)
	{
		$this->input = $input;
		$this->output = $output;
	}

	/**
	 * Set phpBB Version
	 *
	 * @param string $originIso		The ISO of the language to validate
	 * @param string $originPath	Path to the origin directory
	 * @return $this
	 */
	public function setOrigin($originIso, $originPath)
	{
		$this->originIso = $originIso;
		$this->originPath = $originPath;
		return $this;
	}

	/**
	 * Set phpBB Version
	 *
	 * @param string $sourceIso		The ISO of the language to validate against
	 * @param string $sourcePath	Path to the source directory
	 * @return $this
	 */
	public function setSource($sourceIso, $sourcePath)
	{
		$this->sourceIso = $sourceIso;
		$this->sourcePath = $sourcePath;
		return $this;
	}

	/**
	 * Set phpBB Version
	 *
	 * @param string $phpbbVersion	The phpBB Version to validate against (3.0|3.1)
	 * @return $this
	 */
	public function setPhpbbVersion($phpbbVersion)
	{
		$this->phpbbVersion = $phpbbVersion;
		return $this;
	}

	/**
	 * Set debug mode
	 *
	 * @param bool $debug Debug mode
	 * @return $this
	 */
	public function setDebug($debug)
	{
		$this->debug = $debug;
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
		$sourceFiles = $this->getFileList($this->sourcePath);
		// License file is required but missing from en/, so we add it here
		$sourceFiles[] = 'language/' . $this->sourceIso . '/LICENSE';
		$sourceFiles = array_unique($sourceFiles);

		$originFiles = $this->getFileList($this->originPath);

		$validFiles = array();
		foreach ($sourceFiles as $sourceFile)
		{
			$testOriginFile = str_replace('/' . $this->sourceIso . '/', '/' . $this->originIso . '/', $sourceFile);
			if (!in_array($testOriginFile, $originFiles))
			{
				$this->output->addMessage(Output::FATAL, 'Missing required file', $testOriginFile);
			}
			else if (substr($sourceFile, -4) != '.gif' && substr($sourceFile, -12) != 'imageset.cfg')
			{
				$validFiles[$sourceFile] = $testOriginFile;
			}
		}

		foreach ($originFiles as $origin_file)
		{
			$testSourceFile = str_replace('/' . $this->originIso . '/', '/' . $this->sourceIso . '/', $origin_file);
			if (!in_array($testSourceFile, $sourceFiles))
			{
				if (in_array($origin_file, array(
						'language/' . $this->originIso . '/AUTHORS',
						'language/' . $this->originIso . '/CHANGELOG',
						'language/' . $this->originIso . '/README',
						'language/' . $this->originIso . '/VERSION',
					)))
				{
					$this->output->addMessage(Output::NOTICE, 'Found additional file', $origin_file);
				}
				else if ($this->phpbbVersion == '3.1' && in_array($origin_file, array(
						'language/' . $this->originIso . '/AUTHORS.md',
						'language/' . $this->originIso . '/CHANGELOG.md',
						'language/' . $this->originIso . '/README.md',
						'language/' . $this->originIso . '/VERSION.md',
					)))
				{
					$this->output->addMessage(Output::NOTICE, 'Found additional file', $origin_file);
				}
				else
				{
					$level = (substr($origin_file, -3) !== '.md') ? Output::FATAL : Output::ERROR;
					$this->output->addMessage($level, 'Found additional file', $origin_file);
				}
			}
		}

		return $validFiles;
	}

	/**
	 * Returns a list of files in the directory
	 *
	 * @param	string	$dir	Directory to go through
	 * @return	array	List of files
	 */
	protected function getFileList($dir)
	{
		$finder = new Finder();
		$iterator = $finder
			->ignoreDotFiles(false)
			->files()
			->sortByName()
			->in($dir)
		;

		$files = array();
		foreach ($iterator as $file)
		{
			/** @var \SplFileInfo $file */
			$files[] = str_replace(DIRECTORY_SEPARATOR, '/', substr($file->getPathname(), strlen($dir) + 1));
		}

		return $files;
	}
}