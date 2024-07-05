<?php

// I set variables from header.php and iclude on each page to set different Title and description 

$headTitle = "Web Developer";
$headDescription = "Full stack web developer.
Greetings! I'm Dobri Dobrev , a passionate and innovative web developer
with a knack for turning ideas into digital reality. 
Let me take you on a journey through my professional story.";

// include header,navigation and database Mysql
require "includes/header.php";



//connecting to the database article mysql query and ordering by date
$articles = fetchData($con_db, 'articles', "status = 'published'", 'published_at DESC', '6' );

/* Displaying services */
$services = fetchData($con_db, 'services', $condition = '', 'published_at DESC', '6' );


/* Newsletter form  */
$name = $email = $origin =  "";
$nameError = $emailError = $originError = "";

if (isset($_POST["submit"])) {
  // Validate and sanitize name
  $name = trim(mysqli_real_escape_string($con_db, $_POST["name"]));
  if (empty($name)) {
    $nameError = "Name is required";
  }

  // Validate and sanitize email
  $email = trim(mysqli_real_escape_string($con_db, $_POST["email"]));
  if (empty($email)) {
    $emailError = "Email is required";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $emailError = "Invalid email format";
  }

  // Validate origin
  $origin = trim(mysqli_real_escape_string($con_db, $_POST["origin"]));
  if (empty($origin)) {
    $originError = "Please select an option";
  }

  // Check if there are any errors before proceeding
  if (empty($nameError) && empty($emailError) && empty($originError)) {
    // Prepare and execute the SQL statement
    $newsletterSql = "INSERT INTO subscribers (name, email, origin, date) VALUES (?, ?, ?, NOW())";
    $newsletterStmt = mysqli_prepare($con_db, $newsletterSql);
    /* Confirm query */
    confirmQuery($newsletterStmt);
    /* If no errors prepare stmt will be bind and execute */
    mysqli_stmt_bind_param($newsletterStmt, "sss", $name, $email, $origin);
    $execute = mysqli_stmt_execute($newsletterStmt);
    /* Check for executing errors */
    if (confirmQuery($execute)) {
      /* If no execute errors insert new id into database,close stmt and redirect to thanks page */
    } else {
      $subscribersId = mysqli_insert_id($con_db);
      mysqli_stmt_close($newsletterStmt);
      header("Location: newsletter-thank-you.php");
      exit();
    }
  } 
}


?>


<!-- Hero Section that is unique for my homepage-->
<section class="hero-section" id="#hero">
  <div class="hero-container">
    <div class="big-hero-box btn--from-left">
      <!-- Title -->
      <h1 class="big-hero-title">
        Full stack web development every single day
      </h1>
      <!-- Description  -->
      <p class="hero-description">
        Greetings! I'm <strong> Dobri Dobrev </strong>, a passionate and
        innovative web developer with a knack for turning ideas into
        digital reality. Let me take you on a journey through my
        professional story.
      </p>
      <!-- Buttons -->
      <div class="hero-buttons">
        <a href="#contacts" class="btn btn--from-left-2 btn--purple" aria-label="say hello button">Say Hello</a>
        <a href="#my-works" class="btn btn--from-right-2 btn--brown" aria-label="my works button">My Works</a>
      </div>
      <!-- Social media icons -->
      <p class="social-text btn--from-bottom">Follow me on Social media</p>
      <ul class="social-icons btn--from-bottom">
        <li>
          <a href="" aria-label="facebook" title="facebook"><img src="./img/social%20media/facebook.png" alt="facebook" id="face" title="social media platform"></a>
        </li>
        <li>
          <a href="" aria-label="twitter" title="twitter"><img src="./img/social%20media/twitter.png" alt="twitter" title="social media platform"></a>
        </li>
        <li>
          <a href="" aria-label="youtube" title="youtube"><img src="./img/social%20media/youtube.png" alt="youtube" title="social media platform"></a>
        </li>
        <li>
          <a href="" aria-label="google" title="google"><img src="./img/social%20media/google.png" alt="google" title="social media platform"></a>
        </li>
        <li>
          <a href="" aria-label="rss" title="rss"><img src="./img/social%20media/rss.png" alt="rss" title="social media platform"></a>
        </li>
      </ul>
    </div>
    <!-- Big Hero Image -->
    <picture>
      <source srcset="./img/web-developer-img.png" media="(max-width:37.5em)">
      <img src="./img/web-developer-img.png" class="myphoto btn--from-right" alt="My Foto" title="Dobri Dobrev">
    </picture>
  </div>
