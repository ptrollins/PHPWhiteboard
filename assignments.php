<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])) {
    die();
}
############ Configuration ##############
$currentfolder = getcwd();
$destination_folder = $currentfolder . '/documents/'; //upload directory ends with / (slash)
define("UPLOAD_DIR", $destination_folder);
##########################################
if(isset($_GET['c'])) {
    $_SESSION['classid'] = $_GET['c'];
}
if(!isset($_SESSION['classid'])){
    header("Location: account.php"); die();
}
$classid = intval($_SESSION['classid']);

if ($loggedInUser->checkPermission(array(2, 3))) {
    $assignments = fetchAllAssignments($classid);
    $submissions = fetchAllSubmissions($classid);
} else {
    $assignments = fetchAllAssignments($classid);
    $submissions = fetchUserSubmissions($classid);
}
//Forms posted
if(!empty($_POST))
{
//    $errors = array();
    $userid = $loggedInUser->user_id;
    $assignid = $_SESSION['assignid'];
//    $uploaddate = time();
//    $filename = trim($_POST["assignmentfile"]);
//
//    if($url == "")
//    {
//        $errors[] = "enter assignment name";
//    }
//
//
//    //End data validation
//    if(count($errors) == 0)
//    {
//        $url = "/PHPWhiteboard/documents/";
//        $submitAssignment  = submitAssignment($assignid, $userid, $uploaddate, $url);
//        if($submitAssignment <> 1){
//            $errors[] = "OOOPPSS!! your assignment could not be created";
//        }
//
//    }
//    if(count($errors) == 0) {
//        $successes[] = "Assignment successfully created";
//    }

    // check $_FILES[''] not empty
    if (!isset($_FILES['assignmentfile']) || !is_uploaded_file($_FILES['assignmentfile']['tmp_name']) || $_FILES['assignmentfile']['error'] > 0) {

        header("Location: assignment_submit1.php"); /* Redirect browser */
        exit();

    }
    else {
        $file_name = $_FILES['assignmentfile']['name'];
        $file_size = $_FILES['assignmentfile']['size'];
        $file_tmp = $_FILES['assignmentfile']['tmp_name'];
        $file_type = $_FILES['assignmentfile']['type'];
        $file_error = $_FILES['assignmentfile']['error'];

        $path_parts = pathinfo($file_name);

        $file_basename =  strtolower($path_parts['basename']);
        $file_name_new= strtolower($path_parts['filename']);
        $file_ext=strtolower($path_parts['extension']);


        $extensions = array("doc","docx","pdf");

        if(in_array($file_ext,$extensions )=== false || $file_size > 2097152 || $file_error !== UPLOAD_ERR_OK){
            //$errors[]="extension not allowed, please choose a .";
            header("Location: assignment_submit2.php"); /* Redirect browser */
            exit();

        }


//        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//            $ip = $_SERVER['HTTP_CLIENT_IP'];
//        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//        } else {
//            $ip = $_SERVER['REMOTE_ADDR'];
//        }

        // ensure a safe filename
        $new_file_name = preg_replace("/[^A-Z0-9._-]/i", "_", $file_name_new).".". $file_ext;


        if(empty($errors)==true) {
            //preserve file from temporary directory
            $new_file_path = UPLOAD_DIR . $new_file_name;
            $success = move_uploaded_file($file_tmp, $new_file_path);
            // set proper permissions on the new file
            chmod($new_file_path, 0644);
            if (!$success) {
                //$errors[] = 'unable to save file';
                header("Location: assignment_submit3.php"); /* Redirect browser */
                exit();
            }else{
                $url = "/PHPWhiteboard/documents/".$new_file_name;
                $submitAssignment  = submitAssignment($assignid, $userid, $url);
            }
            header("Location: assignments.php");
            exit();
        }
        else{
            // print_r($errors);
            header("Location: assignment_submit4.php"); /* Redirect browser */
            exit();

        }
    }
}

require_once("models/header.php");
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
            <h3>Assignments</h3>
            <table>
                <tr>
                    <th>Assignment</th><th>Description</th><th>Due Date</th>
                </tr>";
if (isset($assignments)) {
    $subAssignId = array();
    foreach ($submissions as $submission){
        $subAssignId[] = $submission['assignid'];
    }
    foreach ($assignments as $assignment) {
        if(!in_array($assignment['assignid'],$subAssignId)) {
            echo "<tr>
                    <td><a href='assignment_submit.php?a=" . $assignment['assignid'] . "'>" . $assignment['assignname'] . "</a></td>
                    <td>" . $assignment['description'] . "</td>
                    <td>" . date("j M, Y", strtotime($assignment['duedate'])) . "</td>
                    </tr>";
        }
    }
}
echo "
            </table> 
                      <br>
                      <h3>Submissions</h3>
            <table>
                <tr>
                    <th>Assignment</th><th>Description</th><th>Upload Date</th><th>Due Date</th>
                </tr>";
if (isset($submissions)) {
    foreach ($submissions as $submission) {
        echo "<tr>
                    <td><a href=" . $submission['url'] . ">" . $submission['assignname'] . "</a></td>
                    <td>". $submission['description'] ."</td>
                    <td>" . date("j M, Y", strtotime($submission['uploaddate'])) . "</td>
                    <td>" . date("j M, Y", strtotime($submission['duedate'])) . "</td>
                    </tr>";
    }
}
echo "
            </table>     
            </div>
         </div>
         <div id='bottom'></div>
    </div>
</body>
</html>";