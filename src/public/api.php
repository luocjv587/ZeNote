<?php
ini_set('session.gc_maxlifetime', 604800);
session_set_cookie_params(604800);
session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

function zenote_send_backup_email($pdo, $userId)
{
    if (!file_exists(DB_PATH)) {
        return ['success' => false, 'error' => 'Database file not found'];
    }
    $stmt = $pdo->prepare("SELECT qq_email_account, qq_email_password, qq_email_to FROM z_user WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) {
        return ['success' => false, 'error' => 'User not found'];
    }
    $from = trim($user['qq_email_account'] ?? '');
    $password = trim($user['qq_email_password'] ?? '');
    $to = trim($user['qq_email_to'] ?? '');
    if ($from === '' || $password === '' || $to === '') {
        return ['success' => false, 'error' => '邮箱配置不完整'];
    }
    $subject = 'ZeNote 数据库备份';
    $boundary = md5(uniqid((string)time(), true));
    $filename = 'zenote-' . date('Ymd-His') . '.db';
    $headers = [];
    $headers[] = 'From: ' . $from;
    $headers[] = 'To: ' . $to;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';
    $body = '';
    $body .= '--' . $boundary . "\r\n";
    $body .= 'Content-Type: text/plain; charset="utf-8"' . "\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= "附件为当前 ZeNote SQLite 数据库备份文件，请妥善保存。\r\n\r\n";
    $body .= '--' . $boundary . "\r\n";
    $body .= 'Content-Type: application/octet-stream; name="' . $filename . '"' . "\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= 'Content-Disposition: attachment; filename="' . $filename . '"' . "\r\n\r\n";
    $body .= chunk_split(base64_encode(file_get_contents(DB_PATH))) . "\r\n";
    $body .= '--' . $boundary . "--\r\n";
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $smtpHost = 'ssl://smtp.qq.com';
    $smtpPort = 465;
    $errno = 0;
    $errstr = '';
    $fp = @stream_socket_client($smtpHost . ':' . $smtpPort, $errno, $errstr, 30);
    if (!$fp) {
        return ['success' => false, 'error' => '无法连接 QQ SMTP: ' . $errstr];
    }
    stream_set_timeout($fp, 30);
    $line = fgets($fp, 515);
    if (substr($line, 0, 3) !== '220') {
        fclose($fp);
        return ['success' => false, 'error' => 'SMTP 欢迎信息异常: ' . trim($line)];
    }
    fwrite($fp, "EHLO localhost\r\n");
    $ehloOk = false;
    while ($l = fgets($fp, 515)) {
        $code = substr($l, 0, 3);
        $ehloOk = $code === '250' ? true : $ehloOk;
        if (isset($l[3]) && $l[3] !== '-') {
            break;
        }
    }
    if (!$ehloOk) {
        fclose($fp);
        return ['success' => false, 'error' => 'SMTP EHLO 失败'];
    }
    fwrite($fp, "AUTH LOGIN\r\n");
    $l = fgets($fp, 515);
    if (substr($l, 0, 3) !== '334') {
        fclose($fp);
        return ['success' => false, 'error' => 'SMTP 不支持 AUTH LOGIN'];
    }
    fwrite($fp, base64_encode($from) . "\r\n");
    $l = fgets($fp, 515);
    if (substr($l, 0, 3) !== '334') {
        fclose($fp);
        return ['success' => false, 'error' => 'SMTP 用户名被拒绝'];
    }
    fwrite($fp, base64_encode($password) . "\r\n");
    $l = fgets($fp, 515);
    if (substr($l, 0, 3) !== '235') {
        fclose($fp);
        return ['success' => false, 'error' => 'SMTP 授权码错误或被拒绝'];
    }
    fwrite($fp, "MAIL FROM:<" . $from . ">\r\n");
    $l = fgets($fp, 515);
    if (substr($l, 0, 3) !== '250') {
        fclose($fp);
        return ['success' => false, 'error' => 'SMTP MAIL FROM 失败: ' . trim($l)];
    }
    fwrite($fp, "RCPT TO:<" . $to . ">\r\n");
    $l = fgets($fp, 515);
    if ($l === false || (substr($l, 0, 3) !== '250' && substr($l, 0, 3) !== '251')) {
        fclose($fp);
        return ['success' => false, 'error' => 'SMTP RCPT TO 失败: ' . trim((string)$l)];
    }
    fwrite($fp, "DATA\r\n");
    $l = fgets($fp, 515);
    if (substr($l, 0, 3) !== '354') {
        fclose($fp);
        return ['success' => false, 'error' => 'SMTP DATA 阶段失败: ' . trim($l)];
    }
    $data = '';
    $data .= implode("\r\n", $headers) . "\r\n";
    $data .= "Subject: " . $encodedSubject . "\r\n\r\n";
    $data .= $body . "\r\n";
    fwrite($fp, $data . "\r\n.\r\n");
    $l = fgets($fp, 515);
    if (substr($l, 0, 3) !== '250') {
        fclose($fp);
        return ['success' => false, 'error' => 'SMTP 发送失败: ' . trim($l)];
    }
    fwrite($fp, "QUIT\r\n");
    fclose($fp);
    $stmt = $pdo->prepare("UPDATE z_user SET qq_email_last_sent_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$userId]);
    $stmt = $pdo->prepare("SELECT qq_email_last_sent_at FROM z_user WHERE id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    return [
        'success' => true,
        'last_sent_at' => $row && !empty($row['qq_email_last_sent_at']) ? $row['qq_email_last_sent_at'] : null
    ];
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? '';

if ($action === 'register' && $method === 'POST') {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    if (!$username || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM z_user WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Username already exists']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO z_user (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $hash])) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Registration failed']);
    }
    exit;
}

