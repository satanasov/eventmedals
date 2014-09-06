<?php
/**
*
* Event Medals
*
* @copyright (c) 2014 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace anavaro\eventmedals\tests\functional;

/**
* @group functional
*/
class eventmedals_base extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('anavaro/eventmedals');
	}

	protected $post;
	
	public function setUp()
	{
		parent::setUp();
		$this->post = array();
	}
	
	public function get_user_id($username)
	{
		$sql = 'SELECT user_id, username 
				FROM ' . USERS_TABLE . '
				WHERE username_clean = \''.$this->db->sql_escape(utf8_clean_string($username)).'\'';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		return $row['user_id'];
	}
	
	public function get_topic_id($topic_title)
	{
		$sql = 'SELECT topic_id
				FROM ' . TOPICS_TABLE . '
				WHERE topic_title = \'' . $topic_title . '\'';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		return $row['topic_id'];
	}
	
	public function set_medal($owner_id, $type, $link, $date, $image = 'none')
	{
		$sql_ary = array(
			'owner_id'	=> (int) $owner_id,
			'type'	=> (int) $type,
			'link'	=> (int) $link,
			'date'	=> (int) $date,
			'image'	=> $image,
		);
		$sql = 'INSERT INTO phpbb_event_medals' . $this->db->sql_build_array('INSERT', $sql_ary);
		$this->db->sql_query($sql);
		
		$sql = 'SELECT COUNT(*) as count FROM phpbb_event_medals WHERE owner_id = ' . (int) $owner_id . ' AND link = ' . (int) $link;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		
		return $row['count'];
	}
	
	public function clean_medals_db()
	{
		$sql = "DELETE FROM phpbb_event_medals WHERE owner_id <> 0";
		$result = $this->db->sql_query($sql);
		
		return 0;
	}
	
	public function medals_for_user($user_id)
	{
		$sql = 'SELECT COUNT(*) as count FROM phpbb_event_medals WHERE owner_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		
		return $row['count'];
	}
	
	public function medals_for_event($event_id)
	{
		$sql = 'SELECT COUNT(*) as count FROM phpbb_event_medals WHERE link = ' . (int) $event_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		
		return $row['count'];
	}
	
	public function medal_type($user_id, $event_id)
	{
		$sql = 'SELECT type FROM phpbb_event_medals WHERE owner_id = ' . (int) $user_id . ' AND link = ' . (int) $event_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		
		return $row['type'];
	}
	
	public function medal_image($user_id, $event_id)
	{	
		$sql = 'SELECT image FROM phpbb_event_medals WHERE owner_id = ' . (int) $user_id . ' AND link = ' . (int) $event_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		
		return htmlspecialchars($row['image']);
	}
	
	public function add_frineds($uid1, $uid2)
	{
		$sql_ary = array(
			'user_id' => $uid1,
			'zebra_id'	=> $uid2,
			'friend' => 1
		);
		$sql = 'INSERT INTO phpbb_zebra' . $this->db->sql_build_array('INSERT', $sql_ary);
		$this->db->sql_query($sql);
	}
	public function add_foe($uid1, $uid2)
	{
		$sql_ary = array(
			'user_id' => $uid1,
			'zebra_id'	=> $uid2,
			'foe' => 1
		);
		$sql = 'INSERT INTO phpbb_zebra' . $this->db->sql_build_array('INSERT', $sql_ary);
		$this->db->sql_query($sql);
	}
	
	public function set_permissions($user_id)
	{
		$sql = 'SELECT auth_option_id FROM ' . ACL_OPTIONS_TABLE . ' WHERE auth_option = \'u_event_add\'';
		$result = $this->db->sql_query($sql);
		$row1 = $this->db->sql_fetchrow($result);
		
		$sql_ary = array(
			'user_id' => (int) $user_id,
			'forum_id' => 0,
			'auth_option_id'	=> $row1['auth_option_id'],
			'auth_role_id'	=> 0,
			'auth_setting' => 1,
		);
		$sql = 'INSERT INTO ' . ACL_USERS_TABLE . $this->db->sql_build_array('INSERT', $sql_ary);
		$this->db->sql_query($sql);
		
		$sql = 'SELECT auth_option_id FROM ' . ACL_OPTIONS_TABLE . ' WHERE auth_option = \'u_event_edit\'';
		$result = $this->db->sql_query($sql);
		$row2 = $this->db->sql_fetchrow($result);
		
		$sql_ary = array(
			'user_id' => (int) $user_id,
			'forum_id' => 0,
			'auth_option_id'	=> $row2['auth_option_id'],
			'auth_role_id'	=> 0,
			'auth_setting' => 1,
		);
		$sql = 'INSERT INTO ' . ACL_USERS_TABLE . $this->db->sql_build_array('INSERT', $sql_ary);
		$this->db->sql_query($sql);
		
		$sql = 'SELECT user_id FROM ' . ACL_USERS_TABLE . ' WHERE auth_option_id = ' . $row2['auth_option_id'];
		$row = $this->db->sql_fetchrow($result);
		
		return $row['user_id'];
	}
}