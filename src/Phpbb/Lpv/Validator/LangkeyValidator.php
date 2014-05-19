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
use Symfony\Component\Finder\Finder;
use Phpbb\Lpv\Output\Output;
use Phpbb\Lpv\Output\OutputInterface;

class LangkeyValidator
{
	/** @var string */
	protected $originIso;
	/** @var string */
	protected $sourceIso;
	/** @var string */
	protected $packageDir;
	/** @var string */
	protected $phpbbVersion;

	/** @var int */
	protected $pluralRule;

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
	 * @param int $pluralRule		The plural rule we want to use
	 * @param bool $debug Debug mode.
	 */
	public function __construct(InputInterface $input, OutputInterface $output, $originIso, $sourceIso, $packageDir, $phpbbVersion, $pluralRule, $debug)
	{
		$this->input = $input;
		$this->output = $output;
		$this->originIso = $originIso;
		$this->sourceIso = $sourceIso;
		$this->packageDir = $packageDir;
		$this->phpbbVersion = $phpbbVersion;
		$this->pluralRule = $pluralRule;
		$this->debug = $debug;
	}

	/**
	* Validates type of the language and decides on further validation
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	mixed	$against_language		Original language
	* @param	mixed	$validate_language		Translated language
	* @return	null
	*/
	public function validate($file, $key, $against_language, $validate_language)
	{
		if (gettype($against_language) !== gettype($validate_language))
		{
			$this->messages->push('fail', $this->user->lang('INVALID_TYPE', $file, $key, gettype($against_language), gettype($validate_language)));
			return;
		}

		if ($this->phpbbVersion !== '3.0' && $key === 'PLURAL_RULE')
		{
			if ($validate_language < 0 || $validate_language > 15)
			{
				$this->messages->push('fail', $this->user->lang('INVALID_PLURAL_RULE', $file, $validate_language));
				return;
			}
		}
		else if ($key === 'DIRECTION')
		{
			if (!in_array($validate_language, array('ltr', 'rtl')))
			{
				$this->messages->push('fail', $this->user->lang('INVALID_DIRECTION', $file, $validate_language));
				return;
			}
		}
		else if ($key === 'USER_LANG')
		{
			if (str_replace('_', '-', $this->originIso) !== $validate_language && strpos($validate_language, $this->originIso . '-') !== 0)
			{
				$this->messages->push('fail', $this->user->lang('INVALID_USER_LANG', $file, $validate_language));
				return;
			}
		}
		else if (gettype($against_language) === 'string')
		{
			$this->validate_string($file, $key, $against_language, $validate_language);
		}
		else
		{
			$this->validate_array($file, $key, $against_language, $validate_language);
		}
	}

