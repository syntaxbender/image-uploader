<?php
if($_FILES){
	// this is not a file uploader code only giving random filename.
	header("Content-type: application/json; charset=utf-8");
	echo json_encode([true,"asdasd.jpg"]); 
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Demo</title>
	<link rel="stylesheet" href="./assets/css/style.css"/>
	<script type="text/javascript" src="./assets/js/sbImageUploader.js"></script>
</head>
<body>
	<div class="sbImageContainer"></div>
</body>
</html>
<script type="text/javascript">
sbImageUploader.init({
	uploadButtonText : "Upload",
	startNumber : 3,
	maxImageNumber : 10,
	contentTypeErrorMessage : "Yalnızca jpeg, gif, bmp ve png dosyaları yükleyebilirsiniz.",
	acceptableTypes : ["image/gif", "image/jpeg", "image/png","image/bmp"]
	//doneFunction : function(){} // this will be ready on next commits.
});
</script>