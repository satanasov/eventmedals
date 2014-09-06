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

	public function test_ucp_add_medals()
	{
		$this->set_permissions($this->get_user_id('admin'));
		//add medals
		$this->login();
		
		$this->add_lang_ext('anavaro/eventmedals', 'event_medals');
		
		$this->set_permissions($this->user->data['user_id']);
		
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
