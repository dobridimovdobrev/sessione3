<?php
/* Include necessary files and start session if needed */
require "admin_header.php";

/* Function to check if user is admin */
checkAdminAccess();

/* Fetch categories from database */
$selectCategories = fetchData($con_db, 'categories');

/* Initialize variables */
$title = $description = $content = $author = $tags = $published_at = $articleCat_id = $status = "";
$image = $temp_image = $upload_image = "";
$maxFileSize = 10 * 1024 * 1024; // 10MB in bytes
$maxFileSizeMB = $maxFileSize / 1024 / 1024; // Convert bytes to MB for user display
/* Initialize error variables */
$articleTitleError = $articleDescriptionError = $articleContentError = $articleAuthorError = $articleTagsError =
    $articlePublished_atError = $articleCat_idError = $articleImageError = $articleStatusError = "";

/* form submission */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    // sanitize input
    $title = trim(mysqli_real_escape_string($con_db, $_POST["title"]));
    $description = trim($_POST["description"]);
    $content = $_POST["content"];
    $author = trim(mysqli_real_escape_string($con_db, $_POST["author"]));
    $tags = trim(mysqli_real_escape_string($con_db, $_POST["tags"]));
    $published_at = trim(mysqli_real_escape_string($con_db, $_POST['published_at']));
    $articleCat_id = trim(mysqli_real_escape_string($con_db, $_POST["cat_id"] ?? ''));
    $status = trim($_POST["status"] ?? ''); // Default value if not set

    // Validate image, filesize,file extension, file type
    if ($_FILES["imageurl"]["error"] === UPLOAD_ERR_NO_FILE) {
        $articleImageError = "Image is required.";
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
            $articleImageError = "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
        } else {
            $imageType = mime_content_type($temp_image);
            $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];

            // Check for valid image type
            if (!in_array($imageType, $allowedImageTypes)) {
                $articleImageError = "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
            } elseif ($fileSize > $maxFileSize) {
                // Check for valid file size
                $articleImageError = "Upload image file must be less than {$maxFileSizeMB} MB.";
            } else {
                // Proceed with upload if no errors
                $upload_image = realpath(__DIR__ . "../../../uploads") . "/" . basename($image);

                // Check if the target directory exists and is writable
                if (!is_dir(dirname($upload_image)) || !is_writable(dirname($upload_image))) {
                    $articleImageError = "Upload directory is not writable.";
                } elseif (!resizeImage($temp_image, $upload_image, 810, 470)) {
                    $articleImageError = "Failed to resize image.";
                }
            }
        }
    }

    /* Validate other fields */
    if (empty($title)) {
        $articleTitleError = "Title is required";
    }
    /* Validate description */
    if (empty($description)) {
        $articleDescriptionError = "Description is required";
    }
    /* Validate content */
    if (empty($content)) {
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
    /* Validate publish date */
    if (empty($published_at)) {
        $articlePublished_atError = "Date is required";
    }
    /* Validate category */
    if (empty($articleCat_id)) {
        $articleCat_idError = "Select a category";
    }
    /* Validate status */
    if (empty($status)) {
        $articleStatusError = "Select a status";
    }


    /* If no errors, proceed to insert into database */
    if (
        empty($articleTitleError) && empty($articleDescriptionError) && empty($articleContentError) && empty($articleAuthorError) &&
        empty($articleTagsError) && empty($articlePublished_atError) && empty($articleCat_idError) && empty($articleImageError) && empty($articleStatusError)
    ) {

        // Insert into database
        $newArticleSql = "INSERT INTO articles (title, description, content, author, tags, published_at, cat_id, imageurl, status)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $newArticleStmt = mysqli_prepare($con_db, $newArticleSql);

        if (!errorsQuery($newArticleStmt)) {
            mysqli_stmt_bind_param($newArticleStmt, 'ssssssiss', $title, $description, $content, $author, $tags, $published_at, $articleCat_id, $image, $status);
            $article_execute = mysqli_stmt_execute($newArticleStmt);
        }

        /* Check for execution errors */
        if (!$article_execute) {
            die("Execute statement failed: " . mysqli_stmt_error($newArticleStmt));
        }

        mysqli_stmt_close($newArticleStmt); // Close statement after execution

        // Redirect after successful insertion
        header("Location: ../articles.php");
        exit();
    }
}
?>



