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

	public function test_request()
	{
		//create new user
		$this->create_user('testuser');
		$this->add_user_group('NEWLY_REGISTERED', array('testuser'));
	}
}