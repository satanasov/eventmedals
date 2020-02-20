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

	public function setUp() : void
	{
		parent::setUp();

		global $phpbb_root_path, $phpEx;

		$this->auth = $this->getMockBuilder('\phpbb\auth\auth')
			->disableOriginalConstructor()
			->getMock();

		$this->db = $this->new_dbal();

		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->disableOriginalConstructor()
			->getMock();

		$this->language = $this->getMockBuilder('\phpbb\language\language')
			->disableOriginalConstructor()
			->getMock();
		$this->language->method('lang')
			->will($this->returnArgument(0));

		$this->user = $this->getMockBuilder('\phpbb\user')
			->setConstructorArgs(array(
				new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx)),
				'\phpbb\datetime'
			))
			->getMock();
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

	public function prepare_medals_data()
	{
		return array(
			'default'	=> array( // We are no friends and seting is default
				1, // User_id
				0, // Is admin
				4, // Profile we review
				2, // Show medals state
				1,  // Expected result
				array(
					array(
						'MEDALS_TITLE'	=> 'MEDALS_TITLE',
						'MEDALS' => '<a href="/viewtopic.php?t=1"><img src="ext/anavaro/eventmedals/images/gold.gif" alt="MEDAL_TYPE_TWO[01 January 1970]" title="MEDAL_TYPE_TWO[01 January 1970]"></a> '
					),
				)
			),
			'admin_override'=> array(
				1,
				1,
				4,
				0,
				3,
				array(
					array(
						'MEDALS_ADD' => 1,
						'MEDALS_EVENT_ADD_URL' => '/app.php/eventmedals/add/4'
					),
					array(
						'MEDALS_MODIFY' => 1,
						'MEDALS_EVENT_EDIT_URL' => '/app.php/eventmedals/edit/4'
					),
					array(
						'MEDALS_TITLE'	=> 'MEDALS_TITLE',
						'MEDALS' => '<a href="/viewtopic.php?t=1"><img src="ext/anavaro/eventmedals/images/gold.gif" alt="MEDAL_TYPE_TWO[01 January 1970]" title="MEDAL_TYPE_TWO[01 January 1970]"></a> '
					)
				)
			),
			'do_not_show'=> array(
				1,
				0,
				4,
				0,
				1,
				array(
					array(
						'MEDALS_TITLE' => 'MEDALS_TITLE',
						'MEDALS' => 'UCP_PROFILE_ACC_ERROR'
					)
				)
			),
			'do_not_show_to_enemy_yes'=> array(
				1,
				0,
				3,
				1,
				1,
				array(
					array(
						'MEDALS_TITLE' => 'MEDALS_TITLE',
						'MEDALS' => 'UCP_PROFILE_ACC_ERROR'
					)
				)
			),
			'do_not_show_to_enemy_no'=> array(
				2,
				0,
				3,
				1,
				1,
				array(
					array(
						'MEDALS_TITLE' => 'MEDALS_TITLE',
						'MEDALS' => '<a href="/viewtopic.php?t=1"><img src="ext/anavaro/eventmedals/images/gold.gif" alt="MEDAL_TYPE_TWO[01 January 1970]" title="MEDAL_TYPE_TWO[01 January 1970]"></a> '
					)
				)
			),
			'do_not_show_to_enemy_yes_admin_override'=> array(
				1,
				1,
				3,
				1,
				3,
				array(
					array(
						'MEDALS_ADD' => 1,
						'MEDALS_EVENT_ADD_URL' => '/app.php/eventmedals/add/3'
					),
					array(
						'MEDALS_MODIFY' => 1,
						'MEDALS_EVENT_EDIT_URL' => '/app.php/eventmedals/edit/3'
					),
					array(
						'MEDALS_TITLE'	=> 'MEDALS_TITLE',
						'MEDALS' => '<a href="/viewtopic.php?t=1"><img src="ext/anavaro/eventmedals/images/gold.gif" alt="MEDAL_TYPE_TWO[01 January 1970]" title="MEDAL_TYPE_TWO[01 January 1970]"></a> '
					)
				),
			),
			'show_only_friends_yes'=> array(
				1,
				0,
				2,
				3,
				1,
				array(
					array(
						'MEDALS_TITLE' => 'MEDALS_TITLE',
						'MEDALS' => '<a href="/viewtopic.php?t=1"><img src="ext/anavaro/eventmedals/images/gold.gif" alt="MEDAL_TYPE_TWO[01 January 1970]" title="MEDAL_TYPE_TWO[01 January 1970]"></a> '
					)
				)
			),
			'show_only_friends_no'=> array(
				3,
				0,
				2,
				3,
				1,
				array(
					array(
						'MEDALS_TITLE' => 'MEDALS_TITLE',
						'MEDALS' => 'UCP_PROFILE_ACC_ERROR'
					)
				)
			),
			'friend_no_medals'=> array(
				1,
				0,
				5,
				3,
				1,
				array(
					array(
						'MEDALS_TITLE' => 'MEDALS_TITLE',
						'MEDALS' => ''
					)
				)
			),
		);
	}

	/**
	 * @dataProvider prepare_medals_data
	 *
	 * Test prepare medals
	 * @param $user_id
	 * @param $is_admin
	 * @param $profile_id
	 * @param $medals_state
	 * @param $expected
	 * @param $specific
	 */
	public function test_prepare_medals($user_id, $is_admin, $profile_id, $medals_state, $expected, $specific)
	{
		$this->set_listener((int) $user_id);
		$this->auth->method('acl_get')
			->will($this->returnValue($is_admin));

		$this->template->expects($this->exactly((int) $expected))
			->method('assign_vars');

		foreach ($specific as $id => $var)
		{
			$this->template->expects($this->at($id))
				->method('assign_vars')
				->with(
					$var
				);
		}
		$data = array(
			'profile_event_show' => (int) $medals_state,
			'user_id' => (int) $profile_id
		);
		$event_data = array('data');
		$event = new \phpbb\event\data(compact($event_data));
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
		$dispatcher->addListener('core.memberlist_prepare_profile_data', array($this->listener, 'prepare_medals'));
		$dispatcher->dispatch('core.memberlist_prepare_profile_data', $event);
	}

}
