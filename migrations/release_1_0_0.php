<?php
/**
*
* @package migration
* @copyright (c) 2012 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License v2
*
*/

namespace anavaro\eventmedals\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['event_medals_version']) && version_compare($this->config['event_medals_version'], '1.0.0', '>=');
	}
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_data()
	{
		return array(

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_EVENT_MEDALS_GRP'
			)),
			array('module.add', array(
				'acp',
				'ACP_EVENT_MEDALS_GRP',
				array(
					'module_basename'	=> '\anavaro\eventmedals\acp\main_module',
					'module_mode'		=> array('add', 'edit'),
					'module_auth'	=> 'ext_anavaro/eventmedals && acl_a_board',
				)
			)),
			array('module.add', array(
				'ucp',
				'UCP_PROFILE',
				array(
					'module_basename'	=> '\anavaro\eventmedals\ucp\ucp_medals_module',
					'modiel_modes' => array('control'),
					'module_auth'	=> 'ext_anavaro/eventmedals',
				),

			)),

			array('config.add', array('event_medals_version', '1.0.0')),

			//setting permissions
			array('permission.add', array('u_event_add', true)),
			array('permission.add', array('u_event_modify', true)),

			// Set permissions
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'u_event_add', true)),
			array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'u_event_add', true)),
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'u_event_modify', true)),
			array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'u_event_modify', true)),
		);
	}

	//lets create the needed table
	public function update_schema()
	{
		return array(
			'add_tables'    => array(
				$this->table_prefix . 'event_medals'		=> array(
					'COLUMNS'		=> array(
						'owner_id'		=> array('UINT:8', 0),
						'type'		=> array('UINT:2', 1),
						'link'		=> array('UINT:8', 0),
						'date'		=> array('UINT:16', 0),
						'image'		=> array('VCHAR:128', 'none')
					),
					'PRIMARY_KEY'	=> 'oid, link',
				),
				$this->table_prefix . 'users_custom'		=> array(
					'COLUMNS'	=> array(
						'user_id'	=> array('UINT', 0),
					),
					'PRIMARY_KEY'    => 'user_id'
				),
			),
			'add_columns'	=> array(
				$this->table_prefix . 'users_custom'	=> array(
					'profile_event_show'    => array('UINT', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'		=> array(
				$this->table_prefix . 'users_custom'	=> array(
					'profile_event_show',
				)
			),
			'drop_tables'		=> array(
				$this->table_prefix . 'event_medals'
			),
		);
	}
}
