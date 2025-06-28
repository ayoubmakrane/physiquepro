<?php
session_start(); // بدء الجلسة لضمان عمل وظائف المستخدم
/**
 * الصفحة الرئيسية لموقع الفيزياء والكيمياء - محسنة حسب نوع المستخدم
 */

// تضمين الملفات الأساسية
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

// تحديد نوع المستخدم الحالي
$user_type = 'guest'; // افتراضي: زائر
$user_id = null;
$user_name = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['username'] ?? 'المستخدم';
    
    if (isset($_SESSION['user_type'])) {
        switch ($_SESSION['user_type']) {
            case 'admin':
                $user_type = 'admin';
                break;
            case 'teacher':
                $user_type = 'teacher';
                break;
            case 'student':
                $user_type = 'student';
                break;
            default:
                $user_type = 'student';
        }
    }
}

$page_title = 'البحث المتقدم';

// معالجة البحث
$search_query = $_GET['search'] ?? '';
$level_filter = $_GET['level'] ?? '';
$content_type_filter = $_GET['content_type'] ?? '';
$teacher_filter = $_GET['teacher'] ?? '';

$search_results = [];
$total_results = 0;

if (!empty($search_query) || !empty($level_filter) || !empty($content_type_filter) || !empty($teacher_filter)) {
    try {
        $sql = "
            SELECT c.*, u.username as teacher_name, l.code as level, ct.code as content_type
            FROM content c
            JOIN users u ON c.teacher_id = u.id
            LEFT JOIN levels l ON c.level_id = l.id
            LEFT JOIN content_types ct ON c.type_id = ct.id
            WHERE c.status = 'published'
        ";
        
        $params = [];
        
        if (!empty($search_query)) {
            $sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
            $params[] = "%$search_query%";
            $params[] = "%$search_query%";
        }
        
        if (!empty($level_filter)) {
            $sql .= " AND l.code = ?";
            $params[] = $level_filter;
        }
        
        if (!empty($content_type_filter)) {
            $sql .= " AND ct.code = ?";
            $params[] = $content_type_filter;
        }
        
        if (!empty($teacher_filter)) {
            $sql .= " AND c.teacher_id = ?";
            $params[] = $teacher_filter;
        }
        
        $sql .= " ORDER BY c.created_at DESC LIMIT 50";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $search_results = $stmt->fetchAll();
        $total_results = count($search_results);
        
    } catch (PDOException $e) {
        $search_results = [];
    }
}

// جلب قوائم الفلاتر
$teachers = [];
$levels = [];
$content_types = [];

