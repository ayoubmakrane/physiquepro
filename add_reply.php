<?php
session_start();
/**
 * ملف معالجة إضافة الردود على التعليقات
 */

// تضمين الملفات الأساسية
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'يجب تسجيل الدخول لإضافة رد';
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
$parent_id = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT);
$reply_text = trim($_POST['reply_text'] ?? '');
$user_id = $_SESSION['user_id'];

// التحقق من صحة البيانات
if (!$content_id || !$parent_id) {
    $_SESSION['error'] = 'معرف المحتوى أو التعليق غير صحيح';
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}

if (empty($reply_text)) {
    $_SESSION['error'] = 'نص الرد مطلوب';
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit();
}

// معالجة إضافة الرد
try {
    // التحقق من وجود المحتوى
    $stmt = $pdo->prepare("SELECT id FROM content WHERE id = ?");
    $stmt->execute([$content_id]);
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = 'المحتوى غير موجود';
        header('Location: index.php');
        exit();
    }
    
    // التحقق من وجود التعليق الأب
    $stmt = $pdo->prepare("SELECT id FROM comments WHERE id = ? AND content_id = ?");
    $stmt->execute([$parent_id, $content_id]);
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = 'التعليق الأصلي غير موجود';
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
        exit();
    }
    
    // إضافة الرد (كتعليق جديد مرتبط بالتعليق الأب)
    $stmt = $pdo->prepare("
        INSERT INTO comments 
        (content_id, user_id, parent_id, comment_text, status) 
        VALUES (?, ?, ?, ?, 'approved')
    ");
    $stmt->execute([$content_id, $user_id, $parent_id, $reply_text]);
    
    $_SESSION['success'] = 'تمت إضافة الرد بنجاح';
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'حدث خطأ أثناء إضافة الرد: ' . $e->getMessage();
    error_log('Error adding reply: ' . $e->getMessage());
}

// العودة إلى الصفحة السابقة
header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
exit();
?> 
