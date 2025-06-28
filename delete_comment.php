<?php
session_start();
/**
 * ملف معالجة حذف التعليقات
 */

// تضمين الملفات الأساسية
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'يجب تسجيل الدخول لحذف التعليق'
    ]);
    exit();
}

// التحقق من طريقة الإرسال
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'طريقة الإرسال غير صحيحة'
    ]);
    exit();
}

// استلام البيانات
$comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
$is_teacher = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'teacher';

// التحقق من صحة البيانات
if (!$comment_id) {
    echo json_encode([
        'success' => false,
        'message' => 'معرف التعليق غير صحيح'
    ]);
    exit();
}

// معالجة حذف التعليق
try {
    // التحقق من وجود التعليق والتأكد من أن المستخدم هو صاحب التعليق أو مدير
    $stmt = $pdo->prepare("
        SELECT c.*, co.teacher_id 
        FROM comments c 
        LEFT JOIN content co ON c.content_id = co.id
        WHERE c.id = ?
    ");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$comment) {
        echo json_encode([
            'success' => false,
            'message' => 'التعليق غير موجود'
        ]);
        exit();
    }
    
    // التحقق من أن المستخدم هو صاحب التعليق أو مدير أو معلم المحتوى
    $is_owner = (int)$comment['user_id'] === (int)$user_id;
    $is_content_teacher = $is_teacher && (int)$comment['teacher_id'] === (int)$user_id;
    
    if (!$is_owner && !$is_admin && !$is_content_teacher) {
        echo json_encode([
            'success' => false,
            'message' => 'ليس لديك صلاحية لحذف هذا التعليق'
        ]);
        exit();
    }
    
    // بدء المعاملة
    $pdo->beginTransaction();
    
    // حذف الإعجابات المرتبطة بالتعليق
    $stmt = $pdo->prepare("DELETE FROM comment_likes WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    
    // حذف الردود على التعليق إذا كان تعليقاً رئيسياً
    if ($comment['parent_id'] === null) {
        // حذف الإعجابات المرتبطة بالردود أولاً
        $stmt = $pdo->prepare("
            DELETE FROM comment_likes 
            WHERE comment_id IN (SELECT id FROM comments WHERE parent_id = ?)
        ");
        $stmt->execute([$comment_id]);
        
        // ثم حذف الردود
        $stmt = $pdo->prepare("DELETE FROM comments WHERE parent_id = ?");
        $stmt->execute([$comment_id]);
    }
    
    // حذف التعليق نفسه
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
    
    // تأكيد المعاملة
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'تم حذف التعليق بنجاح'
    ]);
    
} catch (PDOException $e) {
    // التراجع عن المعاملة في حالة حدوث خطأ
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log('Error deleting comment: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء حذف التعليق: ' . $e->getMessage()
    ]);
}
?> 