	/**
	* Decides which array validation function should be used, based on the key
	*
	* Supports:
	*	- Dateformats
	*	- Datetime
	*	- Timezones
	*	- BBCode Tokens
	*	- Report Reasons
	*	- Plurals
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	array	$against_language		Original language
	* @param	array	$validate_language		Translated language
	* @return	null
	*/
	public function validate_array($file, $key, $against_language, $validate_language)
	{
		//var_dump($key, $against_language); echo '<br /><br />';
		if ($key === 'dateformats')
		{
			$this->validate_dateformats($file, $key, $against_language, $validate_language);
		}
		else if (
			$key === 'datetime' ||
			$key === 'timezones' ||
			$key === 'tokens' ||
			$key === 'report_reasons' ||
			$key === 'PM_ACTION' ||
			$key === 'PM_CHECK' ||
			$key === 'PM_RULE'
		)
		{
			$this->validate_array_key($file, $key, $against_language, $validate_language);
		}
		// ACL array in 3.0, removed in 3.1
		else if ($this->phpbbVersion === '3.0' && strpos($key, 'acl_') === 0)
		{
			$this->validate_acl($file, $key, $against_language, $validate_language);
		}
		// Some special arrays in 3.0, removed in 3.1
		else if ($this->phpbbVersion === '3.0' && (
			$key === 'permission_cat' ||
			$key === 'permission_type' ||
			$key === 'tz' ||
			$key === 'tz_zones'))
		{
			$this->validate_array_key($file, $key, $against_language, $validate_language);
		}
		// Some special plurals in 3.0
		else if ($this->phpbbVersion === '3.0' && ($key === 'datetime.AGO' || $key === 'NUM_POSTS_IN_QUEUE' || $key === 'USER_LAST_REMINDED'))
		{
			$this->validate_array_key($file, $key, $against_language, $validate_language);
		}
		else
		{
			$against_keys = array_keys($against_language);
			$key_types = array();
			foreach ($against_keys as $against_key)
			{
				$type = gettype($against_key);
				if (!isset($key_types[$type]))
				{
					$key_types[$type] = 0;
				}
				$key_types[$type]++;
			}

			if (sizeof($key_types) == 1)
			{
				if (isset($key_types['string']))
				{
					$this->validate_array_key($file, $key, $against_language, $validate_language);
				}
				else if ($this->phpbbVersion !== '3.0' && isset($key_types['integer']))
				{
					$this->validate_plural_keys($file, $key, $against_language, $validate_language);
				}
				else if ($this->phpbbVersion === '3.0' && isset($key_types['integer']))
				{
					// For 3.0 this should not happen
					$this->messages->push('debug', $this->user->lang('LANG_ARRAY_UNSUPPORTED', $file, $key));
				}
				else
				{
					$this->messages->push('debug', $this->user->lang('LANG_ARRAY_MIXED', $file, $key, implode(', ', array_keys($key_types))));
				}
			}
			else
			{
				$this->messages->push('debug', $this->user->lang('LANG_ARRAY_MIXED', $file, $key, implode(', ', array_keys($key_types))));
			}
		}
	}

	/**
	* Validates the plural keys
	*
	* The set of plural cases should not be empty
	* There might be an additional case for 0 items
	* There must not be an additional case
	* There might be less cases then possible
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	array	$validate_language		Translated language
	* @return	null
	*/
	public function validate_plural_keys($file, $key, $against_language, $validate_language)
	{
		$origin_cases = array_keys($validate_language);

		if (empty($origin_cases))
		{
			$this->messages->push('fail', $this->user->lang('LANG_PLURAL_EMPTY', $file, $key));
			return;
		}

		$valid_cases = $this->getPluralKeys($this->pluralRule);

		$intersect_cases = array_intersect($origin_cases, $valid_cases);
		$missing_cases = array_diff($valid_cases, $origin_cases);
		$additional_cases = array_diff($origin_cases, $valid_cases, array(0));

		if (!empty($additional_cases))
		{
			$this->messages->push('fail', $this->user->lang('LANG_PLURAL_ADDITIONAL', $file, $key, implode(', ', $additional_cases)));
		}

		if (empty($intersect_cases))
		{
			// No intersection means there are no entries apart from the 0
			$this->messages->push('fail', $this->user->lang('LANG_PLURAL_EMPTY', $file, $key));
			return;
		}

		if (!empty($missing_cases))
		{
			// Do we want to allow this? Lazy translators...
			$this->messages->push('debug', $this->user->lang('LANG_PLURAL_MISSING', $file, $key, implode(', ', $missing_cases)));
		}

		if (!empty($intersect_cases))
		{
			$compare_against = '';
			if ($against_language)
			{
				$compare_against = end($against_language);
			}

			foreach ($intersect_cases as $case)
			{
				$this->validate_string($file, $key . '.' . $case, $compare_against, $validate_language[$case], true);
			}
		}
	}

	/**
	* Returns an array with the valid cases for the given plural rule
	*
	* @param	int	$pluralRule
	* @return	array
	*/
	protected function getPluralKeys($pluralRule)
	{
		switch ($pluralRule)
		{
			case 0:
				return array(1);
			case 1:
			case 2:
			case 15:
				return array(1, 2);
			case 3:
			case 5:
			case 6:
			case 7:
			case 8:
			case 9:
			case 14:
				return array(1, 2, 3);
			case 4:
			case 10:
			case 13:
				return array(1, 2, 3, 4);
			case 11:
				return array(1, 2, 3, 4, 5);
			case 12:
				return array(1, 2, 3, 4, 5, 6);
		}

		throw new \Exception('Unsupported plural rule');
	}

