<?php
/**
*
* Extension - Topic Prefixes
*
* @copyright (c) 2016 PART3 <http://part3.org>
* @license MIT License
*
*/

namespace part3\topicprefixes;

/**
* This ext class is optional and can be omitted if left empty.
* However you can add special (un)installation commands in the
* methods enable_step(), disable_step() and purge_step(). As it is,
* these methods are defined in \phpbb\extension\base, which this
* class extends, but you can overwrite them to give special
* instructions for those cases.
*/
class ext extends \phpbb\extension\base
{
	/** @var string Require phpBB 3.1.7 due to the use of new events */
	const PHPBB_MIN_VERSION = '3.1.7';

	/**
	* Check whether or not the extension can be enabled.
	* The current phpBB version should meet or exceed
	* the minimum version required by this extension:
	*
	* @return bool
	* @access public
	*/
	public function is_enableable()
	{
		$config = $this->container->get('config');
		return phpbb_version_compare($config['version'], self::PHPBB_MIN_VERSION, '>=');
	}
}