try {
    // جلب الأساتذة
    $stmt = $pdo->prepare("
        SELECT DISTINCT u.id, u.username 
        FROM users u 
        JOIN content c ON u.id = c.teacher_id 
        WHERE u.user_type = 'teacher' AND c.status = 'published'
        ORDER BY u.username
    ");
    $stmt->execute();
    $teachers = $stmt->fetchAll();
    
    // جلب المستويات
    $stmt = $pdo->prepare("SELECT * FROM levels WHERE status = 'active' ORDER BY order_num");
    $stmt->execute();
    $levels = $stmt->fetchAll();
    
    // جلب أنواع المحتوى
    $stmt = $pdo->prepare("SELECT * FROM content_types WHERE status = 'active' ORDER BY order_num");
    $stmt->execute();
    $content_types = $stmt->fetchAll();
    
} catch (PDOException $e) {
    // في حالة خطأ، استخدم القوائم الفارغة
}

include 'includes/header.php';
?>

<!-- CSS للتحسينات المحمولة -->
<link rel="stylesheet" href="assets/css/mobile-optimizations.css">

<!-- JavaScript للتحسينات المحمولة -->
<script src="assets/js/mobile-enhancements.js"></script>

<style>
.search-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.search-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    padding: 3rem 2rem;
    text-align: center;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.search-form {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.search-results {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.result-item {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.result-item:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.content-type-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.type-lesson { background: #e3f2fd; color: #1976d2; }
.type-experiment { background: #e8f5e8; color: #388e3c; }
.type-exam { background: #fff3e0; color: #f57c00; }
.type-exercise { background: #f3e5f5; color: #7b1fa2; }
.type-unknown { background: #f8f9fa; color: #6c757d; }

.no-results {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

/* تحسينات الهاتف المحمول للبحث المتقدم */
@media (max-width: 768px) {
    .search-container {
        padding: 1rem 0 !important;
    }
    
    .container {
        padding: 0 10px !important;
    }
    
    .search-header {
        padding: 1.5rem !important;
        margin-bottom: 1rem !important;
        border-radius: 15px !important;
    }
    
    .search-header h1 {
        font-size: 1.4rem !important;
    }
    
    .search-header h1 i {
        font-size: 1.3rem !important;
        margin-left: 0.5rem !important;
    }
    
    .search-header p {
        font-size: 0.9rem !important;
    }
    
    .search-form {
        padding: 1.25rem !important;
        margin-bottom: 1rem !important;
        border-radius: 12px !important;
    }
    
    .search-form h4 {
        font-size: 1.1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .search-form .row .col-md-3,
    .search-form .row .col-md-6 {
        margin-bottom: 1rem !important;
    }
    
    .search-form .form-label {
        font-size: 0.9rem !important;
        margin-bottom: 0.25rem !important;
    }
    
    .search-form .form-control,
    .search-form .form-select {
        padding: 0.6rem !important;
        font-size: 0.9rem !important;
    }
    
    .search-form .btn {
        padding: 0.6rem 1.2rem !important;
        font-size: 0.9rem !important;
        width: 100% !important;
        margin-top: 0.5rem !important;
    }
    
    .search-results {
        padding: 1.25rem !important;
        border-radius: 12px !important;
    }
    
    .search-results h4 {
        font-size: 1.1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .results-count {
        font-size: 0.85rem !important;
        margin-bottom: 1rem !important;
    }
    
    .result-item {
        padding: 1rem !important;
        margin-bottom: 0.75rem !important;
        border-radius: 8px !important;
    }
    
    .result-item:hover {
        transform: none !important;
    }
    
    .content-type-badge {
        font-size: 0.7rem !important;
        padding: 0.2rem 0.6rem !important;
        margin-bottom: 0.75rem !important;
    }
    
    .result-item h5 {
        font-size: 1rem !important;
        margin-bottom: 0.5rem !important;
        line-height: 1.3 !important;
    }
    
    .result-item p {
        font-size: 0.85rem !important;
        line-height: 1.4 !important;
        margin-bottom: 0.75rem !important;
    }
    
    .result-item .text-muted {
        font-size: 0.8rem !important;
    }
    
    .result-item .btn {
        padding: 0.4rem 0.8rem !important;
        font-size: 0.8rem !important;
    }
    
    .no-results {
        padding: 2rem !important;
    }
    
    .no-results i {
        font-size: 2rem !important;
        margin-bottom: 1rem !important;
    }
    
    .no-results h4 {
        font-size: 1.1rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    .no-results p {
        font-size: 0.9rem !important;
    }
}

@media (max-width: 576px) {
    .search-container {
        padding: 0.5rem 0 !important;
    }
    
    .container {
        padding: 0 5px !important;
    }
    
    .search-header {
        padding: 1rem !important;
        border-radius: 10px !important;
    }
    
    .search-header h1 {
        font-size: 1.2rem !important;
    }
    
    .search-header h1 i {
        font-size: 1.1rem !important;
    }
    
    .search-header p {
        font-size: 0.85rem !important;
    }
    
    .search-form {
        padding: 1rem !important;
        border-radius: 10px !important;
    }
    
    .search-form h4 {
        font-size: 1rem !important;
    }
    
    .search-form .form-label {
        font-size: 0.85rem !important;
    }
    
    .search-form .form-control,
    .search-form .form-select {
        padding: 0.5rem !important;
        font-size: 0.85rem !important;
    }
    
    .search-form .btn {
        padding: 0.5rem 1rem !important;
        font-size: 0.85rem !important;
    }
    
    .search-results {
        padding: 1rem !important;
        border-radius: 10px !important;
    }
    
    .search-results h4 {
        font-size: 1rem !important;
    }
    
    .result-item {
        padding: 0.75rem !important;
        border-radius: 6px !important;
    }
    
    .content-type-badge {
        font-size: 0.65rem !important;
        padding: 0.15rem 0.5rem !important;
    }
    
    .result-item h5 {
        font-size: 0.95rem !important;
        line-height: 1.2 !important;
    }
    
    .result-item p {
        font-size: 0.8rem !important;
        line-height: 1.3 !important;
    }
    
    .result-item .text-muted {
        font-size: 0.75rem !important;
    }
    
    .result-item .btn {
        padding: 0.35rem 0.7rem !important;
        font-size: 0.75rem !important;
    }
    
    .no-results {
        padding: 1.5rem !important;
    }
    
    .no-results i {
        font-size: 1.8rem !important;
    }
    
    .no-results h4 {
        font-size: 1rem !important;
    }
    
    .no-results p {
        font-size: 0.85rem !important;
    }
}
</style>

<div class="search-container">
    <div class="container">
        <!-- رأس الصفحة -->
        <div class="search-header">
            <h1><i class="fas fa-search me-3"></i>البحث المتقدم</h1>
            <p class="lead">ابحث في جميع المحتويات التعليمية بسهولة</p>
        </div>

        <!-- نموذج البحث -->
        <div class="search-form">
            <form method="GET" action="">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">كلمة البحث</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search_query); ?>" 
                               placeholder="ابحث في العناوين والأوصاف...">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="level" class="form-label">المستوى</label>
                        <select class="form-select" id="level" name="level">
                            <option value="">جميع المستويات</option>
                            <?php foreach ($levels as $level): ?>
                            <option value="<?php echo $level['code']; ?>" <?php echo $level_filter === $level['code'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($level['name_ar']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="content_type" class="form-label">نوع المحتوى</label>
                        <select class="form-select" id="content_type" name="content_type">
                            <option value="">جميع الأنواع</option>
                            <?php foreach ($content_types as $type): ?>
                            <option value="<?php echo $type['code']; ?>" <?php echo $content_type_filter === $type['code'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['name_ar']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if (!empty($teachers)): ?>
                    <div class="col-md-6">
                        <label for="teacher" class="form-label">الأستاذ</label>
                        <select class="form-select" id="teacher" name="teacher">
                            <option value="">جميع الأساتذة</option>
                            <?php foreach ($teachers as $teacher): ?>
                            <option value="<?php echo $teacher['id']; ?>" 
                                    <?php echo $teacher_filter == $teacher['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($teacher['username']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>بحث
                        </button>
                        <a href="advanced_search.php" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="fas fa-undo me-2"></i>إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- نتائج البحث -->
        <?php if (!empty($search_query) || !empty($level_filter) || !empty($content_type_filter) || !empty($teacher_filter)): ?>
        <div class="search-results">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>نتائج البحث</h3>
                <span class="badge bg-primary fs-6"><?php echo $total_results; ?> نتيجة</span>
            </div>
            
            <?php if (!empty($search_results)): ?>
                <?php foreach ($search_results as $result): ?>
                <div class="result-item">
                    <div class="row">
                        <div class="col-md-8">
                            <span class="content-type-badge type-<?php echo $result['content_type'] ?? 'unknown'; ?>">
                                <?php 
                                $content_type = $result['content_type'] ?? '';
                                echo [
                                    'lessons' => 'درس',
                                    'exercises' => 'تمرين',
                                    'exams' => 'امتحان',
                                    'experiments' => 'تجربة',
                                    'assignments' => 'فرض',
                                    'interactive_lesson' => 'درس تفاعلي'
                                ][$content_type] ?? $content_type; 
                                ?>
                            </span>
                            <h5 class="mt-2"><?php echo htmlspecialchars($result['title']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars(substr($result['description'] ?? '', 0, 150)); ?>...</p>
                            
                            <div class="result-meta">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo htmlspecialchars($result['teacher_name']); ?>
                                    
                                    <i class="fas fa-layer-group me-1 ms-3"></i>
                                    <?php 
                                    $level = $result['level'] ?? '';
                                    echo [
                                        'tc' => 'الجذع المشترك',
                                        '1bac' => 'الأولى باكالوريا',
                                        '2bac' => 'الثانية باكالوريا'
                                    ][$level] ?? $level; 
                                    ?>
                                    
                                    <i class="fas fa-calendar me-1 ms-3"></i>
                                    <?php echo date('Y/m/d', strtotime($result['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-end">
                            <a href="content_router.php?id=<?php echo $result['id']; ?>" 
                               class="btn btn-<?php echo ($result['content_type'] ?? '') === 'experiments' ? 'success' : 'primary'; ?>">
                                <i class="fas fa-<?php echo ($result['content_type'] ?? '') === 'experiments' ? 'play' : 'eye'; ?> me-1"></i>
                                <?php echo ($result['content_type'] ?? '') === 'experiments' ? 'تشغيل التجربة' : 'عرض المحتوى'; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">لا توجد نتائج</h4>
                    <p>جرب تغيير معايير البحث أو استخدم كلمات مختلفة</p>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- اقتراحات البحث -->
        <?php if (empty($search_query) && empty($level_filter) && empty($content_type_filter) && empty($teacher_filter)): ?>
        <div class="search-results">
            <h3 class="mb-4">اقتراحات للبحث</h3>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-graduation-cap text-primary" style="font-size: 2rem;"></i>
                            <h5 class="mt-3">ابحث حسب المستوى</h5>
                            <p class="text-muted">اختر مستواك الدراسي لعرض المحتوى المناسب</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-user-tie text-success" style="font-size: 2rem;"></i>
                            <h5 class="mt-3">ابحث حسب الأستاذ</h5>
                            <p class="text-muted">اعثر على محتوى أستاذ معين</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-flask text-warning" style="font-size: 2rem;"></i>
                            <h5 class="mt-3">ابحث في التجارب</h5>
                            <p class="text-muted">اكتشف التجارب التفاعلية المتاحة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
