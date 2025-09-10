<?php
require "includes/header.php";
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-title">Sitemap</h1>
            <p class="page-description">Una panoramica completa delle pagine disponibili sul nostro sito.</p>
            
            <div class="sitemap-section">
                <h2>Pagine Principali</h2>
                <ul class="sitemap-list">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="services.php">Servizi</a></li>
                    <li><a href="work.php">Portfolio</a></li>
                    <li><a href="contacts.php">Contatti</a></li>
                </ul>
            </div>
            
            <div class="sitemap-section">
                <h2>Categorie</h2>
                <ul class="sitemap-list">
                    <li><a href="categories.php">Tutte le Categorie</a></li>
                    <?php
                    // Recupera le categorie dal database
                    $query = "SELECT * FROM categories";
                    $select_categories = mysqli_query($con_db, $query);
                    
                    while($row = mysqli_fetch_assoc($select_categories)) {
                        $cat_id = $row['cat_id'];
                        $cat_title = $row['cat_title'];
                        echo "<li><a href='categories.php?category={$cat_id}'>{$cat_title}</a></li>";
                    }
                    ?>
                </ul>
            </div>
            
            <div class="sitemap-section">
                <h2>Articoli Recenti</h2>
                <ul class="sitemap-list">
                    <?php
                    // Recupera gli articoli recenti
                    $query = "SELECT * FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT 10";
                    $select_articles = mysqli_query($con_db, $query);
                    
                    while($row = mysqli_fetch_assoc($select_articles)) {
                        $article_id = $row['id'];
                        $article_title = $row['title'];
                        echo "<li><a href='article.php?id={$article_id}'>{$article_title}</a></li>";
                    }
                    ?>
                </ul>
            </div>
            
            <div class="sitemap-section">
                <h2>Servizi</h2>
                <ul class="sitemap-list">
                    <?php
                    // Recupera i servizi
                    $query = "SELECT * FROM services ORDER BY published_at DESC";
                    $select_services = mysqli_query($con_db, $query);
                    
                    while($row = mysqli_fetch_assoc($select_services)) {
                        $service_id = $row['id'];
                        $service_title = $row['title'];
                        echo "<li><a href='work.php?id={$service_id}'>{$service_title}</a></li>";
                    }
                    ?>
                </ul>
            </div>
            
            <div class="sitemap-section">
                <h2>Pagine Utente</h2>
                <ul class="sitemap-list">
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Registrazione</a></li>
                </ul>
            </div>
            
            <div class="sitemap-section">
                <h2>Informazioni</h2>
                <ul class="sitemap-list">
                    <li><a href="terms.php">Termini e Condizioni</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.sitemap-section {
    margin-bottom: 30px;
}

.sitemap-section h2 {
    font-size: 24px;
    margin-bottom: 15px;
    color: #333;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.sitemap-list {
    list-style-type: none;
    padding-left: 20px;
}

.sitemap-list li {
    margin-bottom: 10px;
}

.sitemap-list li a {
    color: #3498db;
    text-decoration: none;
    transition: color 0.3s ease;
}

.sitemap-list li a:hover {
    color: #2980b9;
    text-decoration: underline;
}

.page-title {
    font-size: 36px;
    margin-bottom: 20px;
    color: #2c3e50;
}

.page-description {
    font-size: 18px;
    color: #7f8c8d;
    margin-bottom: 30px;
}
</style>

<?php
require "includes/footer.php";
?>
