<?php
/**
 * The master permissions class. Has methods to check if the user can or can't do something.
 * The constructor checks for the class and user-specific permissions. The second overrides the first.
 * The scoreboard-specific permissions work as folows:
 * - If the user can do something globaly, scoreboard-specific permissions are ignored, unless the scoreboard has ignore_global_permissions set to true
 * - If the user can't do something globaly, then it checks for scoreboard-specific permissions
 * 
 * @author Windsdon
 *
 */
class Permission {
	/**
	 * Creates the permission object, taking into account the user-specific permissions
	 * Also loads the scoreboard-specific permissions for that user.
	 *
	 * @param string $user
	 *        	The user ID
	 */
	public function __construct($user) {
	}
}

?>