<?php
/**
 *
 * @package LanguagePackValidator
 * @copyright (c) 2014 phpBB Ltd.
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Phpbb\LanguagePackValidator\Output;

class Message
{
	protected $type;
	protected $message;
	protected $file;
	protected $file_details;

	/**
	 * @param int		$type Type message
	 * @param string	$message Message
	 * @param string	$file
	 * @param string	$file_details
	 */
	public function __construct($type, $message, $file = null, $file_details = null)
	{
		$this->type = $type;
		$this->message = $message;
		$this->file = $file;
		$this->file_details = $file_details;
	}

	public function __toString()
	{
		$file = '';

		if ($this->file !== null)
		{
			$file = ' in ' . $this->file;

			if ($this->file_details !== null)
			{
				$file .= ':' . $this->file_details;
			}
		}

		switch ($this->type)
		{
			case Output::NOTICE:
				return "<info>Notice{$file}: $this->message</info>";
			case Output::WARNING:
				return "<comment>Warning{$file}: $this->message</comment>";
			case Output::ERROR:
				return "<question>Error{$file}: $this->message</question>";
			case Output::FATAL:
				return "<error>Fatal{$file}: $this->message</error>";
			default:
				return '';
		}
	}
} 