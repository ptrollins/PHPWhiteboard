<?php


require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
$classid = intval($_SESSION['classid']);
if(isset($_GET['a'])) {
    $_SESSION['assignid'] = $_GET['a'];
}if(!isset($_SESSION['assignid'])){
    header("Location: account.php"); die();
}

////Forms posted
//if(!empty($_POST))
//{
//    $errors = array();
//    $userid = $loggedInUser->user_id;
//    $uploaddate = time();
//    $filename = trim($_POST["url"]);
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
//}

require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>Pace University</h1>
<h2>Create Assignment</h2>

<div id='left-nav'>";
include("left-nav.php");
echo "
</div>

<div id='main'>";

echo resultBlock($errors,$successes);
echo "
<div id='regbox'>
    <form name='submitAssignment' action='assignments.php' method='post' enctype='multipart/form-data'>  
        <p>
            <label>Select file:</label>
            <input type='file' name='assignmentfile' />
        </p><br>
        <p><input type='submit' name='submit' value='Submit'></p>
    </form>
</div>

</div>

<div id='bottom'></div>
</div>
</body>
</html>";

