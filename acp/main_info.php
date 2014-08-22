<?php

/**
*
* @package Anavaro.com Event medals
* @copyright (c) 2013 Lucifer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/

namespace anavaro\eventmedals\acp;

if (!defined('IN_PHPBB'))
{
    exit;
}



class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\anavaro\eventmedals\acp\main_module',
			'title'		=> 'Медали от събития',
			'version'	=> '0.0.9',
			'modes'		=> array(
				'add'		=> array(
									'title' => 'ACP_EVENT_MEDALS_ADD',
									'auth' 		=> 'ext_anavaro/eventmedals && acl_a_board', 
									'cat'		=> array('ACP_EVENT_MEDALS')
									),
				'edit'		=> array(
									'title' => 'ACP_EVENT_MEDALS_EDIT', 
									'auth' 		=> 'ext_anavaro/eventmedals && acl_a_board', 
									'cat'		=> array('ACP_EVENT_MEDALS')
									),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>
