<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");
//destroySession("userCakeUser");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>Pace University</h1>

<div id='left-nav'>";
include("left-nav.php");

echo "
</div>
<div id='main'>
<p>Welcome to Whiteboard!</p>
<p>Developers:</p>
<ul>
    <li>Preston Rollins</li>
    <li>Prateek Dua</li>
    <li>Zach Zhao</li>
    <li>Harpreet Wasan</li>
</ul>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
