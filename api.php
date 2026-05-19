<?php
require 'config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if($action == 'get_services') {
    $stmt = $pdo->query("SELECT id, name, price, duration FROM service WHERE is_active=1");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif($action == 'get_doctors') {
    $stmt = $pdo->query("SELECT id, full_name as name, specialty FROM user WHERE role='doctor' AND is_active=1");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif($action == 'get_free_times') {
    $doctor_id = $_GET['doctor_id'];
    $date = $_GET['date'];
    $stmt = $pdo->prepare("SELECT schedule_json FROM user WHERE id=?");
    $stmt->execute([$doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$doctor || empty($doctor['schedule_json'])) {
        echo json_encode([]);
        exit;
    }
    $schedule = json_decode($doctor['schedule_json'], true);
    if(!is_array($schedule)) $schedule = [];
    $stmt = $pdo->prepare("SELECT appointment_time FROM appointment WHERE doctor_id=? AND appointment_date=? AND status IN ('pending','confirmed')");
    $stmt->execute([$doctor_id, $date]);
    $booked = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $free = array_diff($schedule, $booked);
    echo json_encode(array_values($free));
}
elseif($action == 'create_appointment') {
    if(!isLoggedIn()) { 
        http_response_code(401); 
        echo json_encode(['error'=>'Не авторизован']); 
        exit; 
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $service_id = $data['service_id'];
    $doctor_id = $data['doctor_id'];
    $date = $data['date'];
    $time = $data['time'];
    
    // Проверяем, что время ещё свободно
    $stmt = $pdo->prepare("SELECT schedule_json FROM user WHERE id=?");
    $stmt->execute([$doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    $schedule = json_decode($doctor['schedule_json'], true);
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointment WHERE doctor_id=? AND appointment_date=? AND appointment_time=? AND status IN ('pending','confirmed')");
    $stmt->execute([$doctor_id, $date, $time]);
    $isBooked = $stmt->fetchColumn() > 0;
    
    if($isBooked || !in_array($time, $schedule)) {
        echo json_encode(['error'=>'Это время уже занято']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT price FROM service WHERE id=?");
    $stmt->execute([$service_id]);
    $price = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("INSERT INTO appointment (patient_id, doctor_id, service_id, appointment_date, appointment_time, total_price, status) VALUES (?,?,?,?,?,?,'pending')");
    $stmt->execute([$_SESSION['user_id'], $doctor_id, $service_id, $date, $time, $price]);
    
    // Получаем телефон клиента
    $userPhone = getUserPhone($pdo, $_SESSION['user_id']);
    
    echo json_encode([
        'success'=>true, 
        'appointment_id'=>$pdo->lastInsertId(),
        'phone'=>$userPhone
    ]);
}
elseif($action == 'cancel_appointment' && $_SERVER['REQUEST_METHOD']=='POST') {
    $id = $_POST['appointment_id'];
    $stmt = $pdo->prepare("UPDATE appointment SET status='cancelled' WHERE id=? AND patient_id=?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $_SESSION['flash'] = ['type'=>'success', 'message'=>'Запись отменена'];
    header('Location: cabinet.php');
}
elseif($action == 'get_services_by_category') {
    $category = $_GET['category'] ?? '';
    $stmt = $pdo->prepare("SELECT id, name, price, duration, description FROM service WHERE category=? AND is_active=1");
    $stmt->execute([$category]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif($action == 'get_all_services') {
    $stmt = $pdo->query("SELECT id, name, price, duration, description, category FROM service WHERE is_active=1");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif($action == 'get_service_details') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT id, name, price, duration, description FROM service WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}
elseif($action == 'update_appointment_status' && $_SERVER['REQUEST_METHOD']=='POST') {
    if(!isDoctor() && !isAdmin()) { 
        http_response_code(403); 
        echo json_encode(['error'=>'Доступ запрещён']); 
        exit; 
    }
    $id = $_POST['appointment_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE appointment SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);
    $_SESSION['flash'] = ['type'=>'success', 'message'=>'Статус обновлён'];
    header('Location: cabinet.php');
    exit;
}
elseif($action == 'update_appointment_status' && $_SERVER['REQUEST_METHOD']=='POST') {
    if(!isDoctor() && !isAdmin()) { 
        http_response_code(403); 
        echo json_encode(['error'=>'Доступ запрещён']); 
        exit; 
    }
    $id = $_POST['appointment_id'];
    $status = $_POST['status'];
    
    // Проверяем, что статус допустимый
    $allowedStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if(!in_array($status, $allowedStatuses)) {
        $_SESSION['flash'] = ['type'=>'error', 'message'=>'Недопустимый статус'];
        header('Location: cabinet.php');
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE appointment SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);
    
    $_SESSION['flash'] = ['type'=>'success', 'message'=>'Статус записи обновлён'];
    
    // Возвращаемся на страницу, откуда пришли
    $referer = $_SERVER['HTTP_REFERER'] ?? 'cabinet.php';
    header('Location: ' . $referer);
    exit;
}
// ========== ОТЗЫВЫ ==========
elseif($action == 'get_reviews') {
    $status = $_GET['status'] ?? 'approved';
    $rating = $_GET['rating'] ?? '';
    $sort = $_GET['sort'] ?? 'newest';
    
    $sql = "SELECT r.*, u.full_name as user_name 
            FROM review r 
            JOIN user u ON r.user_id = u.id 
            WHERE r.status = ?";
    $params = [$status];
    
    if($rating && $rating != 'all') {
        $sql .= " AND r.rating = ?";
        $params[] = $rating;
    }
    
    if($sort == 'newest') $sql .= " ORDER BY r.created_at DESC";
    elseif($sort == 'oldest') $sql .= " ORDER BY r.created_at ASC";
    elseif($sort == 'highest') $sql .= " ORDER BY r.rating DESC";
    elseif($sort == 'lowest') $sql .= " ORDER BY r.rating ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reviews = $stmt->fetchAll();
    
    // Получаем среднюю оценку
    $avgStmt = $pdo->query("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM review WHERE status = 'approved'");
    $stats = $avgStmt->fetch();
    
    echo json_encode([
        'reviews' => $reviews,
        'stats' => [
            'avg_rating' => round($stats['avg_rating'] ?? 0, 1),
            'total' => $stats['total'] ?? 0,
            '5_stars' => $pdo->query("SELECT COUNT(*) FROM review WHERE status='approved' AND rating=5")->fetchColumn(),
            '4_stars' => $pdo->query("SELECT COUNT(*) FROM review WHERE status='approved' AND rating=4")->fetchColumn(),
            '3_stars' => $pdo->query("SELECT COUNT(*) FROM review WHERE status='approved' AND rating=3")->fetchColumn(),
            '2_stars' => $pdo->query("SELECT COUNT(*) FROM review WHERE status='approved' AND rating=2")->fetchColumn(),
            '1_stars' => $pdo->query("SELECT COUNT(*) FROM review WHERE status='approved' AND rating=1")->fetchColumn(),
        ]
    ]);
}

elseif($action == 'check_can_review') {
    if(!isLoggedIn()) {
        echo json_encode(['can_review' => false, 'message' => 'Только зарегистрированные пользователи могут оставлять отзывы']);
        exit;
    }
    
    // Проверяем, есть ли у пользователя завершённые записи
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointment WHERE patient_id = ? AND status = 'completed'");
    $stmt->execute([$_SESSION['user_id']]);
    $hasCompleted = $stmt->fetchColumn() > 0;
    
    if(!$hasCompleted) {
        echo json_encode(['can_review' => false, 'message' => 'Вы можете оставить отзыв только после посещения клиники и завершения лечения']);
        exit;
    }
    
    // Проверяем, не оставлял ли пользователь уже отзыв
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM review WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $hasReviewed = $stmt->fetchColumn() > 0;
    
    if($hasReviewed) {
        echo json_encode(['can_review' => false, 'message' => 'Вы уже оставляли отзыв. Спасибо за ваше мнение!']);
        exit;
    }
    
    echo json_encode(['can_review' => true]);
}

elseif($action == 'add_review' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Необходимо авторизоваться']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Проверяем, есть ли завершённые записи
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointment WHERE patient_id = ? AND status = 'completed'");
    $stmt->execute([$_SESSION['user_id']]);
    if($stmt->fetchColumn() == 0) {
        echo json_encode(['success' => false, 'message' => 'Вы можете оставить отзыв только после посещения клиники']);
        exit;
    }
    
    // Проверяем, не оставлял ли отзыв ранее
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM review WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    if($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Вы уже оставляли отзыв']);
        exit;
    }
    
    $rating = (int)$data['rating'];
    $advantages = trim($data['advantages'] ?? '');
    $disadvantages = trim($data['disadvantages'] ?? '');
    $comment = trim($data['comment']);
    
    if($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Некорректная оценка']);
        exit;
    }
    
    if(empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Напишите текст отзыва']);
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO review (user_id, rating, advantages, disadvantages, comment, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$_SESSION['user_id'], $rating, $advantages, $disadvantages, $comment]);
    
    echo json_encode(['success' => true, 'message' => 'Спасибо за отзыв! Он будет опубликован после проверки модератором.']);
}

elseif($action == 'admin_reviews') {
    if(!isAdmin()) {
        echo json_encode(['error' => 'Доступ запрещён']);
        exit;
    }
    
    $stmt = $pdo->query("
        SELECT r.*, u.full_name as user_name, u.email as user_email 
        FROM review r 
        JOIN user u ON r.user_id = u.id 
        ORDER BY 
            CASE WHEN r.status = 'pending' THEN 0 ELSE 1 END,
            r.created_at DESC
    ");
    echo json_encode($stmt->fetchAll());
}

elseif($action == 'moderate_review' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Доступ запрещён']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $review_id = $data['review_id'];
    $status = $data['status'];
    $admin_response = $data['admin_response'] ?? null;
    
    if(!in_array($status, ['approved', 'rejected'])) {
        echo json_encode(['success' => false, 'message' => 'Некорректный статус']);
        exit;
    }
    
    $sql = "UPDATE review SET status = ?";
    $params = [$status];
    
    if($admin_response !== null) {
        $sql .= ", admin_response = ?";
        $params[] = $admin_response;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $review_id;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode(['success' => true]);
}

elseif($action == 'delete_review' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Доступ запрещён']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("DELETE FROM review WHERE id = ?");
    $stmt->execute([$data['review_id']]);
    
    echo json_encode(['success' => true]);
}
else {
    echo json_encode(['error'=>'Unknown action']);
}
?>