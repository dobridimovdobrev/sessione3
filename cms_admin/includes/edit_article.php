<?php
require "admin_header.php";


/* If access denied if user is not an admin */
checkAdminAccess();

/* Initialize variables */
$editSuccess = isset($_SESSION['editSuccess']) ? $_SESSION['editSuccess'] : '';

/* Clear the session variable */
unset($_SESSION['editSuccess']);

/* Initialize variables */
$editArticleId = $_GET["edit"];

/* Message if no article found */
if (!$editArticleId) {
    die("<h1 class='echo-errors'>No article found</h1>");
}

/* Fetch article single row data from database */
$article = fetchSingleData($con_db, 'articles', "id = $editArticleId");

/* Initialize variables with existing article data */
$title = $article["title"];
$description = $article["description"];
$content = $article["content"];
$author = $article["author"];
$tags = $article["tags"];
$published_at = $article["published_at"];
$image = $article["imageurl"];
$articleCat_id = $article["cat_id"];
$status = $article["status"];

// Fetch all data from database and Retrieve categories
$selectCategories = fetchData($con_db, 'categories');

/* Initialize variables */
$articleTitleError = $articleDescriptionError = $articleContentError = $articleAuthorError = $articleTagsError =
    $articlePublished_atError = $articleCat_idError = $articleImageError = $articleStatusError = "";

$maxFileSize = 11 * 1024 * 1024; // 11MB in bytes
$maxFileSizeMB = $maxFileSize / 1024 / 1024; // Convert bytes to MB for user display

