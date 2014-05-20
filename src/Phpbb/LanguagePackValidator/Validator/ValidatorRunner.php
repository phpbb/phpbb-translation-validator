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
use Phpbb\LanguagePackValidator\Output\OutputInterface;

class ValidatorRunner
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
	 * Run the actual test suite.
	 */
	public function runValidators()
	{
		$filelistValidator = new FilelistValidator($this->input, $this->output);

		$this->output->writelnIfDebug("<info>Validating FileList...</info>");

		$validateFiles = $filelistValidator->setSource($this->sourceIso, $this->sourcePath)
			->setOrigin($this->originIso, $this->originPath)
			->setPhpbbVersion($this->phpbbVersion)
			->setDebug($this->debug)
			->validate();

		if (empty($validateFiles))
		{
			$this->output->writelnIfDebug("<info>No files found for validation.</info>");
			return;
		}

		$pluralRule = $this->guessPluralRule();
		$this->output->writelnIfDebug("<info>Using plural rule #$pluralRule for validation.</info>");

		$this->output->writelnIfDebug("<info>Validating Files...</info>");

		$filelistValidator = new FileValidator($this->input, $this->output);
		$filelistValidator->setSource($this->sourceIso, $this->sourcePath)
			->setOrigin($this->originIso, $this->originPath)
			->setPhpbbVersion($this->phpbbVersion)
			->setPluralRule($pluralRule)
			->setDebug($this->debug);

		foreach ($validateFiles as $sourceFile => $originFile)
		{
			$this->output->writelnIfDebug("<info>Validating File: $originFile</info>");
			$filelistValidator->validate($sourceFile, $originFile);
		}
	}

	/**
	 * Try to find the plural rule for the language
	 * @return int
	 */
	protected function guessPluralRule()
	{
		if (file_exists($this->originPath . '/language/' . $this->originIso . '/common.php'))
		{
			include($this->originPath . '/language/' . $this->originIso . '/common.php');

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
}