	/**
	* Validates the dateformats
	*
	* Should not be empty
	* Keys and Descriptions should not contain HTML
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	array	$against_language		Original language
	* @param	array	$validate_language		Translated language
	* @return	null
	*/
	public function validate_dateformats($file, $key, $against_language, $validate_language)
	{
		if (empty($validate_language))
		{
			$this->output->addMessage(Output::FATAL, 'Array must not be empty', $file, $key);
			return;
		}

		foreach ($validate_language as $dateformat => $example_time)
		{
			$this->validate_string($file, $key . '.' . $dateformat, '', $dateformat);
			$this->validate_string($file, $key . '.' . $dateformat, '', $example_time);
		}
	}

	/**
	* Validates a permission entry
	*
	* Should have a cat and lang key
	* cat should be the same as in origin language
	* lang should compare like a string to origin lang
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	array	$against_language		Original language
	* @param	array	$validate_language		Translated language
	* @return	null
	*/
	public function validate_acl($file, $key, $against_language, $validate_language)
	{
		if (!isset($validate_language['cat']))
		{
			$this->messages->push('fail', $this->user->lang('ACL_MISSING_CAT', $file, $key));
		}
		else if ($validate_language['cat'] !== $against_language['cat'])
		{
			$this->messages->push('fail', $this->user->lang('ACL_INVALID_CAT', $file, $key, $against_language['cat'], $validate_language['cat']));
		}

		if (!isset($validate_language['lang']))
		{
			$this->messages->push('fail', $this->user->lang('ACL_MISSING_LANG', $file, $key));
		}
		else
		{
			$this->validate_string($file, $key, $against_language['lang'], $validate_language['lang']);
		}
	}

	/**
	* Validates an array entry
	*
	* Arrays that have strings as key, must have the same keys in the foreign language
	* Arrays that have integers as keys, might have different ones (plurals)
	* Additional keys can not be further validated
	*
	* Function works recursive
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	array	$against_language		Original language
	* @param	array	$validate_language		Translated language
	* @return	null
	*/
	public function validate_array_key($file, $key, $against_language, $validate_language)
	{
		if (gettype($against_language) !== gettype($validate_language))
		{
			$this->messages->push('fail', $this->user->lang('INVALID_TYPE', $file, $key, gettype($against_language), gettype($validate_language)));
			return;
		}

		$cat_validate_keys = array_keys($validate_language);
		$cat_against_keys = array_keys($against_language);
		$missing_keys = array_diff($cat_against_keys, $cat_validate_keys);
		$invalid_keys = array_diff($cat_validate_keys, $cat_against_keys);

		foreach ($against_language as $array_key => $lang)
		{
			// Only error for string keys, plurals might force different keys
			if (!isset($validate_language[$array_key]))
			{
				if (gettype($array_key) == 'string')
				{
					$this->messages->push('fail', $this->user->lang('LANG_ARRAY_MISSING', $file, $key, $array_key));
				}
				continue;
			}

			if (is_string($lang))
			{
				$this->validate_string($file, $key . '.' . $array_key, $lang, $validate_language[$array_key]);
			}
			else
			{
				$this->validate_array($file, $key . '.' . $array_key, $lang, $validate_language[$array_key]);
			}
		}

		if (!empty($invalid_keys))
		{
			foreach ($invalid_keys as $array_key)
			{
				if (gettype($array_key) == 'string')
				{
					$this->messages->push('fail', $this->user->lang('LANG_ARRAY_INVALID', $file, $key, $array_key));
				}
				else
				{
					// Strangly used plural?
					$this->messages->push('warning', $this->user->lang('LANG_ARRAY_INVALID', $file, $key, $array_key));
				}
				$this->messages->push('warning', $this->user->lang('KEY_NOT_VALIDATED', $file, $key . '.' . $array_key));
			}
		}
	}

