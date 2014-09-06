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
}