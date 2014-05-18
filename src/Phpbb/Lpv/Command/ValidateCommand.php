<?php
/**
 *
 * @package LPV
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\Lpv\Command;

use Phpbb\Lpv\Output\Output;
use Phpbb\Lpv\Validator\ValidatorRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('validate')
			->setDescription('Run the validator on your language pack.')
			->addArgument('origin-iso', InputArgument::REQUIRED, 'The ISO of the language to validate')
			->addOption('phpbb-version', null, InputOption::VALUE_OPTIONAL, 'The phpBB Version to validate against (3.0|3.1)', '3.0')
			->addOption('source-iso', null, InputOption::VALUE_OPTIONAL, 'The ISO of the language to validate against', 'en')
			->addOption('package-dir', null, InputOption::VALUE_REQUIRED, 'The path to the directory with the language packages')
			->addOption('debug', null, InputOption::VALUE_NONE, 'Run in debug')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$originIso = $input->getArgument('origin-iso');
		$sourceIso = $input->getOption('source-iso');
		$phpbbVersion = $input->getOption('phpbb-version');
		$packageDir = $input->getOption('package-dir');
		$debug = $input->getOption('debug');

		$output = new Output($output, $debug);

		$output->writeln("Running Language Pack Validator on language <info>$originIso</info>.");
		$runner = new ValidatorRunner($input, $output, $originIso, $sourceIso, $packageDir, $phpbbVersion, $debug);

		$runner->runValidators();
		$output->writeln("<info>Test results for language pack</info>");

		foreach ($output->getMessages() as $msg)
		{
			$output->writeln((string) $msg);
		}

		return ($output->getFatalCount() > 0) ? 1 : 0;
	}
}
