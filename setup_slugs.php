<?php
require_once 'includes/db.php';

try {
    // 1. Add slug column if it doesn't exist
    $pdo->exec("ALTER TABLE clinics ADD COLUMN IF NOT EXISTS slug VARCHAR(255) UNIQUE AFTER name");
    echo "Column 'slug' ensured.\n";

    // 2. Fetch all clinics that don't have a slug
    $stmt = $pdo->query("SELECT id, name FROM clinics WHERE slug IS NULL OR slug = ''");
    $clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Generate and update slugs
    $updateStmt = $pdo->prepare("UPDATE clinics SET slug = ? WHERE id = ?");
    
    foreach ($clinics as $clinic) {
        $name = $clinic['name'];
        // Basic slug generation: lowercase, replace non-alphanumeric with hyphen
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $slug = trim($slug, '-');
        
        // Ensure uniqueness (simple approach for existing)
        $base_slug = $slug;
        $counter = 1;
        while (true) {
            $checkStmt = $pdo->prepare("SELECT id FROM clinics WHERE slug = ? AND id != ?");
            $checkStmt->execute([$slug, $clinic['id']]);
            if (!$checkStmt->fetch()) {
                break; // Unique!
            }
            $slug = $base_slug . '-' . $counter;
            $counter++;
        }
        
        $updateStmt->execute([$slug, $clinic['id']]);
        echo "Updated Clinic ID {$clinic['id']} with slug: $slug\n";
    }

    echo "Migration completed successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
