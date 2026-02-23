<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/services/PaymentService.php'; 
require_once BASE_PATH.'/services/InvoiceService.php';

checkLogin(); 

$paymentService = new PaymentService($conn);
$invoiceService = new InvoiceService($conn);

$message = "";

// 1. Handle New Payment Collection (Updated for Multiple Fees)
if (isset($_POST['collect_now'])) {
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
    
    // Get the array of fee names from the hidden JSON input
    $fee_names = json_decode($_POST['selected_fees_json'] ?? '[]', true);
    $paid_amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

    if (empty($fee_names)) {
        $message = "<div class='alert alert-warning'>Please select at least one fee item.</div>";
    } else {
        $data = [
            'student_id' => $student_id,
            'amount'     => $paid_amount,
            'fee_names'  => $fee_names, // Passing the array to PaymentService
            'mode'       => $_POST['payment_mode'] ?? 'CASH',
            'ref'        => $_POST['txn_ref'] ?? '',
            'remarks'    => $_POST['txn_ref'] ?? '', 
            'admin_id'   => $_SESSION['admin_id']
        ];

        $result = $paymentService->processPayment($data);

        if ($result['success']) {
            try {
                $generatedFile = $invoiceService->generateDigitalInvoice($result['payment_id']);
                $pdfPath = "/fees-system/" . $generatedFile;
                
                $message = "<div class='alert alert-success shadow-sm'>
                                <i class='bi bi-check-circle-fill'></i> <strong>Payment Recorded!</strong> Receipt: {$result['receipt_no']} <br>
                                <a href='$pdfPath' target='_blank' class='btn btn-danger btn-sm mt-2'><i class='bi bi-printer'></i> Download Invoice</a>
                            </div>";
            } catch (Exception $e) {
                $message = "<div class='alert alert-warning'>Payment Saved, but Invoice Error: {$e->getMessage()}</div>";
            }
        } else {
            // This captures the "Already paid 2 times" or "Already paid once" validation errors
            $message = "<div class='alert alert-danger'><strong>Payment Blocked:</strong> {$result['message']}</div>";
        }
    }
}

// 2. Fetch Today's Transactions
$today = date('Y-m-d');
$historyQuery = "SELECT p.*, s.FIRST_NAME, s.LAST_NAME, i.FILE_PATH 
                 FROM PAYMENTS p 
                 JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID 
                 LEFT JOIN INVOICES i ON p.PAYMENT_ID = i.PAYMENT_ID
                 WHERE DATE(p.PAYMENT_DATE) = '$today' 
                 ORDER BY p.PAYMENT_ID DESC LIMIT 10";
