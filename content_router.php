<?php
/**
 * نظام توجيه المحتوى المحسن - Content Router
 * يحل محل content_router.php مع ربط كامل بقواعد البيانات
 */

session_start();

// إعداد Content Security Policy للسماح بفيديوهات YouTube و Google Drive والملفات الصوتية المضمنة
if (!headers_sent()) {
    header("Content-Security-Policy: default-src 'self'; frame-src 'self' https://www.youtube.com https://drive.google.com https://docs.google.com; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:; media-src 'self' data: https: blob:; font-src 'self' https:; connect-src 'self' https:;");
}

// تضمين الملفات الأساسية
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'includes/content_functions.php';
require_once 'includes/view_tracker.php';

// التحقق من وجود معرف المحتوى
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?error=' . urlencode('معرف المحتوى غير صحيح'));
    exit();
}

$content_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'] ?? null;
$user_type = $_SESSION['user_type'] ?? 'visitor';

// تعريف المتغيرات الافتراضية لتجنب أخطاء undefined variable
$content_files = [];
$student_note = '';
$experiment_notes = '';
$is_favorite = false;
$is_study_later = false;
$is_exam_completed = false;
$user_rating = null;
$rating_stats = ['total_ratings' => 0, 'avg_rating' => 0, 'five_stars' => 0, 'four_stars' => 0, 'three_stars' => 0, 'two_stars' => 0, 'one_star' => 0];
$recent_reviews = [];
$related_content = [];

// معالجة النماذج قبل أي output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // حفظ ملاحظات الطالب العادية - نظام موحد
    if (isset($_POST['save_note']) && $user_type === 'student') {
        $note_content = trim($_POST['note_content'] ?? '');
        
        try {
            // حفظ في الجدول الموحد
            $stmt = $pdo->prepare("
                INSERT INTO student_content_notes (student_id, content_id, notes, created_at)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                notes = VALUES(notes), updated_at = NOW()
            ");
            $stmt->execute([$user_id, $content_id, $note_content]);
            
            header('Location: content_router.php?id=' . $content_id . '&success=note_saved');
            exit();
        } catch (Exception $e) {
            error_log("خطأ في حفظ الملاحظات: " . $e->getMessage());
            header('Location: content_router.php?id=' . $content_id . '&error=' . urlencode('حدث خطأ في حفظ الملاحظات'));
            exit();
        }
        
    }
    
    // حفظ ملاحظات التجربة - نظام موحد
    elseif (isset($_POST['save_experiment_notes']) && $user_type === 'student') {
        $experiment_note_content = trim($_POST['experiment_notes'] ?? '');
        
        try {
            // استخدام نفس الجدول الموحد مع علامة نوع المحتوى
            $stmt = $pdo->prepare("
                INSERT INTO student_content_notes (student_id, content_id, notes, created_at)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                notes = VALUES(notes), updated_at = NOW()
            ");
            $stmt->execute([$user_id, $content_id, $experiment_note_content]);
            
            header('Location: content_router.php?id=' . $content_id . '&success=experiment_notes_saved');
            exit();
        } catch (Exception $e) {
            error_log("خطأ في حفظ ملاحظات التجربة: " . $e->getMessage());
            header('Location: content_router.php?id=' . $content_id . '&error=' . urlencode('حدث خطأ في حفظ ملاحظات التجربة'));
            exit();
        }
    }
    
    // حفظ التقييم
    elseif (isset($_POST['submit_rating']) && $user_type !== 'visitor') {
        $rating = (int)($_POST['rating'] ?? 0);
        $review = trim($_POST['review'] ?? '');
        
        if ($rating >= 1 && $rating <= 5) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO content_ratings (content_id, user_id, rating, review, created_at)
                    VALUES (?, ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE 
                    rating = VALUES(rating), 
                    review = VALUES(review)
                ");
                $stmt->execute([$content_id, $user_id, $rating, $review]);
                
                header('Location: content_router.php?id=' . $content_id . '&success=rating_saved');
                exit();
            } catch (Exception $e) {
                header('Location: content_router.php?id=' . $content_id . '&error=' . urlencode('حدث خطأ في حفظ التقييم'));
                exit();
            }
        } else {
            header('Location: content_router.php?id=' . $content_id . '&error=' . urlencode('يرجى اختيار تقييم صحيح من 1 إلى 5'));
            exit();
        }
        
    }
    
    // إرسال رسالة للمعلم
    elseif (isset($_POST['send_message']) && $user_type === 'student') {
        $subject = trim($_POST['message_subject'] ?? '');
        $message_content = trim($_POST['message_content'] ?? '');
        
        if (!empty($subject) && !empty($message_content)) {
            try {
                // جلب معرف المعلم أولاً
                $stmt = $pdo->prepare("SELECT teacher_id FROM content WHERE id = ?");
                $stmt->execute([$content_id]);
                $content_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($content_data) {
                    $stmt = $pdo->prepare("
                        INSERT INTO teacher_messages (teacher_id, student_id, content_id, subject, message, status, created_at)
                        VALUES (?, ?, ?, ?, ?, 'unread', NOW())
                    ");
                    $stmt->execute([$content_data['teacher_id'], $user_id, $content_id, $subject, $message_content]);
                    
                    header('Location: content_router.php?id=' . $content_id . '&success=message_sent');
                    exit();
                } else {
                    header('Location: content_router.php?id=' . $content_id . '&error=' . urlencode('المحتوى غير موجود'));
                    exit();
                }
            } catch (Exception $e) {
                header('Location: content_router.php?id=' . $content_id . '&error=' . urlencode('حدث خطأ في إرسال الرسالة'));
                exit();
            }
        } else {
            header('Location: content_router.php?id=' . $content_id . '&error=' . urlencode('يرجى ملء جميع حقول الرسالة'));
            exit();
        }
    }
}

// رسائل النجاح والخطأ
$success_message = '';
$error_message = '';

// معالجة رسائل URL
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'note_saved':
            $success_message = 'تم حفظ ملاحظاتك بنجاح';
            break;
        case 'rating_saved':
            $success_message = 'تم حفظ تقييمك بنجاح';
            break;
        case 'message_sent':
            $success_message = 'تم إرسال رسالتك للمعلم بنجاح';
            break;
        case 'experiment_notes_saved':
            $success_message = 'تم حفظ ملاحظات التجربة بنجاح';
            break;
    }
}

