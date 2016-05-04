<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

if ($loggedInUser->checkPermission(array(3))){
    $courses = fetchInstructorClasses();
} elseif($loggedInUser->checkPermission(array(2))){
    $courses = fetchAllClasses();
} else{
    $courses = fetchUserClasses();
}

echo "
<body>
    <div id='wrapper'>
        <div id='top'>
            <div id='logo'></div>
        </div>
        <div id='content'>
            <h1>Whiteboard</h1>
            <h2>Account</h2>
            <div id='left-nav'>";
    
            include("left-nav.php");
            
            echo "
            </div>
            <div id='main'>
            <p>Hey, $loggedInUser->displayname. You registered this account on " . date("M d, Y", $loggedInUser->signupTimeStamp()) . ".</p>
            <h2>Courses:</h2>
            </div>
            <table>";
            if(isset($courses)){
            foreach($courses as $course){
                if($loggedInUser->checkPermission(array(2,3))) {
                    echo "<tr><td><a href='roster.php?c=" . $course['courseid'] . "'>" . $course['coursename'] . "</a></td></tr>";
                }else{
                    echo "<tr><td><a href='assignments.php?c=" . $course['courseid'] . "'>" . $course['coursename'] . "</a></td></tr>";
                }
            }}
            echo"
            </table>
            <div id='bottom'></div>
    </div>
</body>
</html>";

?>
