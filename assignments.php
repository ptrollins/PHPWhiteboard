<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");
$_SESSION['classid']=$_GET['c'];
$classid = intval($_SESSION['classid']);
if ($loggedInUser->checkPermission(array(2,3))){
    $assignments = fetchAllAssignments($classid);
}else{
    $assignments = fetchUserAssignments($classid);
}

echo "
<body>
    <div id='wrapper'>
        <div id='top'>
            <div id='logo'></div>
        </div>
        <div id='content'>
            <h1>Whiteboard</h1>
            <h2>Assignments</h2>
            <div id='left-nav'>";

include("left-nav.php");

echo "      </div>
            <div id='main'>
            <table>";
            if(isset($courses)){
                foreach($courses as $course){
                    echo"<tr><td><a href='assignments.php?c=".$course['courseid']."'>".$course['coursename']."</a></td></tr>";
                }
            }
            echo"
            </table>            </div>
            
            <div id='bottom'></div>
    </div>
</body>
</html>";