<?php


require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

$thisblogid = $_GET['blogid'];
$thisblog = fetchThisBlog($thisblogid);


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

echo "<h1 style='color: red;'>"; print $thisblog['title']; echo "</h1>";
echo "<br><br><br><br>";
echo "<h3 style='color:blue;'>"; print $thisblog['blogcontent']; echo "</h3>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";