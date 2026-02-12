<?php
require_once __DIR__ . '/config.php';

try {
    // 1. Add columns to z_article if they don't exist
    $columns = [
        'is_deleted' => 'INTEGER DEFAULT 0',
        'deleted_at' => 'DATETIME DEFAULT NULL',
        'is_favorite' => 'INTEGER DEFAULT 0'
    ];

    foreach ($columns as $col => $def) {
        try {
            $pdo->exec("ALTER TABLE z_article ADD COLUMN $col $def");
            echo "Added column $col to z_article.<br>";
        } catch (PDOException $e) {
            // Column likely exists
            echo "Column $col likely exists (or error: " . $e->getMessage() . ").<br>";
        }
    }

    // 2. Create z_article_history table
    $createHistoryTable = "CREATE TABLE IF NOT EXISTS z_article_history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        article_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        title TEXT,
        content TEXT,
        summary TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (article_id) REFERENCES z_article(id),
        FOREIGN KEY (user_id) REFERENCES z_user(id)
    )";
    $pdo->exec($createHistoryTable);
    echo "Table z_article_history checked/created.<br>";

    // 3. Update db_init.php reference (optional, but good for consistency)
    // We won't modify the file here, but we've updated the DB.

    echo "Migration completed successfully.";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage();
}
?>
