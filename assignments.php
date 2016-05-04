<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");
$_SESSION['classid']=$_GET['c'];
$classid = intval($_SESSION['classid']);
if ($loggedInUser->checkPermission(array(2,3))){
    $assignments = fetchAllAssignments($classid);
    $submissions = fetchAllSubmissions($classid);
}else{
    $assignments = fetchAllAssignments($classid);
    $submissions = fetchUserSubmissions($classid);
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
            if(isset($assignments)){
                foreach($assignments as $assignment){
                    echo"<tr>
                    <td><a href='assignment_submit.php?a=".$assignment['assignid']."'>".$assignment['assignname']."</a></td>
                    <td>".$assignment['description']."</td>
                    <td>".date("j M, Y", strtotime($assignment['duedate']))."</td>
                    </tr>";
                }
            }
            echo"
            </table> 
                      <br>
             <table>
                <tr>
                    <th>Assignment</th><th>Due Date</th><th>Upload Date</th>
                </tr>";
            if(isset($submissions)){
                foreach($submissions as $submission){
                    echo"<tr>
                    <td><a href=".$submission['url'].">".$submission['assignname']."</a></td>
                    <td>".date("j M, Y", strtotime($submission['uploaddate']))."</td>
                    <td>".date("j M, Y", strtotime($submission['duedate']))."</td>
                    </tr>";
                }
            }
            echo"
            </table>     
             </div>
            
            <div id='bottom'></div>
    </div>
</body>
</html>";