	/**
	* Validates a string
	*
	* Checks whether replacements %d and %s are used correctly
	* Checks for HTML
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	string	$against_language		Original language string
	* @param	string	$validate_language		Translated language string
	* @param	string	$is_plural	String is part of a plural (we don't complain the, if the first integer is missing)
	* @return	null
	*/
	public function validate_string($file, $key, $against_language, $validate_language, $is_plural = false)
	{
		if (gettype($against_language) !== gettype($validate_language))
		{
			$this->messages->push('fail', $this->user->lang('INVALID_TYPE', $file, $key, gettype($against_language), gettype($validate_language)));
			return;
		}

		$against_strings = $against_strings_nonumber = substr_count($against_language, '%s');
		$against_integers = $against_integers_nonumber = substr_count($against_language, '%d');
		$validate_strings = $validate_strings_nonumber = substr_count($validate_language, '%s');
		$validate_integers = $validate_integers_nonumber = substr_count($validate_language, '%d');

		$against_integers_ary = $validate_integers_ary = array();
		for ($i = 1; $i < 10; $i++)
		{
			if ($looping_count = substr_count($against_language, '%' . $i . '$s'))
			{
				$against_strings++;
			}
			if ($looping_count = substr_count($against_language, '%' . $i . '$d'))
			{
				$against_integers++;
				$against_integers_ary[] = $i;
			}
			if ($looping_count = substr_count($validate_language, '%' . $i . '$s'))
			{
				$validate_strings++;
			}
			if ($looping_count = substr_count($validate_language, '%' . $i . '$d'))
			{
				$validate_integers++;
				$validate_integers_ary[] = $i;
			}
		}

		if ($against_strings - $validate_strings !== 0)
		{
			$this->messages->push('fail', $this->user->lang('INVALID_NUM_ARGUMENTS', $file, $key, 'string', $against_strings, $validate_strings), $against_language, $validate_language);
		}

		if ($against_integers - $validate_integers !== 0)
		{
			if (!$is_plural || ($is_plural && $against_integers - $validate_integers !== 1 && $against_integers - $validate_integers !== -1))
			{
				$this->messages->push('fail', $this->user->lang('INVALID_NUM_ARGUMENTS', $file, $key, 'integer', $against_integers, $validate_integers), $against_language, $validate_language);
			}
			else if ($is_plural)
			{
				// If there are more then 1 integer parameters and they have no numbers
				// the number of integers must match!
				if ($against_integers_nonumber > 1)
				{
					$this->messages->push('fail', $this->user->lang('INVALID_NUM_ARGUMENTS', $file, $key, 'integer', $against_integers, $validate_integers), $against_language, $validate_language);
				}
				else if (!$against_integers_nonumber)
				{
					if (sizeof($against_integers_ary) > sizeof($validate_integers_ary))
					{
						// If the integers have numbers, only the first integer is allowed to be missing.
						array_pop($against_integers_ary);
						$diff = array_diff($against_integers_ary, $validate_integers_ary);
						if (!empty($diff))
						{
							$this->messages->push('fail', $this->user->lang('INVALID_NUM_ARGUMENTS', $file, $key, 'integer', $against_integers, $validate_integers), $against_language, $validate_language);
						}
					}
					else
					{
						// But this could also happen to the against language. So when the first integer
						// of the validate language is prior to against, that is okay aswell.
						array_pop($validate_integers_ary);
						$diff = array_diff($validate_integers_ary, $against_integers_ary);
						if (empty($diff))
						{
							$this->messages->push('fail', $this->user->lang('INVALID_NUM_ARGUMENTS', $file, $key, 'integer', $against_integers, $validate_integers), $against_language, $validate_language);
						}
					}
				}

			}
		}

		$this->validate_html($file, $key, $against_language, $validate_language);
	}

	/**
	* List of additional html we found
	*
	* This will allow to not display the same error multiple times for the same string
	* Structure: file -> key -> html-tag
	*
	* @var array
	*/
	protected $additional_html_found = array();

