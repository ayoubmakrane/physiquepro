<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

// تحديد المستوى المطلوب
$level = $_GET['level'] ?? 'tc';
$valid_levels = ['tc', '1bac', '2bac'];

if (!in_array($level, $valid_levels)) {
    $level = 'tc';
}

// تحديد نوع المحتوى المطلوب (إضافة جديدة)
$content_type = $_GET['type'] ?? '';
$valid_types = ['lessons', 'exercises', 'experiments', 'exams', 'assignments', 'interactive'];

// تحديد عنوان الصفحة حسب المستوى ونوع المحتوى
$level_titles = [
    'tc' => 'الجذع المشترك العلمي',
    '1bac' => 'الأولى باكالوريا',
    '2bac' => 'الثانية باكالوريا'
];

$content_type_titles = [
    'lessons' => 'الدروس',
    'exercises' => 'التمارين', 
    'experiments' => 'التجارب',
    'exams' => 'الامتحانات',
    'assignments' => 'الفروض',
    'interactive' => 'الدروس التفاعلية'
];

// تحديد عنوان الصفحة
if (!empty($content_type) && in_array($content_type, $valid_types)) {
    $page_title = $content_type_titles[$content_type] . ' - اختر المستوى والأستاذ';
} else {
    $page_title = $level_titles[$level];
}

// تحديد ألوان مخصصة لكل مستوى
$level_colors = [
    'tc' => [
        'primary' => '#4f46e5',     // بنفسجي أزرق للجذع المشترك
        'secondary' => '#7c3aed',
        'light' => '#e0e7ff',
        'gradient_start' => '#4f46e5',
        'gradient_end' => '#7c3aed',
        'shadow' => 'rgba(79, 70, 229, 0.4)'
    ],
    '1bac' => [
        'primary' => '#059669',     // أخضر للأولى باكالوريا
        'secondary' => '#10b981', 
        'light' => '#d1fae5',
        'gradient_start' => '#059669',
        'gradient_end' => '#10b981',
        'shadow' => 'rgba(5, 150, 105, 0.4)'
    ],
    '2bac' => [
        'primary' => '#dc2626',     // أحمر للثانية باكالوريا
        'secondary' => '#f59e0b',
        'light' => '#fef3c7',
        'gradient_start' => '#dc2626', 
        'gradient_end' => '#f59e0b',
        'shadow' => 'rgba(220, 38, 38, 0.4)'
    ]
];

// الحصول على ألوان المستوى الحالي
$current_colors = $level_colors[$level] ?? $level_colors['tc'];

// إدراج البيانات الأساسية للمستويات إذا لم تكن موجودة
try {
    $pdo->exec("INSERT IGNORE INTO levels (code, name_ar, name_fr, order_num) VALUES
        ('tc', 'الجذع المشترك', 'Tronc Commun', 1),
        ('1bac', 'الأولى باكالوريا', 'Première Baccalauréat', 2),
        ('2bac', 'الثانية باكالوريا', 'Deuxième Baccalauréat', 3)
    ");
} catch (PDOException $e) {
    error_log("خطأ في إدراج المستويات: " . $e->getMessage());
}

// التحقق من وجود المستوى في قاعدة البيانات
try {
    $stmt_level = $pdo->prepare("SELECT id FROM levels WHERE code = ?");
    $stmt_level->execute([$level]);
    $level_id = $stmt_level->fetchColumn();
    
} catch (PDOException $e) {
    $level_id = null;
    error_log("خطأ في التحقق من المستوى: " . $e->getMessage());
}

// جلب الأساتذة الذين لديهم محتوى في هذا المستوى
$teachers = [];
try {
    // جلب الأساتذة مع إحصائيات محتواهم لهذا المستوى
    if ($level_id) {
        $sql = "
            SELECT u.id, u.username, u.full_name, u.email,
                   COUNT(c.id) as content_count,
                   COUNT(CASE WHEN ct.code = 'lessons' THEN 1 END) as lessons_count,
                   COUNT(CASE WHEN ct.code = 'exercises' THEN 1 END) as exercises_count,
                   COUNT(CASE WHEN ct.code = 'experiments' THEN 1 END) as experiments_count,
                   COUNT(CASE WHEN ct.code = 'exams' THEN 1 END) as exams_count,
                   MAX(c.created_at) as last_content_date
            FROM users u
            INNER JOIN content c ON u.id = c.teacher_id
            LEFT JOIN content_types ct ON c.type_id = ct.id
            WHERE u.user_type = 'teacher' AND c.status = 'published' AND c.level_id = ?
            GROUP BY u.id, u.username, u.full_name, u.email
            ORDER BY content_count DESC, last_content_date DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$level_id]);
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // إذا لم يتم العثور على أساتذة، جرب البحث باستخدام code
    if (empty($teachers)) {
        $sql = "
            SELECT u.id, u.username, u.full_name, u.email,
                   COUNT(c.id) as content_count,
                   COUNT(CASE WHEN ct.code = 'lessons' THEN 1 END) as lessons_count,
                   COUNT(CASE WHEN ct.code = 'exercises' THEN 1 END) as exercises_count,
                   COUNT(CASE WHEN ct.code = 'experiments' THEN 1 END) as experiments_count,
                   COUNT(CASE WHEN ct.code = 'exams' THEN 1 END) as exams_count,
                   MAX(c.created_at) as last_content_date
            FROM users u
            INNER JOIN content c ON u.id = c.teacher_id
            LEFT JOIN content_types ct ON c.type_id = ct.id
            LEFT JOIN levels l ON c.level_id = l.id
            WHERE u.user_type = 'teacher' AND c.status = 'published' AND l.code = ?
            GROUP BY u.id, u.username, u.full_name, u.email
            ORDER BY content_count DESC, last_content_date DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$level]);
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    error_log("تم جلب " . count($teachers) . " أستاذ للمستوى " . $level);
} catch (PDOException $e) {
    error_log("خطأ في جلب الأساتذة: " . $e->getMessage());
}

