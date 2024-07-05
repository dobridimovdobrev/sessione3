<?php
/* Verify user ip */
function verify_user_ip()
{

    /* Count services */
    function countServices()
    {
        global $con_db;
        $dashboardServiceSql = "SELECT * FROM services";
        $dashboardServiceQuery = mysqli_query($con_db, $dashboardServiceSql);
        if (!$dashboardServiceQuery) {
            die("Query failed" . mysqli_error($con_db));
        } else {
            return mysqli_num_rows($dashboardServiceQuery);
        }
    }

    /* Count articles */
    function countArticles()
    {
        global $con_db;
        $dashboardMessageSql = "SELECT * FROM articles WHERE status = 'published' ";
        $dashboardMessageQuery = mysqli_query($con_db, $dashboardMessageSql);
        if (!$dashboardMessageQuery) {
            die("Query failed" . mysqli_error($con_db));
        } else {
            return mysqli_num_rows($dashboardMessageQuery);
        }
    }

    /* Count users */
    function countUsers()
    {
        global $con_db;
        $dashboardUserSql = "SELECT * FROM users";
        $dashboardUserQuery = mysqli_query($con_db, $dashboardUserSql);
        if (!$dashboardUserQuery) {
            die("Query failed" . mysqli_error($con_db));
        } else {
            return mysqli_num_rows($dashboardUserQuery);
        }
    }

    /* Count newsletters */
    function countNewsletters()
    {
        global $con_db;
        $dashboardNewslettersSql = "SELECT * FROM subscribers";
        $dashboardNewslettersQuery = mysqli_query($con_db, $dashboardNewslettersSql);
        if (!$dashboardNewslettersQuery) {
            die("Query failed" . mysqli_error($con_db));
        } else {
            return mysqli_num_rows($dashboardNewslettersQuery);
        }
    }

    /* Count messages */
    function countMessages()
    {
        global $con_db;
        $dashboardArtilceSql = "SELECT * FROM messages";
        $dashboardArtilceQuery = mysqli_query($con_db, $dashboardArtilceSql);
        if (!$dashboardArtilceQuery) {
            die("Query failed" . mysqli_error($con_db));
        } else {
            return mysqli_num_rows($dashboardArtilceQuery);
        }
    }

    if (isset($_SESSION['role'])) {


        // Check for HTTP_X_FORWARDED_FOR header
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            // Use the first IP address in the list if there are multiple IPs
            $user_ip = explode(',', $user_ip)[0];
        }

        // Fallback to REMOTE_ADDR if HTTP_X_FORWARDED_FOR is not set
        if (empty($user_ip) && !empty($_SERVER['REMOTE_ADDR'])) {
            $user_ip = $_SERVER['REMOTE_ADDR'];
        }

        // Return the detected IP address or null if not available
        return $user_ip;
    }
}

/* Admin access function */
function checkAdminAccess()
{
    // Start the session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user is logged in and if their role is admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        // If the user is not an admin, redirect them to the homepage or show an error message
        header("Location: ./index.php");
        exit();
    }
}

/* Pagination Articles */
function getPaginatedArticles($con_db, $currentPage, $articlesPerPage)
{
    $offset = ($currentPage - 1) * $articlesPerPage;
    $totalRecordsSql = "SELECT COUNT(*) FROM articles WHERE status = 'published' ";
    $totalRecordsResult = mysqli_query($con_db, $totalRecordsSql);

    if (!$totalRecordsResult) {
        die("Query failed: " . mysqli_error($con_db));
    }

    $totalRecordsRow = mysqli_fetch_array($totalRecordsResult);
    $totalRecords = $totalRecordsRow[0];
    $totalPages = ceil($totalRecords / $articlesPerPage);
    /* If status published you will not see the draft */
    $articlesSql = "SELECT * FROM articles ORDER BY id DESC LIMIT $offset, $articlesPerPage";
    $articlesResult = mysqli_query($con_db, $articlesSql);

    if (!$articlesResult) {
        die("Query failed: " . mysqli_error($con_db));
    }

    $articles = mysqli_fetch_all($articlesResult, MYSQLI_ASSOC);

    return ['articles' => $articles, 'totalPages' => $totalPages];
}

