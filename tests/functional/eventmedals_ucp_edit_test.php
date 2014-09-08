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
class eventmedals_ucp_edit_test extends eventmedals_base
{

	public function test_install_edit()
	{
		$this->clean_medals_db();

		$this->assertEquals(0, $this->medals_for_user($this->get_user_id('admin')));
		
		$owner_id = $this->get_user_id('testuser1');
		$type = 2;
		$link = $this->get_topic_id('Test Topic 1');
		$date = 1399248000;
		
		$this->assertEquals(1, $this->set_medal($owner_id, $type, $link, $date));
	}

	public function test_set_permissions_edit()
	{
		$this->login();
		$this->admin_login();
		$this->add_lang('acp/permissions');
		
		// User permissions
		$crawler = self::request('GET', 'adm/index.php?i=acp_permissions&icat=16&mode=setting_user_global&sid=' . $this->sid);
		$this->assertContains($this->lang('ACP_USERS_PERMISSIONS_EXPLAIN'), $this->get_content());

		// Select admin
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$data = array('username[0]' => 'admin');
		$form->setValues($data);
		$crawler = self::submit($form);
		$this->assertContains($this->lang('ACL_SET'), $crawler->filter('h1')->eq(1)->text());
		
		$form = $crawler->selectButton($this->lang('APPLY_PERMISSIONS'))->form();
		$data = array(
			'setting'	=> array(
				$this->get_user_id('admin')	=> array(
					0	=> array(
						'u_event_edit' => '1'
					)
				),
			),
		);
		$form->setValues($data);
		$crawler = self::submit($form);
		
		$this->assertContainsLang('AUTH_UPDATED', $crawler->filter('html')->text());
		
		$this->logout();
		
	}
	/**
     * @depends test_set_permissions_edit
     */
	public function test_ucp_edit_no_user()
	{
		//add medals
		$this->login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'app.php/eventmedals/edit/1000');
		
		$this->assertContainsLang('ERR_NO_USER', $crawler->text());
	}
	/**
     * @depends test_set_permissions_edit
     */
	public function test_ucp_edit_no_medals()
	{
		//add medals
		$this->login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'app.php/eventmedals/edit/'. $this->get_user_id('testuser2'));
		
		$this->assertContainsLang('ERR_USER_NO_MEDALS', $crawler->text());
	}
	
}
