<?php

/**
 *	Purge soft deleted posts and threads
 *
 *	@author Eldenroot <http://community.mybb.com/user-84065.html>
 *	@GitHub <https://github.com/Cu8eR/MyBB_Purge-soft-deleted-threads-and-posts>
 *	@version 1.0
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

// Purge soft deleted
function task_purgesoftdeleted($task)
{
	global $db;

	// Soft deleted posts and threads older than x seconds will be purged	
	$ptime = 3*24*3600; // 3 days for soft deleted posts
	$ttime = 5*24*3600; // 5 days for soft deleted threads

	if ($db->delete_query("posts", "(visible = -1) AND (dateline < ".(TIME_NOW-$ptime).")")) {
    add_task_log($task, "Soft deleted posts were purged successfully!"); 
  } else {
		add_task_log($task, "Something went wrong while cleaning up the soft deleted posts...");
  }
		
	if ($db->delete_query("threads", "(visible = -1) AND (dateline < ".(TIME_NOW-$ttime).")")) {
    add_task_log($task, "Soft deleted threads were purged successfully!");
  } else {
		add_task_log($task, "Something went wrong while cleaning up the soft deleted threads...");
  }

	// Optimize DB table
	if ($db->query("OPTIMIZE TABLE `".TABLE_PREFIX."_posts`, `".TABLE_PREFIX."_threads`, `".TABLE_PREFIX."_reportedposts`")) {
    add_task_log($task, "Purge soft deleted - posts/threads tables were optimized successfully!");
	} else {
		add_task_log($task, "Purge soft deleted - posts/threads tables were NOT optimized! Something went wrong...");
	}
}
