<?php
$pageTitleKey = "success_title";
include 'header.php';
$ticket_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : "N/A";
?>

<div class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-5 bg-white" style="border-top: 5px solid #28a745 !important;">
                <div class="fs-1 text-success mb-3">✅</div>
                <h2 class="text-success mb-3">
                    <?php echo __('success_title'); ?>
                </h2>
                <div class="bg-light p-3 rounded mb-4">
                    <span class="text-muted fw-bold d-block">
                        <?php echo __('ticket_id'); ?>:
                    </span>
                    <h3 class="text-primary m-0">#
                        <?php echo $ticket_id; ?>
                    </h3>
                </div>
                <p class="text-muted mb-4">
                    <?php echo $lang == 'en' ? 'Thank you for your report. Our team will review the issue and start working on it soon.' : 'तपाईंको रिपोर्टको लागि धन्यवाद। हाम्रो टोलीले यसको समीक्षा गर्नेछ र छिट्टै काम सुरु गर्नेछ।'; ?>
                </p>
                <div class="d-grid gap-2">
                    <a href="index.php" class="btn btn-outline-primary shadow-sm">
                        <?php echo __('home'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>