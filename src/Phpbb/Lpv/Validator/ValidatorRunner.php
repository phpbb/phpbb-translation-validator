<?php
/**
 *
 * @package LPV
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\Lpv\Validator;

use Symfony\Component\Console\Input\InputInterface;
use Phpbb\Lpv\Output\OutputInterface;

class ValidatorRunner
{
	/** @var string */
	protected $originIso;
	/** @var string */
	protected $sourceIso;
	/** @var string */
	protected $packageDir;
	/** @var string */
	protected $phpbbVersion;

	/** @var bool */
	protected $debug;

	/** @var \Symfony\Component\Console\Input\InputInterface */
	protected $input;
	/** @var \Phpbb\Lpv\Output\OutputInterface */
	protected $output;

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @param string $originIso		The ISO of the language to validate
	 * @param string $sourceIso		The ISO of the language to validate against
	 * @param string $packageDir	The path to the directory with the language packages
	 * @param string $phpbbVersion	The phpBB Version to validate against (3.0|3.1)
	 * @param bool $debug Debug mode.
	 */
	public function __construct(InputInterface $input, OutputInterface $output, $originIso, $sourceIso, $packageDir, $phpbbVersion, $debug)
	{
		$this->input = $input;
		$this->output = $output;
		$this->originIso = $originIso;
		$this->sourceIso = $sourceIso;
		$this->packageDir = $packageDir;
		$this->phpbbVersion = $phpbbVersion;
		$this->debug = $debug;
	}

	/**
	 * Run the actual test suite.
	 */
	public function runValidators()
	{
		$filelistValidator = new FilelistValidator($this->input, $this->output, $this->originIso, $this->sourceIso, $this->packageDir, $this->phpbbVersion, $this->debug);
		$filelistValidator->validate();
	}
}
