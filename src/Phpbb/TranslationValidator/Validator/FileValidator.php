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
use Phpbb\TranslationValidator\Output\Output;
use Phpbb\TranslationValidator\Output\OutputInterface;

class FileValidator
{
	/** @var string */
	protected $direction;
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

	/** @var LangKeyValidator  */
	protected $langKeyValidator;

	/** @var array List from https://developers.google.com/recaptcha/docs/language */
	private $reCaptchaLanguages = [
		'ar',
		'af',
		'am',
		'hy',
		'az',
		'eu',
		'bn',
		'bg',
		'ca',
		'zh-HK',
		'zh-CN',
		'zh-TW',
		'hr',
		'cs',
		'da',
		'nl',
		'en-GB',
		'en',
		'et',
		'fil',
		'fi',
		'fr',
		'fr-CA',
		'gl',
		'ka',
		'de',
		'de-AT',
		'de-CH',
		'el',
		'gu',
		'iw',
		'hi',
		'hu',
		'is',
		'id',
		'it',
		'ja',
		'kn',
		'ko',
		'lo',
		'lv',
		'lt',
		'ms',
		'ml',
		'mr',
		'mn',
		'no',
		'fa',
		'pl',
		'pt',
		'pt-BR',
		'pt-PT',
		'ro',
		'ru',
		'sr',
		'si',
		'sk',
		'sl',
		'es',
		'es-419',
		'sw',
		'sv',
		'ta',
		'te',
		'th',
		'tr',
		'uk',
		'ur',
		'vi',
		'zu',
		'', // Allow empty strings
	];

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	public function __construct(InputInterface $input, OutputInterface $output)
	{
		$this->input = $input;
		$this->output = $output;
		$this->langKeyValidator = new LangKeyValidator($input, $output);
	}

	/**
	 * Set the language direction
	 * @param $direction
	 * @return $this
	 */
	public function setDirection($direction)
	{
		$this->direction = $direction;
		$this->langKeyValidator->setDirection($direction);
		return $this;
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
		$this->langKeyValidator->setOrigin($originIso, $originPath, $originLanguagePath);
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
		$this->langKeyValidator->setSource($sourceIso, $sourcePath, $sourceLanguagePath);
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
		$this->langKeyValidator->setPhpbbVersion($phpbbVersion);
		return $this;
	}

	/**
	 * Set plural rule
	 *
	 * @param int $pluralRule
	 * @return $this
	 */
	public function setPluralRule($pluralRule)
	{
		$this->pluralRule = $pluralRule;
		$this->langKeyValidator->setPluralRule($pluralRule);
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
		$this->langKeyValidator->setDebug($debug);
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
	 * Decides which validation function to use
	 *
	 * @param	string	$sourceFile		Source file for comparison
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validate($sourceFile, $originFile)
	{
		$this->validateLineEndings($originFile);
		if (substr($originFile, -4) === '.php')
		{
			$this->validateDefinedInPhpbb($originFile);
			$this->validateUtf8withoutbom($originFile);
			$this->validateNoPhpClosingTag($originFile);
		}

		if (strpos($originFile, $this->originLanguagePath . 'email/') === 0 && substr($originFile, -4) === '.txt')
		{
			$this->validateEmail($sourceFile, $originFile);
		}
		else if (strpos($originFile, $this->originLanguagePath . 'help_') === 0 && substr($originFile, -4) === '.php')
		{
			$this->validateHelpFile($sourceFile, $originFile);
		}
		else if (substr($originFile, -4) === '.php')
		{
			$this->validateLangFile($sourceFile, $originFile);
		}
		else if (substr($originFile, -9) === 'index.htm')
		{
			$this->validateIndexFile($originFile);
		}
		else if ($originFile === $this->originLanguagePath . 'LICENSE')
		{
			$this->validateLicenseFile($originFile);
		}
		else if ($originFile === $this->originLanguagePath . 'iso.txt')
		{
			$this->validateIsoFile($originFile);
		}
		else if (substr($originFile, -4) === '.css')
		{
			$this->validateUtf8withoutbom($originFile);
			$this->validateCSSFile($sourceFile, $originFile);
		}
		else
		{
			$this->output->addMessage(Output::NOTICE, 'File is not validated', $originFile);
		}
	}

	/**
	 * Validates a normal language file
	 *
	 * Files should not produce any output.
	 * Files should only define the $lang variable.
	 * Files must have all language keys defined in the source file.
	 * Files should not have additional language keys.
	 *
	 * @param	string	$sourceFile		Source file for comparison
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateLangFile($sourceFile, $originFile)
	{
		$originFilePath = $this->originPath . '/' . $originFile;
		$sourceFilePath = $this->sourcePath . '/' . $sourceFile;

		if (!$this->safeMode)
		{
			ob_start();

			/** @var $lang */
			include($originFilePath);

			$defined_variables = get_defined_vars();
			if (sizeof($defined_variables) != 5 || !isset($defined_variables['lang']) || gettype($defined_variables['lang']) != 'array')
			{
				$this->output->addMessage(Output::FATAL, 'Must only contain the lang-array', $originFile);
				if (!isset($defined_variables['lang']) || gettype($defined_variables['lang']) != 'array')
				{
					return;
				}
			}

			$output = ob_get_contents();
			ob_end_clean();

			if ($output !== '')
			{
				$this->output->addMessage(Output::FATAL, 'Must not produces output: ' . htmlspecialchars($output), $originFile);
			}
		}