if (isset($_POST["update"])) {
    /* Assign values from POST to variables */
    $title = trim(mysqli_real_escape_string($con_db, $_POST["title"]));
    $description = trim($_POST["description"]);
    /* Do not use mysqli real escape string for summernote editor because it not applying your styles and have issues !important */
    $content = trim($_POST["content"]);
    $author = trim(mysqli_real_escape_string($con_db, $_POST["author"]));
    $tags = trim(mysqli_real_escape_string($con_db, $_POST["tags"]));
    $published_at = date("Y-m-d H:i", strtotime($_POST["published_at"]));
    $articleCat_id = trim(mysqli_real_escape_string($con_db, $_POST["cat_id"]));
    $status = $_POST["status"];

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
            $articleImageError = "Upload image file must be less than {$maxFileSizeMB} MB.";
        } elseif (!in_array($imageType, $allowedImageTypes)) {
            $articleImageError = "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
        } else {
            if (!resizeImage($temp_image, $upload_image, 810, 470)) {
                $articleImageError = "Failed to resize image.";
            }
        }
    } else {
        // Use the existing image if no new image is uploaded
        $image = $article["imageurl"];
    }

    /* Validate title */
    if (empty($title)) {
        $articleTitleError = "Title is required";
    }
    /* Validate description */
    if (empty($description)) {
        $articleDescriptionError = "Description is required";
    }
    /* Validate content */
    if (empty($content) || $content === "<p><br></p>") { // This check is needed because sometimes Summernote returns an empty paragraph
        $articleContentError = "Content is required";
    }
    /* Validate author */
    if (empty($author)) {
        $articleAuthorError = "Author is required";
    }
    /* Validate tags */
    if (empty($tags)) {
        $articleTagsError = "Tags are required";
    }
    /* Validate date (I found that the error message is not showing on empty date if both conditions are not set into if statement) */
    if (empty($published_at) || $_POST['published_at'] === '1970-01-01T01:00') {
        $articlePublished_atError = "Date is required";
    }
    /* Validate category */
    if (empty($articleCat_id)) {
        $articleCat_idError = "Select a category";
    }
    if (empty($status)) {
        $articleStatusError = "Select a status";
    }
    /* Check for input form errors */
    if (
        empty($articleTitleError) && empty($articleDescriptionError) && empty($articleContentError) && empty($articleAuthorError) &&
        empty($articleTagsError) && empty($articlePublished_atError) && empty($articleCat_idError) && empty($articleImageError) && empty($articleStatusError)
    ) {
        /* Update database query if no errors */
        $editArticleSql = "UPDATE articles SET title = ?, description = ?, content = ?, author = ?, tags = ?, published_at = ?, cat_id = ?, imageurl = ?, status = ? WHERE id = ?";
        $editArticleStmt = mysqli_prepare($con_db, $editArticleSql);
        /* Check for query errors */
        if (!errorsQuery($editArticleStmt)) {
            mysqli_stmt_bind_param($editArticleStmt, 'ssssssissi', $title, $description, $content, $author, $tags, $published_at, $articleCat_id, $image, $status, $editArticleId);
            $execute = mysqli_stmt_execute($editArticleStmt);
        }
        /* Check for execute stmt errors */
        if (!$execute) {
            die("Execute statement failed: " . mysqli_stmt_error($editArticleStmt));
        } else {
            // Close statement after execution
            mysqli_stmt_close($editArticleStmt);
            /* Redirect to service page after successful insert */
            header("Location: edit_article.php?edit=" . $editArticleId);
            $_SESSION['editSuccess'] = "Edit article successfully!";
            exit();
        }
    }
}
?>
<!-- Container -->
<div class="container">
    <div class="admin-page">
        <div class="admin-page__box">
            <h1 class="admin-page__title">Edit Article</h1>
            <a href="../../article.php?id=<?= htmlspecialchars($article["id"]); ?>" class="admin-page__crud-link" target="_blank">View Article</a>
            <span class="form-group__success"><?= $editSuccess ?></span>
        </div>
    </div>
    <!-- Edit Article Form -->
    <form id="articleForm" class="max-width-80" action="" method="post" enctype="multipart/form-data">

        <!-- Title -->
        <div class="form-group">
            <label class="form-group__label" for="title">Title</label>
            <input class="form-group__input" type="text" id="title" name="title" value="<?= htmlspecialchars($title); ?>">
            <span id="articleTitleError" class="form-group__error"><?= $articleTitleError ?></span>
        </div>
        <!-- Description -->
        <div class="form-group">
            <label class="form-group__label" for="description">Description</label>
            <input class="form-group__input" type="text" id="description" name="description" value="<?= htmlspecialchars($description); ?>">
            <span id="articleDescriptionError" class="form-group__error"><?= $articleDescriptionError ?></span>
            <small class="counter" id="descriptionCounter">180 characters remaining</small>
        </div>
        <!-- Content -->
        <div class="form-group">
            <label class="form-group__label" for="summernote">Content</label>
            <textarea class="form-group__form-content" id="summernote" name="content" cols="30" rows="20"><?= htmlspecialchars($content); ?></textarea>
            <span id="articleContentError" class="form-group__error"><?= $articleContentError ?></span>
            <small id="contentLength">Content length: 0 characters</small>
        </div>
        <!-- Author -->
        <div class="form-group">
            <label class="form-group__label" for="author">Author</label>
            <input class="form-group__input" type="text" id="author" name="author" value="<?= htmlspecialchars($author); ?>">
            <span id="articleAuthorError" class="form-group__error"><?= $articleAuthorError ?></span>
        </div>
        <!-- Tags -->
        <div class="form-group">
            <label class="form-group__label" for="tags">Tags</label>
            <input class="form-group__input" type="text" id="tags" name="tags" value="<?= htmlspecialchars($tags); ?>">
            <span id="articleTagsError" class="form-group__error"><?= $articleTagsError ?></span>
            <small class="counter" id="tagsCounter">84 characters remaining</small>
        </div>
        <!-- Date -->
        <div class="form-group">
            <label class="form-group__label" for="published_at">Date</label>
            <input class="form-group__input" type="datetime-local" id="published_at" name="published_at" value="<?= htmlspecialchars($published_at); ?>">
            <span id="articlePublished_atError" class="form-group__error"><?= $articlePublished_atError ?></span>
        </div>
        <!-- Select category -->
        <div class="form-group">
            <label class="form-group__label" for="cat_id">Select Category</label>
            <select class="form-group__input" name="cat_id" id="cat_id">
                <?php foreach ($selectCategories as $selectCategory) : ?>
                    <option value="<?= $selectCategory["cat_id"] ?>" <?= ($articleCat_id == $selectCategory["cat_id"]) ? 'selected' : '' ?>>
                        <?= $selectCategory["cat_title"] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span id="articleCat_idError" class="form-group__error"><?= $articleCat_idError ?></span>
        </div>
        <!-- Upload image -->
        <div class="form-group">
            <label class="form-group__label" for="imageurl">Image</label>
            <input class="form-group__input" type="file" id="imageurl" name="imageurl">
            <span id="articleImageError" class="form-group__error"><?= $articleImageError ?></span>
            <img id="existingImage" src="../../uploads/<?= $image; ?>" alt="<?= htmlspecialchars($title); ?>" title="<?= htmlspecialchars($title); ?>" width="100" height="60">
        </div>
        <!-- Status -->
        <div class="form-group">
            <label class="form-group__label" for="status">Status</label>
            <select class="form-group__input" name="status" id="status">
                <option selected value="<?= $status ?>"><?= $status ?></option>
                <?php if ($status == "Published") : ?>
                    <option value="Draft">Draft</option>
                <?php elseif ($status == "Draft") : ?>
                    <option value="Published">Published</option>
                <?php endif; ?>
            </select>
            <span id="articleStatusError" class="form-group__error"><?= $articleStatusError ?></span>
        </div>
        <!-- Submit button -->
        <input class="form-group__btn-form" type="submit" name="update" value="Update">
    </form>
</div>
<!-- Footer -->
<?php
require "admin_footer.php";
?>