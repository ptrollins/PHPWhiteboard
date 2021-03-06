<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

//Functions that do not interact with DB
//------------------------------------------------------------------------------

//Retrieve a list of all .php files in models/languages
function getLanguageFiles()
{
	$directory = "models/languages/";
	$languages = glob($directory . "*.php");
	//print each file name
	return $languages;
}

//Retrieve a list of all .css files in models/site-templates 
function getTemplateFiles()
{
	$directory = "models/site-templates/";
	$languages = glob($directory . "*.css");
	//print each file name
	return $languages;
}

//Retrieve a list of all .php files in root files folder
function getPageFiles()
{
	$directory = "";
	$pages = glob($directory . "*.php");
	//print each file name
	foreach ($pages as $page){
		$row[$page] = $page;
	}
	return $row;
}

//Destroys a session as part of logout
function destroySession($name)
{
	if(isset($_SESSION[$name]))
	{
		$_SESSION[$name] = NULL;
		unset($_SESSION[$name]);
	}
}

//Generate a unique code
function getUniqueCode($length = "")
{	
	$code = md5(uniqid(rand(), true));
	if ($length != "") return substr($code, 0, $length);
	else return $code;
}

//Generate an activation key
function generateActivationToken($gen = null)
{
	do
	{
		$gen = md5(uniqid(mt_rand(), false));
	}
	while(validateActivationToken($gen));
	return $gen;
}

//@ Thanks to - http://phpsec.org
function generateHash($plainText, $salt = null)
{
	if ($salt === null)
	{
		$salt = substr(md5(uniqid(rand(), true)), 0, 25);
	}
	else
	{
		$salt = substr($salt, 0, 25);
	}
	
	return $salt . sha1($salt . $plainText);
}

//Checks if an email is valid
function isValidEmail($email)
{
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	}
	else {
		return false;
	}
}

//Inputs language strings from selected language.
function lang($key,$markers = NULL)
{
	global $lang;
	if($markers == NULL)
	{
		$str = $lang[$key];
	}
	else
	{
		//Replace any dyamic markers
		$str = $lang[$key];
		$iteration = 1;
		foreach($markers as $marker)
		{
			$str = str_replace("%m".$iteration."%",$marker,$str);
			$iteration++;
		}
	}
	//Ensure we have something to return
	if($str == "")
	{
		return ("No language key found");
	}
	else
	{
		return $str;
	}
}

//Checks if a string is within a min and max length
function minMaxRange($min, $max, $what)
{
	if(strlen(trim($what)) < $min)
		return true;
	else if(strlen(trim($what)) > $max)
		return true;
	else
	return false;
}

//Replaces hooks with specified text
function replaceDefaultHook($str)
{
	global $default_hooks,$default_replace;	
	return (str_replace($default_hooks,$default_replace,$str));
}

//Displays error and success messages
function resultBlock($errors,$successes){
	//Error block
	if(count($errors) > 0)
	{
		echo "<div id='error'>
		<a href='#' onclick=\"showHide('error');\">[X]</a>
		<ul>";
		foreach($errors as $error)
		{
			echo "<li>".$error."</li>";
		}
		echo "</ul>";
		echo "</div>";
	}
	//Success block
	if(count($successes) > 0)
	{
		echo "<div id='success'>
		<a href='#' onclick=\"showHide('success');\">[X]</a>
		<ul>";
		foreach($successes as $success)
		{
			echo "<li>".$success."</li>";
		}
		echo "</ul>";
		echo "</div>";
	}
}

//Completely sanitizes text
function sanitize($str)
{
	return strtolower(strip_tags(trim(($str))));
}

//Functions that interact mainly with .users table
//------------------------------------------------------------------------------

//Delete a defined array of users
function deleteUsers($users) {
	global $mysqli,$db_table_prefix;
	$i = 0;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."users 
		WHERE id = ?");
	$stmt2 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_permission_matches 
		WHERE user_id = ?");
	foreach($users as $id){
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt2->bind_param("i", $id);
		$stmt2->execute();
		$i++;
	}
	$stmt->close();
	$stmt2->close();
	return $i;
}

//Check if a display name exists in the DB
function displayNameExists($displayname)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT active
		FROM ".$db_table_prefix."users
		WHERE
		display_name = ?
		LIMIT 1");
	$stmt->bind_param("s", $displayname);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Check if an email exists in the DB
function emailExists($email)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT active
		FROM ".$db_table_prefix."users
		WHERE
		email = ?
		LIMIT 1");
	$stmt->bind_param("s", $email);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Check if a user name and email belong to the same user
function emailUsernameLinked($email,$username)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT active
		FROM ".$db_table_prefix."users
		WHERE user_name = ?
		AND
		email = ?
		LIMIT 1
		");
	$stmt->bind_param("ss", $username, $email);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Retrieve information for all users
