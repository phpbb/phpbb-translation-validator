<?php
/**
*
* @package phpBB Translation Validator
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class phpbb_ext_official_translationvalidator_controller_validator
{
	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param phpbb_auth		$auth		Auth object
	* @param phpbb_cache_service	$cache		Cache object
	* @param phpbb_config	$config		Config object
	* @param phpbb_db_driver	$db		Database object
	* @param phpbb_request	$request	Request object
	* @param phpbb_template	$template	Template object
	* @param phpbb_user		$user		User object
	* @param ContainerBuilder	$container	Container object
	* @param phpbb_controller_helper		$helper		Controller helper object
	* @param string			$root_path	phpBB root path
	* @param string			$php_ext	phpEx
	*/
	public function __construct(phpbb_auth $auth, phpbb_cache_service $cache, phpbb_config $config, phpbb_db_driver $db, phpbb_request $request, phpbb_template $template, phpbb_user $user, Symfony\Component\DependencyInjection\ContainerBuilder $container, phpbb_controller_helper $helper, $root_path, $php_ext)
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
			$this->template->assign_block_vars($message[0], array(
				'MESSAGE'		=> $message[1],
			));
		}

		return $this->helper->render('validator_body.html', $this->user->lang('TRANSLATION_VALIDATOR'), 200);
	}
}
