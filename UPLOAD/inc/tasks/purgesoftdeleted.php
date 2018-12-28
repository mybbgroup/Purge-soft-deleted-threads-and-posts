<?php

 /**
 * MyBB Purge soft deleted posts and threads - plugin for MyBB 1.8.x forum software
 * 
 * @package MyBB Plugin
 * @author MyBB Group - Eldenroot - <eldenroot@gmail.com>
 * @copyright 2018 MyBB Group <http://mybb.group>
 * @link <https://github.com/mybbgroup/MyBB_Purge-soft-deleted-threads-and-posts>
 * @license GPL-3.0
 * 
 */
  
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.
 * If not, see <http://www.gnu.org/licenses/>.
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
