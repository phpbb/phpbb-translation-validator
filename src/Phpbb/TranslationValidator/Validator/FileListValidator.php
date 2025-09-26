<?php
/**
 *
 * @package phpBB Translation Validator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\TranslationValidator\Validator;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Phpbb\TranslationValidator\Command\DownloadCommand;
use Phpbb\TranslationValidator\Output\Output;
use Phpbb\TranslationValidator\Output\OutputInterface;

class FileListValidator
{
	/** @var string */
	protected $direction = 'ltr';
	/** @var string */
	protected $originIso;
	/** @var string */
	protected $originPath;
	/** @var string */
	protected $originLanguagePath;
	/** @var string */
	protected $sourceIso;
	/** @var string */
	protected $sourcePath;
	/** @var string */
	protected $sourceLanguagePath;
	/** @var string */
	protected $phpbbVersion;

	/** @var bool */
	protected $debug;
	/** @var bool */
	protected $safeMode;

	/** @var \Symfony\Component\Console\Input\InputInterface */
	protected $input;
	/** @var \Phpbb\TranslationValidator\Output\OutputInterface */
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
	 * @param string $originLanguagePath	Relative path to the origin language/ directory
	 * @return $this
	 */
	public function setOrigin($originIso, $originPath, $originLanguagePath)
	{
		$this->originIso = $originIso;
		$this->originPath = $originPath;
		$this->originLanguagePath = $originLanguagePath;
		return $this;
	}

	/**
	 * Set phpBB Version
	 *
	 * @param string $sourceIso		The ISO of the language to validate against
	 * @param string $sourcePath	Path to the source directory
	 * @param string $sourceLanguagePath	Relative path to the source language/ directory
	 * @return $this
	 */
	public function setSource($sourceIso, $sourcePath, $sourceLanguagePath)
	{
		$this->sourceIso = $sourceIso;
		$this->sourcePath = $sourcePath;
		$this->sourceLanguagePath = $sourceLanguagePath;
		return $this;
	}

	/**
	 * Set phpBB Version
	 *
	 * @param string $phpbbVersion	The phpBB Version to validate against
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
	 * Set safe mode
	 *
	 * @param $safeMode
	 * @return $this
	 */
	public function setSafeMode($safeMode)
	{
		$this->safeMode = $safeMode;
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
		$sourceFiles[] = $this->sourceLanguagePath . 'LICENSE';
		$sourceFiles = array_unique($sourceFiles);

		// Get extra->direction from composer.json to allow additional rtl-files for rtl-translations
		$filePath = $this->originPath . '/' . $this->originLanguagePath . 'composer.json';

		// Safe mode for safe execution on a server
		if ($this->safeMode)
		{
			$jsonContent = self::langParser($filePath);
		}
		else
		{
			$fileContents = (string) file_get_contents($filePath);
			$jsonContent = json_decode($fileContents, true);
		}

		$this->direction = $jsonContent['extra']['direction'];
		// Throw error, if invalid direction is used
		if (!in_array($this->direction, array('rtl', 'ltr')))
		{
			$this->output->addMessage(Output::FATAL, 'DIRECTION needs to be rtl or ltr');
		}

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
					$this->originLanguagePath . 'AUTHORS.md',
					$this->originLanguagePath . 'CHANGELOG.md',
					$this->originLanguagePath . 'README.md',
					$this->originLanguagePath . 'VERSION.md',
				)))
				{
					$this->output->addMessage(Output::NOTICE, 'Found additional file', $origin_file);
				}
				else
				{
					if (substr($origin_file, -10) === '/index.htm' || $origin_file === 'index.htm')
					{
						$level = Output::NOTICE;
					}
					else if ($this->phpbbVersion === '3.2' && strpos($origin_file, 'styles/subsilver2/') === 0)
					{
						$level = Output::FATAL;
					}
					else if (in_array(substr($origin_file, -4), array('.gif', '.png')) && $this->direction === 'rtl')
					{
						$level = Output::WARNING;
					}
					else if (substr($origin_file, -3) !==	'.md')
					{
						$level = Output::FATAL;
					}
					else
					{
						$level = Output::ERROR;
					}

					// Treat some official extensions differently
					if (strpos($origin_file, DownloadCommand::VIGLINK_PATH) !== false)
					{
						$this->output->addMessage($level, 'No source file for the official extension found - to download, run: php translation.php download --files=phpbb-extensions/viglink', $origin_file);
					}

					else
					{
						$this->output->addMessage($level, 'Found additional file', $origin_file);
					}
				}

				if (substr($origin_file, -14) === '/site_logo.gif')
				{
					$this->output->addMessage(Output::FATAL, 'Found additional file', $origin_file);
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

	/**
	 * Get the language direction - we want to be more lenient for rtl languages
	 * @return string
	 */
	public function getDirection()
	{
		return $this->direction;
	}
}
