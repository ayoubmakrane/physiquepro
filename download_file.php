<?php
/**
 * ملف تحميل الملفات - مع دعم Google Drive وصلاحيات الأمان
 */

// بدء الجلسة
session_start();

require_once 'includes/connection.php';
require_once 'includes/download_helper.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?message=' . urlencode('يجب تسجيل الدخول للتحميل'));
    exit;
}

// جلب المعاملات
$content_id = $_GET['content_id'] ?? '';
$file_id = $_GET['file_id'] ?? '';

if (empty($content_id) && empty($file_id)) {
    header('Location: index.php?error=' . urlencode('معرف المحتوى أو الملف مطلوب'));
    exit;
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

try {
    // التحقق من صلاحيات التحميل
    if ($user_type === 'visitor') {
        $redirect_url = !empty($content_id) ? "content_router.php?id=$content_id" : "index.php";
        header('Location: ' . $redirect_url . '&error=' . urlencode('يجب تسجيل الدخول للتحميل'));
        exit;
    }
    
    // إذا تم توفير معرف ملف محدد
    if (!empty($file_id)) {
        $stmt = $pdo->prepare("
            SELECT cf.*, c.id as content_id, c.title
            FROM content_files cf
            JOIN content c ON cf.content_id = c.id
            WHERE cf.id = ?
        ");
        $stmt->execute([$file_id]);
        $file_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$file_data) {
            header('Location: index.php?error=' . urlencode('الملف غير موجود'));
            exit;
        }
        
        // إذا كان الملف في Google Drive
        if ($file_data['storage_type'] === 'google_drive' && !empty($file_data['google_drive_id'])) {
            // تحديث إحصائيات التحميل
            $update_downloads = $pdo->prepare("UPDATE content SET downloads_count = downloads_count + 1 WHERE id = ?");
            $update_downloads->execute([$file_data['content_id']]);
            
            // إعادة التوجيه إلى Google Drive
            $download_url = "https://drive.google.com/uc?export=download&id=" . $file_data['google_drive_id'];
            header('Location: ' . $download_url);
            exit;
        }
        
        // ملف محلي
        $file_path = __DIR__ . '/uploads/content/' . $file_data['file_path'];
        $original_filename = $file_data['file_name'];
        $content_type = $file_data['file_type'];
        $content_id = $file_data['content_id'];
        
        // إنشاء اسم ملف مخصص بناءً على عنوان المحتوى
        $filename = generateCustomFilename($file_data['title'], $original_filename);
        
    } else {
        // الطريقة القديمة - جلب الملف من content.file_path
        $content_id = intval($content_id);
        
        $stmt = $pdo->prepare("
            SELECT c.*, u.full_name as teacher_name 
            FROM content c 
            LEFT JOIN users u ON c.teacher_id = u.id 
            WHERE c.id = ?
        ");
        $stmt->execute([$content_id]);
        $content = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$content) {
            header('Location: index.php?error=' . urlencode('المحتوى غير موجود'));
            exit;
        }
        
        // التحقق من وجود الملف
        if (empty($content['file_path'])) {
            header('Location: content_router.php?id=' . $content_id . '&error=' . urlencode('لا يوجد ملف مرفق مع هذا المحتوى'));
            exit;
        }
        
        $file_path = $content['file_path'];
        
        // إذا كان ملف خارجي (URL)
        if (preg_match('/^https?:\/\//', $file_path)) {
            // تحديث إحصائيات التحميل
            $update_downloads = $pdo->prepare("UPDATE content SET downloads_count = downloads_count + 1 WHERE id = ?");
            $update_downloads->execute([$content_id]);
            
            header('Location: ' . $file_path);
            exit;
        }
        
        // ملف محلي
        $file_path = __DIR__ . '/' . ltrim($file_path, '/');
        $original_filename = basename($content['file_path']);
        
        // إنشاء اسم ملف مخصص بناءً على عنوان المحتوى
        $filename = generateCustomFilename($content['title'], $original_filename);
        
        // تحديد نوع الملف
        $file_info = pathinfo($file_path);
        $extension = strtolower($file_info['extension'] ?? '');
        
        $content_types = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
            'jpg' => 'image/jpeg',
            'png' => 'image/png'
        ];
        
        $content_type = $content_types[$extension] ?? 'application/octet-stream';
    }
    
    // التحقق من وجود الملف المحلي
    if (!file_exists($file_path)) {
        $redirect_url = !empty($content_id) ? "content_router.php?id=$content_id" : "index.php";
        header('Location: ' . $redirect_url . '&error=' . urlencode('الملف غير موجود على الخادم'));
        exit;
    }
    
    // تحديث إحصائيات التحميل
    $update_downloads = $pdo->prepare("UPDATE content SET downloads_count = downloads_count + 1 WHERE id = ?");
    $update_downloads->execute([$content_id]);
    
    // إعداد headers للتحميل
    header('Content-Type: ' . $content_type);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($file_path));
    
    // قراءة وإرسال الملف
    readfile($file_path);
    exit;
    
} catch (Exception $e) {
    error_log("خطأ في download_file.php: " . $e->getMessage());
    $redirect_url = !empty($content_id) ? "content_router.php?id=$content_id" : "index.php";
    header('Location: ' . $redirect_url . '&error=' . urlencode('حدث خطأ في التحميل'));
    exit;
}
?> 
