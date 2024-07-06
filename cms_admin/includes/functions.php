<?php
/* Verify user ip */
function verify_user_ip()
{
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

/* Count tables from database (services,articles,users,subscribers,messages) */
function countTable($table)
{
    global $con_db;
    $dashboardSql = "SELECT * FROM $table ";
    $dashboardQuery = mysqli_query($con_db, $dashboardSql);
    if (!$dashboardQuery) {
        die("Query failed" . mysqli_error($con_db));
    }else{
        $result = mysqli_num_rows($dashboardQuery);
    } 
        return $result;
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

/* confirmQuery */
function confirmQuery($query) {
    global $con_db;

    if (!$query) {
        die("Query failed: " . mysqli_error($con_db));
    } 
}

/* Fetch Data from database */
function fetchData($con_db, $tableName, $condition = '', $orderBy = '', $limit = '') {
    $sql = "SELECT * FROM $tableName";
    
    if (!empty($condition)) {
        $sql .= " WHERE $condition";
    }
    
    if (!empty($orderBy)) {
        $sql .= " ORDER BY $orderBy";
    }
    
    if (!empty($limit)) {
        $sql .= " LIMIT $limit";
    }
    
    $query = mysqli_query($con_db, $sql);
    
    if (!$query) {
        die("Query failed: " . mysqli_error($con_db));
    }
    
    return mysqli_fetch_all($query, MYSQLI_ASSOC);
}

/* Delete data from database */
function deleteQuery($con_db, $tableName, $columnName, $redirectUrl) {
    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
        die("You do not have permission to delete.");
    }

    if (isset($_GET["delete"])) {
        $deleteId = mysqli_real_escape_string($con_db, $_GET["delete"]);
        $deleteSql = "DELETE FROM $tableName WHERE $columnName = $deleteId";
        $deleteQuery = mysqli_query($con_db, $deleteSql);

        if (!$deleteQuery) {
            die("Delete query failed: " . mysqli_error($con_db));
        } else {
            header("Location: $redirectUrl");
            exit();
        }
    }
}


/* Pagination */
function pagination($con_db, $tableName, $currentPage, $itemsPerPage, $condition = '', $orderBy = '') {
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    // Fetch total records
    $totalRecordsSql = "SELECT COUNT(*) FROM $tableName";
    if (!empty($condition)) {
        $totalRecordsSql .= " WHERE $condition";
    }
    $totalResult = mysqli_query($con_db, $totalRecordsSql);
    
    confirmQuery($totalResult);

    $totalRow = mysqli_fetch_array($totalResult);
    $total = $totalRow[0];
    $totalPages = ceil($total / $itemsPerPage);

    // Fetch paginated data
    $limit = "$offset, $itemsPerPage";
    $data = fetchData($con_db, $tableName, $condition, $orderBy, $limit);

    return ['data' => $data, 'totalPages' => $totalPages];
}

/* /////////////////////// CRUD CATEGORIES //////////////////////// */

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
/* Delete categories */
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




