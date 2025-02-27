<?php
/* Menu with database,functions and session included */
require "admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();
/* Initialize variables */
$title = $description = $content = $tags = $published_at =
    $image = $temp_image = $upload_image = "";

$maxFileSize = 10 * 1024 * 1024; // 10MB in bytes
$maxFileSizeMB = $maxFileSize / 1024 / 1024; // Convert bytes to MB for user display

/* Initialize variables */
$serviceTitleError = $serviceDescriptionError = $serviceContentError =
    $serviceTagsError = $servicePublished_atError = $serviceImageError = "";
/* Form post method */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {

    $title = trim(mysqli_real_escape_string($con_db, $_POST["title"]));
    $description = trim($_POST["description"]);
    /* Do not use mysqli real escape string for summernote editor because it not applying your styles and have issues !important */
    $content = $_POST["content"];
    $tags = trim(mysqli_real_escape_string($con_db, $_POST["tags"]));
    $published_at = trim(mysqli_real_escape_string($con_db, $_POST['published_at']));
    
    //validate,resize,compress and save image
    if ($_FILES["imageurl"]["error"] === UPLOAD_ERR_NO_FILE) {
        $serviceImageError = "Image is required.";
    } else {
        $image = $_FILES["imageurl"]["name"];
        // Sanitize the image filename
        $image = preg_replace('/[^a-zA-Z0-9\-_\.]/', '', str_replace(' ', '-', $image));
        $temp_image = $_FILES["imageurl"]["tmp_name"];
        $fileSize = $_FILES["imageurl"]["size"];

        // Get file extension
        $fileExt = pathinfo($image, PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        // Check for valid file extension
        if (!in_array(strtolower($fileExt), $allowedExtensions)) {
            $serviceImageError = "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
        } else {
            $imageType = mime_content_type($temp_image);
            $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];

            // Check for valid image type
            if (!in_array($imageType, $allowedImageTypes)) {
                $serviceImageError = "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
            } elseif ($fileSize > $maxFileSize) {
                // Check for valid file size
                $serviceImageError = "Upload image file must be less than {$maxFileSizeMB} MB.";
            } else {
                // Proceed with upload if no errors
                $upload_image = realpath(__DIR__ . "../../../uploads") . "/" . basename($image);

                // Check if the target directory exists and is writable
                if (!is_dir(dirname($upload_image)) || !is_writable(dirname($upload_image))) {
                    $serviceImageError = "Upload directory is not writable.";
                } elseif (!resizeImage($temp_image, $upload_image, 810, 470)) {
                    $serviceImageError = "Failed to resize image.";
                }
            }
        }
    }

    /* Validate title */
    if (empty($title)) {
        $serviceTitleError = "Title is required";
    }
    /* Validate description */
    if (empty($description)) {
        $serviceDescriptionError = "Description is required";
    }
    /* Validate content */
    if (empty($content)) {
        $serviceContentError = "Content is required";
    }
    /* Validate tags */
    if (empty($tags)) {
        $serviceTagsError = "Tags are required";
    }
    /* Validate date (I found that the error message is not showing on emtpy date if both conditions are not set into if statement)*/
    if (empty($published_at) || $_POST['published_at'] === '1970-01-01T01:00') {
        $servicePublished_atError = "Date is required";
    }

    /* Check for input form errors */
    if (
        empty($serviceTitleError) && empty($serviceDescriptionError) && empty($serviceContentError) &&
        empty($serviceTagsError) && empty($servicePublished_atError) && empty($serviceImageError)
    ) {
        /* Proceed with inserting service if no errors with prepare stmt to avoid sql injection */
        $newServiceSql = "INSERT INTO services (title, description, content,tags, published_at, imageurl)
                         VALUES (?, ?, ?, ?, ?, ?)";

        $newServiceStmt = mysqli_prepare($con_db, $newServiceSql);
        /* Check for query errors */
        if (!errorsQuery($newServiceStmt)) {
            mysqli_stmt_bind_param($newServiceStmt, 'ssssss', $title, $description, $content, $tags, $published_at, $image);
            $service_execute = mysqli_stmt_execute($newServiceStmt);
        }

        if (!$service_execute) {
            die("Execute statement failed: " . mysqli_stmt_error($newServiceStmt));
        }
        mysqli_stmt_close($newServiceStmt); // Close statement after execution
        $serviceId = mysqli_insert_id($con_db);  //Insert new id
        /* Redirect to service page after successful insert */
        header("Location: ../services.php");
        exit();
    }
}
?>
<!-- Page heading -->
<div class="container">
    <!-- Page title -->
    <div class="admin-page">
        <h1 class="admin-page__title">New Service</h1>
    </div>
    <!-- Service form -->
    <form id="serviceForm" class="max-width-80" action="" method="post" enctype="multipart/form-data">
        <!-- Service title -->
        <div class="form-group">
            <label class="form-group__label" for="title">Title</label>
            <input class="form-group__input" type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>">
            <span id="serviceTitleError" class="form-group__error"><?= $serviceTitleError ?></span>
        </div>
        <!-- Service description -->
        <div class="form-group">
            <label class="form-group__label" for="description">Description</label>
            <input class="form-group__input" type="text" id="description" name="description" maxlength="180" oninput="updateDescriptionCounter()" value="<?= htmlspecialchars($description) ?>">
            <span id="serviceDescriptionError" class="form-group__error"><?= $serviceDescriptionError ?></span>
            <small class="counter" id="descriptionCounter">180 characters remaining</small>
        </div>
        <!--Service content  -->
        <div class="form-group">
            <label class="form-group__label" for="summernote">Content</label>
            <textarea class="form-group__form-content" id="summernote" name="content" cols="30" rows="20" oninput="updateContentLength()"><?= htmlspecialchars($content) ?></textarea>
            <span id="serviceContentError" class="form-group__error"><?= $serviceContentError ?></span>
            <small id="contentLength">Content length: 0 characters</small>
        </div>
        <!-- Service tags -->
        <div class="form-group">
            <label class="form-group__label" for="tags">Tags</label>
            <input class="form-group__input" type="text" id="tags" name="tags" maxlength="84" oninput="updateTagsLength()" value="<?= htmlspecialchars($tags) ?>">
            <span id="serviceTagsError" class="form-group__error"><?= $serviceTagsError ?></span>
            <small class="counter" id="tagsCounter">84 characters remaining</small>
        </div>
        <!-- Service image -->
        <div class="form-group">
            <label class="form-group__label" for="imageurl">Image</label>
            <input class="form-group__input" type="file" id="imageurl" name="imageurl">
            <span id="serviceImageError" class="form-group__error"><?= $serviceImageError ?></span>
        </div>
        <!-- Service published date -->
        <div class="form-group">
            <label class="form-group__label" for="published_at">Date</label>
            <input class="form-group__input" type="datetime-local" id="published_at" name="published_at" value="<?= htmlspecialchars($published_at) ?>">
            <span id="servicePublished_atError" class="form-group__error"><?= $servicePublished_atError ?></span>
        </div>
        <!-- Submit button form -->
        <input class="form-group__btn-form" type="submit" name="submit" value="Add Service">
    </form>
</div>
<!-- Footer -->
<?php
require "admin_footer.php";
?>