// جلب إحصائيات عامة للمستوى
$content_items = [];
try {
    if ($level_id) {
        $sql = "SELECT c.* FROM content c WHERE c.status = 'published' AND c.level_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$level_id]);
        $content_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("خطأ في جلب إحصائيات المحتوى: " . $e->getMessage());
}

// جلب إحصائيات المحتوى
$level_stats = [
    'total_count' => count($content_items),
    'lessons_count' => 0,
    'experiments_count' => 0,
    'exams_count' => 0
];

// حساب الإحصائيات يدويًا
foreach ($content_items as $item) {
    // التحقق من وجود المفاتيح قبل استخدامها
    $item_type = isset($item['type']) ? $item['type'] : '';
    $item_type_id = isset($item['type_id']) ? $item['type_id'] : 0;
    
    if ($item_type == 'lesson' || $item_type_id == 1) {
        $level_stats['lessons_count']++;
    } elseif ($item_type == 'experiment' || $item_type_id == 4 || $item_type_id == 6) {
        $level_stats['experiments_count']++;
    } elseif ($item_type == 'exam' || $item_type_id == 5) {
        $level_stats['exams_count']++;
    }
}

include 'includes/header.php';
?>



<style>
:root {
    --level-primary: <?php echo $current_colors['primary']; ?>;
    --level-secondary: <?php echo $current_colors['secondary']; ?>;
    --level-light: <?php echo $current_colors['light']; ?>;
    --level-gradient-start: <?php echo $current_colors['gradient_start']; ?>;
    --level-gradient-end: <?php echo $current_colors['gradient_end']; ?>;
    --level-shadow: <?php echo $current_colors['shadow']; ?>;
}

.level-container {
    background: linear-gradient(135deg, var(--level-light) 0%, #f8fafc 50%, rgba(255,255,255,0.8) 100%);
    min-height: 80vh;
    padding: 2rem 0;
    position: relative;
    overflow: hidden;
}

.level-container::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 40%;
    height: 200%;
    background: radial-gradient(ellipse at center, var(--level-shadow) 0%, transparent 70%);
    opacity: 0.1;
    transform: rotate(-15deg);
    pointer-events: none;
}

.level-header {
    background: linear-gradient(135deg, var(--level-gradient-start) 0%, var(--level-gradient-end) 100%);
    color: white;
    border-radius: 20px;
    padding: 1.5rem 1.5rem;
    text-align: center;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px var(--level-shadow);
}

.level-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.level-navigation {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    text-align: center;
}

.level-nav-item {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    margin: 0 0.25rem;
    border-radius: 25px;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.3s ease;
}

.level-nav-item.active {
    background: linear-gradient(135deg, var(--level-gradient-start) 0%, var(--level-gradient-end) 100%);
    color: white;
    box-shadow: 0 5px 15px var(--level-shadow);
}

.level-nav-item:not(.active) {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}

.level-nav-item:hover:not(.active) {
    background: var(--level-light);
    transform: translateY(-2px);
    border-color: var(--level-primary);
}

.subject-tabs {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.nav-pills .nav-link {
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    margin: 0 0.25rem;
    transition: all 0.3s ease;
    color: var(--level-primary);
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, var(--level-gradient-start) 0%, var(--level-gradient-end) 100%);
    color: white;
    box-shadow: 0 5px 15px var(--level-shadow);
}

.nav-pills .nav-link:not(.active):hover {
    background: var(--level-light);
    color: var(--level-primary);
}

.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.content-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border-left: 4px solid var(--level-primary);
}

