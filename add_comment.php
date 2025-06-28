<?php
session_start();
/**
 * ملف معالجة إضافة التعليقات
 */

// تضمين الملفات الأساسية
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'يجب تسجيل الدخول لإضافة تعليق';
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}

// التحقق من طريقة الإرسال
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'طريقة الإرسال غير صحيحة';
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}

// استلام البيانات
$content_id = filter_input(INPUT_POST, 'content_id', FILTER_VALIDATE_INT);
$comment_text = trim($_POST['comment_text'] ?? '');
$is_question = isset($_POST['is_question']) ? 1 : 0;
$parent_id = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT) ?: null;
$user_id = $_SESSION['user_id'];

// التحقق من صحة البيانات
if (!$content_id) {
    $_SESSION['error'] = 'معرف المحتوى غير صحيح';
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}

if (empty($comment_text)) {
    $_SESSION['error'] = 'نص التعليق مطلوب';
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}

// التحقق من وجود المحتوى
try {
    $stmt = $pdo->prepare("SELECT id FROM content WHERE id = ?");
    $stmt->execute([$content_id]);
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = 'المحتوى غير موجود';
        header('Location: index.php');
        exit();
    }
    
    // التحقق من وجود التعليق الأب (إذا كان رداً)
    if ($parent_id) {
        $stmt = $pdo->prepare("SELECT id FROM comments WHERE id = ? AND content_id = ?");
        $stmt->execute([$parent_id, $content_id]);
        if ($stmt->rowCount() === 0) {
            $_SESSION['error'] = 'التعليق الأصلي غير موجود';
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
            exit();
        }
    }
    
    // إنشاء جدول التعليقات إذا لم يكن موجوداً
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `comments` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `content_id` int(11) NOT NULL,
            `user_id` int(11) NOT NULL,
            `parent_id` int(11) DEFAULT NULL,
            `comment_text` text NOT NULL,
            `is_question` tinyint(1) DEFAULT 0,
            `likes_count` int(11) DEFAULT 0,
            `status` enum('pending','approved','rejected') DEFAULT 'approved',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `content_id` (`content_id`),
            KEY `user_id` (`user_id`),
            KEY `parent_id` (`parent_id`),
            KEY `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // إضافة التعليق
    $stmt = $pdo->prepare("
        INSERT INTO comments 
        (content_id, user_id, parent_id, comment_text, is_question, status) 
        VALUES (?, ?, ?, ?, ?, 'approved')
    ");
    $stmt->execute([$content_id, $user_id, $parent_id, $comment_text, $is_question]);
    
    $_SESSION['success'] = 'تمت إضافة التعليق بنجاح';
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'حدث خطأ أثناء إضافة التعليق: ' . $e->getMessage();
    error_log('Error adding comment: ' . $e->getMessage());
}

// العودة إلى الصفحة السابقة
header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
exit();
?> 
