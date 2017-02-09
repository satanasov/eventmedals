<?php
/**
 * Event Medals
 *
 * @Copyright (c) 2017 to Stanislav Atanasov (lucifer@anavaro.com)
 * @License GNU General Public License, version 2 (GPL-2.0)
 */

namespace anavaro\eventmedals\tests;

/**
 * @group event
 */

class main_listener_test extends \phpbb_database_test_case
{
	protected $listener;

	/**	@var \phpbb\auth\auth */
	protected $auth;

	/** @var  \phpbb\template\template */
	protected $template;

	/** @var  \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var  \phpbb\language\language */
	protected $language;

	/** @var  \phpbb\user */
	protected $user;

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/fixture.xml');
	}

	/**
	 * Define the extensions to be tested
	 *
	 * @return array vendor/name of extension(s) to test
	 */
	static protected function setup_extensions()
	{
		return array('anavaro/eventmedals');
	}

	public function setUp()
	{
		parent::setUp();

		global $phpbb_root_path, $phpEx;

		$this->auth = $this->getMock('\phpbb\auth\auth');

		$this->db = $this->new_dbal();

		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();

		$this->language = $this->getMockBuilder('\phpbb\language\language')
			->disableOriginalConstructor()
			->getMock();
		$this->language->method('lang')
			->will($this->returnArgument(0));

		$this->user = $this->getMock('\phpbb\user', array(), array(
			new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)),
			'\phpbb\datetime'
		));
	}

	public function set_listener($user_id = 1)
	{
		$this->user->data['user_id'] = $user_id;
		$this->listener = new \anavaro\eventmedals\event\main_listener(
			$this->auth,
			$this->db,
			$this->template,
			$this->user,
			$this->language,
			'/',
			'php',
			'phpbb_'
		);
	}

	public function test_getSubscribedEvents()
	{
		$this->assertEquals(array(
			'core.permissions',
			'core.memberlist_prepare_profile_data',
			'core.user_setup',
			'core.viewtopic_modify_post_row'
		), array_keys(\anavaro\eventmedals\event\main_listener::getSubscribedEvents()));
	}

	/**
	 *
	 * Test prepare medals
	 */
	public function test_prepare_medals()
	{
		$this->set_listener();
		$this->template->expects($this->once())
			->method('assign_vars');

		$data = array(
			'profile_event_show' => 1,
			'user_id' => 2
		);
		$event_data = array('data');
		$event = new \phpbb\event\data(compact($event_data));
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.memberlist_prepare_profile_data', array($this->listener, 'prepare_medals'));
		$dispatcher->dispatch('core.memberlist_prepare_profile_data', $event);
	}

}