/* Pagination Users */
function getPaginatedUsers($con_db, $currentPage, $usersPerPage)
{
    $offset = ($currentPage - 1) * $usersPerPage;
    $totalRecordsSql = "SELECT COUNT(*) FROM users ";
    $totalRecordsResult = mysqli_query($con_db, $totalRecordsSql);

    if (!$totalRecordsResult) {
        die("Query failed: " . mysqli_error($con_db));
    }

    $totalRecordsRow = mysqli_fetch_array($totalRecordsResult);
    $totalRecords = $totalRecordsRow[0];
    $totalPages = ceil($totalRecords / $usersPerPage);

    $usersSql = "SELECT * FROM users ORDER BY user_id DESC LIMIT $offset, $usersPerPage ";
    $usersResult = mysqli_query($con_db, $usersSql);

    if (!$usersResult) {
        die("Query failed: " . mysqli_error($con_db));
    }

    $users = mysqli_fetch_all($usersResult, MYSQLI_ASSOC);

    return ['users' => $users, 'totalPages' => $totalPages];
}

/* Create category*/
function create_categories()
{
    global $con_db;
    global $error;
    /* Initialize variables */
    $cat_title = "";
    $error = "";
    if (isset($_POST["submit"])) {
        // Sanitize and retrieve form data
        $cat_title = trim(mysqli_real_escape_string($con_db, $_POST["cat_title"]));
        /* Validate title */
        if (empty($cat_title)) {
            $error = "* Insert category name !";
        }

        if (empty($error)) {

            $catInsertSql = "INSERT INTO categories (cat_title) VALUES (?)";
            $catPrepare = mysqli_prepare($con_db, $catInsertSql);
            mysqli_stmt_bind_param($catPrepare, "s", $cat_title);
            $execute = mysqli_stmt_execute($catPrepare);

            if (!$execute) {
                die("Category name not created: " . mysqli_stmt_error($catPrepare));
            } else {
                mysqli_stmt_close($catPrepare);
                // Redirect to avoid form resubmission
                header("Location: categories.php");
                exit();
            }
        }
    }
}


/* Read / View Categories */
function all_categories()
{
    global $con_db;
    global $categories;

    $catSql = "SELECT * FROM categories";
    $catQuery = mysqli_query($con_db, $catSql);

    if (!$catQuery) {
        die("Query failed: " . mysqli_error($con_db));
    } else {
        $categories = mysqli_fetch_all($catQuery, MYSQLI_ASSOC);
    }
}


/* Update category */

function update_categories()
{
    global $con_db;
    global $updateError;
    /* Initialize variables */
    $updateError = "";
    if (isset($_POST["update"])) {
        // Retrieve and sanitize POST data
        $cat_id = $_POST["cat_id"];
        $new_title = trim(mysqli_real_escape_string($con_db, $_POST["new_title"]));
        /* Validate new title */
        if (empty($new_title)) {
            $updateError = "* Insert new category name !";
        }
        if (empty($updateError)) {
            // Prepare SQL statement
            $catUpdateSql = "UPDATE categories SET cat_title = ? WHERE cat_id = ?";
            $catPrepare = mysqli_prepare($con_db, $catUpdateSql);

            // Bind parameters and execute query
            mysqli_stmt_bind_param($catPrepare, "si", $new_title, $cat_id);
            $execute = mysqli_stmt_execute($catPrepare);

            // Check execution result
            if (!$execute) {
                die("Category not updated: " . mysqli_stmt_error($catPrepare));
            } else {
                mysqli_stmt_close($catPrepare);
                header("Location: categories.php");
                exit();
            }
        }
    }
}

function delete_categories()
{
    global $con_db;

    if (isset($_GET["delete"])) {
        $deleteCatId = $_GET["delete"];
        $deleteCatSql = "DELETE FROM categories WHERE cat_id = $deleteCatId ";
        $deleteCatQuery = mysqli_query($con_db, $deleteCatSql);

        if (!$deleteCatQuery) {
            die("Delete query failed"  . mysqli_error($con_db));
        } else {
            header("Location: ./categories.php");
        }
    }
}



/* Resize images */
