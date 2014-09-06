<?php
/**
*
* EventMedals test
*
* @copyright (c) 2014 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace anavaro\eventmedals\tests\functional;

/**
* @group functional
*/
class eventmedals_ucp_add_test extends eventmedals_base
{

	public function test_install()
	{
		$this->clean_medals_db();

		$this->assertEquals(0, $this->medals_for_user($this->get_user_id('admin')));
	}
	public function permissions_data()
	{
		return array(
			// description
			// permission type
			// permission name
			// mode
			// object name
			// object id
			array(
				'user permission',
				'u_',
				'u_event_add',
				'setting_user_global',
				'user_id',
				2,
			),
			array(
				'user permission',
				'u_',
				'u_event_edit',
				'setting_user_global',
				'user_id',
				2,
			),
			/* Admin does not work yet, probably because founder can do everything
			array(
				'admin permission',
				'a_',
				'a_forum',
				'setting_admin_global',
				'group_id',
				5,
			),
			*/
		);
	}

	/**
	* @dataProvider permissions_data
	*/
	public function test_change_permission($description, $permission_type, $permission, $mode, $object_name, $object_id)
	{
		$this->login();
		$this->admin_login();
		$this->add_lang('acp/permissions');
		
		// Get the form
		$crawler = self::request('GET', "adm/index.php?i=acp_permissions&icat=16&mode=$mode&${object_name}[0]=$object_id&type=$permission_type&sid=" . $this->sid);
		$this->assertContains($this->lang('ACL_SET'), $crawler->filter('h1')->eq(1)->text());

		// XXX globals for \phpbb\auth\auth, refactor it later
		global $db, $cache;
		$db = $this->get_db();
		$cache = new phpbb_mock_null_cache;

		$auth = new \phpbb\auth\auth;
		// XXX hardcoded id
		$user_data = $auth->obtain_user_data(2);
		$auth->acl($user_data);
		$this->assertEquals(0, $auth->acl_get($permission));

		// Set u_hideonline to never
		$form = $crawler->selectButton($this->lang('APPLY_PERMISSIONS'))->form();
		// initially it should be a no
		$values = $form->getValues();
		$this->assertEquals(0, $values["setting[$object_id][0][$permission]"]);
		// set to never
		$data = array("setting[$object_id][0][$permission]" => '1');
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContains($this->lang('AUTH_UPDATED'), $crawler->text());

		// check acl again
		$auth = new \phpbb\auth\auth;
		// XXX hardcoded id
		$user_data = $auth->obtain_user_data(2);
		$auth->acl($user_data);
		$this->assertEquals(1, $auth->acl_get($permission));
	}

	public function test_ucp_add_medals()
	{
		//add medals
		$this->login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'app.php/eventmedals/add/2');
		$this->assertContains('SUCCESS_ADD_INFO', $crawler->text());
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['day'] = 2;
		$form['month'] = 5;
		$form['year'] = 2014;
		$form['link'] = $this->get_topic_id('Test Topic 1');
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('SUCCESS_ADD_INFO', $crawler->text());
		
		$this->logout();

	}
	/**
     * @depends test_ucp_add_medals
     */
	public function test_ucp_add_medals_unique()
	{
		//add medals
		$this->login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'app.php/eventmedals/add/' . $this->get_user_id('testuser1') . '&sid=' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['day'] = 2;
		$form['month'] = 5;
		$form['year'] = 2014;
		$form['link'] = $this->get_topic_id('Test Topic 1');
		
		$crawler = self::submit($form);
		$this->assertContainsLang('ERR_DUPLICATE_MEDAL', $crawler->text());
		$this->logout();
	}
	/**
     * @depends test_ucp_add_medals_unique
     */
	public function test_ucp_add_medals_valid_topic()
	{
		//add medals
		$this->login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'app.php/eventmedals/add/' . $this->get_user_id('testuser1') . '&sid=' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['day'] = 2;
		$form['month'] = 5;
		$form['year'] = 2014;
		$form['link'] = 9999;
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('ERR_TOPIC_ERR', $crawler->text());
		$this->logout();
	}
	
	/**
     * @depends test_ucp_add_medals_valid_topic
     */
	public function test_ucp_add_medals_valid_user()
	{
		//add medals
		$this->login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'app.php/eventmedals/add/' . $this->get_user_id('testuser5') . '&sid=' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['day'] = 2;
		$form['month'] = 5;
		$form['year'] = 2014;
		$form['link'] = $this->get_topic_id('Test Topic 1');
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('ERR_NO_USER', $crawler->text());
		$this->logout();
	}
	/**
     * @depends test_ucp_add_medals_valid_user
     */
	public function test_ucp_add_medals_valid_date()
	{
		//add medals
		$this->login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'app.php/eventmedals/add/' . $this->get_user_id('testuser1') . '&sid=' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['day'] = 2;
		$form['month'] = 5;
		$form['year'] = 1969;
		$form['link'] = $this->get_topic_id('Test Topic 1');
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('ERR_DATE_ERR', $crawler->text());
		$this->logout();
	}
}
