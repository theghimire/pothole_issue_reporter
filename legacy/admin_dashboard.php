<?php
$pageTitle = "Admin Dashboard - Tarkeshwor";
include 'header.php';
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Logic for stats
$pending_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM issues WHERE status='Pending'"));
$ongoing_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM issues WHERE status='Ongoing'"));
$completed_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM issues WHERE status='Completed'"));

// Filters
$where = "1=1";
if (!empty($_GET['ward']))
    $where .= " AND ward=" . intval($_GET['ward']);
if (!empty($_GET['status']))
    $where .= " AND status='" . mysqli_real_escape_string($conn, $_GET['status']) . "'";

$res = mysqli_query($conn, "SELECT * FROM issues WHERE $where ORDER BY id DESC");
?>

<div class="container-fluid mt-4 px-4 pb-5">
    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 pb-2">
        <h3 class="text-primary m-0">Crisis Management Dashboard</h3>
        <div class="d-print-none">
            <a href="export_csv.php" class="btn btn-dark btn-sm me-2">📊 Export CSV</a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm me-2">🖨️ Print</button>
            <a href="index.php" class="btn btn-outline-dark btn-sm me-2">Public Site</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>

    <!-- Print Only Header -->
    <div class="d-none d-print-block text-center mb-4">
        <h1>Tarkeshwor Municipality</h1>
        <h3>Issue Management Records</h3>
        <p>Generated on: <?php echo date("Y-m-d H:i"); ?></p>
        <hr>
    </div>

    <style>
        @media print {

            .navbar,
            .gov-top-bar,
            .gov-header,
            .d-print-none,
            .btn-sm,
            .pagination {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            body {
                padding: 0 !important;
            }

            .table {
                font-size: 12px;
            }

            .bg-primary {
                background-color: #eee !important;
                color: black !important;
            }
        }
    </style>

    <!-- Stats Bar -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white text-center p-3 shadow-sm">
                <h6>Pending</h6>
                <h3><?php echo $pending_count; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark text-center p-3 shadow-sm border-0">
                <h6>Ongoing</h6>
                <h3><?php echo $ongoing_count; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white text-center p-3 shadow-sm border-0">
                <h6>Completed</h6>
                <h3><?php echo $completed_count; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white text-center p-3 shadow-sm border-0">
                <h6>Total Reports</h6>
                <h3><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT id FROM issues")); ?></h3>
            </div>
        </div>
    </div>

    <!-- Filters Case -->
    <div class="card shadow-sm border-0 p-3 mb-4 bg-light">
        <form class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="small fw-bold">Filter by Ward</label>
                <select name="ward" class="form-select form-select-sm shadow-sm">
                    <option value="">All Wards</option>
                    <?php for ($i = 1; $i <= 11; $i++)
                        echo "<option value='$i'>Ward $i</option>"; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold">Filter by Status</label>
                <select name="status" class="form-select form-select-sm shadow-sm">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Completed">Completed</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark btn-sm w-100 shadow-sm">Apply Filters</button>
            </div>
        </form>
    </div>

    <!-- Issues Table -->
    <div class="table-responsive bg-white rounded shadow">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Evidence</th>
                    <th style="min-width: 150px;">Reporter</th>
                    <th>Detail</th>
                    <th>Location</th>
                    <th>Status Control</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                    <tr>
                        <td>
                            <?php if ($row['photo']) { ?>
                                <img src="uploads/<?php echo $row['photo']; ?>"
                                    style="width: 80px; height: 60px; object-fit: cover; border: 1px solid #ddd;"
                                    class="rounded">
                            <?php } else {
                                echo "<span class='text-muted small'>No Photo</span>";
                            } ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                            <span class="badge bg-light text-dark border">Ward <?php echo $row['ward']; ?></span>
                        </td>
                        <td>
                            <span class="badge bg-secondary small mb-1"><?php echo $row['category']; ?></span><br>
                            <div class="small text-muted" style="max-width: 250px;">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </div>
                            <small
                                class="text-primary"><?php echo date("Y-m-d H:i", strtotime($row['created_at'])); ?></small>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['landmark']); ?><br>
                            <a href="https://www.google.com/maps?q=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>"
                                target="_blank" class="btn btn-link btn-sm p-0 text-danger">📍 View on Map</a>
                        </td>
                        <td>
                            <form action="update_status.php" method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <select name="status" class="form-select form-select-sm me-1 shadow-sm"
                                    onchange="this.form.submit()">
                                    <option value="Pending" <?php if ($row['status'] == 'Pending')
                                        echo 'selected'; ?>>Pending
                                    </option>
                                    <option value="Ongoing" <?php if ($row['status'] == 'Ongoing')
                                        echo 'selected'; ?>>Ongoing
                                    </option>
                                    <option value="Completed" <?php if ($row['status'] == 'Completed')
                                        echo 'selected'; ?>>
                                        Completed</option>
                                    <option value="Rejected" <?php if ($row['status'] == 'Rejected')
                                        echo 'selected'; ?>>
                                        Rejected</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <form action="update_status.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="status" value="Ongoing">
                                    <button class="btn btn-success btn-sm border-0" title="Approve">✅</button>
                                </form>
                                <form action="update_status.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="status" value="Rejected">
                                    <button class="btn btn-warning btn-sm border-0" title="Reject">❌</button>
                                </form>
                                <a href="delete_issue.php?id=<?php echo $row['id']; ?>"
                                    class="btn btn-danger btn-sm border-0"
                                    onclick="return confirm('Delete permanently?')">🗑️</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>