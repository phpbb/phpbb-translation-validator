<?php
/**
 *
 * @package LanguagePackValidator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\LanguagePackValidator\Tests\Mock;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Output extends \Phpbb\LanguagePackValidator\Output\Output
{
	/** @var bool */
	protected $debug;

	public function __construct()
	{
	}

	/**
	 * Add a new message to the output of the validator.
	 *
	 * @param int		$type Type message
	 * @param string	$message Message
	 * @param string	$file
	 * @param string	$file_details
	 */
	public function addMessage($type, $message, $file = null, $file_details = null)
	{
		switch ($type)
		{
			case Output::FATAL:
				$this->fatal++;
				break;
			case Output::ERROR:
				$this->error++;
				break;
			case Output::WARNING:
				$this->warning++;
				break;
			case Output::NOTICE:
				$this->notice++;
				break;
			default:
				// TODO: Decide on this?
		}
		$this->messages[] = $type . '-' . $message . '-'. $file . '-'. $file_details;
	}

	/**
	 * Get all messages saved into the message queue.
	 * @return array Array with messages
	 */
	public function getMessages()
	{
		sort($this->messages);
		return $this->messages;
	}

	/**
	 * Get the amount of messages that were fatal.
	 * @return int
	 */
	public function getFatalCount()
	{
		return $this->fatal;
	}
}