</section>

<!-- Featured section -->

<section class="featured-section">
  <div class="container">
    <h2 class="heading-featured-in">As featured in</h2>
    <div class="logos">
      <img src="./img/logos/techcrunch.png" alt="Techcrunch logo">
      <img src="./img/logos/business-insider.png" alt="Business Insider logo">
      <img src="./img/logos/the-new-york-times.png" alt="The New York Times logo">
      <img src="./img/logos/forbes.png" alt="Forbes logo">
      <img src="./img/logos/usa-today.png" alt="USA Today logo">
    </div>
  </div>
</section>

<!-- Works Section -->
<!-- At this point i will not include it in Json file and convert
 the data from static to dynamic because i will not use it in other pages. 
 It remain static for my homepage -->

<section class="work-section" id="my-works">
  <div class="container">
    <span class="sub-heading">My Works</span>
    <h2 class="secondary-heading">Web Development Work</h2>
  </div>
  <div class="container">

    <!-- First work -->

    <div class="box-work ">
      <div class="front-end">
        <p class="work-number">01</p>
        <h3 class="work-heading-title">Front-End</h3>
        <p class="work-description">
          Description: Front-end developers focus on the user interface and
          experience. They bring designs to life by coding in HTML, CSS, and
          JavaScript, ensuring that websites look visually appealing and
          function smoothly for end-users.
        </p>
      </div>
      <picture>
        <source srcset="./img/works/back-end.png" media="(max-width:37.5em)">
        <img src="./img/works/front-end.png" alt="Front-end development" class="work-images">
      </picture>

    </div>

    <!-- Second Work -->

    <div class="box-work">
      <picture>
        <source srcset="./img/works/back-end.png" media="(max-width:37.5em)">
        <img src="./img/works/back-end.png" alt="Back-end development" class="work-images">
      </picture>

      <div class="front-end">
        <p class="work-number">02</p>
        <h3 class="work-heading-title">Back-End</h3>
        <p class="work-description">
          Back-end developers work behind the scenes to build server-side
          logic, databases, and application functionality. They are
          responsible for handling data, ensuring server performance, and
          integrating various systems to make the website function
          seamlessly.
        </p>
      </div>

    </div>

    <!-- Third Work -->

    <div class="box-work ">
      <div class="front-end">
        <p class="work-number">03</p>
        <h3 class="work-heading-title">Fullstack</h3>
        <p class="work-description">
          Full-stack developers have expertise in both front-end and
          back-end development. They can handle the entire web development
          process, from designing user interfaces to managing databases and
          server-side scripting. Full-stack developers provide end-to-end
          solutions.
        </p>
      </div>
      <picture class="picture">
        <source srcset="./img/works/full-stack.png" media="(max-width:37.5em)">
        <img src="./img/works/full-stack.png" alt="Fullstack development" class="work-images">
      </picture>

    </div>
  </div>
</section>

<!-- Section Blog -->

