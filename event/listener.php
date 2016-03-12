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
	 * @var \part3\topicprefixes\core\topicprefixes
	 */
	private $topicprefixes;

	/**
	 * listener constructor.
	 *
	 * @param \part3\topicprefixes\core\topicprefixes $topicprefixes
	 * @param \phpbb\auth\auth                        $auth
	 * @param \phpbb\db\driver\driver_interface       $db
	 * @param \phpbb\request\request                  $request
	 * @param \phpbb\template\template                $template
	 * @param \phpbb\user                             $user
	 */
	public function __construct(
		\part3\topicprefixes\core\topicprefixes $topicprefixes,
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user)
	{
		$this->auth = $auth;
		$this->auth = $auth;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->topicprefixes = $topicprefixes;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.posting_modify_submission_errors'		    => 'posting_modify_submission_errors',
			'core.posting_modify_submit_post_before'	    => 'posting_modify_submit_post_before',
			'core.posting_modify_template_vars'			    => 'posting_modify_template_vars',

			'core.search_modify_tpl_ary'		            => 'modify_topicrow_tpl_ary',
			'core.viewforum_modify_topicrow'			    => 'modify_topicrow_tpl_ary',
			'core.mcp_view_forum_modify_topicrow'		    => 'modify_topicrow_tpl_ary',

			'core.mcp_topic_review_modify_row'				=> 'mcp_topic_review_modify_row',

			'core.submit_post_modify_sql_data'	            => 'submit_post_modify_sql_data',
			'core.user_setup'	                            => 'user_setup',

			'core.viewtopic_assign_template_vars_before'	=> 'viewtopic_assign_template_vars_before',
			'core.viewtopic_modify_page_title'				=> 'viewtopic_modify_page_title',
			'core.viewtopic_modify_post_row'				=> 'viewtopic_modify_post_row',

			'core.acp_manage_forums_display_form'			=> 'acp_manage_forums_display_form',
			'core.acp_manage_forums_update_data_before'		=> 'acp_manage_forums_update_data_before',
		);
	}

	/**
	 * Allow changing the query used to search for posts by author in fulltext_native
	 * @param $event
	 */
	public function posting_modify_submission_errors($event)
	{
		$this->topicprefixes->posting_modify_submission_errors($event);
	}

	/**
	 * This event allows you to define errors before the post action is performed
	 * @param $event
	 */
	public function posting_modify_submit_post_before($event)
	{
		$this->topicprefixes->posting_modify_submit_post_before($event);
	}

	public function posting_modify_template_vars($event)
	{
		$this->topicprefixes->posting_modify_template_vars($event);
	}

	/**
	 *  handler for events
	 *  search_modify_tpl_ary = Modify the topic data before it is assigned to the template
	 *  viewforum_modify_topicrow = Modify the topic data before it is assigned to the template
	 *  mcp_view_forum_modify_topicrow = Modify the topic data before it is assigned to the template in MCP
	 *
	 * @param $event
	 */
	public function modify_topicrow_tpl_ary($event)
	{
		$this->topicprefixes->modify_topicrow_tpl_ary($event);
	}


	/**
	 * Post prefixes in MCP topic view
	 * mcp_topic_review_modify_row = Event to modify the template data block for topic reviews in the MCP
	 * @param $event
	 */
	public function mcp_topic_review_modify_row($event)
	{
		$this->topicprefixes->mcp_topic_review_modify_row($event);
	}


	/**
	 * @param $event
	 */
	public function submit_post_modify_sql_data($event)
	{
		$this->topicprefixes->submit_post_modify_sql_data($event);
	}

	public function user_setup($event)
	{
		$this->topicprefixes->user_setup($event);
	}

	/**
	 * Topic title in viewtopic
	 * @param $event
	 */
	public function viewtopic_assign_template_vars_before($event)
	{
		$this->topicprefixes->viewtopic_assign_template_vars_before($event);
	}

	// Post prefixes in viewtopic
	public function viewtopic_modify_post_row($event)
	{
		$this->topicprefixes->viewtopic_modify_post_row($event);
	}

	// event handler to modify the page title of the viewtopic page
	public function viewtopic_modify_page_title($event)
	{
		$this->topicprefixes->viewtopic_modify_page_title($event);
	}

	// ACP CONFIG
	public function acp_manage_forums_display_form($event)
	{
		$this->topicprefixes->acp_manage_forums_display_form($event);
	}

	public function acp_manage_forums_update_data_before($event)
	{
		$this->topicprefixes->acp_manage_forums_update_data_before($event);
	}

}
