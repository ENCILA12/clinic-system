<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dental Clinic - Inventory</title>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <link rel='stylesheet' href='css/style.css'>
</head>
<body>
    <div class='dashboard-container'>
        <?php
 include 'includes/sidebar.php'; ?>
        <div class='main-content'>
            <?php
 include 'includes/header.php'; ?>
            <main class='content-area'>
                <div class='page-header' style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <h1>Inventory & Supplies</h1>
                        <p>Track clinic consumables, stock levels, and expiration dates.</p>
                    </div>
                    <button class="btn btn-primary" id="addInventoryBtn"><i class="fa-solid fa-box-open"></i> Add New Item</button>
                </div>
                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <form method="GET" action="inventory.php" style="display:flex; gap:8px;">
                        <input type="text" class="form-control" name="search" placeholder="Search item..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="width:250px;">
                        <button type="submit" class="btn btn-outline"><i class="fa-solid fa-search"></i> Search</button>
                        <?php if(!empty($_GET['search'])): ?>
                            <a href="inventory.php" class="btn btn-outline" style="text-decoration:none; color:gray; border:none;">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Current Stock</th>
                                <th>Minimum Stock</th>
                                <th>Expiration Date</th>
                                <th>Status / Alert</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $search = $_GET['search'] ?? '';
                                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                                $limit = 10;
                                $offset = ($page - 1) * $limit;

                                // Count total for pagination
                                if ($search !== '') {
                                    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM inventory WHERE item_name LIKE ?");
                                    $stmtCount->execute(["%$search%"]);
                                } else {
                                    $stmtCount = $pdo->query("SELECT COUNT(*) FROM inventory");
                                }
                                $totalRows = $stmtCount->fetchColumn();
                                $totalPages = ceil($totalRows / $limit);

                                // Fetch data
                                if ($search !== '') {
                                    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE item_name LIKE :search ORDER BY item_name ASC LIMIT :limit OFFSET :offset");
                                    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
                                } else {
                                    $stmt = $pdo->prepare("SELECT * FROM inventory ORDER BY item_name ASC LIMIT :limit OFFSET :offset");
                                }
                                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                                $stmt->execute();
                                
                                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if(count($items) > 0) {
                                    foreach($items as $i) {
                                        // Calculate Status
                                        $statusClass = 'alert-good';
                                        $statusText = 'Good';
                                        
                                        $isLowStock = ($i['current_stock'] <= $i['minimum_stock']);
                                        $isExpired = false;
                                        $isNearExpiry = false;

                                        if ($i['expiration_date']) {
                                            $expDate = new DateTime($i['expiration_date']);
                                            $today = new DateTime();
                                            $interval = $today->diff($expDate);
                                            $days = (int)$interval->format('%R%a'); // e.g. +30 or -5

                                            if ($days < 0) {
                                                $isExpired = true;
                                            } elseif ($days <= 30) {
                                                $isNearExpiry = true;
                                            }
                                        }

                                        // Prioritize Expired > Low Stock > Near Expiry
                                        if ($isExpired) {
                                            $statusClass = 'alert-expired';
                                            $statusText = 'Expired';
                                        } elseif ($isLowStock) {
                                            $statusClass = 'alert-lowstock';
                                            $statusText = 'Low Stock';
                                        } elseif ($isNearExpiry) {
                                            $statusClass = 'alert-nearexpiry';
                                            $statusText = 'Near Expiry';
                                        }

                                        echo "<tr>";
                                        echo "<td><strong>" . htmlspecialchars($i['item_name']) . "</strong></td>";
                                        
                                        $stockColor = $isLowStock ? 'color:#ef4444; font-weight:700;' : '';
                                        echo "<td style='$stockColor'>" . htmlspecialchars($i['current_stock']) . "</td>";
                                        
                                        echo "<td>" . htmlspecialchars($i['minimum_stock']) . "</td>";
                                        
                                        $expStr = $i['expiration_date'] ? date('M d, Y', strtotime($i['expiration_date'])) : '<span style="color:#cbd5e1;">N/A</span>';
                                        echo "<td>" . $expStr . "</td>";
                                        
                                        echo "<td><span class='badge-tag $statusClass'>$statusText</span></td>";
                                        
                                        echo "<td class='action-links'>
                                                <a href='#' class='update-stock-btn' 
                                                    data-id='" . $i['id'] . "' 
                                                    data-name='" . htmlspecialchars($i['item_name']) . "'
                                                    data-stock='" . $i['current_stock'] . "'
                                                    data-min='" . $i['minimum_stock'] . "'
                                                    data-exp='" . $i['expiration_date'] . "'
                                                ><i class='fa-solid fa-boxes-stacked'></i> Update</a>
                                                
                                                <a href='#' class='delete-stock-btn' 
                                                    data-id='" . $i['id'] . "' 
                                                    data-name='" . htmlspecialchars($i['item_name']) . "'
                                                    style='color:var(--danger); margin-left:8px;'
                                                ><i class='fa-solid fa-trash'></i> Delete</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' style='text-align:center;'>No inventory items found.</td></tr>";
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='6' style='text-align:center; color:red;'>Database Error: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if(isset($totalPages) && $totalPages > 1): ?>
                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:16px;">
                    <?php if($page > 1): ?>
                        <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>" class="btn btn-outline" style="text-decoration:none;">&laquo; Prev</a>
                    <?php endif; ?>
                    
                    <div style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; background:white;">
                        Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                    </div>

                    <?php if($page < $totalPages): ?>
                        <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>" class="btn btn-outline" style="text-decoration:none;">Next &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <!-- Update / Add Item Modal -->
    <div class="modal-overlay" id="inventoryModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2 id="modalTitle">Inventory Item</h2>
                <button class="close-modal" id="closeInvModalBtn">&times;</button>
            </div>
            <form id="inventoryForm">
                <input type="hidden" name="id" id="invId">
                <div class="modal-body">
                    <div class="form-group full-width">
                        <label>Item Name</label>
                        <input type="text" class="form-control" name="item_name" id="invName" required>
                    </div>
                    
                    <div style="display:flex; gap:16px;">
                        <div class="form-group" style="flex:1;">
                            <label>Current Stock</label>
                            <input type="number" class="form-control" name="current_stock" id="invStock" min="0" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Minimum Stock Alert</label>
                            <input type="number" class="form-control" name="minimum_stock" id="invMin" min="0" required>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Expiration Date (Optional)</label>
                        <input type="date" class="form-control" name="expiration_date" id="invExp">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancelInvModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveInvBtn">Save Item</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/inventory.js"></script>
</body>
</html>

