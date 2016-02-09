<?php
/**
*
* Extension - Topic Prefixes
*
* @copyright (c) 2016 PART3 <http://part3.org>
* @license MIT License
*
*/

namespace part3\topicprefixes\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	private $first_post_id;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth						$auth			Authentication object
	* @param \phpbb\db\driver\driver_interface		$db				Database object
	* @param \phpbb\request\request					$request		Request object
	* @param \phpbb\template\template				$template		Template object
	* @param \phpbb\user							$user			User object
	* @access public
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	static public function getSubscribedEvents()
	{
		return array(
			// 'core.permissions'							=> 'permissions',
			'core.posting_modify_submission_errors'		=> 'posting_modify_submission_errors',
			'core.posting_modify_submit_post_before'	=> 'posting_modify_submit_post_before',
			'core.posting_modify_template_vars'			=> 'posting_modify_template_vars',

			'core.search_modify_tpl_ary'		=> 'modify_topicrow_tpl_ary',
			'core.submit_post_modify_sql_data'	=> 'submit_post_modify_sql_data',

			'core.user_setup'	=> 'user_setup',

			'core.viewforum_modify_topicrow'				=> 'modify_topicrow_tpl_ary',
			'core.viewtopic_assign_template_vars_before'	=> 'viewtopic_assign_template_vars_before',

			'core.viewtopic_modify_page_title'				=> 'viewtopic_modify_page_title',

			'core.viewtopic_modify_post_row'				=> 'viewtopic_modify_post_row',

			'core.acp_manage_forums_display_form'			=> 'acp_manage_forums_display_form',
			'core.acp_manage_forums_update_data_before'		=> 'acp_manage_forums_update_data_before',
		);
	}

	// Are we looking at the topic's first post?
	private function is_first_post($topic_id, $post_id, $mode) {
		if ($mode == 'reply') {
			return false;
		}
		if ($mode == 'edit')
		{
			if (!isset($this->first_post_id))
			{
				$sql = 'SELECT topic_first_post_id
					FROM ' . TOPICS_TABLE . '
					WHERE topic_id = ' . $topic_id;
				$result = $this->db->sql_query($sql);
				$this->first_post_id = $this->db->sql_fetchfield('topic_first_post_id');
				$this->db->sql_freeresult($result);
			}

			if ($post_id != $this->first_post_id)
			{
				return false;
			}
		}
		return true;
	}

	public function posting_modify_submission_errors($event)
	{
		// Don't throw errors if not first post of the topic
		if (!$this->is_first_post($event['topic_id'], $event['post_id'], $event['mode'])) {
			return;
		}

		// Check if prefix is required and given.
		if ($event['post_data']['forum_topic_prefix_required'] && empty($this->request->variable('topic_prefix', '', true))) {
			$event['error'] = array_merge($event['error'], array(
				$this->user->lang('PREFIX_REQUIRED'),
			));
			return;
		}

		// Has the user given a valid prefix?
		if (!in_array($this->request->variable('topic_prefix', '', true), explode(';', $event['post_data']['forum_topic_prefixes']))) {
			$event['error'] = array_merge($event['error'], array(
				$this->user->lang('PREFIX_INVALID'),
			));
		}
	}

	public function posting_modify_submit_post_before($event)
	{
		$data = $event['data'];

		if ($this->is_first_post($event['topic_id'], $event['post_id'], $event['mode'])) {
			$data['topic_prefix'] = $this->request->variable('topic_prefix', '', true);
		}

		$event['data'] = $data;
	}

	public function submit_post_modify_sql_data($event)
	{
		$data = $event['data'];
		$sql_data = $event['sql_data'];

		// Are we creating a new topic or editting the first post?
		if (in_array($event['post_mode'], array('edit_first_post', 'edit_topic', 'post')) {
			$sql_data[TOPICS_TABLE]['sql']['topic_prefix'] = $data['topic_prefix'];
		}

		$event['sql_data'] = $sql_data;
	}

	public function posting_modify_template_vars($event)
	{
		// No need to load extra stuff if not first post
		if (!$this->is_first_post($event['topic_id'], $event['post_id'], $event['mode'])) {
			return;
		}

		$post_data = $event['post_data'];
		$page_data = $event['page_data'];

		$page_data['HAS_PREFIXES'] = !empty($event['post_data']['forum_topic_prefixes']);
		$page_data['TOPIC_PREFIX'] = (!empty($post_data['topic_prefix'])) ? $post_data['topic_prefix'] : '';
		foreach (explode(';', $event['post_data']['forum_topic_prefixes']) as $prefix) {
			$this->template->assign_block_vars('prefixes', array(
				'CUR' => $prefix,
			));
		}

		$event['page_data'] = $page_data;
	}

	public function user_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'part3/topicprefixes',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function modify_topicrow_tpl_ary($event)
	{
		$block = $event['topic_row'] ? 'topic_row' : 'tpl_ary';
		$event[$block] = $this->display_topic_prefix($event['row'], $event[$block]);
	}

	// Post prefixes in viewtopic
	public function viewtopic_modify_post_row($event)
	{
		if (strlen($event['topic_data']['topic_prefix']) > 0)
		{
			$event['post_row'] = array_merge($event['post_row'], array(
				'POST_SUBJECT'	=> ((!empty($event['topic_data']['topic_prefix'])) ? '[' . $event['topic_data']['topic_prefix'] . '] ' : '')
								 . $event['post_row']['POST_SUBJECT'],
			));
		}
	}

	// Topic title in viewtopic
	public function viewtopic_assign_template_vars_before($event)
	{
		$topic_data = $event['topic_data'];

		$this->template->assign_vars(array(
			// 'TOPIC_DESC'	=> $topic_data['topic_desc'],
			'TOPIC_HAS_PREFIX' => !empty($topic_data['topic_prefix']),
			'TOPIC_PREFIX'	=> $topic_data['topic_prefix'],
		));
	}

	// Topic in viewforum
	private function display_topic_prefix($row, $block)
	{
		$block = array_merge($block, array(
			'TOPIC_TITLE'	=> ((!empty($row['topic_prefix'])) ? '[' . $row['topic_prefix'] . '] ' : '') . $row['topic_title'],
			// 'TOPIC_DESC'	=> $row['topic_desc'],
		));

		return $block;
	}

	// Page title in viewtopic
	public function viewtopic_modify_page_title($event)
	{
		$topic_data = $event['topic_data'];

		$event['page_title'] = ((!empty($topic_data['topic_prefix'])) ? '[' . $topic_data['topic_prefix'] . '] ' : '') . $topic_data['topic_title'];
	}


	// ACP CONFIG
	public function acp_manage_forums_display_form($event)
	{
		$forum_data = $event['forum_data'];

		$this->template->assign_vars(array(
			'FORUM_PREFIX_LIST'	=> str_replace(';', "\r\n", $forum_data['forum_topic_prefixes']),
			'FORUM_PREFIX_REQUIRED'	=> $forum_data['forum_topic_prefix_required'],
		));
	}

	public function acp_manage_forums_update_data_before($event)
	{
		$sql_data = $event['forum_data_sql'];

		$sql_data['forum_topic_prefixes'] = str_replace("\n", ';', str_replace("\r", '', $this->request->variable('forum_prefixes', '', true)));
		$sql_data['forum_topic_prefix_required'] = ($this->request->variable('forum_prefix_required', '', true)) ? 1 : 0;

		$event['forum_data_sql'] = $sql_data;
	}
}
