<?php
/* Header with navigation and database included */
require "includes/admin_header.php";

/* Services fetch data from database */
$services = fetchData($con_db, 'services', $condition = 'published_at', $orderBy = 'id DESC', $limit = '');

/* Delete query */
deleteQuery($con_db, 'services', 'id', 'services.php');
?>
<!-- Container -->
<div class="container">
    <div class="admin-page">
    <div class="admin-page__box">
        <h1 class="admin-page__title">Services</h1>
        <!-- Only admins can add new service -->
        <?php if ($_SESSION['role'] === 'admin') : ?>
        <a href="includes/add_service.php" class="admin-page__crud-link">Add New Service</a>
        <?php endif; ?>
    </div>
    </div>
    <!-- Default table -->
    <div class="default-table">
        <table class="default-table__table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Tags</th>
                    <th>Image</th>
                    <th>Views</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- For each loop for services -->
                <?php foreach ($services as $service) :
                    $serviceId = $service["id"];
                    $serviceTitle = $service["title"];
                    $serviceTags = $service["tags"];
                    $serviceImage = $service["imageurl"];
                    $serviceViews = $service["views"];
                    $serviceDate = date("Y-m-d H:i", strtotime($service["published_at"]));
                ?>
                    <!-- Display fetch data dynamic mode -->
                    <tr>
                        <td><?= $serviceTitle ?></td>   
                        <td><?= $serviceTags ?></td>
                        <td><img src="../uploads/<?= $serviceImage ?>" alt="<?= $serviceTitle ?>" title="<?= $serviceTitle ?>" class="default-table__image"></td>                     
                        <td><?= $serviceViews ?></td>
                        <td><?= $serviceDate ?></td>
                        <td>
                            <a href="../work.php?id=<?= $serviceId ?>" class="default" target="_blank">View </a>
                            <!-- Action buttons only for admin -->
                            <?php if($_SESSION['role'] === 'admin') : ?>
                            <a href="includes/edit_service.php?edit=<?= $serviceId ?>" class="default" target="_blank">Edit</a> 
                            <a href="javascript:void(0);" onclick="showDeleteModal(<?= $serviceId ?>, 'services.php')" class="delete">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close"></span>
            <p class="confirmDeleteparagraph">Are you sure you want to delete this service?</p>
            <button id="cancelBtn" class="btn">Cancel</button>
            <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
        </div>
    </div>
<!-- Footer -->
<?php
require "includes/admin_footer.php";
?>