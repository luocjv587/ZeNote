<?php
define('DB_PATH', __DIR__ . '/../data/znote.db');

try {
    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Auto-initialize tables to ensure they exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS z_user (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS z_article (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        content TEXT,
        summary TEXT,
        is_pinned INTEGER DEFAULT 0,
        is_deleted INTEGER DEFAULT 0,
        deleted_at DATETIME DEFAULT NULL,
        notebook_id INTEGER NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES z_user(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS z_article_history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        article_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        title TEXT,
        content TEXT,
        summary TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (article_id) REFERENCES z_article(id),
        FOREIGN KEY (user_id) REFERENCES z_user(id)
    )");

    // Images table: store image data separately and reference by image_id
    $pdo->exec("CREATE TABLE IF NOT EXISTS z_image (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        article_id INTEGER NOT NULL,
        image_id TEXT NOT NULL,
        data TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(user_id, image_id),
        FOREIGN KEY (user_id) REFERENCES z_user(id),
        FOREIGN KEY (article_id) REFERENCES z_article(id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS z_notebook (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(user_id, name),
        FOREIGN KEY (user_id) REFERENCES z_user(id)
    )");

    // Migration: Add summary column if it doesn't exist
    try {
        $pdo->query("SELECT summary FROM z_article LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE z_article ADD COLUMN summary TEXT");
    }

    // Migration: Add is_pinned column if it doesn't exist
    try {
        $pdo->query("SELECT is_pinned FROM z_article LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE z_article ADD COLUMN is_pinned INTEGER DEFAULT 0");
    }

    try {
        $pdo->query("SELECT notebook_id FROM z_article LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE z_article ADD COLUMN notebook_id INTEGER NULL");
    }

    // Migration: Add is_deleted and deleted_at
    try {
        $pdo->query("SELECT is_deleted FROM z_article LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE z_article ADD COLUMN is_deleted INTEGER DEFAULT 0");
        $pdo->exec("ALTER TABLE z_article ADD COLUMN deleted_at DATETIME DEFAULT NULL");
    }

    // Migration: Add aliyun_api_key and aliyun_model_name to z_user
    try {
        $pdo->query("SELECT aliyun_api_key FROM z_user LIMIT 1");
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE z_user ADD COLUMN aliyun_api_key TEXT");
        $pdo->exec("ALTER TABLE z_user ADD COLUMN aliyun_model_name TEXT DEFAULT 'qwen-plus'");
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
