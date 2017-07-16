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
		$sql = 'SELECT username FROM ' . USERS_TABLE . ' WHERE user_id = ' . (int) $userid;
		$result = $this->db->sql_query($sql);
		$username = $this->db->sql_fetchrow($result);
		$username = $username['username'];
		if (!$username) { trigger_error($this->user->lang('ERR_NO_USER'), E_USER_WARNING); }
		switch ($action)
		{
			case 'add':
			//Before we start we will check if user hase access to the panel
			if ($this->auth->acl_get('u_event_add'))
			{
				if ($confirm)
				{
					$day = $this->request->variable('day', (int) '');
					$month = $this->request->variable('month', (int) '');
					$year = $this->request->variable('year', (int) '');
					$link = $this->request->variable('link', '');
					$image = utf8_normalize_nfc($this->request->variable('image', 'none'));
					$type = $this->request->variable('type', 2);

					$error_array = array();

					$image = ($image ? $image : 'none');

					if (!checkdate($month, $day, $year) or $year < 1971)
					{
						trigger_error($this->user->lang('ERR_DATE_ERR'), E_USER_WARNING);
					}
					if ($type > 4) { $type = 4; }
					$sql = 'SELECT COUNT(*) as count FROM ' . TOPICS_TABLE . ' WHERE topic_id = ' . $this->db->sql_escape((int) $link);
					$result = $this->db->sql_query($sql);
					$tmp = $this->db->sql_fetchrow($result);
					$exists = $tmp['count'] > 0 ? 1 : 0;
					$this->db->sql_freeresult($result);
					if ($link and (!is_numeric($link) or $exists < 1)) { trigger_error($this->user->lang('ERR_TOPIC_ERR'), E_USER_WARNING); }
					//if we are here then no errors were called out
					$timestamp = mktime("0", "0", "0", $month, $day, $year);

					$sql_rq = 'SELECT COUNT(*) as count FROM ' . $this->table_prefix . 'event_medals WHERE owner_id = '.$this->db->sql_escape((int) $userid).' AND link = '.$this->db->sql_escape($link);
					$result = $this->db->sql_fetchrow($this->db->sql_query($sql_rq));
					//$this->var_display($result['COUNT(*)']);
					if ($result['count'] < 1) {
						$sql_ary = array(
							'owner_id'	=> (int) $userid,
							'type'	=> (int) $type,
							'date'	=> (int) $timestamp,
							'link'	=> (int) $link,
							'image'	=> $image,
						);
						$sql = 'INSERT INTO ' . $this->table_prefix  .  'event_medals' . $this->db->sql_build_array('INSERT', $sql_ary);
						//var_dump($sql);
						$this->db->sql_query($sql);
					}
					else
					{
						trigger_error($this->user->lang('ERR_DUPLICATE_MEDAL'), E_USER_WARNING);
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
					$eventsrq = $this->request->variable ('events', array('' => array(''=> (int) '',''=> (int) '')));
					foreach ($eventsrq as $ID => $VAR)
					{
						if (isset($VAR['delete']))
						{
							if($VAR['delete'] == 1)
							{
								$sql = 'DELETE FROM ' . $this->table_prefix . 'event_medals WHERE owner_id = '.$this->db->sql_escape((int) $userid).' AND link = '.$this->db->sql_escape($ID);
								$this->db->sql_query($sql);
							}
						}
						else
						{
							$events_new[$ID] = $VAR['select'];
						}
					}
					//var_dump($eventsrq);
					$sql = 'SELECT link, type, image FROM phpbb_event_medals WHERE owner_id = '.$this->db->sql_escape((int) $userid);
					$result = $this->db->sql_query($sql);
					while ($row = $this->db->sql_fetchrow($result))
					{
						$events_old[$row['link']] = $row['type'];
					}
					if (!empty($events_old))
					{
						$events_diff = array_diff_assoc($events_new, $events_old);
						if ($events_diff)
						{
							foreach ($events_diff as $ID => $VAR)
							{
								$sql = 'UPDATE ' . $this->table_prefix . 'event_medals SET type = '.$this->db->sql_escape($VAR).' WHERE owner_id = '.$this->db->sql_escape((int) $userid).' AND link = '.$this->db->sql_escape($ID).' LIMIT 1';
								$this->db->sql_query($sql);
							}
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
						'WHERE'	=> 'e.link = t.topic_id AND e.owner_id = '. (int) $userid
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
					if (!empty($events))
					{
					foreach ($events as $ID => $VAR)
						{
							$this->template->assign_block_vars('user_edit', array(
								'EVENT_ID'	=>	$ID,
								'TYPE'	=>	$VAR['type'],
								'TITLE'	=>	$VAR['title'],
							));
						}
						$this->template->assign_vars(array(
							'STEP'	=> 'first',
						));
					}
					else { trigger_error($this->user->lang('ERR_USER_NO_MEDALS'), E_USER_WARNING); }
				}
				$this->template->assign_vars(array(
					'TYPE'	=> 'edit',
				));
			}
		else
		{
			trigger_error($this->user->lang['UCP_PROFILE_CONTROL_ERROR']);
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
