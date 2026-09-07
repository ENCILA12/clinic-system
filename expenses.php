<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$clinic_id = $_SESSION['clinic_id'];

// Fetch expenses for current month
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE clinic_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ? ORDER BY expense_date DESC");
$stmt->execute([$clinic_id, $month]);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalExpenses = 0;
foreach($expenses as $e) {
    $totalExpenses += $e['amount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses - DentaFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .expense-summary { background: #fef2f2; border: 1px solid #fca5a5; padding: 20px; border-radius: 8px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .expense-summary h2 { color: #b91c1c; font-size: 24px; margin: 0; }
        .expense-summary p { color: #7f1d1d; margin: 0; font-size: 14px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <main class="content-area">
                <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1><i class="fa-solid fa-money-bill-transfer"></i> Clinic Expenses</h1>
                        <p>Track your daily expenses and calculate net profit.</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" onclick="openExpenseModal()"><i class="fa-solid fa-plus"></i> Add Expense</button>
                    </div>
                </div>

                <div class="expense-summary">
                    <div>
                        <p>Total Expenses (<?php echo date('F Y', strtotime($month . '-01')); ?>)</p>
                        <h2>₱<?php echo number_format($totalExpenses, 2); ?></h2>
                    </div>
                    <div>
                        <form method="GET" style="display:flex; gap:10px;">
                            <input type="month" name="month" class="form-control" value="<?php echo $month; ?>">
                            <button type="submit" class="btn btn-outline" style="padding: 8px 16px;">Filter</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($expenses) > 0): ?>
                                    <?php foreach($expenses as $exp): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($exp['expense_date'])); ?></td>
                                        <td><span class="badge-tag" style="background:#f1f5f9; color:#475569;"><?php echo htmlspecialchars($exp['category']); ?></span></td>
                                        <td><?php echo htmlspecialchars($exp['description']) ?: '-'; ?></td>
                                        <td style="font-weight:600; color:#b91c1c;">₱<?php echo number_format($exp['amount'], 2); ?></td>
                                        <td>
                                            <button class="btn-icon" style="color:var(--danger);" onclick="deleteExpense(<?php echo $exp['id']; ?>)" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" style="text-align:center; padding:20px; color:gray;">No expenses recorded for this month.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal-overlay" id="expenseModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2>Add New Expense</h2>
                <button class="close-modal" onclick="closeExpenseModal()">&times;</button>
            </div>
            <form id="expenseForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <!-- Datalist allows Admin to select default OR type their own custom category -->
                        <input list="categoryOptions" class="form-control" name="category" placeholder="Select or type a category..." required autocomplete="off">
                        <datalist id="categoryOptions">
                            <option value="Dental Supplies">
                            <option value="Utilities (Electricity/Water)">
                            <option value="Salary (Staff/Dentists)">
                            <option value="Rent">
                            <option value="Marketing & Ads">
                            <option value="Office Supplies">
                            <option value="Taxes & Permits">
                        </datalist>
                        <small style="color:gray; font-size:12px;">You can type any custom category you want.</small>
                    </div>
                    <div class="form-group">
                        <label>Amount (₱)</label>
                        <input type="number" step="0.01" class="form-control" name="amount" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Description (Optional)</label>
                        <textarea class="form-control" name="description" placeholder="What is this expense for?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeExpenseModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveExpenseBtn">Save Expense</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openExpenseModal() {
            document.getElementById('expenseModal').classList.add('active');
        }
        function closeExpenseModal() {
            document.getElementById('expenseModal').classList.remove('active');
            document.getElementById('expenseForm').reset();
        }

        document.getElementById('expenseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveExpenseBtn');
            btn.disabled = true;
            btn.innerText = 'Saving...';

            fetch('api/save_expense.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                    btn.disabled = false;
                    btn.innerText = 'Save Expense';
                }
            })
            .catch(err => {
                alert('Server error.');
                btn.disabled = false;
                btn.innerText = 'Save Expense';
            });
        });

        function deleteExpense(id) {
            if(confirm('Are you sure you want to delete this expense?')) {
                fetch('api/delete_expense.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    if(data.success) location.reload();
                    else alert(data.message);
                });
            }
        }
    </script>
</body>
</html>
