<?php
/**
 *
 * @package LPV
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\Lpv\Tests\FileValidator;

class TestBase extends \Phpbb\Lpv\Tests\TestBase
{
	/** @var \Phpbb\Lpv\Validator\FileValidator */
	protected $validator;

	public function setUp()
	{
		parent::setUp();

		$this->validator = new \Phpbb\Lpv\Validator\FileValidator($this->getMock('Symfony\Component\Console\Input\InputInterface'), $this->output, 'origin', 'source', dirname(__FILE__) . '/fixtures/', '3.0', false);
	}
}
