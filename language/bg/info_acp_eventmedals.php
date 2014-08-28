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
	'ACP_EVENT_MEDALS'	=>	'Медали',
	'ACP_EVENT_MEDALS_GRP'	=>	'Медали',
	'ACP_EVENT_MEDALS_ADD'	=>	'Добави медали',
	'ACP_EVENT_MEDALS_EDIT'	=>	'Промени медали',

	'MEDALS_TITLE'	=> 'Медали',
	'MEDALS_ADD_SCRIPT'	=>	'Скрип за добавяне на медали',
	'MEDALS_ADD_STEP_ONE'	=> 'СТЪПКА 1: Добавете списък с потребители',
	'MEDALS_USERS_LIST'	=>	'Списък на потребителите',
	'MEDALS_USERS_LIST_HINT'	=> 'Въведете всяко потребителско име на нов ред.',
	'MEDALS_ADD_STEP_TWO'	=>	'СТЪПКА 2: Тип на медалите.',
	'WARNING'	=>	'ВНИМАНИЕ!',
	'INFO'	=>	'Информация',
	'SUCCESS_ADD_INFO'	=>	'Медалите са добавени успешно',
	'BACK'	=> '« Обратно към предишната страница',
	'USER'	=>	'Потребител',
	'EVENT'	=>	'Събитие',
	'NOT_EXISTENTS'	=>	'не съществува',
	'CORRECT_WARNING_ONE'	=>	'ИЗПОЛЗВАЙТЕ BACK бутона за да се върнете и коригирате или добавете медалите на тези потребители по-късно.',
	'CORRECT_WARNING_THREE'	=>	'ИЗПОЛЗВАЙТЕ BACK бутона за да се върнете и коригирате!',
	'MEDAL_TYPE'	=>	'Тип медал:',
	'MEDAL_TYPE_ONE'	=> 'Организатор',
	'MEDAL_TYPE_TWO'	=> 'Участник',
	'MEDAL_TYPE_THREE'	=> 'Избягал',
	'MEDAL_TYPE_FOUR'	=> 'НЕ ЖЕЛАН!',
	'MEDALS_ADD_STEP_THREE'	=> 'СТЪПКА 3: Дати и картинки.',

	'MEDALS_EDIT_SCRIPT'	=>	'Скрипт за промяна на медалите!',
	'MEDALS_EDIT_STEP_ONE'	=>	'СТЪПКА 1: Изберете потребител или среща за които ще променяте медали!',
	'MEDALS_USER_SELECT'	=>	'Изберете потребител',
	'MEDALS_EVENT_SELECT'	=>	'Изберете събитие',
	'MEDALS_SELECT_TYPE'	=>	'Кое ще променяме?',
	'MEDALS_SELECT_TYPE_EXPLENATION'	=>	'Изберете дали ще променяте медалите за потребител или събитие.',
	'MEDALS_EDIT_STEP_TWO_EVENT'	=>	'СТЪПКА 2: Изберете промените за това събитие.',
	'MEDALS_EDIT_STEP_TWO_USER'	=>	'СТЪПКА 2: Изберете промените за този потребител.',
	'MEDAL_DELETE'	=>	'Премахни медала',
	'SUCCESS_EDIT_INFO'	=>	'Медалите са променени успешно',

	'DATE'	=> 'Дата:',
	'M_JAN'	=>	'Януари',
	'M_FEB'	=>	'Февруари',
	'M_MAR'	=>	'Март',
	'M_APR'	=>	'Април',
	'M_MAY'	=>	'Май',
	'M_JUN'	=>	'Юни',
	'M_JUL'	=>	'Юли',
	'M_AUG'	=>	'Август',
	'M_SEP'	=>	'Септември',
	'M_OCT'	=>	'Октомври',
	'M_NOV'	=>	'Ноември',
	'M_DEC'	=>	'Декември',
	'TOPIC_NUMBER'	=> 'Номер на тема:',
	'IMAGE_PATH'	=> 'Път към картинка:',

	'ERR_DAY_NOT_NUM'	=>	'Деня трябва да е число, нали знаеш?',
	'ERR_DAY_NOT_IN_RANGE'	=>	'Е не може да си написал такова число!',
	'ERR_YEAR_NOT_NUM'	=> 'Е не може годината да не е число!',
	'ERR_DATE_ERR'	=> 'Нещо си объркал в датата ...',
	'ERR_TOPIC_ERR'	=> 'А не ... намери си темата в която е срещата!',

	'UCP_EVENT_CONTROL'	=>	'Настройка на медали',
	'UCP_PROFILE_MEDALS_CONTROL'	=> 'Медали в профилa',
	'UCP_PROFILE_MEDALS_EXPLAIN'	=> 'Кой може да вижда медалите в профила ви',

	'NONE'	=>	'Никой',
	'NOT_ENEMY'	=>	'Всички без врагове',
	'SPECIAL_FRIENDS'	=> 'Специални приятели',

	'UCP_PROFILE_ACC_ERROR' => 'Нямате права да виждате медалите на този потребител',
	'UCP_PROFILE_CONTROL_ERROR'	=> 'Не сте оторизиран да променяте медалите на потребители',

	'ACL_U_EVENT_ADD'	=> 'Може да слага медали',
	'ACL_U_EVENT_MODIFY'	=> 'Може да променя медали',

));
