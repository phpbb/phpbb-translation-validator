<?php
/**
 *
 * @package LPV
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\Lpv;

use Phpbb\Lpv\Command\ValidateCommand;
use Symfony\Component\Console\Application;

class Cli extends Application
{
	protected function getDefaultCommands()
	{
		$commands = parent::getDefaultCommands();
		$commands[] = new ValidateCommand();
		return $commands;
	}
}