.content-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px var(--level-shadow);
}

.content-header {
    padding: 1.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.content-type {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.type-lesson { 
    background: var(--level-light); 
    color: var(--level-primary); 
}
.type-experiment { 
    background: rgba(56, 142, 60, 0.1); 
    color: #388e3c; 
}
.type-exam { 
    background: rgba(245, 124, 0, 0.1); 
    color: #f57c00; 
}
.type-exercise { 
    background: rgba(123, 31, 162, 0.1); 
    color: #7b1fa2; 
}

.content-body {
    padding: 1.5rem;
}

.content-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
    font-size: 0.9rem;
    color: #6c757d;
}

.btn-view {
    background: linear-gradient(135deg, var(--level-gradient-start) 0%, var(--level-gradient-end) 100%);
    border: none;
    border-radius: 25px;
    padding: 0.5rem 1rem;
    color: white;
    transition: all 0.3s ease;
}

.btn-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px var(--level-shadow);
}

.no-content {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    border-left: 4px solid var(--level-primary);
}

.stats-row {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    border-left: 4px solid var(--level-primary);
}

.stat-item {
    text-align: center;
    padding: 1rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--level-primary);
}

/* أنماط بطاقات الأساتذة */
.teachers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.teacher-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border-left: 5px solid var(--level-primary);
    position: relative;
    overflow: hidden;
    transform: scale(0.85);
    opacity: 1;
}

.teacher-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, var(--level-gradient-start) 0%, var(--level-gradient-end) 100%);
}

.teacher-card:hover {
    transform: scale(0.88);
    box-shadow: 0 15px 35px var(--level-shadow);
    text-decoration: none;
    color: inherit;
}

.teacher-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--level-gradient-start) 0%, var(--level-gradient-end) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
    box-shadow: 0 8px 20px var(--level-shadow);
}

.teacher-name {
    font-size: 1.1rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 0.4rem;
    color: #2d3748;
}

.teacher-name .teacher-title {
    color: var(--level-primary);
    font-weight: 600;
    font-size: 0.9rem;
}

.teacher-level {
    text-align: center;
    color: var(--level-primary);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 0.85rem;
}

.teacher-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 1rem;
    padding: 0.8rem 0;
    background: var(--level-light);
    border-radius: 12px;
}

.teacher-stat {
    text-align: center;
    flex: 1;
}

.teacher-stat-number {
    display: block;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--level-primary);
    line-height: 1;
}

.teacher-stat-label {
    font-size: 0.7rem;
    color: #64748b;
    margin-top: 0.25rem;
}

