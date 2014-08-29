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
class eventmedals_main_test extends eventmedals_base
{
	protected $post;
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
	}
	
	public function test_add_medals()
	{
		//add medals
		$this->login();
		$this->admin_login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'info_acp_eventmedals');
		
		$crawler = self::request('GET', 'adm/index.php?i=-anavaro-eventmedals-acp-main_module&mode=add&sid=' . $this->sid);
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['usernames'] = 'admin'. PHP_EOL .' testuser1 '. PHP_EOL .' testuser2 '. PHP_EOL .'testuser3'. PHP_EOL;
		$crawler = self::submit($form);
	
		//test step 2 begins
		$this->assertContainsLang('MEDALS_ADD_STEP_TWO', $crawler->text());
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		
		$form['usesr[2][select]'] = 1;
		$user1 = $this->get_user_id('testuser1');
		$form['usesr['.$user1.'][select]'] = 2;
		$user2 = $this->get_user_id('testuser2');
		$form['usesr['.$user2.'][select]'] =  3;
		$user3 = $this->get_user_id('testuser3');
		$form['usesr['.$user3.'][select]'] =  4;
		
		$crawler = self::submit($form);
		
		//test step 3 begins
		$this->assertContainsLang('MEDALS_ADD_STEP_THREE', $crawler->text());
		
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		
		$form['day'] = 2;
		$form['month'] = 5;
		$form['year'] = 2014;
		$form['link'] = $this->get_topic_id('Test Topic 1');
		
		$crawler = self::submit($form);
		
		$this->assertContainsLang('SUCCESS_ADD_INFO', $crawler->text());
	}
}