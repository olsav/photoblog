<?php
// Make sure, the upload directory has the appropriate permissions. If not, CHMOD as appropriate.
define('DEFAULT_IMAGE_DIR', '../files/image/');
define('DEFAULT_IMAGE_THUMB_DIR', '../files/image/thumb/');

if (isset($_FILES['qqfile']['name'])) {
	include('image.class.php');
	$myImage = new _image;
	$myImage->uploadTo = DEFAULT_IMAGE_DIR;
	$image = $myImage->upload($_FILES['qqfile']);
	if($image) {
	    // RESIZE THUMB
	    $myImage->newPath = DEFAULT_IMAGE_THUMB_DIR;
	    $myImage->newWidth = 180;
	    $myImage->newHeight = 180;
	    $thumb = $myImage->resize();
	}

	header("Content-Type: text/plain");
	echo json_encode(array(
		"success" => true,
		"filepath" => substr($image, 2),
		"thumbnail" => substr($thumb, 2)
	));
} else {
	header("Content-Type: text/plain");
	echo json_encode(array(
		"success" => false
	));
}

?>