if ($action === 'login' && $method === 'POST') {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, password FROM z_user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        echo json_encode(['success' => true]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
    exit;
}

if ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

// Middleware for protected routes
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($action === 'get_notes' && $method === 'GET') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $notebookId = isset($_GET['notebook_id']) && $_GET['notebook_id'] !== '' ? (int)$_GET['notebook_id'] : null;
    $trash = isset($_GET['trash']) && $_GET['trash'] === '1';
    $favorites = isset($_GET['favorites']) && $_GET['favorites'] === '1';
    $offset = ($page - 1) * $limit;

    $sql = "SELECT id, title, updated_at, summary as preview, is_pinned, is_deleted, deleted_at, is_favorite FROM z_article WHERE user_id = ?";
    $params = [$user_id];

    if ($trash) {
        $sql .= " AND is_deleted = 1";
    } else {
        $sql .= " AND is_deleted = 0";
    }

    if ($favorites && !$trash) {
        $sql .= " AND is_favorite = 1";
    }

    if ($notebookId !== null && !$trash) {
        $sql .= " AND notebook_id = ?";
        $params[] = $notebookId;
    }

    if ($q) {
        $sql .= " AND (title LIKE ? OR summary LIKE ?)";
        $params[] = "%$q%";
        $params[] = "%$q%";
    }

    $sql .= " ORDER BY is_pinned DESC, updated_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $notes = $stmt->fetchAll();
    
    echo json_encode(['notes' => $notes]);
    exit;
}

