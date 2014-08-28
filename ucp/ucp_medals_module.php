<?php

/**
*
* @package Anavaro.com Event Medals
* @copyright (c) 2013 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
namespace anavaro\eventmedals\ucp;

class ucp_medals_module
{
	var $u_action;
	function var_display($i)
	{
		echo "<pre>";
		print_r($i);
		echo "</pre>";
	}
	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $request;
		global $config, $SID, $phpbb_root_path, $phpbb_admin_path, $phpEx, $k_config, $table_prefix;
		//$this->var_display($action);

		//$this->var_display($tid);
		//Lets get some groups!
		switch ($mode)
		{
			case 'control':
				$user->add_lang_ext('anavaro/eventmedals', 'event_medals');
				$this->tpl_name		= 'ucp_event_medals_control';

				$stage = $request->variable('stage', 'first');
				//$this->var_display($stage);
				switch ($stage) {
					case 'update':
						$allowLevel = $request->variable('ucp_profile_view', 0);
						if ($allowLevel > 4)
						{
							$allowLevel = 0;
						}
						$sql = 'UPDATE '. $table_prefix .'users_custom SET profile_event_show = ' . $db->sql_escape($allowLevel) . ' WHERE user_id = '.$user->data['user_id'];
						$db->sql_query($sql);

					case 'first':
						//Let's get initial values
						$sql = 'SELECT profile_event_show FROM '. $table_prefix .'users_custom WHERE user_id = '.$user->data['user_id'];
						$result = $db->sql_query($sql);
						$allowLevel = $db->sql_fetchrow($result);

						if (isset($config['zebra_enhance_version']))
						{
							$template->assign_vars(array(
								'S_EVENT_ZE' => '1',
							));
						}
						$template->assign_vars(array(
							'S_EVENT_PROFILE_SELECTION' => $allowLevel['profile_event_show'],
							'S_UCP_ACTION'	=>	append_sid("ucp.php?i=".$id."&mode=".$mode."&stage=update")
						));
					break;
				}
			break;
		}
	}
	function edit($id, $mode)
	{
		$this->var_display($_POST);
	}
}
