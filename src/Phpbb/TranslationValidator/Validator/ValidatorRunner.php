<?php
/**
 *
 * @package phpBB Translation Validator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\TranslationValidator\Validator;

use battye\array_parser\parser;
use Symfony\Component\Console\Input\InputInterface;
use Phpbb\TranslationValidator\Output\Output;
use Phpbb\TranslationValidator\Output\OutputInterface;

class ValidatorRunner
{
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

	/** @var int */
	protected $numFatal = 0;
	/** @var int */
	protected $numError = 0;
	/** @var int */
	protected $numWarning = 0;
	/** @var int */
	protected $numNotice = 0;
	/** @var int */
	protected $progress = 0;
	/** @var int */
	protected $maxProgress = 0;
	/** @var int */
	protected $progressLength = 0;

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
	 * Set safe mode (if true, don't include any PHP files)
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
	 * Run the actual test suite.
	 */
	public function runValidators()
	{
		$filelistValidator = new FileListValidator($this->input, $this->output);

		$validateFiles = $filelistValidator->setSource($this->sourceIso, $this->sourcePath, $this->sourceLanguagePath)
			->setOrigin($this->originIso, $this->originPath, $this->originLanguagePath)
			->setPhpbbVersion($this->phpbbVersion)
			->setDebug($this->debug)
			->setSafeMode($this->safeMode)
			->validate();

		if (empty($validateFiles))
		{
			$this->output->writelnIfDebug('');
			$this->output->writelnIfDebug("<fatal>No files found for validation.</fatal>");
			return;
		}

		$pluralRule = $this->guessPluralRule();
		$this->output->writelnIfDebug("<notice>Using plural rule #$pluralRule for validation.</notice>");
		$this->output->writelnIfDebug('');

		$this->output->writelnIfDebug("Validating file list:");
		$this->printErrorLevel($this->output);

		$this->maxProgress = sizeof($validateFiles) + 1;
		$this->progressLength = 11 + strlen($this->maxProgress) * 2;

		$filelistValidator = new FileValidator($this->input, $this->output);
		$filelistValidator->setSource($this->sourceIso, $this->sourcePath, $this->sourceLanguagePath)
			->setOrigin($this->originIso, $this->originPath, $this->originLanguagePath)
			->setPhpbbVersion($this->phpbbVersion)
			->setPluralRule($pluralRule)
			->setDebug($this->debug)
			->setSafeMode($this->safeMode);

		foreach ($validateFiles as $sourceFile => $originFile)
		{
			$this->output->writelnIfDebug('');
			$this->output->writelnIfDebug("Validating file: $originFile");
			$filelistValidator->validate($sourceFile, $originFile);
			$this->printErrorLevel($this->output);

			usleep(31250);//125000);
		}

		$this->output->writeln('.');
	}

	protected function printErrorLevel(Output $output)
	{
		$fatals = $output->getMessageCount(Output::FATAL);
		$errors = $output->getMessageCount(Output::ERROR);
		$warnings = $output->getMessageCount(Output::WARNING);
		$notices = $output->getMessageCount(Output::NOTICE);
		if ($fatals > $this->numFatal)
		{
			$this->output->write("<fatal>F</fatal>");
		}
		else if ($errors > $this->numError)
		{
			$this->output->write("<error>E</error>");
		}
		else if ($warnings > $this->numWarning)
		{
			$this->output->write("<warning>W</warning>");
		}
		else if ($notices > $this->numNotice)
		{
			$this->output->write("<notice>N</notice>");
		}
		else
		{
			$this->output->write(".");
		}
		$this->progress++;

		if ($this->progress % (79 - $this->progressLength) == 0)
		{
			$this->output->write(' ' . sprintf('%' . strlen($this->maxProgress) . 's', $this->progress));
			$this->output->write(' / ' . $this->maxProgress);
			$this->output->writeln(' (' . sprintf('%3s', floor(100 * ($this->progress / $this->maxProgress))) . '%)');
		}

		$this->numFatal = $fatals;
		$this->numError = $errors;
		$this->numWarning = $warnings;
		$this->numNotice = $notices;
	}

	/**
	 * Try to find the plural rule for the language
	 * @return int
	 */
	protected function guessPluralRule()
	{
		$filePath = $this->originPath . '/' . $this->originLanguagePath . 'common.php';

		if (file_exists($filePath))
		{
			if ($this->safeMode)
			{
				$lang = self::langParser($filePath);
			}

			else
			{
				include($filePath);
			}

			if (!isset($lang['PLURAL_RULE']))
			{
				$this->output->writelnIfDebug("<info>No plural rule set, falling back to plural rule #1</info>");
			}
		}
		else
		{
			$this->output->writelnIfDebug("<info>Could not find common.php, falling back to plural rule #1</info>");
		}

		return isset($lang['PLURAL_RULE']) ? $lang['PLURAL_RULE'] : 1;
	}

	/**
	 * Parse language files for lang arrays
	 * @param $file
	 * @return array|null
	 */
	public static function arrayParser($file)
	{
		// Parse language files that use new or old array formats
		$regex = '/\$lang\s*=\s*array_merge\(\$lang,\s*(?|array\((.*?)\)|\[(.*?)\])\);/s';
		return parser::parse_regex($regex, $file);
	}

	/**
	 * Merge parsed language entries into a single array
	 * @param $filePath
	 * @param string $relativePath
	 * @return array
	 */
	public static function langParser($filePath, $relativePath = './')
	{
		$lang = [];
		$parsed = self::arrayParser($relativePath . $filePath);

		foreach ($parsed as $parse)
		{
			$lang = array_merge($lang, $parse);
		}

		return $lang;
	}
}
