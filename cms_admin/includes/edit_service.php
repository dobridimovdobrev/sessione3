<?php
require "admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();

/* Initialize variables */
$editSuccess = isset($_SESSION['editSuccess']) ? $_SESSION['editSuccess'] : '';

/* Clear the session variable */
unset($_SESSION['editSuccess']);

/* Assign variable */
$editServiceId = $_GET["edit"];

/* Check if service id exist */
if (!$editServiceId) {
    die("<h1 class='echo-errors'>No service found</h1>");
}
/* Fetch service single row data from database */
$service = fetchSingleData($con_db, 'services', "id = $editServiceId");

/* Initialize variables with existing service data */
$title = $service["title"];
$description = $service["description"];
$content = $service["content"];
$tags = $service["tags"];
$published_at = $service["published_at"];
$image = $service["imageurl"];

/* Initialize variables */
$serviceTitleError = $serviceDescriptionError = $serviceContentError =
    $serviceTagsError = $servicePublished_atError = $serviceImageError = "";

$maxFileSize = 11 * 1024 * 1024; // 11MB in bytes
$maxFileSizeMB = $maxFileSize / 1024 / 1024; // Convert bytes to MB for user display

if (isset($_POST["update"])) {
    $title = trim(mysqli_real_escape_string($con_db, $_POST["title"]));
    $description = trim($_POST["description"]);
    /* Do not use mysqli real escape string for summernote editor because it not applying your styles and have issues !important */
    $content = trim($_POST["content"]);
    $tags = trim(mysqli_real_escape_string($con_db, $_POST["tags"]));
    $published_at = trim(mysqli_real_escape_string($con_db, $_POST['published_at']));

    // Check if a new image is uploaded,resize,compress and save it
    if ($_FILES["imageurl"]["error"] === UPLOAD_ERR_OK) {
        $image = $_FILES["imageurl"]["name"];
        // Sanitize the image filename
        $image = preg_replace('/[^a-zA-Z0-9\-_\.]/', '', str_replace(' ', '-', $image));
        $temp_image = $_FILES["imageurl"]["tmp_name"];
        $fileSize = $_FILES["imageurl"]["size"];
        $imageType = mime_content_type($temp_image);

        // Directory where images will be uploaded
        $upload_image = realpath(__DIR__ . "../../../uploads") . "/" . $image;

        /* Validate image file type and size on the server-side */
        $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];

        if ($fileSize > $maxFileSize) {
            $serviceImageError = "Upload image file must be less than {$maxFileSizeMB} MB.";
        } elseif (!in_array($imageType, $allowedImageTypes)) {
            $serviceImageError = "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
        } else {
            if (!resizeImage($temp_image, $upload_image, 810, 470)) {
                $serviceImageError = "Failed to resize image.";
            }
        }
    } else {
        // Use the existing image if no new image is uploaded
        $image = $service["imageurl"];
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
    if (empty($content) || $content === "<p><br></p>") { // This check is needed because sometimes Summernote returns an empty paragraph)) {
        $serviceContentError = "Content is required";
    }
    /* Validate tags */
    if (empty($tags)) {
        $serviceTagsError = "Tags are required";
    }
    /* Validate date */
    if (empty($published_at) || $_POST['published_at'] === '1970-01-01T01:00') {
        $servicePublished_atError = "Date is required";
    }
    /* Check for input form errors */
    if (
        empty($serviceTitleError) && empty($serviceDescriptionError) && empty($serviceContentError) &&
        empty($serviceTagsError) && empty($servicePublished_atError) && empty($serviceImageError)
    ) {
        $editServiceSql = "UPDATE services SET title = ?, description = ?, content = ?, tags = ?, published_at = ?,  imageurl = ? WHERE id = ?";
        $editServiceStmt = mysqli_prepare($con_db, $editServiceSql);
        /* Check for query errors */
        if (!errorsQuery($editServiceStmt)) {
            mysqli_stmt_bind_param($editServiceStmt, 'ssssssi', $title, $description, $content, $tags, $published_at, $image, $editServiceId);
            $execute = mysqli_stmt_execute($editServiceStmt);
        }
        /* Check for execute stmt errors */
        if (!$execute) {
            die("Execution failed" . mysqli_stmt_error($editServiceStmt));
        } else {
            mysqli_stmt_close($editServiceStmt);
            header("Location: edit_service.php?edit=" . $editServiceId);
            $_SESSION['editSuccess'] = "Edit service successfully !";
            exit();
        }
    }
}
?>
<!-- Container -->
<div class="container">
    <div class="admin-page">
        <div class="admin-page__box">
            <h1 class="admin-page__title">Edit Service</h1>
            <a href="../../work.php?id=<?= htmlspecialchars($editServiceId); ?>" class="admin-page__crud-link" target="_blank">View Service</a>
            <span class="form-group__success"><?= $editSuccess ?></span>
        </div>
    </div>
    <!-- Edit Service Form-->
    <form id="serviceForm" class="max-width-80" action="" method="post" enctype="multipart/form-data">
        <!-- Service title -->
        <div class="form-group">
            <label class="form-group__label" for="title">Title</label>
            <input class="form-group__input" type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>">
            <span id="serviceTitleError" class="form-group__error"><?= $serviceTitleError ?></span>
        </div>
        <!-- Service Description-->
        <div class="form-group">
            <label class="form-group__label" for="description">Description</label>
            <input class="form-group__input" type="text" id="description" name="description" value="<?= htmlspecialchars($description); ?>" maxlength="180">
            <span id="serviceDescriptionError" class="form-group__error"><?= $serviceDescriptionError ?></span>
            <small class="counter" id="descriptionCounter">180 characters remaining</small>
        </div>
        <!--  Service Content -->
        <div class="form-group">
            <label class="form-group__label" for="content">Content</label>
            <textarea class="form-group__form-content" id="summernote" name="content" cols="30" rows="20"><?= htmlspecialchars($content); ?></textarea>
            <span id="serviceContentError" class="form-group__error"><?= $serviceContentError ?></span>
            <small id="contentLength">Content length: 0 characters</small>
        </div>
        <!--Service Tags -->
        <div class="form-group">
            <label class="form-group__label" for="tags">Tags</label>
            <input class="form-group__input" type="text" id="tags" name="tags" value="<?= htmlspecialchars($tags); ?>" maxlength="90">
            <span id="serviceTagsError" class="form-group__error"><?= $serviceTagsError ?></span>
            <small class="counter" id="tagsCounter">84 characters remaining</small>
        </div>
        <!-- Upload image -->
        <div class="form-group">
            <label class="form-group__label" for="imageurl">Image</label>
            <input class="form-group__input" type="file" id="imageurl" name="imageurl">
            <span id="serviceImageError" class="form-group__error"><?= $serviceImageError ?></span>
            <img id="existingImage" src="../../uploads/<?= $image; ?>" alt="<?= htmlspecialchars($title); ?>" title="<?= htmlspecialchars($title); ?>" width="100" height="60">
        </div>
        <!-- Service published date -->
        <div class="form-group">
            <label class="form-group__label" for="published_at">Date</label>
            <input class="form-group__input" type="datetime-local" id="published_at" name="published_at" value="<?= htmlspecialchars($published_at) ?>">
            <span id="servicePublished_atError" class="form-group__error"><?= $servicePublished_atError ?></span>
        </div>
        <!-- Submit button -->
        <input class="form-group__btn-form" type="submit" name="update" value="Update">
    </form>
</div>
<!-- Footer -->
<?php
require "admin_footer.php";
?>