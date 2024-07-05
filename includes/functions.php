<?php
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
?>
