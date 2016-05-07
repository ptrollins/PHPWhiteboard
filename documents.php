<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

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

$documents = fetchDocumentByCourseId($classid);

//Forms posted
if(!empty($_POST))
{
    $userid = $loggedInUser->user_id;
    $assignid = $_SESSION['assignid'];

    // check $_FILES[''] not empty
    if (!isset($_FILES['uploadFile']) || !is_uploaded_file($_FILES['uploadFile']['tmp_name']) || $_FILES['uploadFile']['error'] > 0) {

        header("Location: document_upload.php"); /* Redirect browser */
        exit();

    }
    else {
        $docname = trim($_POST['docname']);
        $file_name = $_FILES['uploadFile']['name'];
        $file_size = $_FILES['uploadFile']['size'];
        $file_tmp = $_FILES['uploadFile']['tmp_name'];
        $file_type = $_FILES['uploadFile']['type'];
        $file_error = $_FILES['uploadFile']['error'];

        $path_parts = pathinfo($file_name);

        $file_basename =  strtolower($path_parts['basename']);
        $file_name_new= strtolower($path_parts['filename']);
        $file_ext=strtolower($path_parts['extension']);


        $extensions = array("doc","docx","pdf");

        if(in_array($file_ext,$extensions )=== false || $file_size > 2097152 || $file_error !== UPLOAD_ERR_OK){
            //$errors[]="extension not allowed, please choose a .";
            header("Location: document_upload.php"); /* Redirect browser */
            exit();
        }

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
                header("Location: document_upload.php"); /* Redirect browser */
                exit();
            }else{
                $url = "/PHPWhiteboard/documents/".$new_file_name;
                $submitAssignment  = addDocument($classid, $docname, $url);
            }
            header("Location: documents.php");
            exit();
        }
        else{
            // print_r($errors);
            header("Location: document_upload.php"); /* Redirect browser */
            exit();

        }
    }
}

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
    <table>
        <tr>
            <th>Document Name</th>
        </tr>";
if(isset($documents)) {
    foreach ($documents as $d) {
        echo "
        <tr>
        <td><a href=" . $d['url'] . ">" . $d['name'] . "</a></td>
        </tr>";
    }
}