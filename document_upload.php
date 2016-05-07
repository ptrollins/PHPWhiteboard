<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])) {
    die();
}

$courseid = intval($_SESSION['classid']);
$documents = fetchDocumentByCourseId($courseid);

require_once("models/header.php");
echo "
<body>
    <div id='wrapper'>
        <div id='top'><div id='logo'></div></div>
        <div id='content'>
            <h1>Whiteboard</h1>
            <h2>Documents</h2>
            <div id='left-nav'>";

            include("left-nav.php");

            echo "
            </div>
            <div id='main'>
            
                <form name='uploadDoc' action='processUpload.php' method='post' enctype='multipart/form-data'>
                <table>
                <tr>
                <th>Upload Document</th>
                <th><input type='file' name='document'></th>
                </tr>
                <tr>
                <th><input type='submit' name='submit' value='Upload'></th>
                </tr>
                </table>
                </form>
            
            </div>
        </div>
        <div id='bottom'></div>
    </div>
</body>
</html>";
?>
