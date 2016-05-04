<?php
/**
 * Created by PhpStorm.
 * User: zhenkangzhao15mbp
 * Date: 5/2/16
 * Time: 10:16 AM
 */
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");

echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>UserCake</h1>
<h2>Admin Permissions</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

//  get the $student_id

//$couseid_row = fetchStudentCouseId($student_id);

//  $courseid

$courseid = 610;
$course_info = array("name", "url");

/*
foreach ($courseid_row as $course_id) {

}
*/


$courseinfo_array = fetchDocumentbyCourseId($courseid);

//$arrlength = count($courseinfo_array);

//$courseinfo_array = fetchStudentCouseId($couseid);


print_r($courseinfo_array);


foreach ($courseinfo_array as $ci) {
    echo "
	<tr>
    <td><p>Documents</p><a href=".$ci['url'].">".$ci['name']."</a></td>
	</tr>";
}



//
?>