function fetchAllUsers()
{
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		user_name,
		display_name,
		password,
		email,
		activation_token,
		last_activation_request,
		lost_password_request,
		active,
		title,
		sign_up_stamp,
		last_sign_in_stamp
		FROM ".$db_table_prefix."users");
	$stmt->execute();
	$stmt->bind_result($id, $user, $display, $password, $email, $token, $activationRequest, $passwordRequest, $active, $title, $signUp, $signIn);
	
	while ($stmt->fetch()){
		$row[] = array('id' => $id, 'user_name' => $user, 'display_name' => $display, 'password' => $password, 'email' => $email, 'activation_token' => $token, 'last_activation_request' => $activationRequest, 'lost_password_request' => $passwordRequest, 'active' => $active, 'title' => $title, 'sign_up_stamp' => $signUp, 'last_sign_in_stamp' => $signIn);
	}
	$stmt->close();
	return ($row);
}

//Retrieve complete user information by username, token or ID
function fetchUserDetails($username=NULL,$token=NULL, $id=NULL)
{
	if($username!=NULL) {
		$column = "user_name";
		$data = $username;
	}
	elseif($token!=NULL) {
		$column = "activation_token";
		$data = $token;
	}
	elseif($id!=NULL) {
		$column = "id";
		$data = $id;
	}
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		user_name,
		display_name,
		password,
		email,
		activation_token,
		last_activation_request,
		lost_password_request,
		active,
		title,
		sign_up_stamp,
		last_sign_in_stamp
		FROM ".$db_table_prefix."users
		WHERE
		$column = ?
		LIMIT 1");
		$stmt->bind_param("s", $data);
	
	$stmt->execute();
	$stmt->bind_result($id, $user, $display, $password, $email, $token, $activationRequest, $passwordRequest, $active, $title, $signUp, $signIn);
	while ($stmt->fetch()){
		$row = array('id' => $id, 'user_name' => $user, 'display_name' => $display, 'password' => $password, 'email' => $email, 'activation_token' => $token, 'last_activation_request' => $activationRequest, 'lost_password_request' => $passwordRequest, 'active' => $active, 'title' => $title, 'sign_up_stamp' => $signUp, 'last_sign_in_stamp' => $signIn);
	}
	$stmt->close();
	return ($row);
}

//Toggle if lost password request flag on or off
function flagLostPasswordRequest($username,$value)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET lost_password_request = ?
		WHERE
		user_name = ?
		LIMIT 1
		");
	$stmt->bind_param("ss", $value, $username);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

//Check if a user is logged in
function isUserLoggedIn()
{
	global $loggedInUser,$mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT 
		id,
		password
		FROM ".$db_table_prefix."users
		WHERE
		id = ?
		AND 
		password = ? 
		AND
		active = 1
		LIMIT 1");
	$stmt->bind_param("is", $loggedInUser->user_id, $loggedInUser->hash_pw);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if($loggedInUser == NULL)
	{
		return false;
	}
	else
	{
		if ($num_returns > 0)
		{
			return true;
		}
		else
		{
			destroySession("userCakeUser");
			return false;	
		}
	}
}

//Change a user from inactive to active
function setUserActive($token)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET active = 1
		WHERE
		activation_token = ?
		LIMIT 1");
	$stmt->bind_param("s", $token);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Change a user's display name
function updateDisplayName($id, $display)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET display_name = ?
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("si", $display, $id);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

