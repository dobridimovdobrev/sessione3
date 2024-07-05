<?php
/* require __DIR__ . "/../../includes/mysql-database.php"; */
require "./includes/admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();
create_categories();
all_categories();
update_categories();
delete_categories();
?>

<div class="container">
    <div class="category">

        <!-- Create categories -->

        <div class="category__left-side">
            <span id="categoryTitleError" class="cms_admin-error"><?= $error ?></span>
            <h1 class="admin-page__title">Categories</h1>
            <form id="categoryForm" action="categories.php" method="post">
                <input class="category-input" type="text" name="cat_title" id="cat_title" placeholder="Add New Category">
                <button type="submit" class="admin-page__crud-link" name="submit">Add Category</button>
            </form>
        </div>


        <div class="category__right-side">
            <table class="category__table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- View All categories -->

                    <?php foreach ($categories as $category) : ?>
                        <tr>
                            <td><?= $category["cat_title"] ?></td>
                            <td>
                                <a href="categories.php?edit=<?= $category['cat_id'] ?>" class="default">Edit</a>
                                <a href="javascript:void(0);" onclick="showDeleteModal(<?= $category['cat_id'] ?>, 'categories.php')" class="delete">Delete</a>
                            </td>
                        </tr>
                        <?php if (isset($_GET["edit"]) && $_GET["edit"] == $category['cat_id']) : ?>
                            <tr id="update-form">
                                <td colspan="3">
                                    <form id="updateCategoryForm"  method="post" class="edit-form">
                                        <input type="hidden" name="cat_id" value="<?= $category['cat_id'] ?>">
                                        <input type="text" id="new_title" name="new_title" placeholder="New Category Title" >
                                        <button type="submit" name="update" class="admin-page__crud-link">Update</button>
                                        <a href="categories.php" class="admin-page__crud-link">Cancel</a>
                                        <span id="updateError" class="form-group__error"><?= $updateError ?></span>
                                    </form>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close"></span>
        <p class="confirmDeleteparagraph">Are you sure you want to delete this category?</p>
        <button id="cancelBtn" class="btn">Cancel</button>
        <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
    </div>
</div>
<?php
require "./includes/admin_footer.php";
?>