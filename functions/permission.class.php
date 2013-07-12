<?php
include_once 'db_functions.php';
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
	
	public static $defaultPermissions = array(
		"global.post" => false,
		"global.ban" => false,
		"global.create" => true,
		"admin.accessPannel" => false,
	);
	
	public $permissions = NULL;
	
	/**
	 * Creates the permission object, taking into account the user-specific permissions
	 * Also loads the scoreboard-specific permissions for that user.
	 * Warning! This class does NOT saftify its inputs. Make sure that they are safe before sending!
	 *
	 * @param string $user
	 *        	The user ID
	 */
	public function __construct($user) {
		$globalPermissions = false;
		$userPermissions = false;
		if(isset(Database::$default)){
			if($result = Database::$default->query('SELECT classes.class_attributes, users.user_extra FROM classes, users WHERE users.user_class = classes.class_id')){
				if($result->numRows() == 1){
					$object = $result->getObject();
					//echo "<pre>Dumping user permissions:\n";
					//var_dump($object);
					//echo "</pre>";
					$globalAttributes = json_decode($object->class_attributes);
					$globalPermissions = $globalAttributes->permissions;
					if($object->user_extra){ //remember that user extra can be null (as in no custom extras)
						$userExtra = json_decode($object->user_extra);
						$userPermissions = $userExtra->permissions;
					}
				}
			}
		}
		
		if(!$globalPermissions && !$userPermissions){
			throw new Exception("No such user: $user");
		}
		
		//first, we load the default permissions list
		$permissions = self::$defaultPermissions;
		
		//then, we override with the class permissions
		foreach($globalPermissions as $k => $p){
			$permissions[$k] = $p;
		}
		
		if($userPermissions){
			//finally, we override with the user permissions
			foreach($userPermissions as $k => $p){
				$permissions[$k] = $p;
			}
		}
		
		$this->permissions = $permissions;
		
		//echo "<pre>Dumping the final permissions object:\n";
		//var_dump($permissions);
		//echo "</pre>";
	}
}

?>