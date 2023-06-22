<?php
/* @var $this yii\web\View */
/* @var $urls array */
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://rustorgshop.ru/</loc>
        <priority>1</priority>
    </url>
    <url>
        <loc>https://rustorgshop.ru/catalog</loc>
        <priority>0.8</priority>
    </url>
    <?php foreach ($urls as $url): ?>
        <url>
            <loc>https://rustorgshop.ru/<?= htmlspecialchars($url) ?></loc>
            <priority>0.6</priority>
        </url>
    <?php endforeach ?>
    <url>
        <loc>https://rustorgshop.ru/contacts</loc>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>https://rustorgshop.ru/about-us</loc>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>https://rustorgshop.ru/privacy-policy</loc>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>https://rustorgshop.ru/dostavka-i-oplata</loc>
        <priority>0.5</priority>
    </url>
</urlset>