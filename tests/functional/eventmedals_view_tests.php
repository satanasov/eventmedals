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
class eventmedals_view_tests extends eventmedals_base
{

	public function test_prepare()
	{
		$this->clean_medals_db();
		
		$owner_id = $this->get_user_id('admin');
		$type = 1;
		$link = $this->get_topic_id('Test Topic 1');
		$date = 1399248000;
		
		$this->assertEquals(1, $this->set_medal($owner_id, $type, $link, $date));
		
		$owner_id = $this->get_user_id('testuser1');
		$type = 2;
		$link = $this->get_topic_id('Test Topic 1');
		$date = 1399248000;
		
		$this->assertEquals(1, $this->set_medal($owner_id, $type, $link, $date));
		
		$owner_id = $this->get_user_id('testuser2');
		$type = 3;
		$link = $this->get_topic_id('Test Topic 1');
		$date = 1399248000;
		
		$this->assertEquals(1, $this->set_medal($owner_id, $type, $link, $date));
		
		$owner_id = $this->get_user_id('testuser3');
		$type = 4;
		$link = $this->get_topic_id('Test Topic 1');
		$date = 1399248000;
		
		$this->assertEquals(1, $this->set_medal($owner_id, $type, $link, $date));
		
	}
	/**
     * @depends test_prepare
     */
	public function test_user_viewtopic_medals()
	{
		$this->login('testuser1');
		
		$crawler = self::request('GET', "viewtopic.php?t={$this->get_topic_id('Test Topic 1')}&sid={$this->sid}");
		
		$this->assertContains('x 1', $crawler->filter('.medals_postrow')->text());
		
		$this->logout();
	}
	
	/**
     * @depends test_user_viewtopic_medals
     */
	public function test_change_acl_to_none()
	{
		$this->login();
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'ucp.php?i=-anavaro-eventmedals-ucp-ucp_medals_module&mode=control' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['ucp_profile_view'] = 0;
		
		$crawler = self::submit($form);
		
		$this->logout();
	}
	
	/**
     * @depends test_change_acl_to_none
     */
	public function test_view_acl_none()
	{
		$this->login('testuser1');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('admin') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('UCP_PROFILE_ACC_ERROR', $crawler->filter('html')->text());
		$this->logout();
	}
	
	/**
     * @depends test_view_acl_none
     */
	public function test_change_acl_to_all_except_enemies()
	{
		$this->login();
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'ucp.php?i=-anavaro-eventmedals-ucp-ucp_medals_module&mode=control' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['ucp_profile_view'] = 1;
		
		$crawler = self::submit($form);
		
		//add testuser2 as enemy
		$this->add_foe($this->user->data['user_id'], $this->get_user_id('testuser2');
		
		$this->logout();
	}
	/**
     * @depends test_change_acl_to_all_except_enemies
     */
	public function test_view_acl_all_except_enemies()
	{
		$this->login('testuser2');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('admin') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('UCP_PROFILE_ACC_ERROR', $crawler->filter('html')->text());
		$this->logout();
		
		$this->login('testuser1');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('admin') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('MEDAL_TYPE_ONE', $crawler->filter('#medals_show')->text());
		$this->logout();
		
	}
	
	/**
     * @depends test_view_acl_none
     */
	public function test_change_acl_to_all()
	{
		$this->login();
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'ucp.php?i=-anavaro-eventmedals-ucp-ucp_medals_module&mode=control' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['ucp_profile_view'] = 2;
		
		$crawler = self::submit($form);
		
		//add testuser1 as frind
		$this->add_foe($this->user->data['user_id'], $this->get_user_id('testuser1');
		
		$this->logout();
	}
	
	/**
     * @depends test_change_acl_to_all
     */
	public function test_view_acl_all()
	{
		$this->login('testuser1');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('admin') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('MEDAL_TYPE_ONE', $crawler->filter('#medals_show')->text());
		$this->logout();
		
		$this->login('testuser2');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('admin') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('MEDAL_TYPE_ONE', $crawler->filter('html')->text());
		$this->logout();
		
		$this->login('testuser3');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('admin') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('MEDAL_TYPE_ONE', $crawler->filter('#medals_show')->text());
		$this->logout();
	}
	
	/**
     * @depends test_view_acl_all
     */
	public function test_change_acl_to_frineds()
	{
		$this->login();
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'ucp.php?i=-anavaro-eventmedals-ucp-ucp_medals_module&mode=control' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['ucp_profile_view'] = 3;
		
		$crawler = self::submit($form);
		
		//add testuser1 as frind
		$this->add_foe($this->user->data['user_id'], $this->get_user_id('testuser1');
		
		$this->logout();
	}
	/**
     * @depends test_change_acl_to_frineds
     */
	public function test_view_acl_friends()
	{
		$this->login('testuser1');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('admin') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('MEDAL_TYPE_ONE', $crawler->filter('#medals_show')->text());
		$this->logout();
		
		$this->login('testuser2');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('admin') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('UCP_PROFILE_ACC_ERROR', $crawler->filter('html')->text());
		$this->logout();
		
		$this->login('testuser3');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('admin') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('UCP_PROFILE_ACC_ERROR', $crawler->filter('#medals_show')->text());
		$this->logout();
	}
	
	/**
     * @depends test_view_acl_friends
     */
	public function test_change_acl_to_none_force_admin()
	{
		$this->login('testuser3');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'ucp.php?i=-anavaro-eventmedals-ucp-ucp_medals_module&mode=control' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['ucp_profile_view'] = 0;
		
		$crawler = self::submit($form);
		
		//add testuser1 as frind
		$this->add_foe($this->user->data['user_id'], $this->get_user_id('testuser1');
		
		$this->logout();
	}
	/**
     * @depends test_change_acl_to_none_force_admin
     */
	public function test_view_acl_friends()
	{
		$this->login('testuser1');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('testuser3') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('UCP_PROFILE_ACC_ERROR', $crawler->filter('#medals_show')->text());
		$this->logout();
		
		$this->login('testuser2');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('testuser3') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('UCP_PROFILE_ACC_ERROR', $crawler->filter('html')->text());
		$this->logout();
		
		$this->login('testuser3');
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('testuser3') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('MEDAL_TYPE_FOUR', $crawler->filter('#medals_show')->text());
		$this->logout();
		
		$this->login();
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$crawler = self::request('GET', 'memberlist.php?mode=viewprofile&u=' . $this->get_user_id('testuser3') . '&sid=' . $this->sid);
		
		$this->assertContainsLang('MEDAL_TYPE_FOUR', $crawler->filter('#medals_show')->text());
		$this->logout();
	}
}
