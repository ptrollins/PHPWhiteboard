<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

if (!securePage($_SERVER['PHP_SELF'])){die();}

//Links for logged in user
if(isUserLoggedIn()) {
	echo "
	<h3>".$loggedInUser->displayname."</h3>
	<br>
	<ul>
	<li><a href='account.php'>Account Home</a></li>
	<li><a href='user_settings.php'>User Settings</a></li>
	<li><a href='logout.php'>Logout</a></li>
	</ul>";

	//Links for permission level 1 (student)
	if ($loggedInUser->checkPermission(array(1))){
		echo "
	<ul>
	<li><a href='account.php'>Classes</a></li>";
		
	if (isset($_SESSION['classid']) && !empty($_SESSION['classid'])) {
		echo "
		<ul>
		<li><a href='student_documents.php'>Documents</a></li>
		<li><a href='assignments.php?c=".$_SESSION['classid']."'>Assignments</a></li>
		<li><a href='discussion.php'>Discussion</a></li>
		</ul>";
	}
	echo"
	</ul>";
	}

	//Links for permission level 2 (default admin)
	if ($loggedInUser->checkPermission(array(2))){
		echo "
	<ul>
	<li><a href='admin_configuration.php'>Admin Configuration</a></li>
	<li><a href='admin_users.php'>Admin Users</a></li>
	<li><a href='admin_permissions.php'>Admin Permissions</a></li>
	<li><a href='admin_pages.php'>Admin Pages</a></li>
	<li><a href='create_class.php'>Admin Courses</a></li>
	</ul>";
	}

	//Links for permission level 3 (instructor)
	if ($loggedInUser->checkPermission(array(3)) && isset($_SESSION['classid'])){
		echo"
		<ul>
			<li><a href='assignment_create.php'>Create Assignment</a></li>
		</ul>
		";
	}
}
//Links for users not logged in
else {
	echo "
	<ul>
	<li><a href='index.php'>Home</a></li>
	<li><a href='login.php'>Login</a></li>
	<li><a href='register.php'>Register</a></li>
	<li><a href='forgot-password.php'>Forgot Password</a></li>";
	if ($emailActivation)
	{
	echo "<li><a href='resend-activation.php'>Resend Activation Email</a></li>";
	}
	echo "</ul>";
}

?>
