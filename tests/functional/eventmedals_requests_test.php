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
class eventmedals_requests_test extends eventmedals_base
{

	public function test_request()
	{
		//create new user
		$this->create_user('testuser');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser'));

		//login as admin
		$this->login();
		$this->add_lang('ucp');
		$this->add_lang('common');
		
		//Send friend request
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");
		
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		//Check if user request is there
		$this->assertContains($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('testuser', $crawler->filter('html')->text());
	}
	public function test_own_reqest_cancel()
	{
		//login as admin
		$this->login();
		$this->add_lang('ucp');
		
		//send friend request
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");
		
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$this->assertContains($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());
		
		//check if friend request is present
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('testuser', $crawler->filter('html')->text());
		
		//get request URL
		$link = $crawler->filter('#ze_slef_req')->filter('span')->filter('a')->first()->link()->getUri();
		
		//cancel friend request
		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		//see if friend reques is canceled
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertNotContains('testuser', $crawler->filter('html')->text());
	}
	public function test_user_reqest_cancel()
	{
		$this->login();
		$this->add_lang('ucp');
		
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");
		
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$this->assertContains($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());
		
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('testuser', $crawler->filter('html')->text());
		
		$this->logout();
		
		$this->login('testuser');
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		
		$link = $crawler->filter('#ze_other_req')->filter('span')->filter('a')->eq(1)->link()->getUri();
		
		$this->assertContains('2', $link);
		
		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertNotContains($this->lang('UCP_ZEBRA_PENDING_OUT'), $crawler->filter('html')->text());
	}
	public function test_user_reqest_accept()
	{
		$this->login();
		$this->add_lang('ucp');
		
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");
		
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$this->assertContains($this->lang('FRIENDS_UPDATED'), $crawler->filter('html')->text());
		
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains('testuser', $crawler->filter('html')->text());
		
		$this->logout();
		
		$this->login('testuser');
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains($this->lang('UCP_ZEBRA_PENDING_IN'), $crawler->filter('html')->text());
		
		$link = $crawler->filter('#ze_other_req')->filter('span')->filter('a')->eq(0)->link()->getUri();
		
		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertNotContains($this->lang('UCP_ZEBRA_PENDING_IN'), $crawler->filter('html')->text());
		$this->assertContains('admin', $crawler->filter('#ze_ajaxify')->text());
	}
	public function test_remove_friend()
	{
		$this->login();
		$this->add_lang('ucp');
		
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$link = $crawler->filter('#ze_ajaxify')->filter('a')->eq(2)->link()->getUri();
		
		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertNotContains('testuser', $crawler->filter('html')->text());
		$this->assertEquals(0, $crawler->filter('#ze_ajaxify')->count());
		
		$this->logout();
		
		$this->login('testuser');
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertNotContains('admin', $crawler->filter('html')->text());
		$this->assertEquals(0, $crawler->filter('#ze_ajaxify')->count());
		$this->logout();
	}
	
	public function test_togle_bff()
	{
		$this->login();
		//we create friends
		$crawler = self::request('GET', "ucp.php?i=zebra&add=testuser&sid={$this->sid}");
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		$this->logout();
		
		$this->login('testuser');
		$this->add_lang_ext('anavaro/zebraenhance', 'zebra_enchance');
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$this->assertContains($this->lang('UCP_ZEBRA_PENDING_IN'), $crawler->filter('html')->text());
		$link = $crawler->filter('#ze_other_req')->filter('span')->filter('a')->eq(0)->link()->getUri();
		$crawler = self::request('GET', substr($link, strpos($link, 'ucp.')));
		$this->assertContains($this->lang('CONFIRM_OPERATION'), $crawler->filter('html')->text());
		$form = $crawler->selectButton($this->lang('YES'))->form();
		$crawler = self::submit($form);
		
		$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		$link = $crawler->filter('#ze_ajaxify')->filter('a')->eq(0)->link()->getUri();
		//togle like
		$crw1 = self::request('GET', $link, array(), array(), array('CONTENT_TYPE'	=> 'application/json'));
		
		//$this->assertContains('add', $crwl->filter('exit'));
		
		//$crawler = self::request('GET', "ucp.php?i=ucp_zebra&mode=friends&sid={$this->sid}");
		//$this->assertContains('favorite_remove.png', $crawler->filter('#ze_ajaxify')->filter('a')->eq(0)->filter('img')->getAttribute('src')->text());
	}
}