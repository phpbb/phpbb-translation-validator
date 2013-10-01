<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace official\translationvalidator\controller;
/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class validator
{
	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param \phpbb\auth\auth		$auth		Auth object
	* @param \phpbb\cache\service	$cache		Cache object
	* @param \phpbb\config\config	$config		Config object
	* @param \phpbb\db\driver\driver	$db		Database object
	* @param \phpbb\request\request	$request	Request object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\user		$user		User object
	* @param \Symfony\Component\DependencyInjection\ContainerBuilder	$container	Container object
	* @param \phpbb\controller\helper		$helper		Controller helper object
	* @param string			$root_path	phpBB root path
	* @param string			$php_ext	phpEx
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\cache\service $cache, \phpbb\config\config $config, \phpbb\db\driver\driver $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \Symfony\Component\DependencyInjection\ContainerBuilder $container, \phpbb\controller\helper $helper, $root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->container = $container;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->user->add_lang_ext('official/translationvalidator', 'validator');
	}

	/**
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function validate($lang, $validate)
	{
		$validator = $this->container->get('translation.validator');

		try
		{
			$validation_report = $validator->set_upstream_language($validate)
				->set_origin_language($lang)
				->validate();
		}
		catch (Exception $e)
		{
			trigger_error($e->getMessage());
		}

		$this->template->assign_vars(array(
			'TITLE'		=> $this->user->lang('TRANSLATION_VALIDATE_AGAINST', $lang, $validate),
		));

		foreach ($validation_report->get_messages() as $message)
		{
			$this->template->assign_block_vars($message['type'], array(
				'MESSAGE'		=> $message['message'],
				'SOURCE_LANG'	=> htmlspecialchars($message['source']),
				'ORIGIN_LANG'	=> htmlspecialchars($message['origin']),
			));
		}

		return $this->helper->render('validator_body.html', $this->user->lang('TRANSLATION_VALIDATOR'), 200);
	}
}
