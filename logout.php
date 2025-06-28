<?php
// تضمين الملفات الأساسية
require_once 'includes/config.php';
require_once 'includes/functions.php';

// بدء الجلسة إذا لم تكن بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تسجيل عملية تسجيل الخروج إذا كان المستخدم مسجل دخول
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $user_type = $_SESSION['user_type'] ?? 'unknown';
    
    // تسجيل تسجيل الخروج للتدقيق
    error_log("تسجيل خروج: $username (ID: $user_id, نوع: $user_type) من IP: " . $_SERVER['REMOTE_ADDR']);
}

// حذف كوكي التذكر إذا كان موجوداً
if (isset($_COOKIE['remember_token'])) {
    // إذا كان لدينا اتصال بقاعدة البيانات، يمكننا إزالة الرمز من قاعدة البيانات أيضاً
    if (isset($_SESSION['user_id'])) {
        require_once 'includes/connection.php';
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL, token_expires = NULL WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        } catch (Exception $e) {
            error_log("خطأ في حذف رمز التذكر: " . $e->getMessage());
        }
    }
    
    // حذف الكوكي من المتصفح
    setcookie('remember_token', '', time() - 3600, '/');
    unset($_COOKIE['remember_token']);
}

// تدمير الجلسة
$_SESSION = array();

// حذف كوكي الجلسة
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// تدمير ملف الجلسة
session_destroy();

// إعداد رسالة تسجيل الخروج
setcookie('logout_message', 'تم تسجيل الخروج بنجاح', time() + 10, '/');

// التوجيه إلى الصفحة الرئيسية
header("Location: index.php");
exit();
?> 
