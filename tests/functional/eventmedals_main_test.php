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
		
	/*	$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$form['username'] = 'admin\n testuser1 \n testuser2 \ntestuser3\n';
		$crawler = self::submit($form);
	*/	
		$this->assertContains('zzzz', $crawler->text());
	}
}