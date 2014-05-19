<?php
/**
 *
 * @package LanguagePackValidator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\LanguagePackValidator\Tests;

class TestBase extends \PHPUnit_Framework_TestCase
{
	/** @var \Phpbb\LanguagePackValidator\Output\Output */
	protected $output;

	public function setUp()
	{
		parent::setUp();

		$this->output = new \Phpbb\LanguagePackValidator\Tests\Mock\Output();
	}

	public function assertOutputMessages($expected)
	{
		sort($expected);
		$this->assertEquals($expected, $this->output->getMessages());
	}
}
