<?php
/* Head title, Description,Page title meta data, Keywords */
function pageMetaData($title, $description, $keywords)
{
    global $headTitle, $headDescription, $headKeywords, $pageTitle;

    // Initialize default values for metadata variables
    $headTitle = $pageTitle = $headDescription = $headKeywords = "";

    $headTitle = $pageTitle = $title;
    $headDescription = $description;
    $headKeywords = $keywords;
}

/* errors Query */
function errorsQuery($query)
{
    global $con_db;

    if (!$query) {
        die("Query failed: " . mysqli_error($con_db));
    }
}

/* confirm Query */
function confirmQuery($query)
{
    global $con_db;

    if (!$query) {
        die("Query failed: " . mysqli_error($con_db));
    }
}


/* Fetch Data from database */
function fetchData($con_db, $tableName, $condition = '', $orderBy = '', $limit = '')
{
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


/* Pagination */
function pagination($con_db, $tableName, $currentPage, $itemsPerPage, $condition = '', $orderBy = '')
{
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
