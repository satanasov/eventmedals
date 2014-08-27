<?php

/**
*
* @package Anavaro.com Event Medals
* @copyright (c) 2013 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace anavaro\eventmedals\event;

/**
* @ignore
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Event listener
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{	
	static public function getSubscribedEvents()
    {
		return array(
			'core.memberlist_prepare_profile_data'	       => 'prepare_medals',
			'core.user_setup'		=> 'load_language_on_setup',
			'core.viewtopic_modify_post_row'	=>	'modify_post_row',
		);
    }
	
	
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
	
	public function load_language_on_setup($event){
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
            'ext_name' => 'anavaro/eventmedals',
            'lang_set' => 'event_medals',
        );
        $event['lang_set_ext'] = $lang_set_ext;
	}
	
	
	protected $image_dir = 'ext/anavaro/eventmedals/images';
	public function prepare_medals($event)
    {
		//$this->var_display($this->user->lang);
		$sql = 'SELECT profile_event_show FROM ' . $this->table_prefix . 'users_custom WHERE user_id = '.$this->db->sql_escape($event['data']['user_id']);
		$result = $this->db->sql_query($sql);
		$optResult = $this->db->sql_fetchrow($result);
		$sql = 'SELECT * FROM ' . ZEBRA_TABLE . ' WHERE user_id = '.$this->db->sql_escape($event['data']['user_id']).' AND zebra_id = '.$this->user->data['user_id'];
		$result = $this->db->sql_fetchrow($this->db->sql_query($sql));
		$friend_state;
		if ($result) {
			if ($result['bff'] == '0') {
				$friend_state = 2;
			}
			else {
				$friend_state = 3;
			}
		}
		else {
			$friend_state = 1;
		}

		if ($event['data']['user_id'] == $this->user->data['user_id'] || $this->auth->acl_getf_global('m_approve') || $this->auth->acl_get('a_user') || ($optResult['profile_event_show'] > 0 AND $optResult['profile_event_show'] <= $friend_state)) {
			$sql='SELECT * FROM ' . $this->table_prefix . 'event_medals WHERE oid = '.$this->db->sql_escape($event['data']['user_id']).' ORDER BY date ASC';
			$result=$this->db->sql_query($sql);
			$outputMedals = '';
			$medals = array();
			while ($row = $this->db->sql_fetchrow($result)) {
				$medals[$row['date']] = array (
					'type'	=>	$row['type'],
					'link'	=>	$row['link'],
					'date'	=>	$row['date'],
					'image' =>	$row['image'],
				);
			}
			if ($medals)
			{
				asort($medals);
			}

			if (isset($medals)) {
				$outputMedals = '';
				$count = "1";
				foreach($medals AS $VAR) {
					$outputMedals .= "<a href=\"{$this->root_path}viewtopic.{$this->php_ext}?t=".$VAR['link']."\">";
					$date = date("[d F Y]", $VAR['date']);
					if ($count == "") {
						$outputMedals .= "<br>";
						$count = "1";
					}
					if ($VAR['image'] == 'none') {
						if ($VAR['type'] == "1") {
							$outputMedals .= '<img src="' . $this->image_dir . '/red.gif" alt="' . $this->user->lang['MEDAL_TYPE_ONE'] .$date.'" title="' . $this->user->lang['MEDAL_TYPE_ONE'] .$date.'">';
						}
						if ($VAR['type'] == "2") {
							$outputMedals .= '<img src="' . $this->image_dir . '/gold.gif" alt="' . $this->user->lang['MEDAL_TYPE_TWO'] .$date.'" title="' . $this->user->lang['MEDAL_TYPE_TWO'] .$date.'">';
						}
						if ($VAR['type'] == "3") {
							$outputMedals .= '<img src="' . $this->image_dir . '/blue.gif" alt="' . $this->user->lang['MEDAL_TYPE_THREE'] .$date.'" title="' . $this->user->lang['MEDAL_TYPE_THREE'] .$date.'">';
						}
						if ($VAR['type'] == "4") {
							$outputMedals .= '<img src="' . $this->image_dir . '/black.gif" alt="' . $this->user->lang['MEDAL_TYPE_FOUR'] .$date.'" title="' . $this->user->lang['MEDAL_TYPE_FOUR'] .$date.'">';
						}	
					}
					else {
						$outputMedals .= "<img src=\"" . $this->root_path . $VAR['image'] ."\" alt=\"" . $date . "\" title=\"" . $date . "\"/>";
					}
					$count++;
					$outputMedals .= "</a>";
				}
			}
		}
		else
		{	
			$outputMedals = $this->user->lang['UCP_PROFILE_ACC_ERROR'];
		}
		//Let's see if user hase "u_event_control"
		
		if ($this->auth->acl_get('u_event_add'))
		{
			$this->template->assign_var('MEDALS_ADD', "1");
			$this->template->assign_var('MEDALS_EVENT_ADD_URL', $this->root_path . 'app.php/eventmedals/add/'. $event['data']['user_id']);
		}
		if ($this->auth->acl_get('u_event_modify'))
		{
			$this->template->assign_var('MEDALS_MODIFY', "1");
			$this->template->assign_var('MEDALS_EVENT_EDIT_URL', $this->root_path . 'app.php/eventmedals/edit/'. $event['data']['user_id']);
		}
		
		$this->template->assign_var('MEDALS_TITLE', $this->user->lang['MEDALS_TITLE']);
		$this->template->assign_var('MEDALS', $outputMedals);
    }

	public function modify_post_row($event)
	{
		//$this->var_display($event['post_row']);
		$medals = '';
		$event_medals[1]=0;
        $event_medals[2]=0;
        $event_medals[3]=0;
        $event_medals[4]=0;
        $result1 = $this->db->sql_query('SELECT type FROM ' . $this->table_prefix . 'event_medals WHERE oid = '.$this->db->sql_escape($event['post_row']['POSTER_ID']));
        while ($row1 = $this->db->sql_fetchrow($result1)) {
                if ($row1['type'] == "1") {
                        $event_medals[1]++;
                }
                if ($row1['type'] == "2") {
                        $event_medals[2]++;
                }
                if ($row1['type'] == "3") {
                        $event_medals[3]++;
                }
                if ($row1['type'] == "4") {
                        $event_medals[4]++;
                }
        }
		
		if ($event_medals[1] > 0) {
			$medals .= '<img src="' . $this->image_dir . '/red16.gif" alt="' . $this->user->lang['MEDAL_TYPE_ONE'] .'"> x '. $event_medals[1];
		}
		if ($event_medals[2] > 0) {
			$medals .= '<img src="' . $this->image_dir . '/gold16.gif" alt="' . $this->user->lang['MEDAL_TYPE_TWO'] .'"> x '. $event_medals[2];
		}
		if ($event_medals[3] > 0) {
			$medals .= '<img src="' . $this->image_dir . '/blue16.gif" alt="' . $this->user->lang['MEDAL_TYPE_THREE'] .'"> x '. $event_medals[3];
		}
		if ($event_medals[4] > 0) {
			$medals .= '<img src="' . $this->image_dir . '/blck16.gif" alt="' . $this->user->lang['MEDAL_TYPE_FOUR'] .'"> x '. $event_medals[4];
		}
		//$this->var_display($event);
		global $user;

        if ($event['row']['user_id'] != ANONYMOUS)
        {

            $post_row = $event['post_row'];
            $post_row['MEDALS'] = $medals;
            $event['post_row'] = $post_row;
        }
		//$this->var_display($event['post_row']);
		
	}
	protected function var_display($i) 
	{
		echo '<pre>';
		print_r($i);
		echo '</pre>';
		return true;
	}
	
}
