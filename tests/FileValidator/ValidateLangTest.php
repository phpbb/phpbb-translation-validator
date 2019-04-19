<?php
/**
 *
 * @package phpBB Translation Validator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\TranslationValidator\Tests\FileValidator;

use Phpbb\TranslationValidator\Output\Output;

class ValidateLangTest extends TestBase
{
	public function validateLangFileData()
	{
		return array(
			array('language/lang.php', array(
				Output::FATAL . '-Must only contain the lang-array-language/lang.php-',
				Output::FATAL . '-Must contain key: 7_DAYS-language/lang.php-',
				Output::FATAL . '-Must not contain key: 8_DAYS-language/lang.php-',
			)),
			array('language/lang_output.php', array(
				Output::FATAL . '-Must not produces output: ' . "\n\n" . '-language/lang_output.php-',
			)),
			array('language/lang2.php', array(
				Output::FATAL . '-Must only contain the lang-array-language/lang2.php-',
			)),
		);
	}

	/**
	 * @dataProvider validateLangFileData
	 */
	public function testValidateLangFile($file, $expected)
	{
		$this->validator->validateLangFile($file, $file);
		$this->assertOutputMessages($expected);
	}

	/**
	 * Test the reCaptcha checks
	 */
	public function testValidateLangReCaptcha()
	{
		// Failure - as we supply a key that isn't valid
		$reCaptchaLanguage = ['RECAPTCHA_LANG' => 'incorrect'];
		$this->validator->validateReCaptchaValue('', $reCaptchaLanguage);

		$output = $this->output->getMessages();
		$expected = Output::WARNING . '-reCaptcha must match a language/country code on https://developers.google.com/recaptcha/docs/language - if no code exists for your language you can use "en" or leave the string empty--RECAPTCHA_LANG';

		$this->assertEquals($this->output->getMessageCount(Output::WARNING), 1);
		$this->assertEquals($output[0], $expected);

		// Pass - as 'en' is valid
		$reCaptchaLanguage['RECAPTCHA_LANG'] = 'en';
		$this->validator->validateReCaptchaValue('', $reCaptchaLanguage);

		$this->assertEquals($this->output->getMessageCount(Output::WARNING), 1); // Shouldn't change in size as no error added
	}
}
