<?php
/* Database */
require "includes/mysql-database.php";
/* include functions */
require "includes/functions.php";

/*head title,keywords and description for the page */
pageMetaData(
  "Services",
  "In this page I will show you all my services with details.Please check and feel free to contact me.", 
  "custom web design, responsive websites, support and maintenance, web development"
);

/* Include menu */
require "includes/header.php";
/* Default section with the image after navigation  */
require "includes/main.php";

/* Database service fetching data */
$services = fetchData($con_db, 'services', 'id', 'published_at');
?>
<!-- Page section -->
<section class="page-section">
  <div class="container">
    <!-- Page title -->
    <div class="center-text">
      <span class="sub-heading">Services</span>
      <h2 class="secondary-heading">Web Development</h2>
    </div>
    <!-- Display service data -->
    <div class="service-box martop-big">
    <?php foreach ($services as $service) : 
        $serviceImage = $service['imageurl'];
        $serviceTitle = $service['title'];
        $serviceDescription = strip_tags($service['description']);
        $serviceId = $service['id'];
      ?>
        <div class="text-box">
          <h3 class="heading-service">
            <!-- Service title -->
            <a href="work.php?id=<?= $serviceId ?>" class="service-link" aria-label="$serviceTitle" title="$serviceTitle"><?= $serviceTitle ?></a>
          </h3>
          <!-- Description with limit to 180 chars -->
          <p class="service-description"><?= substr($serviceDescription, 0, 180) . '...' ?></p>
          <!-- Image -->
          <picture>
            <img src="uploads/<?= $serviceImage ?>" alt="<?= $serviceTitle ?>" title="<?= $serviceTitle ?>" class="gallery-img">
          </picture>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<!-- Footer -->
<?php
require "includes/footer.php";
?>