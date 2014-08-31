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
		$sql = 'INSERT INTO phpbb_event_medals VALUES (' . (int) $owner_id . ', ' . (int) $type . ', ' . (int) $link . ', ' . (int) $date . ', ' . $image . ')';
		$this->db->sql_query($sql);
		
		$sql = 'SELECT COUNT(*) as count FROM phpbb_event_medals WHERE owner_id = ' . (int) $owner_id . ' AND link = ' . (int) $link;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		
		return $row['count'];
	}
}