		else
		{
			/** @var $lang */
			$lang = ValidatorRunner::langParser($originFilePath);
			$this->output->addMessage(Output::NOTICE, '<bg=yellow;options=bold>[Safe Mode]</> Manually run the translation validator to check for disallowed output.', $originFile);
		}

		$validate = $lang;
		unset($lang);

		if (!$this->safeMode)
		{
			/** @var $lang */
			include($sourceFilePath);
		}

		else
		{
			/** @var $lang */
			$lang = ValidatorRunner::langParser($sourceFilePath);
		}

		$against = $lang;
		unset($lang);

		foreach ($against as $againstLangKey => $againstLanguage)
		{
			if (!isset($validate[$againstLangKey]))
			{
				$this->output->addMessage(Output::FATAL, 'Must contain key: ' . $againstLangKey, $originFile);
				continue;
			}

			$this->langKeyValidator->validate($originFile, $againstLangKey, $againstLanguage, $validate[$againstLangKey]);
		}

		foreach ($validate as $validateLangKey => $validateLanguage)
		{
			if (!isset($against[$validateLangKey]))
			{
				$this->output->addMessage(Output::FATAL, 'Must not contain key: ' . $validateLangKey, $originFile);
			}
		}

		// Check reCaptcha file
		if ($originFile === $this->originLanguagePath . 'captcha_recaptcha.php')
		{
			$this->validateReCaptchaValue($originFile, $validate);
		}
	}

	/**
	 * Check that the reCaptcha key provided is allowed
	 * @param $originFile
	 * @param array $validate
	 */
	public function validateReCaptchaValue($originFile, $validate)
	{
		// The key 'RECAPTCHA_LANG' must match the list provided by Google, or be left empty
		// If any other key is used, we will show an error
		if (array_key_exists('RECAPTCHA_LANG', $validate) && !in_array($validate['RECAPTCHA_LANG'], $this->reCaptchaLanguages))
		{
			// The supplied value doesn't match the allowed values
			$this->output->addMessage(Output::ERROR, 'reCaptcha must match a language/country code on https://developers.google.com/recaptcha/docs/language - if no code exists for your language you can use "en" or leave the string empty', $originFile, 'RECAPTCHA_LANG');
		}
	}

	/**
	 * Validates a email .txt file
	 *
	 * Emails must have a subject when the source file has one, otherwise must not have one.
	 * Emails must have a signature when the source file has one, otherwise must not have one.
	 * Emails should use template vars, used by the source file.
	 * Emails should not use additional template vars.
	 * Emails should not use any HTML.
	 * Emails should contain a newline at their end.
	 *
	 * @param	string	$sourceFile		Source file for comparison
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateEmail($sourceFile, $originFile)
	{
		$sourceContent = (string) file_get_contents($this->sourcePath . '/' . $sourceFile);
		$originContent = (string) file_get_contents($this->originPath . '/' . $originFile);
		$originContent = str_replace("\r\n", "\n", $originContent);
		$originContent = str_replace("\r", "\n", $originContent);

		$sourceContent = explode("\n", $sourceContent);
		$originContent = explode("\n", $originContent);

		// Is the file saved as UTF8 with BOM?
		if (substr($originContent[0], 0, 3) === "\xEF\xBB\xBF")
		{
			$this->output->addMessage(Output::FATAL, 'File must be encoded using UTF8 without BOM', $originFile);
			$originContent[0] = substr($originContent[0], 3);
		}

		// One language contains a subject, the other one does not
		if (strpos($sourceContent[0], 'Subject: ') === 0 && strpos($originContent[0], 'Subject: ') !== 0)
		{
			$this->output->addMessage(Output::FATAL, 'Must have a "Subject: "-line', $originFile);
		}
		else if (strpos($sourceContent[0], 'Subject: ') !== 0 && strpos($originContent[0], 'Subject: ') === 0)
		{
			$this->output->addMessage(Output::FATAL, 'Must not have a "Subject: "-line', $originFile);
		}

		// One language contains the signature, the other one does not
		if ((end($sourceContent) === '{EMAIL_SIG}' || prev($sourceContent) === '{EMAIL_SIG}')
			&& end($originContent) !== '{EMAIL_SIG}' && prev($originContent) !== '{EMAIL_SIG}')
		{
			$this->output->addMessage(Output::FATAL, 'Must have the signature appended', $originFile);
		}
		else if ((end($originContent) === '{EMAIL_SIG}' || prev($originContent) === '{EMAIL_SIG}')
			&& end($sourceContent) !== '{EMAIL_SIG}' && prev($sourceContent) !== '{EMAIL_SIG}')
		{
			$this->output->addMessage(Output::FATAL, 'Must not have the signature appended', $originFile);
		}

		$originTemplateVars = $sourceTemplateVars = array();
		preg_match_all('/{.+?}/', implode("\n", $originContent), $originTemplateVars);
		preg_match_all('/{.+?}/', implode("\n", $sourceContent), $sourceTemplateVars);


		$additionalOrigin = array_diff($sourceTemplateVars[0], $originTemplateVars[0]);
		$additionalSource = array_diff($originTemplateVars[0], array_merge(array(
			'{U_BOARD}',
			'{EMAIL_SIG}',
			'{SITENAME}',
		), $sourceTemplateVars[0]));

		// Check the used template variables
		if (!empty($additionalSource))
		{
			$this->output->addMessage(Output::FATAL, 'Is using additional variables: ' . implode(', ', $additionalSource), $originFile);
		}

		if (!empty($additionalOrigin))
		{
			$this->output->addMessage(Output::ERROR, 'Is not using variables: ' . implode(', ', $additionalOrigin), $originFile);
		}

		$validateHtml = array();
		preg_match_all('/\<.+?\>/', implode("\n", $originContent), $validateHtml);
		if (!empty($validateHtml) && !empty($validateHtml[0]))
		{
			foreach ($validateHtml[0] as $possibleHtml)
			{
				if ((substr($possibleHtml, 0, 5) !== '<!-- ' || substr($possibleHtml, -4) !== ' -->')
					&& (substr($possibleHtml, 0, 2) !== '<{' || substr($possibleHtml, -2) !== '}>')
					)
				{
					$this->output->addMessage(Output::FATAL, 'Using additional HTML: ' . htmlspecialchars($possibleHtml), $originFile);
				}
			}
		}

		// Check for new liens at the end of the file
		if (end($originContent) !== '')
		{
			$this->output->addMessage(Output::FATAL, 'Missing new line at the end of the file', $originFile);
		}
	}

	/**
	 * Validates a help_*.php file
	 *
	 * Files must only contain the variable $help.
	 * This variable must be an array of arrays.
	 * Subarrays must only have the indexes 0 and 1,
	 * with 0 being the headline and 1 being the description.
	 *
	 * Files must contain an entry with 0 and 1 being '--',
	 * causing the column break in the page.
	 *
	 * @todo		Check for template vars and html
	 * @todo		Check for triple --- and other typos of it.
	 *
	 * @param	string	$sourceFile		Source file for comparison
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateHelpFile($sourceFile, $originFile)
	{
		$originFilePath = $this->originPath . '/' . $originFile;
		$sourceFilePath = $this->sourcePath . '/' . $sourceFile;

		if (!$this->safeMode)
		{
			/** @var $help */
			include($originFilePath);

			$defined_variables = get_defined_vars();
			if (sizeof($defined_variables) != 5 || !isset($defined_variables['help']) || gettype($defined_variables['help']) != 'array')
			{
				$this->output->addMessage(Output::FATAL, 'Should only contain the help-array', $originFile);
				return;
			}
		}

		else
		{
			/** @var $help */
			$help = ValidatorRunner::langParser($originFilePath);
			$this->output->addMessage(Output::NOTICE, '<bg=yellow;options=bold>[Safe Mode]</> Manually run the translation validator to check help variables.', $originFile);
		}

		$validate = $help;
		unset($help);

		if (!$this->safeMode)
		{
			/** @var $help */
			include($sourceFilePath);
		}

		else
		{
			/** @var $help */
			$help = ValidatorRunner::langParser($sourceFilePath);
		}

		$against = $help;
		unset($help);

		$column_breaks = 0;
		$entry = 0;
		foreach ($validate as $help)
		{
			if (gettype($help) != 'array' || sizeof($help) != 2 || !isset($help[0]) || !isset($help[1]))
			{
				$this->output->addMessage(Output::FATAL, 'Found invalid entry: ' . serialize($help), $originFile, $entry);
			}
			else if ($help[0] == '--' && $help[1] == '--')
			{
				$column_breaks++;
			}

			if (isset($help[0]))
			{
				$compare = isset($against[$entry][0]) ? $against[$entry][0] : '';
				$this->langKeyValidator->validate($originFile, $entry . '.0', $compare, $help[0]);
			}

			if (isset($help[1]))
			{
				$compare = isset($against[$entry][1]) ? $against[$entry][1] : '';
				$this->langKeyValidator->validate($originFile, $entry . '.1', $compare, $help[1]);
			}
			$entry++;
		}

		if ($column_breaks != 1)
		{
			$this->output->addMessage(Output::FATAL, 'Must have exactly one column break entry', $originFile);
		}
	}

	/**
	 * Validates the LICENSE file
	 *
	 * Only "GNU GENERAL PUBLIC LICENSE Version 2" is allowed
	 *
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateLicenseFile($originFile)
	{
		$fileContents = (string) file_get_contents($this->originPath . '/' . $originFile);

		if (md5($fileContents) != 'e060338598cd2cd6b8503733fdd40a11')
		{
			$this->output->addMessage(Output::FATAL, 'License must be: GNU GENERAL PUBLIC LICENSE Version 2, June 1991', $originFile);
		}
	}

	/**
	 * Validates a index.htm file
	 *
	 * Only empty index.htm or the default htm file are allowed
	 *
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateIndexFile($originFile)
	{
		$fileContents = (string) file_get_contents($this->originPath . '/' . $originFile);

		// Empty index.htm file or one that displayes an empty white page
		if ($fileContents !== '' && md5($fileContents) != '16703867d439efbd7c373dc2269e25a7')
		{
			$this->output->addMessage(Output::FATAL, 'File must be empty', $originFile);
		}
	}

	/**
	 * Validates the iso.txt file
	 *
	 * Should only contain 3 lines:
	 * 1. English name of the language
	 * 2. Native name of the language
	 * 3. Line with information about the author
	 *
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateIsoFile($originFile)
	{
		$fileContents = (string) file_get_contents($this->originPath . '/' . $originFile);
		$isoFile = explode("\n", $fileContents);

		if (sizeof($isoFile) != 3)
		{
			$this->output->addMessage(Output::FATAL, 'Must contain exactly 3 lines: 1. English name, 2. Native name, 3. Author information', $originFile);
		}
	}

	/**
	 * Validates whether a file checks for the IN_PHPBB constant
	 *
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateDefinedInPhpbb($originFile)
	{
		$fileContents = (string) file_get_contents($this->originPath . '/' . $originFile);

		// Regex copied from MPV
		if (!preg_match("#defined([ ]+){0,1}\\(([ ]+){0,1}'IN_PHPBB'#", $fileContents))
		{
			$this->output->addMessage(Output::FATAL, 'Must check whether IN_PHPBB is defined', $originFile);
		}
	}

	/**
	 * Validates whether a file checks for the IN_PHPBB constant
	 *
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateUtf8withoutbom($originFile)
	{
		$fileContents = (string) file_get_contents($this->originPath . '/' . $originFile);
		$fileContents = explode("\n", $fileContents);
		$fileContents = $fileContents[0];

		// Is the file saved as UTF8 with BOM?
		if (substr($fileContents, 0, 3) === "\xEF\xBB\xBF")
		{
			$this->output->addMessage(Output::FATAL, 'File must be encoded using UTF8 without BOM', $originFile);
		}
	}

	/**
	 * Validates whether a file does not contain a closing php tag
	 *
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateNoPhpClosingTag($originFile)
	{
		$fileContents = (string) file_get_contents($this->originPath . '/' . $originFile);
		$fileContents = str_replace("\r\n", "\n", $fileContents);
		$fileContents = str_replace("\r", "\n", $fileContents);

		// Does the file contain anything after the last ");"
		if (substr($fileContents, -3) !== ");\n")
		{
			if (substr($fileContents, -3) !== "];\n")
			{
				$this->output->addMessage(Output::FATAL, 'File must not contain a PHP closing tag, but end with one new line', $originFile);
			}
		}
	}

	/**
	 * Validates whether a file checks whether the file uses Linux line endings
	 *
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateLineEndings($originFile)
	{
		$fileContents = (string) file_get_contents($this->originPath . '/' . $originFile);

		if (strpos($fileContents, "\r") !== false)
		{
			$this->output->addMessage(Output::FATAL, 'Not using Linux line endings (LF)', $originFile);
		}
	}

	/**
	 * Validates whether a file checks whether the file uses Linux line endings
	 *
	 * @param	string	$sourceFile		Source file for comparison
	 * @param	string	$originFile		File to validate
	 * @return	null
	 */
	public function validateCSSFile($sourceFile, $originFile)
	{
		$sourceFileContents = (string) file_get_contents($this->sourcePath . '/' . $sourceFile);
		$originFileContents = (string) file_get_contents($this->originPath . '/' . $originFile);

		$sourceRules = $this->getCSSRules($sourceFile, $sourceFileContents);
		$originRules = $this->getCSSRules($originFile, $originFileContents);

		$missingRules = array_diff(array_keys($sourceRules), array_keys($originRules));
		$additionalRules = array_diff(array_keys($originRules), array_keys($sourceRules));
		if (!empty($missingRules))
		{
			$this->output->addMessage(Output::FATAL, 'Stylesheet file is missing CSS rules: ' . implode(', ', $missingRules), $originFile);
		}
		if (!empty($additionalRules))
		{
			$additionalRulesLevel = ($this->direction == 'rtl') ? Output::WARNING : Output::FATAL; // be more lenient for RTL
			$this->output->addMessage($additionalRulesLevel, 'Stylesheet file has additional CSS rules: ' . implode(', ', $additionalRules), $originFile);
		}
	}

	protected function getCSSRules($fileName, $fileContent)
	{
		// Remove comments
		$fileContent = preg_replace('#/\*(?:.(?!/)|[^\*](?=/)|(?<!\*)/)*\*/#s', '', $fileContent);
		#$fileContent = str_replace(array("\t", "\n"), ' ', $fileContent);
		$fileContent = preg_replace('!\s+!', ' ', $fileContent) . ' ';

		$content = explode('} ', $fileContent);
		if (trim(array_pop($content)) !== '')
		{
			$this->output->addMessage(Output::FATAL, 'Stylesheet file structure is invalid (Output after last rule)', $fileName);
		}

		$cssRules = array();
		foreach ($content as $section)
		{
			if (strpos($section, '{') === false)
			{
				$this->output->addMessage(Output::FATAL, 'Stylesheet file structure is invalid: ' . trim($section), $fileName);
				continue;
			}

			list($rule, $ruleContent) = explode(' {', $section, 2);
			$rule = trim($rule);
			$ruleContent = trim($ruleContent);

			if (strpos($ruleContent, '{') !== false)
			{
				$this->output->addMessage(Output::FATAL, 'CSS rule is invalid: ' . $rule, $fileName);
				continue;
			}

			if (!isset($cssRules[$rule]))
			{
				$cssRules[$rule] = '';
			}

			$cssRules[$rule] .= $ruleContent;
		}

		return $cssRules;
	}
}
