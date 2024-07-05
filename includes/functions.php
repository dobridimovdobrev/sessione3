<?php
function getPaginatedArticles($con_db, $currentPage, $articlesPerPage) {
    $offset = ($currentPage - 1) * $articlesPerPage;
    $totalRecordsSql = "SELECT COUNT(*) FROM articles WHERE status = 'Published' ";
    $totalRecordsResult = mysqli_query($con_db, $totalRecordsSql);
    
    if (!$totalRecordsResult) {
        die("Query failed: " . mysqli_error($con_db));
    }

    $totalRecordsRow = mysqli_fetch_array($totalRecordsResult);
    $totalRecords = $totalRecordsRow[0];
    $totalPages = ceil($totalRecords / $articlesPerPage);

    $articlesSql = "SELECT * FROM articles WHERE status = 'Published' ORDER BY id DESC LIMIT $offset, $articlesPerPage";
    $articlesResult = mysqli_query($con_db, $articlesSql);
    
    if (!$articlesResult) {
        die("Query failed: " . mysqli_error($con_db));
    }

    $articles = mysqli_fetch_all($articlesResult, MYSQLI_ASSOC);

    return ['articles' => $articles, 'totalPages' => $totalPages];
}

?>
