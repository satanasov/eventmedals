<?php

/**
*
* newspage [Bulgarian]
*
* @package language
* @version $Id$
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_PHPBB'))
{
		exit;
}
if (empty($lang) || !is_array($lang))
{
		$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_EVENT_MEDALS'	=>	'Event medals',
	'ACP_EVENT_MEDALS_ADD'	=>	'Add event medals',
	'ACP_EVENT_MEDALS_EDIT'	=>	'Edit event medals',
	'ACP_EVENT_MEDALS_GRP'	=>	'Event medals',

	'MEDALS_TITLE'	=> 'Event medals',
	'MEDALS_ADD_SCRIPT'	=>	'Event medals addition script',
	'MEDALS_ADD_STEP_ONE'	=> 'STEP 1: User list',
	'MEDALS_USERS_LIST'	=>	'User list',
	'MEDALS_USERS_LIST_HINT'	=> 'Add every username on new line.',
	'MEDALS_ADD_STEP_TWO'	=>	'STEP 2: Event medals type.',
	'WARNING'	=>	'Warning!',
	'INFO'	=>	'Information',
	'SUCCESS_ADD_INFO'	=>	'Event medals are added successfully',
	'BACK'	=> '« Back to previous page',
	'USER'	=>	'User',
	'EVENT'	=>	'Event',
	'NOT_EXISTENTS'	=>	'does not exist',
	'CORRECT_WARNING_ONE'	=>	'USE BACK button to go back and change username or add user manualy.',
	'CORRECT_WARNING_THREE'	=>	'USE BACK button to go back and correct.',
	'MEDAL_TYPE'	=>	'Event medal type:',
	'MEDAL_TYPE_ONE'	=> 'Organizer',
	'MEDAL_TYPE_TWO'	=> 'Participant',
	'MEDAL_TYPE_THREE'	=> 'Ran away',
	'MEDAL_TYPE_FOUR'	=> 'NOT WELCOMED!',
	'MEDALS_ADD_STEP_THREE'	=> 'STEP 3: Dates and custom images.',

	'MEDALS_EDIT_SCRIPT'	=>	'Script to edit events!',
	'MEDALS_EDIT_STEP_ONE'	=>	'Step 1: Choose user or event you want to edit!',
	'MEDALS_USER_SELECT'	=>	'Choose user',
	'MEDALS_EVENT_SELECT'	=>	'Choose event',
	'MEDALS_SELECT_TYPE'	=>	'What will you change?',
	'MEDALS_SELECT_TYPE_EXPLENATION'	=>	'Choose ehat kind of change you are doing - event or user.',
	'MEDALS_EDIT_STEP_TWO_EVENT'	=>	'Step 2: Choose change for this event.',
	'MEDALS_EDIT_STEP_TWO_USER'	=>	'Step 2: Choose changes for user.',
	'MEDAL_DELETE'	=>	'Remove event medal',
	'SUCCESS_EDIT_INFO'	=>	'Event medals changed successfully!',

	'DATE'	=> 'Date:',
	'M_JAN'	=>	'January',
	'M_FEB'	=>	'February',
	'M_MAR'	=>	'March',
	'M_APR'	=>	'April',
	'M_MAY'	=>	'May',
	'M_JUN'	=>	'June',
	'M_JUL'	=>	'July',
	'M_AUG'	=>	'August',
	'M_SEP'	=>	'September',
	'M_OCT'	=>	'October',
	'M_NOV'	=>	'November',
	'M_DEC'	=>	'December',
	'TOPIC_NUMBER'	=>	'Topic ID:',
	'IMAGE_PATH'	=> 'Custom images path:',

	'ERR_DAY_NOT_NUM'	=>	'You know that the day should be a number, right?',
	'ERR_DAY_NOT_IN_RANGE'	=>	'There is no such day in the month!',
	'ERR_YEAR_NOT_NUM'	=> 'Not numeral Year?',
	'ERR_DATE_ERR'	=> 'The date is wrong ...',
	'ERR_TOPIC_ERR'	=> 'Nope! There is no Topic ID like the one you\'ve provided',
	'ERR_DUPLICATE_MEDAL'	=> 'There is allredy such medal. Go back and check the list!',
	'ERR_NO_MEDALS'	=> 'There are no medals. Please add some so you can edit them!',
	'ERR_NO_USER'	=> 'User does not exist',
	'ERR_USER_NO_MEDALS'	=> 'Selected user has no medals',

	'UCP_EVENT_CONTROL'	=>	'Event control',
	'UCP_PROFILE_MEDALS_CONTROL'	=> 'Event medals in profile',
	'UCP_PROFILE_MEDALS_EXPLAIN'	=> 'Who can see the event medals',

	'NONE'	=>	'No one',
	'NOT_ENEMY'	=>	'All except enemies',
	'SPECIAL_FRIENDS'	=> 'Special friends',

	'UCP_PROFILE_ACC_ERROR' => 'You don\'t have access to see this user\'s event medals',
	'UCP_PROFILE_CONTROL_ERROR'	=> 'You are not authorized to change event medals',

	'ACL_U_EVENT_ADD'	=> 'Add event medals',
	'ACL_U_EVENT_MODIFY'	=> 'Modify event medals',

));
