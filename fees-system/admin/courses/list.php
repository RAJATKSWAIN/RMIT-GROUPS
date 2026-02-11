<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

$sql = "SELECT * FROM COURSES ORDER BY COURSE_NAME ASC";
$result = $conn->query($sql);
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Course Master List</h5>
            <a href="add.php" class="btn btn-primary btn-sm">Add New</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Course Name</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['COURSE_ID'] ?></td>
                            <td><span class="badge bg-light text-dark border"><?= $row['COURSE_CODE'] ?></span></td>
                            <td class="fw-bold"><?= $row['COURSE_NAME'] ?></td>
                            <td><?= $row['DURATION_YEARS'] ?> Years</td>
                            <td>
                                <span class="badge <?= $row['STATUS'] == 'A' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $row['STATUS'] == 'A' ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="edit.php?id=<?= $row['COURSE_ID'] ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>