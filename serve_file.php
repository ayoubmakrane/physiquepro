<?php
/**
 * ملف لخدمة الملفات من uploads/content أو Google Drive
 */

include 'includes/connection.php';
include 'includes/download_helper.php';

// جلب معرف الملف أو اسم الملف
$file_id = $_GET['file_id'] ?? '';
$filename = $_GET['file'] ?? '';

if (empty($file_id) && empty($filename)) {
    http_response_code(400);
    die('معرف الملف أو اسم الملف مطلوب');
}

// إذا تم توفير معرف الملف، جلب المعلومات من قاعدة البيانات
if (!empty($file_id)) {
    try {
        $stmt = $pdo->prepare("
            SELECT cf.file_name, cf.file_path, cf.file_type, cf.storage_type, cf.google_drive_id, c.title
            FROM content_files cf
            JOIN content c ON cf.content_id = c.id
            WHERE cf.id = ?
        ");
        $stmt->execute([$file_id]);
        $file_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$file_data) {
    http_response_code(404);
    die('الملف غير موجود');
}

        // إذا كان الملف في Google Drive، إعادة التوجيه
        if ($file_data['storage_type'] === 'google_drive' && !empty($file_data['google_drive_id'])) {
            $google_drive_url = "https://drive.google.com/file/d/" . $file_data['google_drive_id'] . "/view";
            header("Location: " . $google_drive_url);
            exit;
        }
        
        // الملف محلي
        $original_filename = $file_data['file_name'];
        $file_path = __DIR__ . '/uploads/content/' . $file_data['file_path'];
        $content_type = $file_data['file_type'];
        
        // إنشاء اسم ملف مخصص بناءً على عنوان المحتوى
        $filename = generateCustomFilename($file_data['title'], $original_filename);
        
    } catch (Exception $e) {
        error_log("خطأ في serve_file.php: " . $e->getMessage());
        http_response_code(500);
        die('خطأ في الخادم');
    }
} else {
    // الطريقة القديمة - خدمة الملف بالاسم مباشرة
    $filename = basename($filename);
    $file_path = __DIR__ . '/uploads/content/' . $filename;

// تحديد نوع المحتوى
$file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

switch ($file_extension) {
    case 'pdf':
        $content_type = 'application/pdf';
        break;
    case 'jpg':
    case 'jpeg':
        $content_type = 'image/jpeg';
        break;
    case 'png':
        $content_type = 'image/png';
        break;
    case 'gif':
        $content_type = 'image/gif';
        break;
    default:
        $content_type = 'application/octet-stream';
        break;
    }
}

// البحث عن المسار الصحيح للملف
$correct_path = null;
$possible_paths = [
    $file_path,                                    // المسار كما هو
    __DIR__ . '/uploads/content/' . basename($file_path), // اسم الملف فقط في uploads/content
    __DIR__ . '/uploads/content/' . $filename,     // اسم الملف المطلوب
    'uploads/content/' . $filename,                // مسار نسبي
    'uploads/content/' . basename($file_path)      // مسار نسبي مع اسم الملف
];

foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $correct_path = $path;
        error_log("serve_file.php: Found file at: $path");
        break;
    }
}

if (!$correct_path) {
    error_log("serve_file.php: File not found in any expected location. Searched paths:");
    foreach ($possible_paths as $path) {
        error_log("  - $path");
    }
    http_response_code(404);
    die('الملف غير موجود');
}

// تحديث مسار الملف
$file_path = $correct_path;

// التحقق من أن الملف في المجلد المسموح
$real_path = realpath($file_path);
$allowed_dir = realpath(__DIR__ . '/uploads/content/');

// تسجيل المسارات للتشخيص
error_log("serve_file.php: Real path: $real_path");
error_log("serve_file.php: Allowed dir: $allowed_dir");

if ($real_path && $allowed_dir && strpos($real_path, $allowed_dir) !== 0) {
    error_log("serve_file.php: File not in allowed directory");
    http_response_code(403);
    die('غير مسموح بالوصول لهذا الملف');
}

// إرسال headers
header('Content-Type: ' . $content_type);
header('Content-Length: ' . filesize($file_path));
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: public, max-age=86400');
header('X-Frame-Options: SAMEORIGIN'); // السماح بعرض الملف في iframe من نفس الموقع

// إرسال الملف
readfile($file_path);
?> 
