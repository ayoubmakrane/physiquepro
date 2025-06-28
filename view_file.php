<?php
/**
 * ملف عرض الملفات - view_file.php
 * يستخدم لعرض الملفات بشكل آمن
 */

session_start();
require_once 'includes/connection.php';
require_once 'includes/functions.php';

// جلب المعاملات
$content_id = $_GET['content_id'] ?? '';
$file_id = $_GET['file_id'] ?? '';

if (empty($content_id) || empty($file_id)) {
    http_response_code(400);
    die('معرف المحتوى ومعرف الملف مطلوبان');
}

try {
    // جلب بيانات الملف
    $stmt = $pdo->prepare("
        SELECT cf.*, c.title as content_title, c.status
        FROM content_files cf
        JOIN content c ON cf.content_id = c.id
        WHERE cf.id = ? AND cf.content_id = ? AND c.status = 'published'
    ");
    $stmt->execute([$file_id, $content_id]);
    $file_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$file_data) {
        http_response_code(404);
        die('الملف غير موجود أو غير متاح');
    }
    
    // التحقق من نوع التخزين
    if ($file_data['storage_type'] === 'google_drive' && !empty($file_data['google_drive_id'])) {
        // إعادة التوجيه إلى Google Drive
        $view_url = "https://drive.google.com/file/d/" . $file_data['google_drive_id'] . "/view";
        header("Location: " . $view_url);
        exit;
    }
    
    // الملف محلي - إعادة التوجيه إلى serve_file.php
    header("Location: serve_file.php?file_id=" . $file_id);
    exit;
    
} catch (Exception $e) {
    error_log("خطأ في view_file.php: " . $e->getMessage());
    http_response_code(500);
    die('خطأ في الخادم');
}
?> 