if ($action === 'toggle_favorite' && $method === 'POST') {
    $id = $input['id'] ?? 0;
    $isFavorite = isset($input['is_favorite']) ? (int)$input['is_favorite'] : 0;
    
    $stmt = $pdo->prepare("UPDATE z_article SET is_favorite = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
    $stmt->execute([$isFavorite, $id, $user_id]);
    
    echo json_encode(['success' => true, 'is_favorite' => $isFavorite]);
    exit;
}

if ($action === 'get_image' && $method === 'GET') {
    $imageId = isset($_GET['image_id']) ? trim($_GET['image_id']) : '';
    if (!$imageId) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid parameters']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT data FROM z_image WHERE user_id = ? AND image_id = ?");
    $stmt->execute([$user_id, $imageId]);
    $img = $stmt->fetch();
    if ($img && !empty($img['data'])) {
        echo json_encode(['image_id' => $imageId, 'src' => $img['data']]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Image not found']);
    }
    exit;
}

if ($action === 'get_note' && $method === 'GET') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT id, title, updated_at, summary, is_pinned, content, notebook_id, is_favorite FROM z_article WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $note = $stmt->fetch();
    if ($note) {
        echo json_encode(['note' => $note]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Note not found']);
    }
    exit;
}

if ($action === 'get_note_content' && $method === 'GET') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT content FROM z_article WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $note = $stmt->fetch();
    if ($note) {
        echo json_encode(['id' => (int)$id, 'content' => $note['content'] ?? '']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Note not found']);
    }
    exit;
}
if ($action === 'save_note' && $method === 'POST') {
    $id = $input['id'] ?? null;
    $title = $input['title'] ?? '';
    $content = $input['content'] ?? '';
    $notebookId = array_key_exists('notebook_id', $input) && $input['notebook_id'] !== '' ? (int)$input['notebook_id'] : null;

    // Extract images into z_image and replace content with placeholders
    $images = [];
    $replacedContent = preg_replace_callback('/<img\b[^>]*>/i', function ($matches) use (&$images) {
        $imgTag = $matches[0];
        $src = '';
        if (preg_match('/src=["\']([^"\']+)["\']/', $imgTag, $s)) {
            $src = $s[1];
        }
        $imgId = '';
        if (preg_match('/data-image-id=["\']([^"\']+)["\']/', $imgTag, $m)) {
            $imgId = $m[1];
        }
        if (!$imgId) {
            $basis = $src ?: $imgTag;
            $imgId = substr(sha1($basis), 0, 12);
        }
        $images[] = ['image_id' => $imgId, 'data' => $src];
        return '<img data-image-id="' . $imgId . '" alt="image-' . $imgId . '" src="about:blank">';
    }, $content);

    // Build summary text with image placeholders
    $summaryText = preg_replace_callback('/<img\b[^>]*>/i', function ($m) {
        $tag = $m[0];
        $imgId = '';
        if (preg_match('/data-image-id=["\']([^"\']+)["\']/', $tag, $mm)) {
            $imgId = $mm[1];
        }
        return $imgId ? " [图片-$imgId] " : " [图片] ";
    }, $replacedContent);
    $plainText = trim(html_entity_decode(strip_tags($summaryText)));
    $plainText = preg_replace('/\s+/u', ' ', $plainText);
    $summary = $plainText;

    if ($id) {
        // History Logic: Save current version if changed
        $stmtCurr = $pdo->prepare("SELECT title, content, summary FROM z_article WHERE id = ? AND user_id = ?");
        $stmtCurr->execute([$id, $user_id]);
        $curr = $stmtCurr->fetch();
        if ($curr && ($curr['content'] !== $replacedContent || $curr['title'] !== $title)) {
             $stmtHist = $pdo->prepare("INSERT INTO z_article_history (article_id, user_id, title, content, summary) VALUES (?, ?, ?, ?, ?)");
             $stmtHist->execute([$id, $user_id, $curr['title'], $curr['content'], $curr['summary']]);
        }

        // Update note with replaced content
        $stmt = $pdo->prepare("UPDATE z_article SET title = ?, content = ?, summary = ?, notebook_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $replacedContent, $summary, $notebookId, $id, $user_id]);
        // Upsert images
        foreach ($images as $img) {
            if (!empty($img['data'])) {
                $stmt = $pdo->prepare("INSERT INTO z_image (user_id, article_id, image_id, data) VALUES (?, ?, ?, ?)
                    ON CONFLICT(user_id, image_id) DO UPDATE SET article_id=excluded.article_id, data=excluded.data, updated_at=CURRENT_TIMESTAMP");
                $stmt->execute([$user_id, $id, $img['image_id'], $img['data']]);
            }
        }
        echo json_encode(['id' => $id, 'success' => true]);
    } else {
        // Create note with replaced content
        $stmt = $pdo->prepare("INSERT INTO z_article (user_id, title, content, summary, notebook_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $replacedContent, $summary, $notebookId]);
        $newId = (int)$pdo->lastInsertId();
        // Insert images
        foreach ($images as $img) {
            if (!empty($img['data'])) {
                $stmt = $pdo->prepare("INSERT INTO z_image (user_id, article_id, image_id, data) VALUES (?, ?, ?, ?)
                    ON CONFLICT(user_id, image_id) DO UPDATE SET article_id=excluded.article_id, data=excluded.data, updated_at=CURRENT_TIMESTAMP");
                $stmt->execute([$user_id, $newId, $img['image_id'], $img['data']]);
            }
        }
        echo json_encode(['id' => $newId, 'success' => true]);
    }
    exit;
}

if ($action === 'get_notebooks' && $method === 'GET') {
    $stmt = $pdo->prepare("SELECT id, name FROM z_notebook WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();
    echo json_encode(['notebooks' => $items]);
    exit;
}

if ($action === 'create_notebook' && $method === 'POST') {
    $name = trim($input['name'] ?? '');
    if ($name === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Name required']);
        exit;
    }
    try {
        $stmt = $pdo->prepare("INSERT INTO z_notebook (user_id, name) VALUES (?, ?)");
        $stmt->execute([$user_id, $name]);
        echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId(), 'name' => $name]);
    } catch (PDOException $e) {
        http_response_code(409);
        echo json_encode(['error' => 'Notebook already exists']);
    }
    exit;
}

if ($action === 'delete_notebook' && $method === 'POST') {
    $id = $input['id'] ?? 0;
    
    // First, set notebook_id to NULL for all notes in this notebook
    $stmt = $pdo->prepare("UPDATE z_article SET notebook_id = NULL WHERE notebook_id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    
    // Then delete the notebook
    $stmt = $pdo->prepare("DELETE FROM z_notebook WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'set_note_notebook' && $method === 'POST') {
    $id = $input['id'] ?? 0;
    $notebookId = array_key_exists('notebook_id', $input) && $input['notebook_id'] !== '' ? (int)$input['notebook_id'] : null;
    $stmt = $pdo->prepare("UPDATE z_article SET notebook_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
    $stmt->execute([$notebookId, $id, $user_id]);
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'delete_note' && $method === 'POST') {
    $id = $input['id'] ?? 0;
    $force = $input['force'] ?? false;
    
    if ($force) {
        // Delete history first to avoid foreign key constraints issues
        $stmt = $pdo->prepare("DELETE FROM z_article_history WHERE article_id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        
        $stmt = $pdo->prepare("DELETE FROM z_article WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE z_article SET is_deleted = 1, deleted_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
    }
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'restore_note' && $method === 'POST') {
    $id = $input['id'] ?? 0;
    $stmt = $pdo->prepare("UPDATE z_article SET is_deleted = 0, deleted_at = NULL WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'get_history' && $method === 'GET') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT id, created_at, title, summary, length(content) as size FROM z_article_history WHERE article_id = ? AND user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$id, $user_id]);
    $history = $stmt->fetchAll();
    echo json_encode(['history' => $history]);
    exit;
}

if ($action === 'get_history_detail' && $method === 'GET') {
    $historyId = $_GET['history_id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM z_article_history WHERE id = ? AND user_id = ?");
    $stmt->execute([$historyId, $user_id]);
    $item = $stmt->fetch();
    if ($item) {
        echo json_encode(['history' => $item]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'History not found']);
    }
    exit;
}

if ($action === 'toggle_pin' && $method === 'POST') {
    $id = $input['id'] ?? 0;
    // First get current status
    $stmt = $pdo->prepare("SELECT is_pinned FROM z_article WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $note = $stmt->fetch();
    
    if ($note) {
        $newStatus = $note['is_pinned'] ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE z_article SET is_pinned = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$newStatus, $id, $user_id]);
        echo json_encode(['success' => true, 'is_pinned' => $newStatus]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Note not found']);
    }
    exit;
}

if ($action === 'get_db_info' && $method === 'GET') {
    $size = file_exists(DB_PATH) ? filesize(DB_PATH) : 0;
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $display = $size;
    $unitIndex = 0;
    while ($display >= 1024 && $unitIndex < count($units) - 1) {
        $display /= 1024;
        $unitIndex++;
    }
    $sizeText = $unitIndex === 0 ? $display . ' ' . $units[$unitIndex] : number_format($display, 2) . ' ' . $units[$unitIndex];
    echo json_encode(['size_bytes' => $size, 'size_text' => $sizeText]);
    exit;
}

if ($action === 'download_db' && $method === 'GET') {
    if (!file_exists(DB_PATH)) {
        http_response_code(404);
        echo json_encode(['error' => 'Database not found']);
        exit;
    }
    $filename = 'zenote-' . date('Ymd-His') . '.db';
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize(DB_PATH));
    readfile(DB_PATH);
    exit;
}

if ($action === 'test_backup_email' && $method === 'POST') {
    $result = zenote_send_backup_email($pdo, $user_id);
    if ($result['success']) {
        echo json_encode(['success' => true, 'last_sent_at' => $result['last_sent_at']]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => $result['error'] ?? 'Unknown error']);
    }
    exit;
}

if ($action === 'maybe_send_backup_email' && $method === 'POST') {
    $stmt = $pdo->prepare("SELECT qq_email_auto_enabled, qq_email_last_sent_at FROM z_user WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    if (!(int)($user['qq_email_auto_enabled'] ?? 0)) {
        echo json_encode(['success' => true, 'ran' => false]);
        exit;
    }
    $lastSent = $user['qq_email_last_sent_at'] ?? null;
    $shouldSend = false;
    if (!$lastSent) {
        $shouldSend = true;
    } else {
        $lastTs = strtotime($lastSent);
        if ($lastTs === false) {
            $shouldSend = true;
        } else {
            if (time() - $lastTs >= 86400) {
                $shouldSend = true;
            }
        }
    }
    if (!$shouldSend) {
        echo json_encode(['success' => true, 'ran' => false, 'last_sent_at' => $lastSent]);
        exit;
    }
    $result = zenote_send_backup_email($pdo, $user_id);
    if ($result['success']) {
        echo json_encode(['success' => true, 'ran' => true, 'last_sent_at' => $result['last_sent_at']]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => $result['error'] ?? 'Unknown error']);
    }
    exit;
}

if ($action === 'get_settings' && $method === 'GET') {
    $stmt = $pdo->prepare("SELECT aliyun_api_key, aliyun_model_name, qq_email_account, qq_email_to, qq_email_auto_enabled, qq_email_last_sent_at FROM z_user WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    echo json_encode([
        'aliyun_api_key' => $user['aliyun_api_key'] ?? '',
        'aliyun_model_name' => $user['aliyun_model_name'] ?? 'qwen-plus',
        'qq_email_account' => $user['qq_email_account'] ?? '',
        'qq_email_to' => $user['qq_email_to'] ?? '',
        'qq_email_auto_enabled' => (int)($user['qq_email_auto_enabled'] ?? 0),
        'qq_email_last_sent_at' => $user['qq_email_last_sent_at'] ?? null
    ]);
    exit;
}

if ($action === 'save_settings' && $method === 'POST') {
    $apiKey = trim($input['aliyun_api_key'] ?? '');
    $modelName = trim($input['aliyun_model_name'] ?? 'qwen-plus');
    $qqAccount = array_key_exists('qq_email_account', $input) ? trim($input['qq_email_account']) : null;
    $qqPassword = array_key_exists('qq_email_password', $input) ? trim($input['qq_email_password']) : null;
    $qqTo = array_key_exists('qq_email_to', $input) ? trim($input['qq_email_to']) : null;
    $qqAutoEnabled = array_key_exists('qq_email_auto_enabled', $input) ? (int)$input['qq_email_auto_enabled'] : null;

    $stmt = $pdo->prepare("SELECT qq_email_account, qq_email_password, qq_email_to, qq_email_auto_enabled FROM z_user WHERE id = ?");
    $stmt->execute([$user_id]);
    $current = $stmt->fetch();

    $newAccount = $qqAccount !== null ? $qqAccount : ($current['qq_email_account'] ?? '');
    $newPassword = $qqPassword !== null && $qqPassword !== '' ? $qqPassword : ($current['qq_email_password'] ?? '');
    $newTo = $qqTo !== null ? $qqTo : ($current['qq_email_to'] ?? '');
    $newAutoEnabled = $qqAutoEnabled !== null ? $qqAutoEnabled : (int)($current['qq_email_auto_enabled'] ?? 0);

    $stmt = $pdo->prepare("UPDATE z_user SET aliyun_api_key = ?, aliyun_model_name = ?, qq_email_account = ?, qq_email_password = ?, qq_email_to = ?, qq_email_auto_enabled = ? WHERE id = ?");
    $stmt->execute([$apiKey, $modelName, $newAccount, $newPassword, $newTo, $newAutoEnabled, $user_id]);

    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
?>