.teacher-cta {
    text-align: center;
    background: linear-gradient(135deg, var(--level-gradient-start) 0%, var(--level-gradient-end) 100%);
    color: white;
    padding: 0.6rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.teacher-card:hover .teacher-cta {
    transform: scale(1.05);
}

/* أيقونات خاصة بكل مستوى */
.level-icon-tc { color: #4f46e5; }
.level-icon-1bac { color: #059669; }
.level-icon-2bac { color: #dc2626; }

/* تحسينات إضافية للتصميم */
.level-header h1, .level-header h2 {
    font-size: 2rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 0.5rem;
}

.level-header p {
    font-size: 1rem;
    opacity: 0.9;
    margin-top: 0.5rem;
}

@media (max-width: 768px) {
    .teachers-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.8rem;
    }
    
    .teacher-card {
        padding: 1rem;
        transform: scale(1);
        aspect-ratio: 1 / 1.2;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .teacher-card:hover {
        transform: scale(1.02);
    }
    
    .teacher-avatar {
        width: 40px;
        height: 40px;
        font-size: 1rem;
        margin: 0 auto 0.5rem;
        flex-shrink: 0;
    }
    
    .teacher-name {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
        flex-shrink: 0;
    }
    
    .teacher-name .teacher-title {
        font-size: 0.7rem;
    }
    
    .teacher-level {
        font-size: 0.7rem;
        margin-bottom: 0.5rem;
        flex-shrink: 0;
    }
    
    .teacher-stats {
        flex-direction: column;
        gap: 0.3rem;
        padding: 0.5rem 0;
        margin-bottom: 0.5rem;
        flex: 1;
        display: flex;
        justify-content: center;
    }
    
    .teacher-stat-number {
        font-size: 1rem;
    }
    
    .teacher-stat-label {
        font-size: 0.6rem;
    }
    
    .teacher-cta {
        padding: 0.4rem 0.8rem;
        font-size: 0.7rem;
    }
    
    .level-nav-item {
        display: block;
        margin: 0.25rem 0;
    }
}

/* تحسينات إضافية للشاشات الصغيرة جداً */
@media (max-width: 480px) {
    .teachers-grid {
        gap: 0.5rem;
    }
    
    .teacher-card {
        padding: 0.8rem;
        border-radius: 15px;
        aspect-ratio: 1 / 1.2;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .teacher-avatar {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
        margin-bottom: 0.6rem;
    }
    
    .teacher-name {
        font-size: 0.8rem;
    }
    
    .teacher-name .teacher-title {
        font-size: 0.65rem;
    }
    
    .teacher-level {
        font-size: 0.65rem;
        margin-bottom: 0.6rem;
    }
    
    .teacher-stats {
        padding: 0.4rem 0;
        margin-bottom: 0.6rem;
    }
    
    .teacher-stat-number {
        font-size: 0.9rem;
    }
    
    .teacher-stat-label {
        font-size: 0.55rem;
    }
    
    .teacher-cta {
        padding: 0.3rem 0.6rem;
        font-size: 0.65rem;
        border-radius: 20px;
    }
}

/* رسالة تلميح للمستخدم */
.scroll-hint {
    display: none !important;
}
</style>

<div class="level-container">
    <div class="container" style="position: relative; z-index: 1;">
        <div class="level-header">
            <div class="level-icon">
                <?php if (!empty($content_type) && in_array($content_type, $valid_types)): ?>
                    <?php
                    $content_icons = [
                        'lessons' => 'fas fa-book-open',
                        'exercises' => 'fas fa-pencil-alt',
                        'experiments' => 'fas fa-vial',
                        'exams' => 'fas fa-file-alt',
                        'assignments' => 'fas fa-clipboard-check',
                        'interactive' => 'fas fa-play-circle'
                    ];
                    ?>
                    <i class="<?php echo $content_icons[$content_type]; ?>"></i>
                <?php else: ?>
                    <?php if ($level == 'tc'): ?>
                        <i class="fas fa-star level-icon-tc"></i>
                    <?php elseif ($level == '1bac'): ?>
                        <i class="fas fa-star-half-alt level-icon-1bac"></i>
                    <?php else: ?>
                        <i class="fas fa-medal level-icon-2bac"></i>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <h2 class="mb-2">
                <?php 
                if (!empty($content_type) && in_array($content_type, $valid_types)) {
                    echo $content_type_titles[$content_type];
                } else {
                    echo $level_titles[$level];
                }
                ?>
            </h2>
            <p class="mb-0">
                <?php 
                if (!empty($content_type) && in_array($content_type, $valid_types)) {
                    echo 'اختر المستوى والأستاذ المناسب لتصفح ' . $content_type_titles[$content_type];
                } else {
                    echo 'استكشف المحتوى التعليمي المخصص لمستواك الدراسي';
                }
                ?>
            </p>
        </div>

        <!-- التنقل بين المستويات -->
        <div class="level-navigation">
            <h5 class="mb-3"><i class="fas fa-layer-group me-2"></i>المستويات التعليمية</h5>
            <div class="text-center">
                <a href="level.php?level=tc<?php echo !empty($content_type) ? '&type=' . $content_type : ''; ?>" class="level-nav-item <?php echo $level == 'tc' ? 'active' : ''; ?>" style="<?php echo $level != 'tc' ? 'border-color: #4f46e5; color: #4f46e5;' : ''; ?>">
                    <i class="fas fa-star me-2" style="color: #4f46e5;"></i>الجذع المشترك
                </a>
                <a href="level.php?level=1bac<?php echo !empty($content_type) ? '&type=' . $content_type : ''; ?>" class="level-nav-item <?php echo $level == '1bac' ? 'active' : ''; ?>" style="<?php echo $level != '1bac' ? 'border-color: #059669; color: #059669;' : ''; ?>">
                    <i class="fas fa-star-half-alt me-2" style="color: #059669;"></i>الأولى باك
                </a>
                <a href="level.php?level=2bac<?php echo !empty($content_type) ? '&type=' . $content_type : ''; ?>" class="level-nav-item <?php echo $level == '2bac' ? 'active' : ''; ?>" style="<?php echo $level != '2bac' ? 'border-color: #dc2626; color: #dc2626;' : ''; ?>">
                    <i class="fas fa-medal me-2" style="color: #dc2626;"></i>الثانية باك
                </a>
            </div>
        </div>


        
        <!-- عرض بطاقات الأساتذة لجميع المستخدمين -->
        <div>
            <?php if (empty($teachers)): ?>
                <div class="no-content">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4>لا يوجد أساتذة متاحون</h4>
                    <p class="text-muted">لم يتم إضافة محتوى من أساتذة لهذا المستوى بعد. تحقق لاحقاً للحصول على تحديثات.</p>
                    <div class="mt-4">
                        <a href="register.php" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>سجل للوصول لجميع المحتويات
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="teachers-grid" id="teachersGrid">
                    <?php foreach ($teachers as $teacher): ?>
                        <a href="teachers_lessons.php?teacher_id=<?php echo $teacher['id']; ?>&level=<?php echo $level; ?>&type=<?php echo $content_type ?? 'lessons'; ?>" class="teacher-card">
                            <div class="teacher-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            
                            <div class="teacher-name">
                                <span class="teacher-title">أستاذ</span><br>
                                <?php 
                                $display_name = !empty($teacher['full_name']) ? $teacher['full_name'] : $teacher['username'];
                                echo htmlspecialchars($display_name);
                                ?>
                            </div>
                            
                            <div class="teacher-level">
                                <i class="fas fa-graduation-cap me-1"></i>
                                <?php 
                                if (!empty($content_type) && in_array($content_type, $valid_types)) {
                                    echo $content_type_titles[$content_type] . ' - ' . $level_titles[$level];
                                } else {
                                    echo 'محتوى ' . $level_titles[$level];
                                }
                                ?>
                            </div>
                            
                            <div class="teacher-stats">
                                <div class="teacher-stat">
                                    <span class="teacher-stat-number"><?php echo $teacher['content_count']; ?></span>
                                    <small class="teacher-stat-label">محتوى</small>
                                </div>
                                <div class="teacher-stat">
                                    <span class="teacher-stat-number"><?php echo $teacher['lessons_count']; ?></span>
                                    <small class="teacher-stat-label">دروس</small>
                                </div>
                                <div class="teacher-stat">
                                    <span class="teacher-stat-number"><?php echo $teacher['experiments_count']; ?></span>
                                    <small class="teacher-stat-label">تجارب</small>
                                </div>
                            </div>

                            <div class="teacher-cta">
                                <i class="fas fa-arrow-left me-2"></i>
                                <?php 
                                if (!empty($content_type) && in_array($content_type, $valid_types)) {
                                    echo 'تصفح ' . $content_type_titles[$content_type];
                                } else {
                                    echo 'تصفح المحتوى';
                                }
                                ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>


    </div>
</div>

<script>


document.addEventListener('DOMContentLoaded', function() {
    const teacherCards = document.querySelectorAll('.teacher-card');
    
    // تأثير تدريجي لظهور البطاقات عند تحميل الصفحة
    teacherCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeInUp 0.6s ease forwards';
    });
});

// إضافة CSS للتأثير التدريجي
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.85);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(0.85);
        }
    }
    
    .teacher-card {
        opacity: 0;
    }
`;
document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?>