//Update a user's email
function updateEmail($id, $email)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET 
		email = ?
		WHERE
		id = ?");
	$stmt->bind_param("si", $email, $id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Input new activation token, and update the time of the most recent activation request
function updateLastActivationRequest($new_activation_token,$username,$email)
{
	global $mysqli,$db_table_prefix; 	
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET activation_token = ?,
		last_activation_request = ?
		WHERE email = ?
		AND
		user_name = ?");
	$stmt->bind_param("ssss", $new_activation_token, time(), $email, $username);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Generate a random password, and new token
function updatePasswordFromToken($pass,$token)
{
	global $mysqli,$db_table_prefix;
	$new_activation_token = generateActivationToken();
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET password = ?,
		activation_token = ?
		WHERE
		activation_token = ?");
	$stmt->bind_param("sss", $pass, $new_activation_token, $token);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Update a user's title
function updateTitle($id, $title)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."users
		SET 
		title = ?
		WHERE
		id = ?");
	$stmt->bind_param("si", $title, $id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;	
}

//Check if a user ID exists in the DB
function userIdExists($id)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT active
		FROM ".$db_table_prefix."users
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("i", $id);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Checks if a username exists in the DB
function usernameExists($username)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT active
		FROM ".$db_table_prefix."users
		WHERE
		user_name = ?
		LIMIT 1");
	$stmt->bind_param("s", $username);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Check if activation token exists in DB
function validateActivationToken($token,$lostpass=NULL)
{
	global $mysqli,$db_table_prefix;
	if($lostpass == NULL) 
	{	
		$stmt = $mysqli->prepare("SELECT active
			FROM ".$db_table_prefix."users
			WHERE active = 0
			AND
			activation_token = ?
			LIMIT 1");
	}
	else 
	{
		$stmt = $mysqli->prepare("SELECT active
			FROM ".$db_table_prefix."users
			WHERE active = 1
			AND
			activation_token = ?
			AND
			lost_password_request = 1 
			LIMIT 1");
	}
	$stmt->bind_param("s", $token);
	$stmt->execute();
	$stmt->store_result();
		$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Functions that interact mainly with .permissions table
//------------------------------------------------------------------------------

//Create a permission level in DB
function createPermission($permission) {
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."permissions (
		name
		)
		VALUES (
		?
		)");
	$stmt->bind_param("s", $permission);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;
}

//Delete a permission level from the DB
function deletePermission($permission) {
	global $mysqli,$db_table_prefix,$errors; 
	$i = 0;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."permissions 
		WHERE id = ?");
	$stmt2 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_permission_matches 
		WHERE permission_id = ?");
	$stmt3 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."permission_page_matches 
		WHERE permission_id = ?");
	foreach($permission as $id){
		if ($id == 1){
			$errors[] = lang("CANNOT_DELETE_NEWUSERS");
		}
		elseif ($id == 2){
			$errors[] = lang("CANNOT_DELETE_ADMIN");
		}
		else{
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$stmt2->bind_param("i", $id);
			$stmt2->execute();
			$stmt3->bind_param("i", $id);
			$stmt3->execute();
			$i++;
		}
	}
	$stmt->close();
	$stmt2->close();
	$stmt3->close();
	return $i;
}

//Retrieve information for all permission levels
function fetchAllPermissions()
{
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		name
		FROM ".$db_table_prefix."permissions");
	$stmt->execute();
	$stmt->bind_result($id, $name);
	while ($stmt->fetch()){
		$row[] = array('id' => $id, 'name' => $name);
	}
	$stmt->close();
	return ($row);
}

//Retrieve information for a single permission level
function fetchPermissionDetails($id)
{
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		name
		FROM ".$db_table_prefix."permissions
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($id, $name);
	while ($stmt->fetch()){
		$row = array('id' => $id, 'name' => $name);
	}
	$stmt->close();
	return ($row);
}

//Check if a permission level ID exists in the DB
function permissionIdExists($id)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT id
		FROM ".$db_table_prefix."permissions
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("i", $id);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Check if a permission level name exists in the DB
function permissionNameExists($permission)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT id
		FROM ".$db_table_prefix."permissions
		WHERE
		name = ?
		LIMIT 1");
	$stmt->bind_param("s", $permission);	
	$stmt->execute();
	$stmt->store_result();
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Change a permission level's name
function updatePermissionName($id, $name)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."permissions
		SET name = ?
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("si", $name, $id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;	
}

//Functions that interact mainly with .user_permission_matches table
//------------------------------------------------------------------------------

//Match permission level(s) with user(s)
function addPermission($permission, $user) {
	global $mysqli,$db_table_prefix; 
	$i = 0;
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."user_permission_matches (
		permission_id,
		user_id
		)
		VALUES (
		?,
		?
		)");
	if (is_array($permission)){
		foreach($permission as $id){
			$stmt->bind_param("ii", $id, $user);
			$stmt->execute();
			$i++;
		}
	}
	elseif (is_array($user)){
		foreach($user as $id){
			$stmt->bind_param("ii", $permission, $id);
			$stmt->execute();
			$i++;
		}
	}
	else {
		$stmt->bind_param("ii", $permission, $user);
		$stmt->execute();
		$i++;
	}
	$stmt->close();
	return $i;
}

//Retrieve information for all user/permission level matches
function fetchAllMatches()
{
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		user_id,
		permission_id
		FROM ".$db_table_prefix."user_permission_matches");
	$stmt->execute();
	$stmt->bind_result($id, $user, $permission);
	while ($stmt->fetch()){
		$row[] = array('id' => $id, 'user_id' => $user, 'permission_id' => $permission);
	}
	$stmt->close();
	return ($row);	
}

//Retrieve list of permission levels a user has
function fetchUserPermissions($user_id)
{
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT
		id,
		permission_id
		FROM ".$db_table_prefix."user_permission_matches
		WHERE user_id = ?
		");
	$stmt->bind_param("i", $user_id);	
	$stmt->execute();
	$stmt->bind_result($id, $permission);
	while ($stmt->fetch()){
		$row[$permission] = array('id' => $id, 'permission_id' => $permission);
	}
	$stmt->close();
	if (isset($row)){
		return ($row);
	}
}

//Retrieve list of users who have a permission level
function fetchPermissionUsers($permission_id)
{
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT id, user_id
		FROM ".$db_table_prefix."user_permission_matches
		WHERE permission_id = ?
		");
	$stmt->bind_param("i", $permission_id);	
	$stmt->execute();
	$stmt->bind_result($id, $user);
	while ($stmt->fetch()){
		$row[$user] = array('id' => $id, 'user_id' => $user);
	}
	$stmt->close();
	if (isset($row)){
		return ($row);
	}
}

//Unmatch permission level(s) from user(s)
function removePermission($permission, $user) {
	global $mysqli,$db_table_prefix; 
	$i = 0;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."user_permission_matches 
		WHERE permission_id = ?
		AND user_id =?");
	if (is_array($permission)){
		foreach($permission as $id){
			$stmt->bind_param("ii", $id, $user);
			$stmt->execute();
			$i++;
		}
	}
	elseif (is_array($user)){
		foreach($user as $id){
			$stmt->bind_param("ii", $permission, $id);
			$stmt->execute();
			$i++;
		}
	}
	else {
		$stmt->bind_param("ii", $permission, $user);
		$stmt->execute();
		$i++;
	}
	$stmt->close();
	return $i;
}

//Functions that interact mainly with .configuration table
//------------------------------------------------------------------------------

//Update configuration table
function updateConfig($id, $value)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."configuration
		SET 
		value = ?
		WHERE
		id = ?");
	foreach ($id as $cfg){
		$stmt->bind_param("si", $value[$cfg], $cfg);
		$stmt->execute();
	}
	$stmt->close();	
}

//Functions that interact mainly with .pages table
//------------------------------------------------------------------------------

//Add a page to the DB
function createPages($pages) {
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."pages (
		page
		)
		VALUES (
		?
		)");
	foreach($pages as $page){
		$stmt->bind_param("s", $page);
		$stmt->execute();
	}
	$stmt->close();
}

//Delete a page from the DB
function deletePages($pages) {
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."pages 
		WHERE id = ?");
	$stmt2 = $mysqli->prepare("DELETE FROM ".$db_table_prefix."permission_page_matches 
		WHERE page_id = ?");
	foreach($pages as $id){
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt2->bind_param("i", $id);
		$stmt2->execute();
	}
	$stmt->close();
	$stmt2->close();
}

//Fetch information on all pages
function fetchAllPages()
{
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		page,
		private
		FROM ".$db_table_prefix."pages");
	$stmt->execute();
	$stmt->bind_result($id, $page, $private);
	while ($stmt->fetch()){
		$row[$page] = array('id' => $id, 'page' => $page, 'private' => $private);
	}
	$stmt->close();
	if (isset($row)){
		return ($row);
	}
}

//Fetch information for a specific page
function fetchPageDetails($id)
{
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT 
		id,
		page,
		private
		FROM ".$db_table_prefix."pages
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($id, $page, $private);
	while ($stmt->fetch()){
		$row = array('id' => $id, 'page' => $page, 'private' => $private);
	}
	$stmt->close();
	return ($row);
}

//Check if a page ID exists
function pageIdExists($id)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT private
		FROM ".$db_table_prefix."pages
		WHERE
		id = ?
		LIMIT 1");
	$stmt->bind_param("i", $id);	
	$stmt->execute();
	$stmt->store_result();	
	$num_returns = $stmt->num_rows;
	$stmt->close();
	
	if ($num_returns > 0)
	{
		return true;
	}
	else
	{
		return false;	
	}
}

//Toggle private/public setting of a page
function updatePrivate($id, $private)
{
	global $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("UPDATE ".$db_table_prefix."pages
		SET 
		private = ?
		WHERE
		id = ?");
	$stmt->bind_param("ii", $private, $id);
	$result = $stmt->execute();
	$stmt->close();	
	return $result;	
}

//Functions that interact mainly with .permission_page_matches table
//------------------------------------------------------------------------------

//Match permission level(s) with page(s)
function addPage($page, $permission) {
	global $mysqli,$db_table_prefix; 
	$i = 0;
	$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."permission_page_matches (
		permission_id,
		page_id
		)
		VALUES (
		?,
		?
		)");
	if (is_array($permission)){
		foreach($permission as $id){
			$stmt->bind_param("ii", $id, $page);
			$stmt->execute();
			$i++;
		}
	}
	elseif (is_array($page)){
		foreach($page as $id){
			$stmt->bind_param("ii", $permission, $id);
			$stmt->execute();
			$i++;
		}
	}
	else {
		$stmt->bind_param("ii", $permission, $page);
		$stmt->execute();
		$i++;
	}
	$stmt->close();
	return $i;
}

//Retrieve list of permission levels that can access a page
function fetchPagePermissions($page_id)
{
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT
		id,
		permission_id
		FROM ".$db_table_prefix."permission_page_matches
		WHERE page_id = ?
		");
	$stmt->bind_param("i", $page_id);	
	$stmt->execute();
	$stmt->bind_result($id, $permission);
	while ($stmt->fetch()){
		$row[$permission] = array('id' => $id, 'permission_id' => $permission);
	}
	$stmt->close();
	if (isset($row)){
		return ($row);
	}
}

//Retrieve list of pages that a permission level can access
function fetchPermissionPages($permission_id)
{
	global $mysqli,$db_table_prefix; 
	$stmt = $mysqli->prepare("SELECT
		id,
		page_id
		FROM ".$db_table_prefix."permission_page_matches
		WHERE permission_id = ?
		");
	$stmt->bind_param("i", $permission_id);	
	$stmt->execute();
	$stmt->bind_result($id, $page);
	while ($stmt->fetch()){
		$row[$page] = array('id' => $id, 'permission_id' => $page);
	}
	$stmt->close();
	if (isset($row)){
		return ($row);
	}
}

//Unmatched permission and page
function removePage($page, $permission) {
	global $mysqli,$db_table_prefix; 
	$i = 0;
	$stmt = $mysqli->prepare("DELETE FROM ".$db_table_prefix."permission_page_matches 
		WHERE page_id = ?
		AND permission_id =?");
	if (is_array($page)){
		foreach($page as $id){
			$stmt->bind_param("ii", $id, $permission);
			$stmt->execute();
			$i++;
		}
	}
	elseif (is_array($permission)){
		foreach($permission as $id){
			$stmt->bind_param("ii", $page, $id);
			$stmt->execute();
			$i++;
		}
	}
	else {
		$stmt->bind_param("ii", $permission, $user);
		$stmt->execute();
		$i++;
	}
	$stmt->close();
	return $i;
}

//Check if a user has access to a page
function securePage($uri){
	$master_account = -1;
	//Separate document name from uri
	$tokens = explode('/', $uri);
	$page = $tokens[sizeof($tokens)-1];
	global $mysqli,$db_table_prefix,$loggedInUser;
	//retrieve page details
	$stmt = $mysqli->prepare("SELECT 
		id,
		page,
		private
		FROM ".$db_table_prefix."pages
		WHERE
		page = ?
		LIMIT 1");
	$stmt->bind_param("s", $page);
	$stmt->execute();
	$stmt->bind_result($id, $page, $private);
	while ($stmt->fetch()){
		$pageDetails = array('id' => $id, 'page' => $page, 'private' => $private);
	}
	$stmt->close();
	//If page does not exist in DB, allow access
	if (empty($pageDetails)){
		return true;
	}
	//If page is public, allow access
	elseif ($pageDetails['private'] == 0) {
		return true;	
	}
	//If user is not logged in, deny access
	elseif(!isUserLoggedIn()) 
	{
		header("Location: login.php");
		return false;
	}
	else {
		//Retrieve list of permission levels with access to page
		$stmt = $mysqli->prepare("SELECT
			permission_id
			FROM ".$db_table_prefix."permission_page_matches
			WHERE page_id = ?
			");
		$stmt->bind_param("i", $pageDetails['id']);	
		$stmt->execute();
		$stmt->bind_result($permission);
		while ($stmt->fetch()){
			$pagePermissions[] = $permission;
		}
		$stmt->close();
		//Check if user's permission levels allow access to page
		if ($loggedInUser->checkPermission($pagePermissions)){ 
			return true;
		}
		//Grant access if master user
		elseif ($loggedInUser->user_id == $master_account){
			return true;
		}
		else {
			header("Location: account.php");
			return false;	
		}
	}
}


//Functions for Blog
//------------------------------------------------------------------------------

//function to fetch all the blogs that are available. currently not ordered, we can order it by date
//function fetchAllBlogs() {
//	global $mysqli,$db_table_prefix;
//	$stmt = $mysqli->prepare("SELECT
//		bloglisting.blogid,
//		bloglisting.title,
//	    bloglisting.datecreated,
//	    bloglisting.deleteflag,
//	    bloglisting.active,
//	    whomadewho.userid,
//	    blogcontent.blogcontent,
//	    UserDetails.UserName,
//	    UserDetails.FirstName,
//	    UserDetails.LastName,
//	    UserDetails.Email
//
//        FROM whomadewho INNER JOIN bloglisting ON whomadewho.blogid = bloglisting.blogid
//	 INNER JOIN UserDetails ON whomadewho.userid = UserDetails.UserID
//	 INNER JOIN blogcontent ON blogcontent.blogid = bloglisting.blogid
//		");
//
//	$stmt->execute();
//	$stmt->bind_result($blogid, $title, $datecreated, $deleteflag, $active, $userid, $blogcontent, $username, $firstname, $lastname, $email);
//	while ($stmt->fetch()){
//		$row[] = array('blogid' => $blogid,
//			'title' => $title,
//			'datecreated' => $datecreated,
//			'deleteflag' => $deleteflag,
//			'active' => $active,
//			'userid' => $userid,
//			'blogcontent' => $blogcontent,
//			'username' => $username,
//			'firstname' => $firstname,
//			'lastname' => $lastname,
//			'email'  => $email
//		);
//	}
//	$stmt->close();
//	return ($row);
//}


// fetch a particular blog with blog id.
function fetchThisBlog($blogid) {
	global $loggedInUser, $mysqli,$db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		discussion_id, 
		blog_title, 
		datetime, 
		content
        FROM ".$db_table_prefix."discussion
		WHERE discussion_id = ?");
	$stmt->bind_param("s", $blogid);
	$stmt->execute();
	$stmt->bind_result($blogid, $title, $datecreated, $blogcontent);
	while ($stmt->fetch()){
		$row = array('blogid'       => $blogid,
			'title'        => $title,
			'datecreated'  => $datecreated,
			'blogcontent'  => $blogcontent
		);
	}
	$stmt->close();
	return ($row);
}



//only fetch the blogs that the logged in user has created. Notice that we have used $loggedInUser
function fetchMyBlogs() {
	global $loggedInUser, $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		discussion_id, 
		blog_title, 
		datetime, 
		content
        FROM ".$db_table_prefix."discussion
		WHERE user_id = ?");
	$stmt->bind_param("i", $loggedInUser->user_id);
	$stmt->execute();
	$stmt->bind_result($blogid, $title, $datecreated, $content);

	while ($stmt->fetch()){
		$row[] = array('blogid'       => $blogid,
			'title'       => $title,
			'datetime'    => $datecreated,
			'content'     => $content
		);
	}
	$stmt->close();
	return ($row);
}

function fetchClassBlogs() {
	global $loggedInUser, $mysqli, $db_table_prefix;

	$classid = intval($_SESSION['classid']);
	$stmt = $mysqli->prepare("SELECT
		discussion_id, 
		blog_title, 
		datetime, 
		content,
		display_name
        FROM ".$db_table_prefix."discussion d1
        INNER JOIN ".$db_table_prefix."users u1 ON d1.user_id = u1.id
		WHERE class_id = ?");
	$stmt->bind_param("i", $classid);
	$stmt->execute();
	$stmt->bind_result($blogid, $title, $datecreated, $content, $username);
	while ($stmt->fetch()){
		$row[] = array('blogid'       => $blogid,
			'title'       	=> $title,
			'datetime'    	=> $datecreated,
			'content'   	=> $content,
			'username'		=> $username
		);
	}
	$stmt->close();

	return ($row);
}


//create a blog, notice the similarity with create user.
function createBlog($title, $content)
{
	global $loggedInUser, $mysqli, $db_table_prefix;
	$classid = intval($_SESSION["classid"]);
	$stmt = $mysqli->prepare(
		"INSERT INTO ".$db_table_prefix."discussion(
		user_id,
		class_id,
		blog_title,
		datetime,
		content
		)
		VALUES (
		?,
		?,
		?,
		'" . time() . "',
		?
		)"
	);
	$stmt->bind_param("iiss", $loggedInUser->user_id, $classid, $title, $content);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

//	$stmt = $mysqli->prepare(
//		"INSERT INTO blogcontent (
//		blogid,
//		blogcontent
//		)
//		VALUES (
//		?,
//		?
//		)"
//	);
//	$stmt->bind_param("ss",$inserted_blogid, $blog);
//	$result = $stmt->execute();
//	$stmt->close();
//	//return $result;
//
//
//	$stmt = $mysqli->prepare(
//		"INSERT INTO whomadewho (
//		blogid,
//		userid
//		)
//		VALUES (
//        ?,
//        ?
//		)"
//	);
//	$stmt->bind_param("ss",$inserted_blogid, $loggedInUser->user_id);
//	$result = $stmt->execute();
//	$stmt->close();
//	return $result;}


//Other page functions
//------------------------------------------------------------------------------
function fetchUserClasses() {
	global $loggedInUser, $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		c1.id, c1.instructor_id, c1.course_title, c1.schedule, c1.classroom
        FROM ".$db_table_prefix."courses c1
         INNER JOIN ".$db_table_prefix."course_student cs ON c1.id = cs.course_id
		WHERE cs.student_id = ?");
	$stmt->bind_param("s", $loggedInUser->user_id);
	$stmt->execute();
	$stmt->bind_result($courseid, $instructorid, $coursename, $coursesched, $courseroom);
	while ($stmt->fetch()){
		$row[] = array(
			'courseid'     => $courseid,
			'instructorid' => $instructorid,
			'coursename'   => $coursename,
			'coursesched'  => $coursesched,
			'courseroom'   => $courseroom
		);
	}
	$stmt->close();
	return ($row);
}

function fetchInstructorClasses() {
	global $loggedInUser, $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		id, instructor_id, course_title, schedule, classroom
        FROM ".$db_table_prefix."courses
		WHERE instructor_id = ?");
	$stmt->bind_param("s", $loggedInUser->user_id);
	$stmt->execute();
	$stmt->bind_result($courseid, $instructorid, $coursename, $coursesched, $courseroom);
	while ($stmt->fetch()){
		$row[] = array(
			'courseid'     => $courseid,
			'instructorid' => $instructorid,
			'coursename'   => $coursename,
			'coursesched'  => $coursesched,
			'courseroom'   => $courseroom
		);
	}
	$stmt->close();
	return ($row);
}

function fetchAllClasses() {
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		id, instructor_id, course_title, schedule, classroom
        FROM ".$db_table_prefix."courses");
	$stmt->execute();
	$stmt->bind_result($courseid, $instructorid, $coursename, $coursesched, $courseroom);
	while ($stmt->fetch()){
		$row[] = array(
			'courseid'     => $courseid,
			'instructorid' => $instructorid,
			'coursename'   => $coursename,
			'coursesched'  => $coursesched,
			'courseroom'   => $courseroom
		);
	}
	$stmt->close();
	return ($row);
}

function fetchInstructors(){
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		u1.id , u1.display_name
        FROM ".$db_table_prefix."users u1
         INNER JOIN (SELECT user_ID, permission_id FROM ".$db_table_prefix."user_permission_matches WHERE permission_id = 3) up1 
         ON u1.id = up1.user_id
		WHERE up1.user_id NOT IN (
			SELECT DISTINCT user_id 
			FROM ".$db_table_prefix."user_permission_matches
			WHERE permission_id = 2)");
	$stmt->execute();
	$stmt->bind_result($instrid, $instrname);
	while ($stmt->fetch()){
		$row[] = array(
			'instrid' => $instrid,
			'instrname'   => $instrname
		);
	}
	$stmt->close();

	return ($row);
}

function fetchAllStudents(){
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		u1.id, u1.display_name
        FROM ".$db_table_prefix."users u1
         INNER JOIN ".$db_table_prefix."user_permission_matches up1 
         ON u1.id = up1.user_id
		WHERE up1.user_id NOT IN(
			SELECT DISTINCT user_id 
			FROM ".$db_table_prefix."user_permission_matches
			WHERE permission_id > 1)");
	$stmt->execute();
	$stmt->bind_result($studid, $studname);
	while ($stmt->fetch()){
		$row[] = array(
			'studid'	 => $studid,
			'studname'   => $studname
		);
	}
	$stmt->close();

	return ($row);
}

//function fetchUserAssignments($courseid) {
//	global $loggedInUser, $mysqli, $db_table_prefix;
//	$stmt = $mysqli->prepare("SELECT
//		a1.course_id, a1.assignment_name, a1.description, a1.due_date, as1.assignment_id, as1.user_id, as1.uploade_date,
//		as1.url
//        FROM ".$db_table_prefix."assignments a1
//         JOIN ".$db_table_prefix."assignment_submissions as1 ON a1.id = as1.assignment_id
//		WHERE cs.student_id = ? AND a1.course_id = ?");
//	$stmt->bind_param("i", $courseid);
//	$stmt->execute();
//	$stmt->bind_result($courseid, $assignname, $description, $duedate);
//	while ($stmt->fetch()){
//		$row[] = array(
//			'courseid'    	=> $courseid,
//			'assignname' 	=> $assignname,
//			'description'   => $description,
//			'duedate'  		=> $duedate
//		);
//	}
//	$stmt->close();
//	return ($row);
//}


function fetchAllAssignments($courseid) {
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		id, course_id, assignment_name, description, due_date
        FROM  ".$db_table_prefix."assignments
		WHERE course_id = ?");
	$stmt->bind_param("i", $courseid);
	$stmt->execute();
	$stmt->bind_result($assignid, $courseid, $assignname, $description, $duedate);
	while ($stmt->fetch()){
		$row[] = array(
			'assignid'    	=> $assignid,
			'courseid'    	=> $courseid,
			'assignname' 	=> $assignname,
			'description'   => $description,
			'duedate'  		=> $duedate
		);
	}
	$stmt->close();
	return ($row);
}

function fetchUserSubmissions($courseid) {
	global $loggedInUser, $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		a1.course_id, a1.assignment_name, a1.description, a1.due_date, as1.assignment_id, as1.user_id, as1.upload_date, 
		as1.url
        FROM ".$db_table_prefix."assignments a1
         JOIN ".$db_table_prefix."assignment_submissions as1 ON a1.id = as1.assignment_id
		WHERE as1.user_id = ? AND a1.course_id = ?");
	$stmt->bind_param("si", $loggedInUser->user_id, $courseid);
	$stmt->execute();
	$stmt->bind_result($courseid, $assignname, $description, $duedate, $assignid, $userid, $uploaddate, $url);
	while ($stmt->fetch()){
		$row[] = array(
			'courseid'    	=> $courseid,
			'assignname' 	=> $assignname,
			'description'   => $description,
			'duedate'  		=> $duedate,
			'assignid'   	=> $assignid,
			'userid'     	=> $userid,
			'uploaddate' 	=> $uploaddate,
			'url'   		=> $url
		);
	}
	$stmt->close();
	return ($row);
}

function fetchAllSubmissions($courseid) {
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		a1.course_id, a1.assignment_name, a1.description, a1.due_date, as1.assignment_id, as1.user_id, as1.upload_date, 
		as1.url
        FROM ".$db_table_prefix."assignments a1
         JOIN ".$db_table_prefix."assignment_submissions as1 ON a1.id = as1.assignment_id
		WHERE a1.course_id = ?");
	$stmt->bind_param("i", $courseid);
	$stmt->execute();
	$stmt->bind_result($courseid, $assignname, $description, $duedate, $assignid, $userid, $uploaddate, $url);
	while ($stmt->fetch()){
		$row[] = array(
			'courseid'    	=> $courseid,
			'assignname' 	=> $assignname,
			'description'   => $description,
			'duedate'  		=> $duedate,
			'assignid'   	=> $assignid,
			'userid'     	=> $userid,
			'uploaddate' 	=> $uploaddate,
			'url'   		=> $url
		);
	}
	$stmt->close();
	return ($row);
}

function fetchRoster($courseid) {
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("SELECT
		u1.display_name, u1.email, u1.last_sign_in_stamp
        FROM ".$db_table_prefix."users u1
         JOIN ".$db_table_prefix."course_student cs1 ON u1.id = cs1.student_id
		WHERE cs1.course_id = ?");
	$stmt->bind_param("i", $courseid);
	$stmt->execute();
	$stmt->bind_result($display_name, $email, $timestamp);
	while ($stmt->fetch()){
		$row[] = array(
			'display_name'    		=> $display_name,
			'email' 				=> $email,
			'last_sign_in_stamp' 	=> $timestamp
		);
	}
	$stmt->close();
	return ($row);
}

//create a new assignment.
function createAssignment($courseid, $assignname, $description, $duedate)
{
	$currentfolder = getcwd();
	$destination_folder = $currentfolder . '/documents/';
	define("UPLOAD_DIR", $destination_folder);
	
	
	
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare(
		"INSERT INTO ".$db_table_prefix."assignments (
		course_id,
		assignment_name,
		description,
		due_date
		)
		VALUES (
		?,
		?,
		?,
		?
		)"
	);
	$stmt->bind_param("isss", $courseid, $assignname, $description, $duedate);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

function submitAssignment($assignid, $userid, $url)
{
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare(
		"INSERT INTO ".$db_table_prefix."assignment_submissions (
		assignment_id,
		user_id,
		upload_date,
		url
		)
		VALUES (
		?,
		?,
		'" . date('Y-m-d H:i:s') . "',
		?
		)"
	);
	$stmt->bind_param("iis", $assignid, $userid, $url);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

//create a new course, notice the similarity with create user.
function createCourse($coursename, $instructor, $schedule, $location)
{
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare(
		"INSERT INTO ".$db_table_prefix."courses (
		course_title,
		instructor_id,
		schedule,
		classroom
		)
		VALUES (
		?,
		?,
		?,
		?
		)"
	);
	$stmt->bind_param("siss", $coursename, $instructor, $schedule, $location);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

//create a new course, notice the similarity with create user.
function addStudent($studentid, $courseid)
{
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare(
		"INSERT INTO ".$db_table_prefix."course_student (
		course_id,
		student_id
		)
		VALUES (
		?,
		?
		)"
	);
	$stmt->bind_param("ii", $courseid, $studentid);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

//truncate characters on the front page for description.
function truncate_chars($text, $limit, $ellipsis = '...') {
	if( strlen($text) > $limit )
		$text = trim(substr($text, 0, $limit)) . $ellipsis;
	return $text;
}

//---------------------------------------------

//function fetchStudentByCouseId($studentid)
//{
//	global $mysqli,$db_table_prefix;
//	$stmt = $mysqli->prepare("SELECT
//		course_id
//		FROM ".$db_table_prefix."users
//		WHERE
//		student_id = ?");
//	$stmt->bind_param("i", $studentid);
//	$stmt->execute();
//	$stmt->bind_result($courseid);
//
//	while ($stmt->fetch()){
//		$row = array("course_id" => $courseid);
//	}
//	$stmt->close();
//	return ($row);
//}
function addDocument($classid, $docname, $url){
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare(
		"INSERT INTO ".$db_table_prefix."documents (
		course_id,
		docname,
		url
		)
		VALUES (
		?,
		?,
		?
		)"
	);
	$stmt->bind_param("iss", $classid, $docname, $url);
	$result = $stmt->execute();
	$stmt->close();
	return $result;
}

function fetchDocumentByCourseId($courseid)
{
	global $mysqli, $db_table_prefix;
	$stmt = $mysqli->prepare("
	  SELECT docname, url
      FROM ".$db_table_prefix."documents
      WHERE course_id = ?");
	$stmt->bind_param("i", $courseid);
	$result = $stmt->execute();
	$stmt->bind_result($docname, $course_url);
	while ($stmt->fetch()) {
		$row[] = array(
			'name'    => $docname,
			'url'     => $course_url
		);
	}
	$stmt->close();

	return $row;
}

//function fetchAssignmentbyCourseId($courseid)
//{
//	global $mysqli, $db_table_prefix;
//
//	$stmt = $mysqli->prepare("SELECT name, description, due_date
//      FROM ".$db_table_prefix."assignments
//      WHERE course_id = ? LIMIT 1");
//	$stmt->bind_param("i", $courseid);
//	$result = $stmt->execute();
//	$stmt->bind_result($assignment_name, $assignment_des, $assignment_due);
//	while ($stmt->fetch()) {
//		$row[] = array(
//			'name'    => $assignment_name,
//			'des'     => $assignment_des,
//			'due'     => $assignment_due
//		);
//	}
//	$stmt->close();
//	return $row;
//}

?>