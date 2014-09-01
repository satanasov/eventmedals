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
		//add medals
		$this->login();
		$this->admin_login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'info_acp_eventmedals');
		
		$crawler = self::request('GET', 'adm/index.php?i=-anavaro-eventmedals-acp-main_module&mode=edit&sid=' . $this->sid);
		$this->assertContainsLang('ERR_NO_MEDALS', $crawler->filter('html')->text());
		$this->logout();
	}
		
	public function test_install()
	{
		//add users so we can test medals
		$this->create_user('testuser1');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser1'));
		
		$this->create_user('testuser2');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser2'));
		
		$this->create_user('testuser3');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser3'));
		
		$this->login();
		
		// Test creating topic and post to test
		$this->post = $this->create_topic(2, 'Test Topic 1', 'This is a test topic posted by the testing framework.');
		$crawler = self::request('GET', "viewtopic.php?t={$this->post['topic_id']}&sid={$this->sid}");
		
		$this->assertContains('This is a test topic posted by the testing framework.', $crawler->filter('html')->text());
		
		$this->logout();
		
		$owner_id = $this->get_user_id('admin');
		$type = 1;
		$link = $this->post['topic_id'];
		$date = 1399248000;
		
		$this->assertEquals(1, $this->set_medal($owner_id, $type, $link, $date));
		
		$owner_id = $this->get_user_id('testuser1');
		$type = 2;
		$link = $this->post['topic_id'];
		$date = 1399248000;
		
		$this->assertEquals(1, $this->set_medal($owner_id, $type, $link, $date));
		
		$owner_id = $this->get_user_id('testuser2');
		$type = 3;
		$link = $this->post['topic_id'];
		$date = 1399248000;
		
		$this->assertEquals(1, $this->set_medal($owner_id, $type, $link, $date));
		
		$owner_id = $this->get_user_id('testuser3');
		$type = 4;
		$link = $this->post['topic_id'];
		$date = 1399248000;
		
		$this->assertEquals(1, $this->set_medal($owner_id, $type, $link, $date));
		
	}
}