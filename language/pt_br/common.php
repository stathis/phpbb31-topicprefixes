<?php
/**
*
* Extension - Topic Prefixes
*
* @copyright (c) 2016 PART3 <http://part3.org>
* @license MIT License
* Brazilian Portuguese translation by eunaumtenhoid (c) 2017 [ver 2.0.0] (https://github.com/phpBBTraducoes)
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'TOPIC_DESC'	=> 'Descrição do tópico',
	'TOPIC_PREFIX'	=> 'Prefixo do tópico',
	'PREFIX_REQUIRED' => 'Prefixo é necessário',
	'PREFIX_INVALID' => 'HAX?',

	'ACP_PREFIXES' => 'Prefixos',
	'ACP_PREFIXES_EXPLAIN' => 'Quais prefixos estão disponíveis para este fórum',
	'ACP_PREFIX_REQUIRED' => 'Exigir prefixo',
	'ACP_PREFIX_REQ_EXPLAIN' => 'Se um prefixo deve ser especificado para postar neste fórum',
));
