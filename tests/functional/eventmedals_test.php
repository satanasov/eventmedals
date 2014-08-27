<?php
/**
*
* ZebraEnhance test
*
* @copyright (c) 2014 Stanislav Atanasov
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace anavaro\eventmedals\tests\functional;

/**
* @group functional
*/
class eventmedals_test extends eventmedals_base
{

	public $post;
	public function test_request()
	{
		//create new user
		$this->create_user('testuser');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser'));
		
		$this->login();
		
		$this->post = $this->create_topic(2, 'Test Topic 1', 'this is test topic for events');
		$crawler = self::request('GET', "viewtopic.php?t={$this->post['topic_id']}&sid={$this->sid}");
		
		$this->assertContains('this is test topic for events', $crawler->filter('html')->text());
		
		
	}
}