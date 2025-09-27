<?php
/**
 *
 * @package phpBB Translation Validator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\TranslationValidator\Command;

use Phpbb\TranslationValidator\Output\Output;
use Phpbb\TranslationValidator\Output\OutputFormatter;
use Phpbb\TranslationValidator\Validator\ValidatorRunner;
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
			->addOption('phpbb-version', null, InputOption::VALUE_OPTIONAL, 'The phpBB Version to validate against', '4.0')
			->addOption('source-iso', null, InputOption::VALUE_OPTIONAL, 'The ISO of the language to validate against', 'en')
			->addOption('package-dir', null, InputOption::VALUE_OPTIONAL, 'The path to the directory with the language packages', null)
			->addOption('language-dir', null, InputOption::VALUE_OPTIONAL, 'The path to the directory with the language folders', null)
			->addOption('debug', null, InputOption::VALUE_NONE, 'Run in debug')
			->addOption('display-notices', null, InputOption::VALUE_NONE, 'Display notices in report')
			->addOption('safe-mode', 's', InputOption::VALUE_NONE, 'Run in web safe mode to parse files instead of including them')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if (!defined('IN_PHPBB'))
		{
			// Need to set this, otherwise we can not load the language files
			define('IN_PHPBB', true);
		}

		$originIso = $input->getArgument('origin-iso');
		$sourceIso = $input->getOption('source-iso');
		$phpbbVersion = $input->getOption('phpbb-version');
		$packageDir = $input->getOption('package-dir');
		$languageDir = $input->getOption('language-dir');
		$debug = $input->getOption('debug');
		$displayNotices = $input->getOption('display-notices');
		$safeMode = $input->getOption('safe-mode');

		if ($phpbbVersion != '4.0')
		{
			throw new \RuntimeException('Invalid phpbb-version, allowed versions: 4.0');
		}

		$output = new Output($output, $debug);
		$output->setFormatter(new OutputFormatter($output->isDecorated()));

		$output->writeln("<noticebg>Running Language Pack Validator on language $originIso.</noticebg>");

		// If it's safe mode, just put a note so the person running knows it is not as thorough as running it manually
		if ($safeMode)
		{
			$output->writeln('<bg=yellow;options=bold>[Safe Mode]</> Running in web safe mode; it is recommended to still run the script manually for completeness.');
		}

		$output->writeln('');
		$runner = new ValidatorRunner($input, $output);
		$runner->setPhpbbVersion($phpbbVersion)
			->setDebug($debug)
			->setSafeMode($safeMode);

		if ($packageDir !== null)
		{
			$runner->setSource($sourceIso, $packageDir . '/' . $sourceIso, 'language/' . $sourceIso . '/')
				->setOrigin($originIso, $packageDir . '/' . $originIso, 'language/' . $originIso . '/');
		}
		else if ($languageDir !== null)
		{
			$runner->setSource($sourceIso, $languageDir . '/' . $sourceIso, '')
				->setOrigin($originIso, $languageDir . '/' . $originIso, '');
		}
		else
		{
			$runner->setSource($sourceIso, $phpbbVersion . '/' . $sourceIso, 'language/' . $sourceIso . '/')
				->setOrigin($originIso, $phpbbVersion . '/' . $originIso, 'language/' . $originIso . '/');
		}

		$output->writelnIfDebug("Setup ValidatorRunner");

		$runner->runValidators();
		$output->writeln('');
		$output->writeln("Test results for language pack:");
		$output->writeln('');

		$found_msg = '';
		$found_msg .= 'Fatal: ' . $output->getMessageCount(Output::FATAL);
		$found_msg .= ', Error: ' . $output->getMessageCount(Output::ERROR);
		$found_msg .= ', Warning: ' . $output->getMessageCount(Output::WARNING);
		$found_msg .= ', Notice: ' . $output->getMessageCount(Output::NOTICE);

		if ($output->getMessageCount(Output::FATAL))
		{
			$output->writeln('<fatal>' . str_repeat(' ', strlen($found_msg)) . '</fatal>');
			$output->writeln('<fatal>Validation: FAILED' . str_repeat(' ', strlen($found_msg) - 18) . '</fatal>');
			$output->writeln('<fatal>' . $found_msg .  '</fatal>');
			$output->writeln('');
			$output->writeln('');
		}
		else
		{
			$output->writeln('<success>PASSED: ' . $found_msg . '</success>');
		}

		foreach ($output->getMessages() as $msg)
		{
			/** @var \Phpbb\TranslationValidator\Output\Message $msg */
			if ($msg->getType() === Output::NOTICE && !$debug && !$displayNotices)
			{
				continue;
			}

			$output->writeln((string) $msg);
			$output->writeln('');
		}
		$output->writeln('');

		if ($output->getMessageCount(Output::FATAL))
		{
			$output->writeln('<fatal>' . str_repeat(' ', strlen($found_msg)) . '</fatal>');
			$output->writeln('<fatal>Validation: FAILED' . str_repeat(' ', strlen($found_msg) - 18) . '</fatal>');
			$output->writeln('<fatal>' . $found_msg .  '</fatal>');
		}
		else
		{
			$output->writeln('<success>PASSED: ' . $found_msg . '</success>');
		}


		return ($output->getMessageCount(Output::FATAL) > 0) ? 1 : 0;
	}
}