$historyResult = $conn->query($historyQuery);
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-dark text-white fw-bold">Find Student</div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" id="reg_search" class="form-control" placeholder="Registration No">
                        <button class="btn btn-primary" onclick="searchStudent()"><i class="bi bi-search"></i> Search</button>
                    </div>
                    <div id="student_info" style="display:none;" class="p-3 bg-light rounded border border-primary">
                        <h5 id="view_name" class="text-primary fw-bold mb-1"></h5>
                        <p id="view_course" class="text-muted small mb-2"></p>
                        <hr class="my-2">
                        <div id="balance_container">
                            <div class="h4 text-danger fw-bold" id="view_balance"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <?= $message ?>
            <form method="POST" id="payment_form" style="display:none;">
                <input type="hidden" name="student_id" id="form_student_id">
                <input type="hidden" name="selected_fees_json" id="selected_fees_json">
                
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">Fee Selection</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="small fw-bold text-uppercase">Select Fees to Collect</label>
                                <div id="fee_checkbox_container" class="border rounded p-2 bg-light" style="max-height: 250px; overflow-y: auto;">
                                    </div>
                            </div>

                           <!-- <div class="col-md-6 mb-3">
                                <label class="small fw-bold text-uppercase">Total Payable (INR)</label>
                                <input type="number" step="0.01" name="amount" id="amount_input" class="form-control form-control-lg text-success fw-bold" readonly required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold text-uppercase">Payment Mode</label>
                                <select name="payment_mode" class="form-select form-select-lg">
                                    <option value="CASH">CASH</option>
                                    <option value="UPI">UPI / GPAY</option>
                                    <option value="BANK TRANSFER">BANK TRANSFER</option>
                                </select>
                            </div>
                        </div>
						-->
                        <div class="row">
    						<div class="col-md-4 mb-3">
        						<label class="small fw-bold text-uppercase">Total Payable (INR)</label>
        						<input type="number" id="amount_input" class="form-control form-control-lg bg-light" readonly>
    						</div>

    					<div class="col-md-4 mb-3">
        					<label class="small fw-bold text-uppercase text-primary">Total Paid (INR)</label>
        					<input type="number" step="0.01" name="amount" id="paid_input" class="form-control form-control-lg border-primary fw-bold" 
                                   required oninput="validatePaidAmount()">
        					<div id="payment_warning" class="form-text text-danger"></div>
    					</div>
    
    					<div class="col-md-4 mb-3">
        					<label class="small fw-bold text-uppercase">Payment Mode</label>
        						<select name="payment_mode" class="form-select form-select-lg">
            						<option value="CASH">CASH</option>
                					<option value="UPI">UPI / GPAY</option>
                					<option value="BANK TRANSFER">BANK TRANSFER</option>
        						</select>
    					</div>
						</div>


                        <div class="mb-3">
                            <label class="small fw-bold text-uppercase">Transaction Ref / Note</label>
                            <input type="text" name="txn_ref" class="form-control" placeholder="TXN ID or Remarks">
                        </div>

                        <button type="submit" name="collect_now" class="btn btn-success btn-lg w-100 fw-bold shadow">
                            <i class="bi bi-cash-stack"></i> COLLECT & PRINT RECEIPT
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold border-bottom">
                    <i class="bi bi-clock-history text-primary"></i> Today's Collections
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Mode</th>
                                    <th>Fee Items</th>
                                    <th class="text-center">Print</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($historyResult->num_rows > 0): ?>
                                    <?php while($row = $historyResult->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold"><?= $row['RECEIPT_NO'] ?></td>
                                        <td><?= $row['FIRST_NAME'] ?></td>
                                        
										<td class="fw-bold">&#8377; <?= number_format($row['PAID_AMOUNT'], 2) ?></td>
                                        
                                        <td><?= $row['PAYMENT_MODE'] ?></td>
                                        <td class="small text-muted">
    											<?php 
        										// This converts any '?' back to '₹' for display purposes
        										echo str_replace('?', '&#8377;', $row['REMARKS']); 
    											?>
										</td>
                                        <td class="text-center">
                                            <a href="/fees-system/<?= $row['FILE_PATH'] ?>" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-printer"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let availableFees = []; 
let studentBalance = 0;

function searchStudent() {
    let term = document.getElementById('reg_search').value;
    if(!term) return;

    // Use absolute path to avoid directory depth issues
    fetch(`../../api/search_student.php?term=${term}`)
        .then(res => res.json())
        .then(data => {
            if(data && data.STUDENT_ID) {
                // SUCCESS CASE
                document.getElementById('student_info').style.display = 'block';
                document.getElementById('payment_form').style.display = 'block';
                document.getElementById('form_student_id').value = data.STUDENT_ID;
                document.getElementById('view_name').innerText = data.FIRST_NAME + ' ' + data.LAST_NAME;
                document.getElementById('view_course').innerText = data.COURSE_NAME;
                
                studentBalance = parseFloat(data.BALANCE_AMOUNT);
                availableFees = data.available_fees;
                
                let container = document.getElementById('fee_checkbox_container');
                container.innerHTML = '';
                
                availableFees.forEach((fee, index) => {
                    container.innerHTML += `
                        <div class="form-check border-bottom py-2">
                            <input class="form-check-input fee-item-check" type="checkbox" value="${fee.amount}" id="fee_${index}" data-name="${fee.fees_name}" onchange="updateTotal()">
                            <label class="form-check-label d-flex justify-content-between w-100" for="fee_${index}">
                                <span>${fee.fees_name} <small class="text-muted">(${fee.level})</small></span>
                                <span class="fw-bold">₹${fee.amount}</span>
                            </label>
                        </div>`;
                });

                document.getElementById('view_balance').innerText = `Total Outstanding: ₹${studentBalance.toLocaleString()}`;
            } else { 
                // ERROR CASE
                alert(data.error || 'Student not found!');
                document.getElementById('student_info').style.display = 'none';
                document.getElementById('payment_form').style.display = 'none';
            }
        })
        .catch(err => {
            console.error("Fetch Error:", err);
            alert("Critical Error: Could not connect to the search API.");
        });
}

function updateTotal() {
    let totalPayable = 0;
    document.querySelectorAll('.fee-item-check:checked').forEach(cb => {
        totalPayable += parseFloat(cb.value);
    });
    
    // This is the max amount for the SELECTED items
    document.getElementById('amount_input').value = totalPayable.toFixed(2);
    
    // Auto-fill Paid Input with Total Payable as a default suggestion
    const paidInput = document.getElementById('paid_input');
    if(paidInput.value == "" || paidInput.value == "0") {
        paidInput.value = totalPayable.toFixed(2);
    }
    
    validatePaidAmount();
}

function validatePaidAmount() {
    const totalSelectedMax = parseFloat(document.getElementById('amount_input').value) || 0;
    const totalPaid = parseFloat(document.getElementById('paid_input').value) || 0;
    const warning = document.getElementById('payment_warning');
    const submitBtn = document.querySelector('button[name="collect_now"]');

    // 1. Identify if any "Service" fees are in the current selection
    let hasServiceFee = false;
    document.querySelectorAll('.fee-item-check:checked').forEach(cb => {
        const feeName = cb.getAttribute('data-name');
        const feeData = availableFees.find(f => f.fees_name === feeName);
        if (feeData && ['SEMESTER', 'ONETIME', 'GLOBAL'].includes(feeData.level)) {
            hasServiceFee = true;
        }
    });

    // 2. Validation Logic Chain
    if (totalPaid <= 0) {
        warning.innerText = "Please enter a valid amount.";
        submitBtn.disabled = true;
    } 
    // RULE A: Absolute Master Price Cap (Never overcharge for specific items)
    else if (totalPaid > totalSelectedMax) {
        warning.innerText = `Error: Cannot exceed Master Price (Max: ₹${totalSelectedMax.toLocaleString()})`;
        submitBtn.disabled = true;
    } 
    // RULE B: Ledger Balance Protection (Only applies if NO service fee is selected)
    else if (!hasServiceFee && totalPaid > studentBalance) {
        warning.innerText = `Error: Amount exceeds Ledger Balance (₹${studentBalance.toLocaleString()})`;
        submitBtn.disabled = true;
    } 
    // RULE C: Success Case
    else {
        warning.innerText = "";
        submitBtn.disabled = false;
    }
}

// Single Consolidated Submit Handler
document.getElementById('payment_form').onsubmit = function() {
    const totalPaid = parseFloat(document.getElementById('paid_input').value);
    const selectedBoxes = document.querySelectorAll('.fee-item-check:checked');

    if (selectedBoxes.length === 0) {
        alert("Please select at least one fee item.");
        return false;
    }

    if (totalPaid <= 0) {
        alert("Please enter a valid paid amount.");
        return false;
    }

    // Logic: Sort fees by amount (Max to Min) for the Receipt Description
    let selectedFees = [];
    selectedBoxes.forEach(cb => {
        selectedFees.push({
            name: cb.getAttribute('data-name'),
            amount: parseFloat(cb.value)
        });
    });

    selectedFees.sort((a, b) => b.amount - a.amount);
    
    const namesOnly = selectedFees.map(f => f.name);
    document.getElementById('selected_fees_json').value = JSON.stringify(namesOnly);
    
    return true;
};
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
