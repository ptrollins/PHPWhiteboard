<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])) {
    die();
}

require_once("models/header.php");
echo "
<body>
    <div id='wrapper'>
        <div id='top'><div id='logo'></div></div>
        <div id='content'>
            <h1>Whiteboard</h1>
            <h2>Upload Documents</h2>
            <div id='left-nav'>";

            include("left-nav.php");

            echo "
            </div>
            <div id='main'>
            
                <form name='uploadDoc' action='documents.php' method='post' enctype='multipart/form-data'>
                <p>
                    <label>Document Name</label>
                    <input type='text' name='docname'>
                </p><br>
                <p>
                    <label>Select file:</label>
                    <input type='file' name='uploadFile' />
                </p><br>
                <p><input type='submit' name='submit' value='Submit'></p>
                </form>
            
            </div>
        </div>
        <div id='bottom'></div>
    </div>
</body>
</html>";
