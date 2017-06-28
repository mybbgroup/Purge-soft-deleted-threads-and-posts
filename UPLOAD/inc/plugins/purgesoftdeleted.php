<?php

/**
 *	Purge soft deleted posts and threads
 *
 *	@author Eldenroot <http://community.mybb.com/user-84065.html>
 *	@GitHub <https://github.com/Cu8eR/MyBB_Purge-soft-deleted-threads-and-posts>
 */
 
// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

// Plugin info
function purgesoftdeleted_info()
{
    return array(
        "name"          => "Purge soft deleted posts and threads",
        "description"   => "Automatically purges soft deleted posts and threads",
        "website"       => "http://community.mybb.com/user-84065.html",
        "author"        => "Eldenroot",
        "authorsite"    => "http://community.mybb.com/user-84065.html",
        "version"       => "1.0",
        "codename"      => "purgesoftdeleted",
        "compatibility" => "18*"
    );
}

// Plugin activate
function purgesoftdeleted_activate()
{
	global $db, $cache;
	
		// Create task - Purge soft deleted
		// Have we already added this task?
			$query = $db->simple_select('tasks', 'tid', "file='purgesoftdeleted'", array('limit' => '1'));
			if($db->num_rows($query) == 0)
			{
				// Load tasks function needed to run a task and add nextrun time
					require_once MYBB_ROOT."/inc/functions_task.php";
				
				// If not then add
					$new_task = array(
						"title" => "Purge soft deleted posts and threads",
						"description" => "Checks for soft deleted posts and threads and purges them automatically.",
						"file" => "purgesoftdeleted",
						"minute" => '2',
						"hour" => '0',
						"day" => '*',
						"month" => '*',
						"weekday" => '*',
						"enabled" => '1',
						"logging" => '1',
					);
        
			$new_task['nextrun'] = fetch_next_run($new_task);
			$tid = $db->insert_query("tasks", $new_task);
		
		// Update the task and run it right now
			$cache->update_tasks();
			run_task($tid);
			}
}

// Plugin deactivate
function purgesoftdeleted_deactivate()
{
	global $db, $mybb;
    
		// Remove task from task manager
			$db->delete_query('tasks', 'file=\'purgesoftdeleted\''); // Delete Purge soft deleted task
	
		// Rebuild settings
			rebuild_settings();
}