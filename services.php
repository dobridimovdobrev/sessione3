<?php
$headTitle = "Services"; 
$headDescription = "In this page I will show you all my services with details. Please check and feel free to contact me.";
require "includes/header.php";
$pageTitle = "Services"; // Title for the services page

/* Default section with the image after navigation  */
require "includes/main.php";

$serviceSql = " SELECT * FROM services ORDER BY id ;";
$serviceQuery = mysqli_query($con_db, $serviceSql);

if (!$serviceQuery) {
  die("Service query failed" . mysqli_error($con_db)); // check  for issues 

  // fetch is for reading and extracting all data and convert them into array
} else {
  $services = mysqli_fetch_all($serviceQuery, MYSQLI_ASSOC);
}


?>

<section class="page-section">
  <div class="container">
    <div class="center-text">
      <span class="sub-heading">Services</span>
      <h2 class="secondary-heading">Web Development</h2>
    </div>


    <div class="service-box martop-big">
    <?php foreach ($services as $service) : 
        $serviceImage = $service['imageurl'];
        $serviceTitle = $service['title'];
        $serviceDescription = strip_tags($service['description']);
        $serviceId = $service['id'];
        
      ?>
        <div class="text-box">
          <h3 class="heading-service">
            <a href="work.php?id=<?= $serviceId ?>" class="service-link" aria-label="$serviceTitle" title="$serviceTitle"><?= $serviceTitle ?></a>
          </h3>
          <p class="service-description"><?= substr($serviceDescription, 0, 180) . '...' ?></p>
          <picture>
            <img src="uploads/<?= $serviceImage ?>" alt="<?= $serviceTitle ?>" class="gallery-img">
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