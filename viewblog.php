<?php


require_once("models/config.php");
require_once("models/header.php");

//$thisblogid = $_GET['blogid'];
//$thisblog = fetchThisBlog($thisblogid);
$listofblogs=fetchMyBlogs();

echo "
<body>
<div id='wrapper'>

<div id='content'>

<h2>Blogs, Blogs and More Blogs!!!</h2>

<div id='left-nav'>";
include("left-nav.php");

echo "
</div>
<div id='main'>";
echo "<br><br><br>";

echo "<a href='createblog.php'><button>Create Discussion Post</button></a>";
echo "<br><br><br>";
print_r($listofblogs);
//echo "<h1 style='color: red;'>"; print $thisblog['title']; echo "</h1>";
//echo "<br><br><br><br>";
//echo "<h3 style='color:blue;'>"; print $thisblog['blogcontent']; echo "</h3>";
