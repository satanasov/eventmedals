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
class eventmedals_acp_edit_test extends eventmedals_base
{
	public function test_acp_edit_no_medals()
	{
		$this->clean_medals_db();
		//add medals
		$this->login();
		$this->admin_login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'info_acp_eventmedals');
		
		$crawler = self::request('GET', 'adm/index.php?i=-anavaro-eventmedals-acp-main_module&mode=edit&sid=' . $this->sid);
		$this->assertContainsLang('ERR_NO_MEDALS', $crawler->filter('html')->text());
		$this->logout();
	}
		
	public function test_acp_edit_build_medals()
	{
		
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
	
	public function test_acp_edit_user_no_user()
	{
		$this->login();
		$this->admin_login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'info_acp_eventmedals');
		
		$crawler = self::request('GET', 'adm/index.php?i=-anavaro-eventmedals-acp-main_module&mode=edit&sid=' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['username'] = 'testuser4';
		$form['event_edit_type'] = 'user';
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('ERR_NO_USER', $crawler->filter('html')->text());
		$this->logout();
		
	}
	
	public function test_acp_edit_user_no_medals()
	{
		$this->create_user('testuser4');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser1'));
		
		$this->login();
		$this->admin_login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'info_acp_eventmedals');
		
		$crawler = self::request('GET', 'adm/index.php?i=-anavaro-eventmedals-acp-main_module&mode=edit&sid=' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['username'] = 'testuser4';
		$form['event_edit_type'] = 'user';
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('ERR_USER_NO_MEDALS', $crawler->filter('html')->text());
		$this->logout();
	}
	
	public function test_acp_edit_remove_medal()
	{
		$this->login();
		$this->admin_login();
		
		$this->assertEquals(4, $this->medals_for_event($this->get_topic_id('Test Topic 1')));
		$this->assertEquals(1, $this->medals_for_user($this->get_user_id('testuser3')));
		
		$this->add_lang_ext('anavaro/eventmedals', 'info_acp_eventmedals');
		
		$crawler = self::request('GET', 'adm/index.php?i=-anavaro-eventmedals-acp-main_module&mode=edit&sid=' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['username'] = 'testuser3';
		$form['event_edit_type'] = 'user';
		
		$crawler = self::submit($form);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['delete'] = array($this->get_topic_id('Test Topic 1'));
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('SUCCESS_EDIT_INFO', $crawler->filter('html')->text());
		
		$this->assertEquals(0, $this->medals_for_user($this->get_user_id('testuser3')));
		
		$this->assertEquals(3, $this->medals_for_event($this->get_topic_id('Test Topic 1')));
	}
}