<?php
/**
 *
 * @package phpBB Translation Validator
 * @copyright (c) 2021 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Phpbb\TranslationValidator\Command;

use Phpbb\TranslationValidator\Output\Output;
use Phpbb\TranslationValidator\Output\OutputFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCommand extends Command
{
	const GITHUB_API_URL = 'https://api.github.com/repos/%s/git/trees/master?recursive=1';
	const GITHUB_LANGUAGE_EXTRACT = 'language/en/';

	// Supported extensions
	const VIGLINK_EXTENSION = 'phpbb-extensions/viglink';
	const VIGLINK_PATH = 'ext/phpbb/viglink/';

	protected function configure()
	{
		$this
			->setName('download')
			->setDescription('If you are missing important files, this tool can automatically download them for you.')
			->addOption('files', null, InputOption::VALUE_REQUIRED, 'Which files do you want to download?', 'phpbb-extensions/viglink')
			->addOption('phpbb-version', null, InputOption::VALUE_OPTIONAL, 'The phpBB version you use to validate against', '3.3');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$files = $input->getOption('files');
		$phpbbVersion = $input->getOption('phpbb-version');

		if (!in_array($files, [self::VIGLINK_EXTENSION]))
		{
			throw new \RuntimeException($files . ' is not supported for automatic download.');
		}

		$output = new Output($output, false);
		$output->setFormatter(new OutputFormatter($output->isDecorated()));

		$output->writeln('Downloading ' . $files);

		if ($files === self::VIGLINK_EXTENSION)
		{
			// Download Viglink files if they are missing
			$this->downloadViglinkExtensionLanguagesFiles($output, $phpbbVersion);
		}

		$output->writeln('Script complete.');
	}

	/**
	 * Download missing Viglink files and store them in phpbb-translation-validator/3.x/en/ext/phpbb/viglink/language/en
	 * @param $output
	 * @param $phpbbVersion
	 */
	private function downloadViglinkExtensionLanguagesFiles($output, $phpbbVersion)
	{
		$files = $this->readGitHubApiUrl(sprintf(self::GITHUB_API_URL, self::VIGLINK_EXTENSION));

		// Create Viglink folder structure if it doesn't exist
		$directory = __DIR__ . '/../../../../' . $phpbbVersion . '/en/' . self::VIGLINK_PATH;

		if (!file_exists($directory))
		{
			$output->writeln('Viglink directory does not exist, creating now at ' . $directory . self::GITHUB_LANGUAGE_EXTRACT);
			mkdir($directory . self::GITHUB_LANGUAGE_EXTRACT, 0777, true);
		}

		foreach ($files['tree'] as $file)
		{
			if (strpos($file['path'], self::GITHUB_LANGUAGE_EXTRACT) !== false)
			{
				$fileToCreate = $directory . '/' . $file['path'];

				if (!file_exists($fileToCreate))
				{
					// This is a file we want
					$languageFile = $this->readGitHubApiUrl($file['url']);
					$languageFileContents = base64_decode($languageFile['content']);

					// Save the file
					$output->writeln('Creating missing file now at ' . $fileToCreate);
					file_put_contents($fileToCreate, $languageFileContents);
				}
			}
		}
	}

	/**
	 * Return JSON from GitHub API
	 * Must supply a user agent otherwise GitHub will reject the request
	 * @param $file
	 * @return mixed
	 */
	private function readGitHubApiUrl($file)
	{
		$context = stream_context_create([
			'http' => [
				'method' => 'GET',
				'header' => [
					'User-Agent: phpbb-translation-validator'
				]
			]
		]);

		// An unauthenticated request is fine as long is this isn't run over 60 times within an hour
		// More information at: https://docs.github.com/en/rest/guides/getting-started-with-the-rest-api
		$content = json_decode(
			file_get_contents($file, false, $context),
			true
		);

		return $content;
	}
}