<section class="blog-section" id="blog">
  <div class="container">
    <span class="sub-heading">Blog</span>
    <h2 class="secondary-heading">Latest Articles</h2>
  </div>
  <div class="container">

    <!-- All Articles that i include in json file to generate dynamic data ,
  because in this section i will show only the latest 6 articles sorted by date from new to old -->

    <div class="blog-articles">
      <?php foreach ($articles as $article) :
        $articleImage = $article['imageurl'];
        $articleTitle = $article['title'];
        $articlePublishDate = date("Y-m-d H:i", strtotime($article["published_at"]));
        $articleDescription = strip_tags($article['description']);
        $articleId = $article['id'];
      ?>
        <article class="article">
          <img src="uploads/<?= $articleImage ?>" alt="<?= $articleTitle ?>" class="blog-imgs">
          <div class="blog-box">
            <div class="blog-tags">
              <div>
                <span class="tag"><?= $articleTitle ?></span>
              </div>
              <div>
                <span class="published">Published on <?= $articlePublishDate ?></span>
              </div>
            </div>
            <h2 class="h2-center"><?= $articleTitle ?></h2>

            <p class="paragraf-description"><?= substr($articleDescription, 0, 176) . '...' ?></p>
            <a href="article.php?id=<?= $articleId  ?>" class="read-more" aria-label="Read more" title="Read more">Read More</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Service section -->
<div id="services" class="container martop-big center-text">
  <span class="sub-heading">Services</span>
  <h2 class="secondary-heading">Web Development</h2>
</div>
<section class="service-section">
  <div class="container">

    <!-- All services that i include in json file to generate dynamic data.
     I decide to give access to each service directly from my homepage-->

    <div class="service-box ">
      <?php foreach ($services as $service) :
        $serviceImage = $service['imageurl'];
        $serviceTitle = $service['title'];
        $serviceDescription = strip_tags($service['description']);
        $serviceId = $service['id'];

      ?>
        <div class="text-box">
          <h3 class="heading-service">
            <a href="work.php?id=<?= $serviceId ?>" class="service-link" aria-label="<?= $serviceTitle ?>" title="<?= $serviceTitle ?>"><?= $serviceTitle ?></a>
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

<!-- Subscribe CTA Section -->
<section class="subscribe-section" id="contacts">
  <div class="container">
    <div class="cta">

      <div class="cta__box">
      
        <h2 class="secondary-heading">
          Newsletter
        </h2>
        <p class="cta__text">
          Get latest news, updates, tips and trics in your inbox.
        </p>

        <!-- Newsletter form only in the homepage -->
        <form id="newsletterForm"  class="cta__form" method="post">
          <!-- Name -->
          <div>
            <label class="cta__label" for="name">Full Name</label>
            <input id="newsletterName" class="cta__input" name="name" type="text" placeholder="John Wick" value="<?= htmlspecialchars($name) ?>">
            <span id="newsletterNameError" class="form__error"><?= $nameError ?></span>
          </div>
          <!-- Email -->
          <div>
            <label class="cta__label" for="email">Email address</label>
            <input id="newsletterEmail" class="cta__input" name="email" type="email" placeholder="your-email@example.com" value="<?= htmlspecialchars($email) ?>" >
            <span id="newsletterEmailError" class="form__error"><?= $emailError ?></span>
          </div>
          <!-- Select origin -->
          <div>
            <label class="cta__label" for="origin">Learn for me from?</label>
            <select id="newsletterOrigin" class="cta__select" name="origin" value="<?= htmlspecialchars($origin) ?>" >
              <option value="">Please choose one option</option>
              <option value="Google Search">Google Search</option>
              <option value="Facebook">Facebook</option>
              <option value="Podcast">Podcast</option>
              <option value="Friends and Family">Friends and Family</option>
              <option value="Youtube">Youtube</option>
              <option value="Other">Other</option>
            </select>
            <span id="newsletterOriginError" class="form__error"><?= $originError ?></span>
          </div>
          <!-- Submit Button -->
          <div> 
          <button type="submit" name="submit" class="cta__btn">Subscribe Now</button>
          </div>
        </form>
      </div>
      <!--Web developer  -->
      <img src="./img/Dobri-Dobrev.png" class="cta__img-box" alt="Dobri Dobrev" title="Dobri Dobrev">

    </div>
  </div>
</section>



<!-- Footer  -->

<?php
require "includes/footer.php";
?>