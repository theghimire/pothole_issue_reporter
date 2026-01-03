<?php
$pageTitle = "Home - Tarkeshwor Municipality";
include 'header.php';
?>

<!-- Hero Banner -->
<div class="hero-banner" style="background-color: var(--gov-blue);">
    <div class="text-center">
        <h1 class="display-3"><?php echo __('hero_title'); ?></h1>
        <p class="lead fw-bold"><?php echo __('hero_subtitle'); ?></p>
        <a href="report_issue.php" class="btn btn-danger btn-lg px-5 shadow"><?php echo __('report_btn'); ?></a>
    </div>
</div>



<?php include 'footer.php'; ?>