if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
}

try {
    // جلب بيانات المحتوى مع معلومات إضافية شاملة
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            ct.name_ar as type_name,
            ct.code as type_code,
            ct.icon as type_icon,
            ct.color as type_color,
            l.name_ar as level_name,
            l.code as level_code,
            u.full_name as teacher_name,
            u.email as teacher_email
        FROM content c
        JOIN content_types ct ON c.type_id = ct.id
        JOIN levels l ON c.level_id = l.id
        JOIN users u ON c.teacher_id = u.id
        WHERE c.id = ? AND c.status = 'published'
    ");
    
    $stmt->execute([$content_id]);
    $content = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$content) {
        throw new Exception('المحتوى غير موجود أو غير متاح للعرض');
    }
    
    // تتبع المشاهدة
    try {
        $viewTracker = new ViewTracker($pdo);
        $viewTracker->trackView($content_id, $user_id, $content['teacher_id']);
        
        // إعادة جلب العدد المحدث للمشاهدات
        $stmt = $pdo->prepare("SELECT views_count FROM content WHERE id = ?");
        $stmt->execute([$content_id]);
        $content['views_count'] = $stmt->fetchColumn() ?: 0;
    } catch (Exception $e) {
        error_log("خطأ في تتبع المشاهدة: " . $e->getMessage());
        $content['views_count'] = $content['views_count'] ?? 0;
    }
    
    // جلب الملفات المرفقة مع معالجة أفضل للأخطاء
    try {
        $stmt = $pdo->prepare("
            SELECT 
                id, content_id, file_name, file_path, file_type, file_size, 
                is_primary, description, created_at, storage_type, google_drive_id
            FROM content_files 
            WHERE content_id = ? 
            ORDER BY is_primary DESC, created_at ASC
        ");
        $stmt->execute([$content_id]);
        $content_files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // تسجيل معلومات تشخيصية
        error_log("Content files loaded for content_id {$content_id}: " . count($content_files) . " files found");
        if (!empty($content_files)) {
            foreach ($content_files as $index => $file) {
                error_log("File {$index}: {$file['file_name']} ({$file['file_type']}) - Storage: {$file['storage_type']} - Google Drive ID: " . ($file['google_drive_id'] ?? 'None'));
            }
        }
        
    } catch (Exception $e) {
        error_log("خطأ في جلب الملفات المرفقة: " . $e->getMessage());
        $content_files = [];
    }
    
    // جلب ملاحظات الطالب العادية - نظام موحد
    $student_note = '';
    if ($user_id && $user_type === 'student') {
        try {
            // محاولة جلب من الجدول الجديد أولاً
            $stmt = $pdo->prepare("
                SELECT notes 
                FROM student_content_notes 
                WHERE student_id = ? AND content_id = ?
            ");
            $stmt->execute([$user_id, $content_id]);
            $note_result = $stmt->fetch(PDO::FETCH_ASSOC);
            $student_note = $note_result['notes'] ?? '';
            
            // إذا لم توجد، تحقق من الجدول القديم ونقل البيانات
            if (empty($student_note)) {
                $stmt = $pdo->prepare("
                    SELECT note_content 
                    FROM student_notes 
                    WHERE user_id = ? AND content_id = ?
                ");
                $stmt->execute([$user_id, $content_id]);
                $old_note = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($old_note && !empty($old_note['note_content'])) {
                    $student_note = $old_note['note_content'];
                    
                    // نقل البيانات للجدول الجديد
                    $stmt = $pdo->prepare("
                        INSERT INTO student_content_notes (student_id, content_id, notes, created_at) 
                        VALUES (?, ?, ?, NOW()) 
                        ON DUPLICATE KEY UPDATE notes = VALUES(notes)
                    ");
                    $stmt->execute([$user_id, $content_id, $student_note]);
                }
            }
        } catch (Exception $e) {
            error_log("خطأ في جلب الملاحظات: " . $e->getMessage());
            $student_note = '';
        }
    }
    
    // جلب ملاحظات التجربة (للتجارب فقط) - نظام موحد
    $experiment_notes = '';
    if ($user_id && $user_type === 'student' && 
        (in_array($content['type_code'], ['experiments', 'experiment']) || 
         strpos($content['type_code'], 'experiment') !== false)) {
        
        try {
            // نفس الملاحظات من الجدول الموحد
            $experiment_notes = $student_note;
            
            // إذا لم توجد، تحقق من الجدول القديم للتجارب
            if (empty($experiment_notes)) {
            $stmt = $pdo->prepare("
                    SELECT experiment_notes 
                FROM student_experiment_notes 
                WHERE student_id = ? AND content_id = ?
            ");
            $stmt->execute([$user_id, $content_id]);
                $old_exp_note = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($old_exp_note && !empty($old_exp_note['experiment_notes'])) {
                    $experiment_notes = $old_exp_note['experiment_notes'];
                    
                    // نقل البيانات للجدول الموحد
                    $stmt = $pdo->prepare("
                        INSERT INTO student_content_notes (student_id, content_id, notes, created_at) 
                        VALUES (?, ?, ?, NOW()) 
                        ON DUPLICATE KEY UPDATE notes = VALUES(notes)
                    ");
                    $stmt->execute([$user_id, $content_id, $experiment_notes]);
                }
            }
        } catch (Exception $e) {
            error_log("خطأ في جلب ملاحظات التجربة: " . $e->getMessage());
            $experiment_notes = '';
        }
    }
    
    // جلب حالة المفضلة و"أدرس لاحقا" للطلاب
    $is_favorite = false;
    $is_study_later = false;
    $is_exam_completed = false;
    if ($user_id && $user_type === 'student') {
        // التحقق من المفضلة
        $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND content_id = ?");
        $stmt->execute([$user_id, $content_id]);
        $is_favorite = (bool)$stmt->fetch();
        
        // التحقق من "أدرس لاحقا"
        $stmt = $pdo->prepare("SELECT id FROM study_later WHERE user_id = ? AND content_id = ?");
        $stmt->execute([$user_id, $content_id]);
        $is_study_later = (bool)$stmt->fetch();
        
        // التحقق من الامتحانات المنتهية (للامتحانات فقط)
        if (isset($content['type_code']) && $content['type_code'] === 'exams') {
            $stmt = $pdo->prepare("SELECT id FROM exam_completed WHERE user_id = ? AND content_id = ?");
            $stmt->execute([$user_id, $content_id]);
            $is_exam_completed = (bool)$stmt->fetch();
        }
    }
    
    // جلب تقييم المستخدم
    $user_rating = null;
    if ($user_id && $user_type !== 'visitor') {
        $stmt = $pdo->prepare("
            SELECT rating, review, created_at 
            FROM content_ratings 
            WHERE content_id = ? AND user_id = ?
        ");
        $stmt->execute([$content_id, $user_id]);
        $user_rating = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // جلب إحصائيات التقييم
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_ratings,
            AVG(rating) as avg_rating,
            COUNT(CASE WHEN rating = 5 THEN 1 END) as five_stars,
            COUNT(CASE WHEN rating = 4 THEN 1 END) as four_stars,
            COUNT(CASE WHEN rating = 3 THEN 1 END) as three_stars,
            COUNT(CASE WHEN rating = 2 THEN 1 END) as two_stars,
            COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
        FROM content_ratings 
        WHERE content_id = ?
    ");
    $stmt->execute([$content_id]);
    $rating_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // جلب المراجعات الحديثة
    $stmt = $pdo->prepare("
        SELECT cr.rating, cr.review, cr.created_at, u.full_name
        FROM content_ratings cr
        JOIN users u ON cr.user_id = u.id
        WHERE cr.content_id = ? AND cr.review IS NOT NULL AND cr.review != ''
        ORDER BY cr.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$content_id]);
    $recent_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // جلب المحتوى المرتبط (نفس المعلم أو نفس المستوى)
    $stmt = $pdo->prepare("
        SELECT c.id, c.title, c.description, ct.icon, ct.color, ct.name_ar as type_name
        FROM content c
        JOIN content_types ct ON c.type_id = ct.id
        WHERE (c.teacher_id = ? OR c.level_id = ?) 
        AND c.id != ? 
        AND c.status = 'published'
        ORDER BY c.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$content['teacher_id'], $content['level_id'], $content_id]);
    $related_content = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("خطأ في content_router.php: " . $e->getMessage());
    $error_message = $e->getMessage();
    
    // تعريف المتغيرات الافتراضية في حالة الخطأ
    if (!isset($content_files)) $content_files = [];
    if (!isset($student_note)) $student_note = '';
    if (!isset($experiment_notes)) $experiment_notes = '';
    if (!isset($is_favorite)) $is_favorite = false;
    if (!isset($is_study_later)) $is_study_later = false;
    if (!isset($is_exam_completed)) $is_exam_completed = false;
    if (!isset($user_rating)) $user_rating = null;
    if (!isset($rating_stats)) $rating_stats = ['total_ratings' => 0, 'avg_rating' => 0];
    if (!isset($recent_reviews)) $recent_reviews = [];
    if (!isset($related_content)) $related_content = [];
    
    // عرض الخطأ للتشخيص بدلاً من إعادة التوجيه
    if (!isset($content)) {
        echo "<!DOCTYPE html><html><head><title>خطأ في عرض المحتوى</title></head><body>";
        echo "<h1>خطأ في عرض المحتوى</h1>";
        echo "<p><strong>رسالة الخطأ:</strong> " . htmlspecialchars($error_message) . "</p>";
        echo "<p><strong>الملف:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p><strong>السطر:</strong> " . $e->getLine() . "</p>";
        echo "<hr>";
        echo "<p><a href='debug_content.php?id=" . $content_id . "'>تشخيص مفصل للمشكلة</a></p>";
        echo "<p><a href='index.php'>العودة للصفحة الرئيسية</a></p>";
        echo "</body></html>";
        exit();
    }
}

// تحديد نوع المحتوى وتوجيهه للملف المناسب
$content_type_code = $content['type_code'] ?? '';
$view_file = 'view_types/default.php'; // الافتراضي

// تحديد ملف العرض المناسب بناءً على نوع المحتوى
switch (strtolower($content_type_code)) {
    case 'lessons':
    case 'lesson':
        $view_file = 'view_types/lessons.php';
        break;
        
    case 'exercises':
    case 'exercise':
        $view_file = 'view_types/exercises.php';
        break;
        
    case 'exams':
    case 'exam':
        $view_file = 'view_types/exams.php';
        break;
        
    case 'assignments':
    case 'assignment':
        $view_file = 'view_types/assignments.php';
        break;
        
    case 'interactive_lesson':
    case 'interactive_lessons':
    case 'interactive':
        $view_file = 'view_types/interactive_lesson.php';
        break;
        
    case 'experiments':
    case 'experiment':
    case 'practical_experiment':
    case 'lab_experiment':
        // تعريف المتغير للسماح بالوصول لملف التجارب
        define('ALLOW_INCLUDED', true);
        $view_file = 'view_types/experiments.php';
        break;
        
    default:
        // أي نوع آخر يستخدم الملف الافتراضي
        $view_file = 'view_types/default.php';
        break;
}

// التحقق من وجود ملف العرض
if (!file_exists($view_file)) {
    error_log("ملف العرض غير موجود: " . $view_file);
    $view_file = 'view_types/default.php';
    
    // التحقق من وجود الملف الافتراضي
    if (!file_exists($view_file)) {
        // إنشاء عرض طوارئ
        echo "<!DOCTYPE html><html><head><title>خطأ</title></head><body>";
        echo "<h1>خطأ في النظام</h1>";
        echo "<p>عذراً، حدث خطأ في عرض المحتوى. يرجى المحاولة لاحقاً.</p>";
        echo "<a href='index.php'>العودة للصفحة الرئيسية</a>";
        echo "</body></html>";
        exit();
    }
}

// تضمين الهيدر
include 'includes/header.php';

// ملاحظة: تم إزالة ملفات CSS و JavaScript الإضافية لتجنب التعارض مع التصميم الموحد
// هذه الملفات كانت تسبب مشاكل في تصميم الهاتف المحمول

// تضمين ملف العرض المناسب
include $view_file;

// تضمين الفوتر
include 'includes/footer.php';
?>
