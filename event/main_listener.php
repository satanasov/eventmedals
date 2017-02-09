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
* Event listener
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var */
	protected $table_prefix;

	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions'	       => 'acl_perms_add',
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
	 * @param \phpbb\auth\auth                  $auth      Auth object
	 * @param \phpbb\db\driver\driver_interface $db        Database object
	 * @param \phpbb\template\template          $template  Template object
	 * @param \phpbb\user                       $user      User object
	 * @param \phpbb\language\language          $language
	 * @param string                            $root_path phpBB root path
	 * @param string                            $php_ext   phpEx
	 * @param                                   $table_prefix
	 * @internal param \phpbb\content_visibility $content_visibility Content visibility object
	 */
	public function __construct(\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db, \phpbb\template\template $template,
		\phpbb\user $user, \phpbb\language\language $language,
		$root_path, $php_ext, $table_prefix)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->lang = $language;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
	}

	public function acl_perms_add($event)
	{
		$permissions = $event['permissions'];
		$permissions['u_event_add'] = array('lang' => 'ACL_U_EVENT_ADD', 'cat' => 'misc');
		$permissions['u_event_modify'] = array('lang' => 'ACL_U_EVENT_MODIFY', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}

	public function load_language_on_setup($event)
	{
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
		$optResult = $event['data']['profile_event_show'];
		$sql = 'SELECT * FROM ' . ZEBRA_TABLE . ' WHERE user_id = '.$this->db->sql_escape($event['data']['user_id']).' AND zebra_id = '.$this->user->data['user_id'];
		$result = $this->db->sql_fetchrow($this->db->sql_query($sql));
		$zebra_state = 0;
		if ($result)
		{
			if ($result['foe'] == 1)
			{
				$zebra_state = 1;
			}
			else
			{
				if ($result['bff'] == '0') {
					$zebra_state = 3;
				}
				else {
					$zebra_state = 4;
				}
			}
		}
		else
		{
			$zebra_state = 2;
		}

		$show = false;
		if ($optResult > 0 && ($optResult <= $zebra_state) && !($optResult == 1 and $zebra_state != 1))
		{
			$show = true;
		}
		if ($event['data']['user_id'] == $this->user->data['user_id'] || $this->auth->acl_getf_global('m_approve') || $this->auth->acl_get('a_user') || $show) {
			$sql='SELECT * FROM ' . $this->table_prefix . 'event_medals WHERE owner_id = '.$this->db->sql_escape($event['data']['user_id']).' ORDER BY date ASC';
			$result=$this->db->sql_query($sql);
			$outputMedals = '';
			$medals = array();
			while ($row = $this->db->sql_fetchrow($result)) {
				$medals[] = array (
					'type'	=>	$row['type'],
					'link'	=>	$row['link'],
					'date'	=>	$row['date'],
					'image' =>	$row['image'],
				);
			}

			if (isset($medals)) {
				$outputMedals = '';
				$count = "1";
				foreach($medals as $VAR) {
					$outputMedals .= "<a href=\"{$this->root_path}viewtopic.{$this->php_ext}?t=".$VAR['link']."\">";
					$date = date("[d F Y]", $VAR['date']);
					if ($VAR['image'] == 'none') {
						if ($VAR['type'] == "1") {
							$outputMedals .= '<img src="' . $this->image_dir . '/red.gif" alt="' . $this->lang->lang('MEDAL_TYPE_ONE') .$date.'" title="' . $this->lang->lang('MEDAL_TYPE_ONE') .$date.'">';
						}
						if ($VAR['type'] == "2") {
							$outputMedals .= '<img src="' . $this->image_dir . '/gold.gif" alt="' . $this->lang->lang('MEDAL_TYPE_TWO') .$date.'" title="' . $this->lang->lang('MEDAL_TYPE_TWO') .$date.'">';
						}
						if ($VAR['type'] == "3") {
							$outputMedals .= '<img src="' . $this->image_dir . '/blue.gif" alt="' . $this->lang->lang('MEDAL_TYPE_THREE') .$date.'" title="' . $this->lang->lang('MEDAL_TYPE_THREE') .$date.'">';
						}
						if ($VAR['type'] == "4") {
							$outputMedals .= '<img src="' . $this->image_dir . '/black.gif" alt="' . $this->lang->lang('MEDAL_TYPE_FOUR') .$date.'" title="' . $this->lang->lang('MEDAL_TYPE_FOUR') .$date.'">';
						}
					}
					else {
						$outputMedals .= "<img src=\"" . $this->root_path . $VAR['image'] ."\" alt=\"" . $date . "\" title=\"" . $date . "\"/>";
					}
					$outputMedals .= "</a> ";
				}
			}
		}
		else
		{
			$outputMedals = $this->lang->lang('UCP_PROFILE_ACC_ERROR');
		}
		//Let's see if user hase "u_event_control"

		if ($this->auth->acl_get('u_event_add'))
		{
			$this->template->assign_vars(array(
				'MEDALS_ADD'	=> 1,
				'MEDALS_EVENT_ADD_URL' => $this->root_path . 'app.php/eventmedals/add/'. $event['data']['user_id']
			));
		}
		if ($this->auth->acl_get('u_event_modify'))
		{
			$this->template->assign_vars(array(
				'MEDALS_MODIFY'	=> 1,
				'MEDALS_EVENT_EDIT_URL' => $this->root_path . 'app.php/eventmedals/edit/'. $event['data']['user_id']
			));
		}

		$this->template->assign_vars(array(
			'MEDALS_TITLE'	=> $this->lang->lang('MEDALS_TITLE'),
			'MEDALS'	=> $outputMedals
		));
	}

	public function modify_post_row($event)
	{
		//$this->var_display($event['post_row']);
		$medals = '';
		$event_medals[1]=0;
		$event_medals[2]=0;
		$event_medals[3]=0;
		$event_medals[4]=0;
		$result1 = $this->db->sql_query('SELECT type FROM ' . $this->table_prefix . 'event_medals WHERE owner_id = '.$this->db->sql_escape($event['post_row']['POSTER_ID']));
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
			$medals .= '<img src="' . $this->image_dir . '/red16.gif" alt="' . $this->lang->lang('MEDAL_TYPE_ONE') .'"> x '. $event_medals[1];
		}
		if ($event_medals[2] > 0) {
			$medals .= '<img src="' . $this->image_dir . '/gold16.gif" alt="' . $this->lang->lang('MEDAL_TYPE_TWO') .'"> x '. $event_medals[2];
		}
		if ($event_medals[3] > 0) {
			$medals .= '<img src="' . $this->image_dir . '/blue16.gif" alt="' . $this->lang->lang('MEDAL_TYPE_THREE') .'"> x '. $event_medals[3];
		}
		if ($event_medals[4] > 0) {
			$medals .= '<img src="' . $this->image_dir . '/black16.gif" alt="' . $this->lang->lang('MEDAL_TYPE_FOUR') .'"> x '. $event_medals[4];
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
