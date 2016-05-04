<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}


//Forms posted
if(!empty($_POST))
{
    $errors = array();
    $studentid = trim($_POST["student"]);
    $classid = intval($_SESSION['classid']);


    if($studentid == "")
    {
        $errors[] = "select a student";
    }

    //End data validation
    if(count($errors) == 0)
    {
        $addStudent = addStudent($studentid, $classid);
        if($addStudent <> 1){
            $errors[] = "OOOPPSS!! your blog could not be created";
        }

    }
    if(count($errors) == 0) {
        $successes[] = "Course successfully created";
    }
}else{
    if(isset($_GET['c'])) {
        $_SESSION['classid'] = $_GET['c'];
    }elseif(!isset($_SESSION['classid'])){
        header("Location: account.php"); die();
    }
    $classid = intval($_SESSION['classid']);
}

$userData = fetchRoster($classid);
$studentlist = fetchAllStudents();

require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>Pace University</h1>
<h2>Admin Users</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<table>
<tr>
<th>Name</th><th>Email</th><th>Last Sign In</th>
</tr>";

//Cycle through users
foreach ($userData as $v1) {
    echo "
	<tr>
	<td>".$v1['display_name']."</td>
	<td>".$v1['email']."</td>
	<td>
	";

    //Interprety last login
    if ($v1['last_sign_in_stamp'] == '0'){
        echo "Never";
    }
    else {
        echo date("j M, Y", $v1['last_sign_in_stamp']);
    }
    echo "
	</td>
	</tr>";
}

echo "
</table>
<br><br>
<form name='addStudent' action='".$_SERVER['PHP_SELF']."' method='post'>
<select name='student'>
        <option value=''></option>";
    foreach ($studentlist as $s) { ?>
        <option value='<?php print $s['studid']  ?>'><?php print $s['studname'] ?></option>
    <?php  }
    echo"
</select>
<input type='submit' name='Submit' value='Add' />
</form>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>