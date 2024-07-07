<?php
/* Menu with database,functions and session included */
require "admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();

/* Check for errors query and Retrieve categories */
$selectCategories = fetchData($con_db, 'categories');

/* Initialize variables */
$title = $description = $content = $author = $tags = $published_at = $articleCat_id = $image = $status = "";

/* Assign a empty string on variables for valaidation */
$articleTitleError = $articleDescriptionError = $articleContentError = $articleAuthorError = $articleTagsError =
    $articlePublished_atError = $articleCat_idError = $articleImageError = $fileError = $articleStatusError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    /* Assign values from POST to variables */
    $title = trim(mysqli_real_escape_string($con_db, $_POST["title"]));
    $description = trim($_POST["description"]);
    /* Do not use mysqli real escape string for summernote editor because it not applying your styles and have issues !important */
    $content = trim($_POST["content"]);
    $author = trim(mysqli_real_escape_string($con_db, $_POST["author"]));
    $tags = trim(mysqli_real_escape_string($con_db, $_POST["tags"]));
    $published_at = trim(mysqli_real_escape_string($con_db, $_POST['published_at']));
    $articleCat_id = trim(mysqli_real_escape_string($con_db, $_POST["cat_id"] ?? ''));
    $image = $_FILES["imageurl"]["name"];
    $temp_image = $_FILES["imageurl"]["tmp_name"];
    $fileError = $_FILES["imageurl"]["error"];
    $upload_image = realpath(__DIR__ . "../../../uploads") . "/" . $image;

    /* Check for file upload error */
    if (!empty($image) && $fileError === UPLOAD_ERR_OK) {
        move_uploaded_file($temp_image, $upload_image);
    } else {
        $articleImageError = "Image is required ";
    }

    $status = trim(mysqli_real_escape_string($con_db, $_POST["status"] ?? ''));

    /* Validate title */
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
    
    /* Validate date (I found that the error message is not showing on emtpy date if both conditions are not set into if statement)*/
    if (empty($published_at) || $_POST['published_at'] === '1970-01-01T01:00') {
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

    /* Check for input form errors */
    if (
        empty($articleTitleError) && empty($articleDescriptionError) && empty($articleContentError) && empty($articleAuthorError) &&
        empty($articleTagsError) && empty($articlePublished_atError) && empty($articleCat_idError) && empty($articleImageError) && empty($articleStatusError)
    ) {
        /* If no errors, insert article into database */
        $newArticleSql = "INSERT INTO articles (title, description, content, author, tags, published_at, cat_id, imageurl, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        /* Using prepare stmt instead query to avoid sql injection */
        $newArticleStmt = mysqli_prepare($con_db, $newArticleSql);

        /* Check for query errors, if not proceed with bind and execute */
        if (!errorsQuery($newArticleStmt)) {
            mysqli_stmt_bind_param($newArticleStmt, 'ssssssiss', $title, $description, $content, $author, $tags, $published_at, $articleCat_id, $image, $status);
            $article_execute = mysqli_stmt_execute($newArticleStmt);
        }

        /* Check for execute errors */
        if (!$article_execute) {
            die("Execute statement failed: " . mysqli_stmt_error($newArticleStmt));
        }

        mysqli_stmt_close($newArticleStmt); // Close statement after execution
        $articleId = mysqli_insert_id($con_db);
        /* Redirect to service page after successful insert */
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
    <form id="articleForm" class="max-width-80" action="" method="post" enctype="multipart/form-data">
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
        <!--Article content  -->
        <div class="form-group">
            <label class="form-group__label" for="summernote">Content</label>
            <textarea class="form-group__form-content" id="summernote" name="content" cols="30" rows="20" oninput="updateContentLength()"><?= htmlspecialchars($content)?></textarea>
            <span id="articleContentError" class="form-group__error"><?= $articleContentError ?></span>
            <small id="contentLength">Content length: 0 characters</small>
        </div>
        <!-- Article author -->
        <div class="form-group">
            <label class="form-group__label" for="author">Author</label>
            <input class="form-group__input" type="text" id="author" name="author" value="<?= htmlspecialchars($author) ?>">
            <span id="articleAuthorError" class="form-group__error"><?= $articleAuthorError ?></span>
        </div>
        <!-- Artilce tags -->
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
            <label class="form-group__label" for="imageurl">Image</label>
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
        <input class="form-group__btn-form" type="submit" name="submit"  value="Add Article">
    </form>
</div>
<!-- Footer -->
<?php
require "admin_footer.php";
?>