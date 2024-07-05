<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $fileName = basename($_FILES["file"]["name"]);
    $fileType = ["jpg", "pdf", "jpeg", "txt", "mp4", "mpeg4", "avi"];
    $fileSize = $_FILES["file"]["size"];

    if (strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) == $fileType[1] && $fileSize < 1000000) {
        $fileDestination = __DIR__ . "/uploads/" . $fileName;
        $temporary = $_FILES["file"]["tmp_name"];
        move_uploaded_file($temporary, $fileDestination);
        echo $fileName . " " . " is a" . " " . $fileType[1];
    } elseif (pathinfo($fileName, PATHINFO_EXTENSION) !== $fileType[1]) {
        echo $fileName . " is not" . " " . $fileType[1];
    } else {
        echo $fileName . " " . "have" . " " . $fileSize . " bytes" . " <br>" . "Please upload a file less than 1MB";
    }
}

?>

<h1>Form to upload files</h1>
<form action="upload.php" method="POST" enctype="multipart/form-data">
    <label for="file">Upload file</label>
    <input type="file" id="file" name="file">
    <button name="button" value="button">Upload</button>

</form>

