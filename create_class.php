<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Forms posted
if(!empty($_POST))
{
    $errors = array();
    $coursename = trim($_POST["coursename"]);
    $instructor = trim($_POST["instructor"]);
    $schedule = trim($_POST["schedule"]);
    $location = trim($_POST["location"]);


    if($coursename == "")
    {
        $errors[] = "enter course title";
    }

    if($instructor == "")
    {
        $errors[] = "enter instructor name";
    }

    if($schedule == "")
    {
        $errors[] = "enter course schedule";
    }

    if($location == "")
    {
        $errors[] = "enter course location";
    }
//    if(minMaxRange(5,25,$username))
//    {
//        $errors[] = lang("ACCOUNT_USER_CHAR_LIMIT",array(5,25));
//    }
//    if(!ctype_alnum($username)){
//        $errors[] = lang("ACCOUNT_USER_INVALID_CHARACTERS");
//    }
//    if(minMaxRange(5,25,$displayname))
//    {
//        $errors[] = lang("ACCOUNT_DISPLAY_CHAR_LIMIT",array(5,25));
//    }
//    if(!ctype_alnum($displayname)){
//        $errors[] = lang("ACCOUNT_DISPLAY_INVALID_CHARACTERS");
//    }
//    if(minMaxRange(8,50,$password) && minMaxRange(8,50,$confirm_pass))
//    {
//        $errors[] = lang("ACCOUNT_PASS_CHAR_LIMIT",array(8,50));
//    }
//    else if($password != $confirm_pass)
//    {
//        $errors[] = lang("ACCOUNT_PASS_MISMATCH");
//    }
//    if(!isValidEmail($email))
//    {
//        $errors[] = lang("ACCOUNT_INVALID_EMAIL");
//    }
    //End data validation
    if(count($errors) == 0)
    {
        $createBlog = createCourse($coursename, $instructor, $schedule, $location);
        print_r($createBlog);
        if($createBlog <> 1){
            $errors[] = "OOOPPSS!! your blog could not be created";
        }

    }
    if(count($errors) == 0) {
        $successes[] = "Course successfully created";
    }
}
$instructorlist = fetchInstructors();
require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>Pace University</h1>
<h2>Add Course</h2>

<div id='left-nav'>";
include("left-nav.php");
echo "
</div>

<div id='main'>";

echo resultBlock($errors,$successes);
echo "
<div id='regbox'>
<form name='newCourse' action='".$_SERVER['PHP_SELF']."' method='post'>

<p>
<label>Course:</label>
<input type='text' name='coursename' />
</p><br>
<p>
<label>Instructor Name:</label>

    <select name='instructor'>";
    foreach ($instructorlist as $i) { ?>
        <option value='<?php print $i['instrid']  ?>' name='instructor'><?php print $i['instrname'] ?></option>";
    <?php  }

echo "
</select>
</p><br>
<p>
<label>Course Schedule:</label>
<input type='text' name='schedule' />
</p><br>
<p>
<label>Location:</label>
<input type='text' name='location' />
</p>
<label>&nbsp;<br>
<input type='submit' value='Create'/>
</p>

</form>
</div>

</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
