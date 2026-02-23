<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

// Get the institute ID from the session (populated during login)
$adminId   = $_SESSION['admin_id'];
$adminName = $_SESSION['admin_name'];
$instId 	= $_SESSION['inst_id'];

$course_filter = $_GET['course_id'] ?? '';

$sql = "SELECT s.STUDENT_ID, s.REGISTRATION_NO, s.FIRST_NAME, s.LAST_NAME, s.MOBILE,
				c.COURSE_NAME, c.COURSE_CODE, l.TOTAL_FEE, l.BALANCE_AMOUNT 
        FROM STUDENTS s
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
        WHERE l.BALANCE_AMOUNT > 0 AND s.STATUS = 'A'
		AND s.INST_ID = $instId";

if ($course_filter) {
    $sql .= " AND s.COURSE_ID = " . intval($course_filter);
}
$sql .= " ORDER BY l.BALANCE_AMOUNT DESC";

$result = $conn->query($sql);
$courses = $conn->query("SELECT * FROM COURSES WHERE INST_ID = $instId");
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold">Pending Dues Report</h5>
            <form class="d-flex gap-2">
                <select name="course_id" class="form-select form-select-sm">
                    <option value="">All Courses</option>
                    <?php while($c = $courses->fetch_assoc()): ?>
                        <option value="<?= $c['COURSE_ID'] ?>" <?= ($course_filter == $c['COURSE_ID']) ? 'selected' : '' ?>><?= $c['COURSE_CODE'] ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-light btn-sm">Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="duesTable" class="table table-hover table-bordered">
                    <thead class="table-light text-uppercase small">
                        <tr>
                            <th>Student Name</th>
                            <th>Reg No</th>
                            <th>Course</th>
                            <th>Mobile</th>
                            <th>Total Fee</th>
                            <th>Outstanding Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= strtoupper($row['FIRST_NAME'].' '.$row['LAST_NAME']) ?></td>
                            <td><?= $row['REGISTRATION_NO'] ?></td>
                            <td><?= strtoupper($row['COURSE_NAME']. '(' .$row['COURSE_CODE'] .')')?></td>
                            <td><?= $row['MOBILE'] ?></td>
                            <td><?= $row['TOTAL_FEE'] ?></td>
                            <td><?= $row['BALANCE_AMOUNT'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="4" class="text-end">Page Summary:</th>
                            <th id="page_total_fee" class="text-end">0.00</th>
                            <th id="page_total_due" class="text-end">0.00</th>
                        </tr>
                        <tr class="table-secondary text-dark">
                            <th colspan="4" class="text-end">Overall Total (Filtered):</th>
                            <th id="grand_total_fee" class="text-end">0.00</th>
                            <th id="grand_total_due" class="text-end">0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#duesTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'btn btn-success btn-sm',
                text: 'Export Excel',
                footer: true,
                title: 'Pending_Dues_Summary_Report'
            },
            { extend: 'print', className: 'btn btn-info btn-sm', footer: true }
        ],
        "pageLength": 25,
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();

            // Numeric parsing helper
            var getVal = function (i) {
                return typeof i === 'string' ? i.replace(/[^\d.-]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            // 1. SUM TOTAL FEES (Column Index 4)
            var pageFee = api.column(4, { page: 'current' }).data().reduce((a, b) => getVal(a) + getVal(b), 0);
            var grandFee = api.column(4).data().reduce((a, b) => getVal(a) + getVal(b), 0);

            // 2. SUM OUTSTANDING DUES (Column Index 5)
            var pageDue = api.column(5, { page: 'current' }).data().reduce((a, b) => getVal(a) + getVal(b), 0);
            var grandDue = api.column(5).data().reduce((a, b) => getVal(a) + getVal(b), 0);

            // Update Page Totals
            $('#page_total_fee').html(pageFee.toFixed(2));
            $('#page_total_due').html(pageDue.toFixed(2));

            // Update Grand Totals
            $('#grand_total_fee').html(grandFee.toFixed(2));
            $('#grand_total_due').html(grandDue.toFixed(2));
        }
    });
});
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