<!-- Page heading -->
<div class="container">
    <div class="admin-page">
        <h1 class="admin-page__title">New Article</h1>
    </div>
    <!-- Article form -->
    <form id="articleForm" class="max-width-80" method="post" enctype="multipart/form-data">
        <!-- Article title -->
        <div class="form-group">
            <label class="form-group__label" for="title">Title</label>
            <input class="form-group__input" type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>">
            <span id="articleTitleError" class="form-group__error"><?= $articleTitleError ?></span>
        </div>
        <!-- Article description -->
        <div class="form-group">
            <label class="form-group__label" for="description">Description</label>
            <input class="form-group__input" type="text" id="description" name="description" maxlength="180" oninput="updateDescriptionCounter()" value="<?= htmlspecialchars($description) ?>">
            <span id="articleDescriptionError" class="form-group__error"><?= $articleDescriptionError ?></span>
            <small class="counter" id="descriptionCounter">180 characters remaining</small>
        </div>
        <!-- Article content -->
        <div class="form-group">
            <label class="form-group__label" for="summernote">Content</label>
            <textarea class="form-group__form-content" id="summernote" name="content" cols="30" rows="20" oninput="updateContentLength()"><?= $content ?></textarea>
            <span id="articleContentError" class="form-group__error"><?= $articleContentError ?></span>
            <small id="contentLength">Content length: 0 characters</small>
        </div>
        <!-- Article author -->
        <div class="form-group">
            <label class="form-group__label" for="author">Author</label>
            <input class="form-group__input" type="text" id="author" name="author" value="<?= htmlspecialchars($author) ?>">
            <span id="articleAuthorError" class="form-group__error"><?= $articleAuthorError ?></span>
        </div>
        <!-- Article tags -->
        <div class="form-group">
            <label class="form-group__label" for="tags">Tags</label>
            <input class="form-group__input" type="text" id="tags" name="tags" maxlength="84" oninput="updateTagsLength()" value="<?= htmlspecialchars($tags) ?>">
            <span id="articleTagsError" class="form-group__error"><?= $articleTagsError ?></span>
            <small class="counter" id="tagsCounter">84 characters remaining</small>
        </div>
        <!-- Article published date -->
        <div class="form-group">
            <label class="form-group__label" for="published_at">Date</label>
            <input class="form-group__input" type="datetime-local" id="published_at" name="published_at" value="<?= htmlspecialchars($published_at) ?>">
            <span id="articlePublished_atError" class="form-group__error"><?= $articlePublished_atError ?></span>
        </div>
        <!-- Select category -->
        <div class="form-group">
            <label class="form-group__label" for="cat_id">Select Category</label>
            <select class="form-group__input" name="cat_id" id="cat_id">
                <option selected disabled>Select option</option>
                <?php foreach ($selectCategories as $selectCategory) : ?>
                    <option value="<?= $selectCategory["cat_id"] ?>" <?= $articleCat_id == $selectCategory["cat_id"] ? 'selected' : '' ?>>
                        <?= $selectCategory["cat_title"] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span id="articleCat_idError" class="form-group__error"><?= $articleCat_idError ?></span>
        </div>
        <!-- Article image -->
        <div class="form-group">
            <label class="form-group__label" for="imageurl">Image (max upload size &lt; <?= $maxFileSizeMB ?> MB)</label>
            <input class="form-group__input" type="file" id="imageurl" name="imageurl">
            <span id="articleImageError" class="form-group__error"><?= $articleImageError ?></span>
        </div>
        <!-- Article status -->
        <div class="form-group">
            <label class="form-group__label" for="status">Status</label>
            <select class="form-group__input" name="status" id="status">
                <option selected disabled>Select option</option>
                <option value="Published" <?= $status == "Published" ? 'selected' : '' ?>>Publish</option>
                <option value="Draft" <?= $status == "Draft" ? 'selected' : '' ?>>Draft</option>
            </select>
            <span id="articleStatusError" class="form-group__error"><?= $articleStatusError ?></span>
        </div>
        <!-- Submit button -->
        <input class="form-group__btn-form" type="submit" name="submit" value="Add Article">
    </form>
</div>

<!-- Footer -->
<?php require "admin_footer.php"; ?>