<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/connection.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// توجيه المستخدم حسب نوع الحساب
$user_type = $_SESSION['user_type'] ?? null;

switch ($user_type) {
    case 'student':
        header('Location: student/profile.php');
        exit();
        
    case 'teacher':
        header('Location: teacher/profile.php');
        exit();
        
    case 'admin':
        header('Location: admin/settings.php');
        exit();
        
    default:
        // في حالة عدم تحديد نوع المستخدم، نحاول تحديده من قاعدة البيانات
        try {
            $stmt = $pdo->prepare("SELECT user_type FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['user_type'] = $user['user_type'];
                
                switch ($user['user_type']) {
                    case 'student':
                        header('Location: student/profile.php');
                        exit();
                        
                    case 'teacher':
                        header('Location: teacher/profile.php');
                        exit();
                        
                    case 'admin':
                        header('Location: admin/settings.php');
                        exit();
                        
                    default:
                        // نوع مستخدم غير معروف
                        session_destroy();
                        header('Location: login.php?error=invalid_user_type');
                        exit();
                }
            } else {
                // المستخدم غير موجود في قاعدة البيانات
                session_destroy();
                header('Location: login.php?error=user_not_found');
                exit();
            }
        } catch (PDOException $e) {
            // خطأ في قاعدة البيانات
            error_log("خطأ في توجيه الملف الشخصي: " . $e->getMessage());
            header('Location: login.php?error=database_error');
            exit();
        }
}
?> 
