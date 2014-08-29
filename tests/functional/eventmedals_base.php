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
}