<?php
/**
*
* @package Anavaro.com Zebra Enchance
* @copyright (c) 2013 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace anavaro\eventmedals\controller;

/**
* @ignore
*/

class ajaxify
{
	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param \phpbb\auth		$auth		Auth object
	* @param \phpbb\cache\service	$cache		Cache object
	* @param \phpbb\config	$config		Config object
	* @param \phpbb\db\driver	$db		Database object
	* @param \phpbb\request	$request	Request object
	* @param \phpbb\template	$template	Template object
	* @param \phpbb\user		$user		User object
	* @param \phpbb\content_visibility		$content_visibility	Content visibility object
	* @param \phpbb\controller\helper		$helper				Controller helper object
	* @param string			$root_path	phpBB root path
	* @param string			$php_ext	phpEx
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\cache\service $cache, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, $root_path, $php_ext, $table_prefix)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
	}

	public function base ($action, $userid)
	{
		$this->user->add_lang_ext('anavaro/eventmedals', 'event_medals');
		$confirm = $this->request->variable('confirm', '');
		$sql = 'SELECT username FROM ' . USERS_TABLE . ' WHERE user_id = ' . $userid;
		$result = $this->db->sql_query($sql);
		$username = $this->db->sql_fetchrow($result);
		$username = $username['username'];
		switch ($action)
		{
			case 'add':
			//Before we start we will check if user hase access to the panel
			if ($this->auth->acl_get('u_event_add'))
			{
				if ($confirm)
				{
					$day = $this->request->variable('day', '');
					$month = $this->request->variable('month', '');
					$year = $this->request->variable('year', '');
					$link = $this->request->variable('link', '');
					$image = utf8_normalize_nfc($this->request->variable('image', 'none'));
					$type = $this->request->variable('type', 2);

					$error_array = array();

					if (!is_numeric($day)) { $error_array[] = '{L_ERR_DAY_NOT_NUM}'; }
					if ($day < 1 or $day > 31) { $error_array[] = '{L_ERR_DAY_NOT_IN_RANGE}'; }

					if (!is_numeric($year)) { $error_array[] = '{L_ERR_YEAR_NOT_NUM}'; }

					$months_long = array("1", "3", "5", "7", "8", "10", "12");
					if ((in_array($month, $months_long) and $day <= "31") or (!in_array($month, $months_long) and $month != "2" and $day <= "30") or ($month == "2" and $year % 4 == "0" and $day <= "29") or ($month == "2" and $year % 4 != "0" and $day <= "28")) {

					}
					else { $error_array[] = '{L_ERR_DATE_ERR}'; }
					if ($link and !is_numeric($link)) { $error_array[] = '{L_ERR_TOPIC_ERR}'; }

					if (!$error_array)
					{
							$timestamp = mktime("0", "0", "0", $month, $day, $year);

							$sql_rq = 'SELECT  oid, link, COUNT(*) FROM ' . $this->table_prefix . 'event_medals WHERE oid = '.$this->db->sql_escape($userid).' AND link = '.$this->db->sql_escape($link);
							$result = $this->db->sql_fetchrow($this->db->sql_query($sql_rq));
							//$this->var_display($result['COUNT(*)']);
							if ($result['COUNT(*)'] < 1) {
								$sql = 'INSERT INTO ' . $this->table_prefix . 'event_medals SET oid = '.$this->db->sql_escape($userid).', type = '.$this->db->sql_escape($type).', date = '.$this->db->sql_escape($timestamp);
								if ($link) { $sql .= ', link = '.$this->db->sql_escape($link); }
								if ($image) { $sql .= ', image = \''.$this->db->sql_escape($image).'\''; }

								$this->db->sql_query($sql);
							}
						}
					else
					{
						//$this->var_display($error_array);
						$template->assign_vars(array(
							'S_ERROR'	=>	'1',
						));

						foreach ($error_array as $VAR)
						{
							$template->assign_block_vars('errs', array(
								'MSG'	=>	$VAR,
							));
						}
					}
					$this->template->assign_vars(array(
						'STEP'	=> 'second',
					));
				}
				else
				{
					$this->template->assign_vars(array(
						'STEP'	=> 'first',
					));
				}
				$this->template->assign_vars(array(
					'TYPE'	=> 'add',
				));
			}
			else
			{
				trigger_error($this->user->lang['UCP_PROFILE_CONTROL_ERROR']);
			}
			break;
			case 'edit':
			if ($this->auth->acl_get('u_event_modify'))
			{
				if ($confirm)
				{
					$user_id = $this->request->variable('target_user', '');
					$delete = $this->request->variable('delete', array(''=>''));
					foreach ($delete as $VAR)
					{
						$sql = 'DELETE FROM phpbb_event_medals WHERE oid = '.$db->sql_escape($user_id).' AND link = '.$db->sql_escape($VAR).' LIMIT 1';
						$db->sql_query($sql);
					}
					$eventsrq = $this->request->variable ('events', array('' => array(''=>'',''=>'',''=>'')));
					foreach ($eventsrq as $ID => $VAR)
					{
						$events_new[$ID] = $VAR['select'];
						$events_image_new[$ID] = $VAR['image'];
					}

					$sql = 'SELECT link, type, image FROM phpbb_event_medals WHERE oid = '.$this->db->sql_escape($userid);
					$result = $this->db->sql_query($sql);
					while ($row = $this->db->sql_fetchrow($result))
					{
						$events_old[$row['link']] = $row['type'];
						$events_image_old[$row['link']] = $row['image'];
					}
					$events_diff = array_diff_assoc($events_new, $events_old);
					$events_image_diff = array_diff_assoc($events_image_new, $events_image_old);
					foreach ($delete as $VAR)
					{
						unset($events_diff[$VAR]);
						unset($events_image_diff[$VAR]);
					}
					if ($events_diff)
					{
						foreach ($events_diff as $ID => $VAR)
						{
							$sql = 'UPDATE ' . $this->table_prefix . 'event_medals SET type = '.$this->db->sql_escape($VAR).' WHERE oid = '.$this->db->sql_escape($userid).' AND link = '.$this->db->sql_escape($ID).' LIMIT 1';
							$this->db->sql_query($sql);
						}
					}
					if ($events_image_diff)
					{
						foreach ($events_image_diff as $ID => $VAR)
						{
							$sql = 'UPDATE ' . $this->table_prefix . 'event_medals SET image = \''.$this->db->sql_escape($VAR).'\' WHERE oid = '.$this->db->sql_escape($userid).' AND link = '.$this->db->sql_escape($ID).' LIMIT 1';
							$this->db->sql_query($sql);
						}
					}
				}
				else
				{
					$sql_array = array(
						'SELECT'	=>	'e.type as type, e.link as link, e.image as image, t.topic_title as title',
						'FROM'	=>	array(
							$this->table_prefix . 'event_medals'	=>	'e',
							TOPICS_TABLE	=> 't',
						),
						'WHERE'	=> 'e.link = t.topic_id AND oid = '. $userid
					);
					$sql = $this->db->sql_build_query('SELECT', $sql_array);
					$result = $this->db->sql_query($sql);
					while ($row = $this->db->sql_fetchrow($result))
					{
						$events[$row['link']] = array(
							'type'	=>	$row['type'],
							'title'	=>	$row['title'],
							'image'	=>	$row['image']
						);
					}
					foreach ($events as $ID => $VAR)
					{
						$this->template->assign_block_vars('user_edit', array(
							'EVENT_ID'	=>	$ID,
							'TYPE'	=>	$VAR['type'],
							'TITLE'	=>	$VAR['title'],
							'IMAGE'	=>	$VAR['image']
						));
					}
					$this->template->assign_vars(array(
						'STEP'	=> 'first',
					));
				}
				$this->template->assign_vars(array(
					'TYPE'	=> 'edit',
				));
			}
			break;
		}
		$this->template->assign_vars(array(
					'USERNAME'	=>	$username,
					'S_ACTION'	=>	$this->root_path . $userid
		));
		return $this->helper->render('event_medals.html', $this->user->lang('MEDALS_TITLE'));
	}
}
