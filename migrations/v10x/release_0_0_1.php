<?php
/**
*
* Extension - Topic Prefixes
*
* @copyright (c) 2015 PART3 <http://part3.org>
* @license MIT License
*
*/

namespace part3\topicprefixes\migrations\v10x;

class release_0_0_1 extends \phpbb\db\migration\migration
{
	/**
	* Add or update database schema
	*/
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'topics'	=> array(
					'topic_prefix'	=> array('VCHAR_UNI', ''),
				),
				$this->table_prefix . 'forums'	=> array(
					'forum_topic_prefixes'	=> array('TEXT', ''),
					'forum_topic_prefix_required' => array('BOOL', 0),
				),
			),
		);
	}

	/**
	* Add or update data in the database
	*/
	// public function update_data()
	// {
	// 	return array(
	// 		// Add permissions
	// 		array('permission.add', array('f_topic_desc', false)),

	// 		// Set permissions
	// 		array('permission.permission_set', array('ROLE_FORUM_FULL', 'f_topic_desc')),
	// 	);
	// }

	/**
	* Drop database schema
	*/
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'topics'	=> array(
					'topic_prefix',
				),
				$this->table_prefix . 'forums'	=> array(
					'forum_topic_prefixes',
					'forum_topic_prefix_required',
				),
			),
		);
	}
}
