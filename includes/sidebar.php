<?php
require "includes/mysql-database.php";

    $categorySql = "SELECT * FROM categories";
    $categoryQuery = mysqli_query($con_db, $categorySql);

    if (!$categoryQuery) {

        die("Query failed " . mysqli_error($con_db));
    } else {
        $categories = mysqli_fetch_all($categoryQuery, MYSQLI_ASSOC);
    }

?>



<!-- Sidebar -->
<aside class="right-sidebar">


    <!--Peach Style Search widget -->
    <div class="widget widget--peach-color">
        <form action="search.php" class="widget__search" method="post">
            <input type="search" name="search" id="search" class="widget__search-input" placeholder="Search...">
            <button class="widget__search-button" type="submit" name="submit">
                <svg class="widget__search-icon-magnifying-glass">
                    <use href="./assets/front-icons/symbol-defs.svg#icon-magnifying-glass"></use>
                </svg>
            </button>
        </form>
    </div>

    <!--Peach Style Widget categories -->
    <div class="widget widget--peach-color">
        <h3 class="fourth-heading">Categories</h3>

        <div class="widget__categories">
            <ul class="widget__categories-list">

                <?php foreach ($categories as $category) : ?>
                    <li class="widget__categories-items">
                        <a href="./categories.php?id=<?= $category["cat_id"]; ?>" class="widget__categories-link" aria-label="<?= $category["cat_title"]; ?>" title="<?= $category["cat_title"]; ?>">
                            <?= $category["cat_title"]; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</aside>