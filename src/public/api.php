<?php
session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

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
    $offset = ($page - 1) * $limit;

    $sql = "SELECT id, title, updated_at, summary as preview, is_pinned FROM z_article WHERE user_id = ?";
    $params = [$user_id];

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

if ($action === 'get_note' && $method === 'GET') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM z_article WHERE id = ? AND user_id = ?");
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

if ($action === 'save_note' && $method === 'POST') {
    $id = $input['id'] ?? null;
    $title = $input['title'] ?? 'Untitled';
    $content = $input['content'] ?? '';

    if ($id) {
        $withPlaceholders = preg_replace('/<img\b[^>]*>/i', ' [图片] ', $content);
        $plainText = trim(html_entity_decode(strip_tags($withPlaceholders)));
        $plainText = preg_replace('/\s+/u', ' ', $plainText);
        $summary = $plainText;

        // Update
        $stmt = $pdo->prepare("UPDATE z_article SET title = ?, content = ?, summary = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $summary, $id, $user_id]);
        echo json_encode(['id' => $id, 'success' => true]);
    } else {
        $withPlaceholders = preg_replace('/<img\b[^>]*>/i', ' [图片] ', $content);
        $plainText = trim(html_entity_decode(strip_tags($withPlaceholders)));
        $plainText = preg_replace('/\s+/u', ' ', $plainText);
        $summary = $plainText;

        // Create
        $stmt = $pdo->prepare("INSERT INTO z_article (user_id, title, content, summary) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $content, $summary]);
        echo json_encode(['id' => $pdo->lastInsertId(), 'success' => true]);
    }
    exit;
}

if ($action === 'delete_note' && $method === 'POST') {
    $id = $input['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM z_article WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    echo json_encode(['success' => true]);
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

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
?>