	/**
	* Validates the html usage in a string
	*
	* Checks whether the used HTML tags are also used in the original language.
	* Omitting tags is okay, as long as both (start and end) are omitted.
	*
	* @param	string	$file		File to validate
	* @param	string	$key		Key to validate
	* @param	string	$against_language		Original language string
	* @param	string	$validate_language		Translated language string
	* @return	null
	*/
	public function validateHtml($file, $key, $against_language, $validate_language)
	{
		if (substr($file, -12) == '/install.php' && in_array($key, array(
			'INSTALL_CONGRATS_EXPLAIN',
			'INSTALL_INTRO_BODY',
			'SUPPORT_BODY',
			'UPDATE_INSTALLATION_EXPLAIN',
			'OVERVIEW_BODY',
		)) || substr($file, -8) == '/ucp.php' && in_array($key, array(
			'TERMS_OF_USE_CONTENT',
			'PRIVACY_POLICY',
		)))
		{
			$against_language = '<p>' . $against_language . '</p>';
			$validate_language = '<p>' . $validate_language . '</p>';
		}

		$against_html = $validate_html = $open_tags = array();
		preg_match_all('/\<.+?\>/', $against_language, $against_html);
		preg_match_all('/\<.+?\>/', $validate_language, $validate_html);

		if (!empty($validate_html) && !empty($validate_html[0]))
		{
			$failed_unclosed = false;
			foreach ($validate_html[0] as $possible_html)
			{
				$opening_tag = $possible_html[1] !== '/';
				$ignore_additional = false;

				// The closing tag contains a space
				if (!$opening_tag && strpos($possible_html, ' ') !== false)
				{
					$this->output->addMessage(Output::FATAL, 'String is using invalid html: ' . htmlspecialchars($possible_html), $file, $key);
					$ignore_additional = true;
				}

				$tag = (strpos($possible_html, ' ') !== false) ? substr($possible_html, 1, strpos($possible_html, ' ') - 1) : substr($possible_html, 1, strpos($possible_html, '>') - 1);
				$tag = ($opening_tag) ? $tag : substr($tag, 1);

				if ($opening_tag)
				{
					if (in_array($tag, $open_tags))
					{
						if (!$failed_unclosed)
						{
							$this->output->addMessage(Output::FATAL, 'String is missing closing tag for html: ' . $tag, $file, $key);
						}
						$failed_unclosed = true;
					}
					else if (substr($possible_html, -3) !== ' />')
					{
						$open_tags[] = $tag;
					}
				}
				else if (empty($open_tags))
				{
					if (!$failed_unclosed)
					{
						$this->output->addMessage(Output::FATAL, sprintf('String is closing tag for html “%s” which was not opened before', $tag), $file, $key);
					}
					$failed_unclosed = true;
				}
				else if (end($open_tags) != $tag)
				{
					if (!$failed_unclosed)
					{
						$this->output->addMessage(Output::FATAL, 'String is missing closing tag for html: ' . end($open_tags), $file, $key);
					}
					$failed_unclosed = true;
				}
				else
				{
					array_pop($open_tags);
				}

				$possible_html_specialchars = htmlspecialchars($possible_html);
				// HTML tag is not used in original language
				if (!$ignore_additional && !in_array($possible_html, $against_html[0]) && !isset($this->additional_html_found[$file][$key][$possible_html_specialchars]))
				{
					$this->additional_html_found[$file][$key][$possible_html_specialchars] = true;
					$level = (in_array($possible_html_specialchars, array(
						'&lt;i&gt;',
						'&lt;/i&gt;',
						'&lt;b&gt;',
						'&lt;/b&gt;',
					))) ? Output::ERROR : ((in_array($possible_html_specialchars, array(
						'&lt;em&gt;',
						'&lt;/em&gt;',
						'&lt;strong&gt;',
						'&lt;/strong&gt;',
						'&lt;u&gt;',
						'&lt;/u&gt;',
						'&lt;/a&gt;',
						'&lt;br /&gt;',
					))) ? Output::NOTICE : Output::FATAL);
					$this->output->addMessage($level, 'String is using additional html: ' . $possible_html_specialchars, $file, $key);
				}

			}

			if (!empty($open_tags))
			{
				if (!$failed_unclosed)
				{
					$this->output->addMessage(Output::FATAL, 'String is missing closing tag for html: ' . $open_tags[0], $file, $key);
				}
			}
		}
	}
}
