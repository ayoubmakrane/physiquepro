<?php
session_start();

// منع التخزين المؤقت
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';
require_once 'includes/content_functions.php';

// استلام معرف الأستاذ إذا تم تمريره
$teacher_id = $_GET['teacher_id'] ?? null;
$level_filter = $_GET['level'] ?? 'all';
$subject_filter = $_GET['subject'] ?? 'all';
$search_query = $_GET['search'] ?? '';
$content_type = $_GET['type'] ?? 'lessons';

$teacher_id = $teacher_id ? (int)$teacher_id : null;
$level_filter = htmlspecialchars($level_filter);
$subject_filter = htmlspecialchars($subject_filter);
$search_query = trim(htmlspecialchars($search_query));

include 'includes/header.php';
?>

<div class="container mt-4">
    <?php if (!$teacher_id): ?>
        <!-- عرض صفحة الأساتذة -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-primary mb-3">
                <i class="fas fa-chalkboard-teacher me-3"></i>أساتذة الدروس
            </h1>
            <p class="lead text-muted">اختر الأستاذ لعرض دروسه التعليمية</p>
        </div>

        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'teacher'): ?>
        <div class="alert alert-info mb-4">
            <i class="fas fa-star me-2"></i>
            <strong>مرحباً أستاذ <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>!</strong>
            دروسك ستظهر في المقدمة. يمكنك <a href="teacher/add_content.php" class="alert-link">إضافة درس جديد</a>.
        </div>
        <?php endif; ?>

        <?php
        try {
            // جلب جميع الأساتذة مع دروسهم
            $teachers = getAllTeachersWithContent($pdo, 'lessons');
            
            // إذا لم توجد دروس، إنشاء بيانات تجريبية
            if (empty($teachers)) {
                echo "<div class='alert alert-warning text-center'>";
                echo "<h5>لا توجد دروس منشورة حالياً</h5>";
                echo "<p>جاري إنشاء دروس تجريبية للاختبار...</p>";
                echo "</div>";
                
                if (createSampleLessonsData($pdo)) {
                    echo "<div class='alert alert-success text-center'>";
                    echo "<p>تم إنشاء الدروس التجريبية بنجاح!</p>";
                    echo "</div>";
                    $teachers = getAllTeachersWithContent($pdo, 'lessons');
                }
            }
            
            // ترتيب الأساتذة - الأستاذ الحالي أولاً
            if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'teacher') {
                $current_user_id = $_SESSION['user_id'];
                usort($teachers, function($a, $b) use ($current_user_id) {
                    if ($a['id'] == $current_user_id) return -1;
                    if ($b['id'] == $current_user_id) return 1;
                    return $b['total_content'] - $a['total_content']; // ترتيب حسب عدد الدروس
                });
            } else {
                // ترتيب حسب عدد الدروس للزوار
                usort($teachers, function($a, $b) {
                    return $b['total_content'] - $a['total_content'];
                });
            }
            
            if (!empty($teachers)): ?>
                <div class="alert alert-success mb-4">
                    <i class="fas fa-users me-2"></i>
                    <strong>يوجد <?php echo count($teachers); ?> أستاذ لديه دروس منشورة</strong>
                </div>
                
                <div class="row">
                    <?php foreach ($teachers as $teacher): 
                        $is_current_user = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $teacher['id'];
                    ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 teacher-card <?php echo $is_current_user ? 'border-primary current-teacher' : ''; ?>" 
                                 style="border-radius: 20px; overflow: hidden; cursor: pointer;"
                                 onclick="window.location.href='teachers_lessons.php?teacher_id=<?php echo $teacher['id']; ?>'">
                                
                                <?php if ($is_current_user): ?>
                                <div class="ribbon">
                                    <span>أنت</span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="card-header bg-gradient text-white text-center" 
                                     style="background: linear-gradient(135deg, <?php echo $is_current_user ? '#28a745, #20c997' : '#007bff, #6610f2'; ?>); position: relative;">
                                    <div class="avatar-container mb-3">
                                        <div class="teacher-avatar">
                                            <i class="fas fa-user-tie fa-3x"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title mb-1 fw-bold">
                                        <?php echo htmlspecialchars($teacher['full_name'] ?: $teacher['username']); ?>
                                    </h5>
                                    <p class="card-subtitle mb-0 opacity-75">
                                        أستاذ <?php echo $teacher['specialization'] ?? 'الفيزياء والكيمياء'; ?>
                                    </p>
                                </div>
                                
                                <div class="card-body text-center">
                                    <div class="stats-grid mb-3">
                                        <div class="stat-item">
                                            <div class="stat-number text-primary fw-bold display-6">
                                                <?php echo $teacher['total_content']; ?>
                                            </div>
                                            <div class="stat-label text-muted">درس منشور</div>
                                        </div>
                                    </div>
                                    
                                    <div class="subjects-badges mb-3">
                                        <?php
                                        // عرض المواد التي يدرسها
                                        $subjects = [];
                                        if (!empty($teacher['content'])) {
                                            foreach ($teacher['content'] as $level => $lessons) {
                                                foreach ($lessons as $lesson) {
                                                    if (!in_array($lesson['subject'], $subjects)) {
                                                        $subjects[] = $lesson['subject'];
                                                    }
                                                }
                                            }
                                        }
                                        
                                        foreach (array_unique($subjects) as $subject):
                                            $subject_name = $subject == 'physics' ? 'فيزياء' : ($subject == 'chemistry' ? 'كيمياء' : 'عام');
                                            $badge_class = $subject == 'physics' ? 'bg-info' : ($subject == 'chemistry' ? 'bg-success' : 'bg-secondary');
                                        ?>
                                            <span class="badge <?php echo $badge_class; ?> me-1">
                                                <?php echo $subject_name; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <p class="text-muted mb-3">
                                        <?php echo $teacher['bio'] ?? 'أستاذ متخصص في تدريس مواد العلوم الفيزيائية'; ?>
                                    </p>
                                    
                                    <!-- عرض توزيع الدروس حسب المستوى -->
                                    <div class="levels-summary">
                                        <?php if (!empty($teacher['content'])): ?>
                                            <small class="text-muted d-block mb-2">توزيع الدروس:</small>
                                            <div class="row text-center">
                                                <?php foreach ($teacher['content'] as $level => $lessons): 
                                                    if (!empty($lessons)):
                                                        $level_name = $level == 'tc' ? 'الجذع المشترك' : 
                                                                     ($level == '1bac' ? 'الأولى باك' : 'الثانية باك');
                                                ?>
                                                    <div class="col-4">
                                                        <div class="level-stat">
                                                            <small class="fw-bold text-primary"><?php echo count($lessons); ?></small>
                                                            <small class="d-block text-muted"><?php echo $level_name; ?></small>
                                                        </div>
                                                    </div>
                                                <?php endif; endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-light text-center">
                                    <div class="d-grid">
                                                                <a href="teachers_lessons.php?teacher_id=<?php echo $teacher['id']; ?>&level=all&type=lessons" 
                           class="btn btn-<?php echo $is_current_user ? 'success' : 'primary'; ?> btn-lg">
                                            <i class="fas fa-eye me-2"></i>
                                            عرض الملف الشخصي
                                            <?php if ($is_current_user): ?>
                                                <i class="fas fa-star ms-2"></i>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-chalkboard-teacher fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                    <h4>لا يوجد أساتذة بدروس منشورة</h4>
                    <p class="text-muted">لم يتم العثور على أي أستاذ لديه دروس منشورة.</p>
                    <div class="mt-3">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-home me-2"></i>الرئيسية
                        </a>
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'teacher'): ?>
                            <a href="teacher/add_content.php" class="btn btn-success ms-2">
                                <i class="fas fa-plus me-2"></i>إضافة درس جديد
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; 
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger text-center'>";
            echo "<h5>حدث خطأ!</h5>";
            echo "<p>خطأ: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
        ?>

    <?php else: ?>
        <!-- عرض محتويات أستاذ معين -->
        <?php
        try {
            // جلب بيانات الأستاذ
            $teacher_query = "SELECT u.*, COUNT(c.id) as total_content 
                             FROM users u 
                             LEFT JOIN content c ON u.id = c.teacher_id 
                             WHERE u.id = ? AND u.user_type = 'teacher'
                             AND (c.id IS NULL OR c.status = 'published')
                             GROUP BY u.id";
            $stmt = $pdo->prepare($teacher_query);
            $stmt->execute([$teacher_id]);
            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$teacher) {
                echo "<div class='alert alert-danger text-center'>";
                echo "<h5>أستاذ غير موجود!</h5>";
                echo "<a href='teachers_lessons.php' class='btn btn-primary'>العودة للأساتذة</a>";
                echo "</div>";
            } else {
                $is_current_user = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $teacher_id;
                
                // جلب إحصائيات المحتوى مع فلترة المستوى
                $content_stats_query = "SELECT 
                    ct.code,
                    ct.name_ar,
                    ct.icon,
                    ct.color,
                    COUNT(c.id) as count
                FROM content_types ct
                LEFT JOIN content c ON ct.id = c.type_id AND c.teacher_id = ? AND c.status = 'published'
                LEFT JOIN levels l ON c.level_id = l.id
                WHERE ct.status = 'active'";
                
                $stats_params = [$teacher_id];
                
                // إضافة فلتر المستوى إذا لم يكن 'all'
                if ($level_filter !== 'all') {
                    $content_stats_query .= " AND l.code = ?";
                    $stats_params[] = $level_filter;
                }
                
                $content_stats_query .= " GROUP BY ct.id, ct.code, ct.name_ar, ct.icon, ct.color
                ORDER BY ct.order_num";
                
                $stmt = $pdo->prepare($content_stats_query);
                $stmt->execute($stats_params);
                $content_types_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
                <!-- شريط معلومات الأستاذ - تصميم مدمج وصغير -->
                <div class="teacher-info-bar mb-4" style="background: white; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-left: 5px solid <?php echo $is_current_user ? '#28a745' : '#007bff'; ?>;">
                    <div class="row align-items-center teacher-info-row">
                        <!-- معلومات المستوى -->
                        <div class="col-12 col-md-3 mb-3 mb-md-0 text-center text-md-start">
                            <div class="level-badge-container">
                                <?php
                                // تحديد المستوى الحالي من URL أو أول مستوى متاح
                                $current_level = $_GET['level'] ?? '2bac';
                                $level_colors = [
                                    'tc' => 'primary',
                                    '1bac' => 'success', 
                                    '2bac' => 'warning'
                                ];
                                $level_names = [
                                    'tc' => 'الجذع المشترك',
                                    '1bac' => 'الأولى باكالوريا',
                                    '2bac' => 'الثانية باكالوريا'
                                ];
                                $level_color = $level_colors[$current_level] ?? 'primary';
                                $level_name = $level_names[$current_level] ?? 'الثانية باكالوريا';
                                ?>
                                <span class="badge bg-<?php echo $level_color; ?> fs-6 px-3 py-2 rounded-pill teacher-level-badge">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    <?php echo $level_name; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- معلومات الأستاذ -->
                        <div class="col-12 col-md-6 mb-3 mb-md-0 text-center text-md-start">
                            <div class="teacher-info">
                                <h4 class="mb-1 fw-bold text-dark teacher-name">
                                    <i class="fas fa-user-tie me-2 text-<?php echo $is_current_user ? 'success' : 'primary'; ?>"></i>
                                    أستاذ <?php echo htmlspecialchars($teacher['full_name'] ?: $teacher['username']); ?>
                                    <?php if ($is_current_user): ?>
                                        <span class="badge bg-success ms-2 teacher-badge">أنت</span>
                                    <?php endif; ?>
                                </h4>
                                <p class="text-muted mb-0 teacher-specialization">
                                    <i class="fas fa-flask me-1"></i>
                                    <?php echo getTeacherSpecialization($pdo, $teacher_id) ?? 'الفيزياء والكيمياء'; ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- الإحصائيات -->
                        <div class="col-12 col-md-3 text-center">
                            <div class="stats-mini d-flex justify-content-around text-center">
                                <div class="stat-mini">
                                    <div class="stat-number text-primary fw-bold">
                                        <?php 
                                        // حساب المحتوى للمستوى المحدد فقط
                                        $level_content_query = "SELECT COUNT(*) as level_content FROM content c 
                                                               LEFT JOIN levels l ON c.level_id = l.id 
                                                               WHERE c.teacher_id = ? AND c.status = 'published'";
                                        $level_content_params = [$teacher_id];
                                        
                                        if ($level_filter !== 'all') {
                                            $level_content_query .= " AND l.code = ?";
                                            $level_content_params[] = $level_filter;
                                        }
                                        
                                        $stmt = $pdo->prepare($level_content_query);
                                        $stmt->execute($level_content_params);
                                        $level_content = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo $level_content['level_content'] ?? 0;
                                        ?>
                                    </div>
                                    <small class="text-muted">محتوى</small>
                                </div>
                                <div class="stat-mini">
                                    <div class="stat-number text-info fw-bold">
                                        <?php 
                                        // حساب المشاهدات للمستوى المحدد فقط
                                        $views_query = "SELECT SUM(views_count) as total_views FROM content c 
                                                       LEFT JOIN levels l ON c.level_id = l.id 
                                                       WHERE c.teacher_id = ? AND c.status = 'published'";
                                        $views_params = [$teacher_id];
                                        
                                        if ($level_filter !== 'all') {
                                            $views_query .= " AND l.code = ?";
                                            $views_params[] = $level_filter;
                                        }
                                        
                                        $stmt = $pdo->prepare($views_query);
                                        $stmt->execute($views_params);
                                        $views = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo $views['total_views'] ?? 0;
                                        ?>
                                    </div>
                                    <small class="text-muted">مشاهدة</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- أزرار التحكم -->
                    <div class="mt-3 text-center teacher-controls">
                        <a href="teachers_lessons.php" class="btn btn-outline-secondary btn-sm me-2 teacher-control-btn">
                            <i class="fas fa-arrow-right me-1"></i>العودة للأساتذة
                        </a>
                        <?php if ($is_current_user): ?>
                            <a href="teacher/add_content.php" class="btn btn-success btn-sm teacher-control-btn">
                                <i class="fas fa-plus me-1"></i>إضافة محتوى جديد
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- بطاقات أنواع المحتوى - تصميم صغير ومتراص -->
                <div class="content-types-mini mb-4">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">

                        <?php 
                        $mini_colors = [
                            'lessons' => '#007bff',
                            'exercises' => '#28a745', 
                            'assignments' => '#ffc107',
                            'experiments' => '#dc3545',
                            'exams' => '#17a2b8',
                            'interactive_lessons' => '#6f42c1'
                        ];
                        
                        foreach ($content_types_stats as $type): 
                            $bg_color = $mini_colors[$type['code']] ?? '#6c757d';
                        ?>
                        <button class="content-type-mini <?php echo $content_type == $type['code'] ? 'active' : ''; ?>" 
                                onclick="filterContent('<?php echo $type['code']; ?>')"
                                style="background: <?php echo $bg_color; ?>; color: white;">
                            <i class="<?php echo $type['icon']; ?> me-1"></i>
                            <?php echo $type['name_ar']; ?> (<?php echo $type['count']; ?>)
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- فلاتر المستويات - تظهر فقط إذا لم يتم تحديد مستوى معين -->
                <?php if ($level_filter === 'all'): ?>
                <div class="level-filters mb-4">
                    <div class="text-center">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-layer-group me-2"></i>تصفية حسب المستوى
                        </h6>
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <?php
                            $level_options = [
                                'all' => ['name' => 'جميع المستويات', 'color' => 'secondary'],
                                'tc' => ['name' => 'الجذع المشترك', 'color' => 'primary'],
                                '1bac' => ['name' => 'الأولى باكالوريا', 'color' => 'success'],
                                '2bac' => ['name' => 'الثانية باكالوريا', 'color' => 'warning']
                            ];
                            
                            foreach ($level_options as $level_code => $level_info):
                                $is_active = $level_filter === $level_code;
                                $btn_class = $is_active ? 'btn-' . $level_info['color'] : 'btn-outline-' . $level_info['color'];
                            ?>
                                <a href="?teacher_id=<?php echo $teacher_id; ?>&level=<?php echo $level_code; ?>&type=<?php echo $content_type; ?>" 
                                   class="btn <?php echo $btn_class; ?> btn-sm">
                                    <i class="fas fa-graduation-cap me-1"></i>
                                    <?php echo $level_info['name']; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- محتويات الأستاذ -->
                <?php
                // جلب محتويات الأستاذ المحدد
                $content_query = "SELECT c.*, l.name_ar as level_name, l.code as level_code, 
                                        ct.name_ar as type_name, ct.code as type_code, ct.icon, ct.color 
                                 FROM content c
                                 LEFT JOIN levels l ON c.level_id = l.id
                                 LEFT JOIN content_types ct ON c.type_id = ct.id
                                 WHERE c.teacher_id = ? AND c.status = 'published'";
                
                $params = [$teacher_id];
                
                // إضافة فلتر نوع المحتوى (الدروس هي الافتراضية)
                $content_query .= " AND ct.code = ?";
                $params[] = $content_type;
                
                if ($level_filter !== 'all') {
                    $content_query .= " AND l.code = ?";
                    $params[] = $level_filter;
                }
                
                if (!empty($search_query)) {
                    $content_query .= " AND (c.title LIKE ? OR c.description LIKE ?)";
                    $params[] = "%$search_query%";
                    $params[] = "%$search_query%";
                }
                
                $content_query .= " ORDER BY c.created_at DESC";
                
                $stmt = $pdo->prepare($content_query);
                $stmt->execute($params);
                $content_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($content_items)): ?>
                    <?php
                    // تحديد أسماء المستويات
                    $level_names_ar = [
                        'tc' => 'الجذع المشترك',
                        '1bac' => 'الأولى باكالوريا',
                        '2bac' => 'الثانية باكالوريا'
                    ];
                    
                    $level_names_fr = [
                        'tc' => 'Tronc Commun',
                        '1bac' => 'Première Baccalauréat',
                        '2bac' => 'Deuxième Baccalauréat'
                    ];
                    
                    // تحديد أسماء أنواع المحتوى بالفرنسية
                    $content_type_names_fr = [
                        'الدروس' => 'Cours',
                        'التمارين' => 'Exercices',
                        'التجارب' => 'Expériences',
                        'الامتحانات' => 'Examens',
                        'الفروض' => 'Devoirs',
                        'الدروس التفاعلية' => 'Cours Interactifs'
                    ];
                    
                    // الحصول على المستوى الحالي
                    $current_level_code = $content_items[0]['level_code'] ?? $level_filter;
                    $level_name_ar = $level_names_ar[$current_level_code] ?? 'غير محدد';
                    $level_name_fr = $level_names_fr[$current_level_code] ?? 'Non spécifié';
                    
                    // نوع المحتوى
                    $content_type_name = $content_items[0]['type_name'] ?? 'غير محدد';
                    $content_type_name_fr = $content_type_names_fr[$content_type_name] ?? 'Non spécifié';
                    ?>
                    <!-- رسالة المحتوى في الأعلى -->
                    <div class="content-header-message mb-4">
                        <div class="alert alert-info text-center py-3" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <div class="row align-items-center content-header-row">
                                <div class="col-12 col-md-6 text-center text-md-end mb-2 mb-md-0">
                                    <h5 class="mb-0 fw-bold text-primary content-header-title">
                                        <i class="fas fa-book-open me-2"></i>
                                        <?php echo $content_type_name; ?> <?php echo $level_name_ar; ?>
                                    </h5>
                                </div>
                                <div class="col-12 col-md-6 text-center text-md-start">
                                    <h5 class="mb-0 fw-bold text-secondary content-header-title">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        <?php echo $content_type_name_fr; ?> <?php echo $level_name_fr; ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- بطاقات المحتوى الأفقية -->
                    <div class="content-list">
                        <?php foreach ($content_items as $item): ?>
                            <div class="content-item-horizontal mb-3" 
                                 onclick="window.location.href='content_router.php?id=<?php echo $item['id']; ?>'" 
                                 style="cursor: pointer;"
                                 data-bs-toggle="tooltip" 
                                 data-bs-placement="top" 
                                 data-bs-title="انقر لعرض: <?php echo htmlspecialchars($item['title']); ?>"
                                 title="انقر لعرض: <?php echo htmlspecialchars($item['title']); ?>">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; transition: all 0.3s ease;">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center">
                                            <!-- أيقونة نوع المحتوى -->
                                            <div class="col-auto">
                                                <div class="content-type-icon-mini bg-<?php echo $item['color']; ?>" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="<?php echo $item['icon']; ?> text-white fa-lg"></i>
                                                </div>
                                            </div>
                                            
                                            <!-- معلومات المحتوى -->
                                            <div class="col">
                                                <div class="content-info-mobile">
                                                    <!-- السطر الأول: العنوان + زر العرض -->
                                                    <div class="mobile-title-row">
                                                        <h6 class="content-title-mobile text-dark">
                                                            <?php echo htmlspecialchars($item['title']); ?>
                                                        </h6>
                                                        <button class="btn btn-<?php echo $item['color']; ?> mobile-action-btn"
                                                                onclick="window.location.href='content_router.php?id=<?php echo $item['id']; ?>'"
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="left" 
                                                                data-bs-title="عرض تفاصيل الدرس">
                                                            <i class="fas fa-play me-1"></i>
                                                            عرض
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- السطر الثاني: المعطيات الصغيرة -->
                                                    <div class="mobile-meta-row">
                                                        <!-- البطاقات (النوع والمستوى) -->
                                                        <div class="mobile-badges">
                                                            <span class="badge bg-<?php echo $item['color']; ?>">
                                                                <?php echo $item['type_name']; ?>
                                                            </span>
                                                            <span class="badge bg-light text-dark">
                                                                <?php echo htmlspecialchars($item['level_name'] ?? 'غير محدد'); ?>
                                                            </span>
                                                        </div>
                                                        
                                                        <!-- الإحصائيات (التاريخ والمشاهدات) -->
                                                        <div class="mobile-stats">
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar"></i>
                                                                <?php echo date('Y/m/d', strtotime($item['created_at'])); ?>
                                                            </small>
                                                            <small class="text-muted">
                                                                <i class="fas fa-eye"></i>
                                                                <?php echo $item['views_count'] ?? 0; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- العناصر القديمة للحاسوب -->
                                                    <div class="content-title-section d-none d-md-block">
                                                        <h6 class="mb-1 fw-bold text-dark">
                                                            <?php echo htmlspecialchars($item['title']); ?>
                                                        </h6>
                                                        <div class="content-badges-mobile mb-2">
                                                            <span class="badge bg-<?php echo $item['color']; ?> badge-sm me-1">
                                                                <?php echo $item['type_name']; ?>
                                                            </span>
                                                            <span class="badge bg-light text-dark badge-sm">
                                                                <?php echo htmlspecialchars($item['level_name'] ?? 'غير محدد'); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- معلومات إضافية للحاسوب -->
                                                    <div class="content-meta-mobile d-none d-md-flex align-items-center justify-content-between">
                                                        <div class="content-date-views d-flex align-items-center gap-3">
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                <?php echo date('Y/m/d', strtotime($item['created_at'])); ?>
                                                            </small>
                                                            <small class="text-muted">
                                                                <i class="fas fa-eye me-1"></i>
                                                                <?php echo $item['views_count'] ?? 0; ?> مشاهدة
                                                            </small>
                                                        </div>
                                                        
                                                        <div class="content-action-mobile">
                                                            <button class="btn btn-<?php echo $item['color']; ?> btn-sm"
                                                                    data-bs-toggle="tooltip" 
                                                                    data-bs-placement="left" 
                                                                    data-bs-title="عرض تفاصيل الدرس">
                                                                <i class="fas fa-play me-1"></i>
                                                                عرض
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                        <h4>لا توجد محتويات</h4>
                        <p class="text-muted">لا توجد محتويات تطابق معايير البحث المحددة لهذا الأستاذ.</p>
                        <div class="mt-3">
                            <a href="teachers_lessons.php?teacher_id=<?php echo $teacher_id; ?>" class="btn btn-primary me-2">
                                <i class="fas fa-refresh me-2"></i>إعادة تحميل
                            </a>
                            <?php if ($is_current_user): ?>
                                <a href="teacher/add_content.php" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>إضافة محتوى جديد
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php 
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger text-center'>";
            echo "<h5>حدث خطأ!</h5>";
            echo "<p>خطأ: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
        ?>
    <?php endif; ?>
</div>

<style>
/* شريط معلومات الأستاذ */
.teacher-info-bar {
    transition: all 0.3s ease;
}

.teacher-info-bar:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.stats-mini .stat-number {
    font-size: 1.5rem;
    line-height: 1;
}

.teacher-info-row {
    align-items: center;
    justify-content: center;
}

.teacher-level-badge {
    display: inline-block;
    text-align: center;
    white-space: nowrap;
}

.teacher-name {
    font-size: 1.2rem;
    line-height: 1.3;
    word-wrap: break-word;
}

.teacher-specialization {
    font-size: 1rem;
    line-height: 1.2;
}

.teacher-badge {
    font-size: 0.8rem;
    padding: 0.2rem 0.5rem;
}

/* بطاقات أنواع المحتوى الصغيرة */
.content-types-mini {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 15px;
    margin-bottom: 1.5rem;
}

.content-type-mini {
    border: none;
    border-radius: 25px;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin: 0.25rem;
    display: inline-block;
    text-align: center;
    white-space: nowrap;
    min-width: fit-content;
}

.content-type-mini:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.content-type-mini.active {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

/* بطاقات المحتوى الأفقية */
.content-item-horizontal {
    transition: all 0.3s ease;
    position: relative;
}

.content-item-horizontal:hover {
    transform: translateY(-3px);
}

.content-item-horizontal:hover .card {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

/* تخصيص tooltip */
.tooltip {
    font-family: 'Tajawal', sans-serif;
}

.tooltip .tooltip-inner {
    background-color: #2c3e50;
    color: white;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 0.9rem;
    font-weight: 500;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    max-width: 300px;
}

.tooltip .tooltip-arrow {
    border-top-color: #2c3e50;
}

/* تأثير إضافي عند التحويم */
.content-item-horizontal:hover {
    z-index: 10;
}

.content-type-icon-mini {
    transition: all 0.3s ease;
}

.content-item-horizontal:hover .content-type-icon-mini {
    transform: scale(1.1) rotate(5deg);
}

.badge-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* بطاقات الأساتذة */
.teacher-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.teacher-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.current-teacher {
    border-color: #28a745 !important;
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
}

.current-teacher:hover {
    box-shadow: 0 15px 35px rgba(40, 167, 69, 0.3);
}

.ribbon {
    position: absolute;
    top: 15px;
    right: -10px;
    background: #ffc107;
    color: #000;
    padding: 5px 20px;
    font-size: 12px;
    font-weight: bold;
    transform: rotate(15deg);
    z-index: 10;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.teacher-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    transition: all 0.3s ease;
}

.teacher-card:hover .teacher-avatar {
    transform: scale(1.1);
}

.stat-item {
    padding: 1rem;
    text-align: center;
}

.level-stat {
    padding: 0.5rem;
    border-radius: 8px;
    background: rgba(0,123,255,0.1);
    margin-bottom: 0.5rem;
}

/* تحسين عرض المحتوى للحاسوب - الإعدادات الأصلية */
.content-info-mobile {
    width: 100%;
}

.content-title-mobile {
    font-size: 1rem;
    line-height: 1.3;
    margin-bottom: 0.5rem !important;
}

.content-badges-mobile {
    margin-bottom: 0.75rem !important;
}

.content-meta-mobile {
    gap: 0.5rem;
    align-items: center !important;
}

.content-date-views {
    flex: 1;
    min-width: 0;
}

.content-action-mobile {
    flex-shrink: 0;
}

/* إخفاء العناصر الجديدة على الحاسوب */
@media (min-width: 769px) {
    .mobile-title-row,
    .mobile-meta-row {
        display: none !important;
    }
    
    .content-item-horizontal .col-auto {
        display: block !important;
    }
    
    /* تحسين الأيقونة الجانبية للحاسوب */
    .content-type-icon-mini {
        background: linear-gradient(135deg, var(--bs-bg-opacity, 1) 0%, color-mix(in srgb, var(--bs-bg-opacity, 1) 80%, white) 100%) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        border: 2px solid rgba(255, 255, 255, 0.3) !important;
        backdrop-filter: blur(10px) !important;
        transition: all 0.3s ease !important;
    }
    
    .content-item-horizontal:hover .content-type-icon-mini {
        transform: scale(1.1) rotate(5deg) !important;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important;
    }
    
    .content-item-horizontal .card-body {
        background: linear-gradient(135deg, #f8f9ff 0%, #fff5f8 50%, #f0fff4 100%) !important;
        border: 1px solid rgba(0, 123, 255, 0.1) !important;
        position: relative !important;
        overflow: hidden !important;
    }
    
    .content-item-horizontal .card-body::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 3px !important;
        background: linear-gradient(90deg, #007bff, #28a745, #ffc107, #dc3545, #17a2b8, #6f42c1) !important;
        opacity: 0.6 !important;
    }
    
    .content-title-section,
    .content-meta-mobile {
        display: block !important;
    }
    
    .content-meta-mobile {
        display: flex !important;
    }
    
    /* تحسين الألوان للعناصر في الحاسوب */
    .content-badges-mobile {
        padding: 0.3rem 0.5rem !important;
        background: rgba(248, 249, 255, 0.8) !important;
        border-radius: 8px !important;
        border: 1px solid rgba(0, 123, 255, 0.08) !important;
        margin-bottom: 0.75rem !important;
    }
    
    .content-badges-mobile .badge {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        backdrop-filter: blur(5px) !important;
    }
    
    .content-date-views {
        background: rgba(255, 255, 255, 0.7) !important;
        padding: 0.3rem 0.6rem !important;
        border-radius: 8px !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(10px) !important;
    }
    
    .content-date-views small {
        background: rgba(255, 255, 255, 0.6) !important;
        padding: 0.15rem 0.3rem !important;
        border-radius: 10px !important;
        border: 1px solid rgba(108, 117, 125, 0.1) !important;
        backdrop-filter: blur(3px) !important;
    }
    
    .content-action-mobile .btn {
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15) !important;
        border: none !important;
        background: linear-gradient(135deg, var(--bs-btn-bg) 0%, color-mix(in srgb, var(--bs-btn-bg) 80%, white) 100%) !important;
        backdrop-filter: blur(10px) !important;
        transition: all 0.3s ease !important;
        border-radius: 20px !important;
    }
    
    .content-action-mobile .btn:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 5px 12px rgba(0, 0, 0, 0.2) !important;
    }
    
    /* تحسين التصميم الملون للحاسوب */
    .content-item-horizontal .card {
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.1) !important;
        border: 1px solid rgba(0, 123, 255, 0.08) !important;
        transition: all 0.3s ease !important;
        border-radius: 12px !important;
        overflow: hidden !important;
    }
    
    .content-item-horizontal:hover .card {
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.18) !important;
        border: 1px solid rgba(0, 123, 255, 0.15) !important;
        transform: translateY(-3px) !important;
    }
    
    .content-item-horizontal:hover .card-body {
        background: linear-gradient(135deg, #f0f4ff 0%, #fff0f5 50%, #f0fff8 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 20px rgba(0, 123, 255, 0.15) !important;
    }
    
    .content-item-horizontal:hover .mobile-title-row,
    .content-item-horizontal:hover .mobile-meta-row {
        transform: none !important;
        background: transparent !important;
    }
}

/* تحسين البطاقة الأولى للهاتف المحمول */
.content-header-message {
    margin-bottom: 1.5rem !important;
}

.content-header-row {
    margin: 0 !important;
    justify-content: center !important;
    align-items: center !important;
}

.content-header-title {
    font-size: 1.1rem;
    line-height: 1.3;
    text-align: center;
    word-wrap: break-word;
    padding: 0 0.5rem;
}

/* تحسينات الاستجابة */
@media (max-width: 768px) {
    .teacher-avatar {
        width: 60px;
        height: 60px;
    }
    
    /* تحسين بطاقات نوع المحتوى للهاتف فقط */
    .content-types-mini {
        padding: 0.75rem 0.5rem !important;
        background: #f8f9fa !important;
        border-radius: 12px !important;
        margin-bottom: 1rem !important;
        text-align: center !important;
    }
    
    .content-types-mini .d-flex {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)) !important;
        gap: 0.5rem !important;
        justify-content: center !important;
        align-items: center !important;
    }
    
    .content-type-mini {
        padding: 0.5rem 0.75rem !important;
        font-size: 0.8rem !important;
        margin: 0 !important;
        border-radius: 20px !important;
        font-weight: 600 !important;
        text-align: center !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        min-height: 36px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
        transition: all 0.3s ease !important;
        line-height: 1.2 !important;
    }
    
    .content-type-mini:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15) !important;
    }
    
    .content-type-mini.active {
        transform: translateY(-1px) scale(1.02) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
        font-weight: 700 !important;
    }
    
    .content-type-mini i {
        margin-left: 0.3rem !important;
        margin-right: 0.3rem !important;
        font-size: 0.9rem !important;
    }
    
    /* للشاشات الصغيرة جداً */
    @media (max-width: 480px) {
        .content-types-mini .d-flex {
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)) !important;
            gap: 0.4rem !important;
        }
        
        .content-type-mini {
            padding: 0.4rem 0.6rem !important;
            font-size: 0.75rem !important;
            min-height: 32px !important;
        }
        
        .content-type-mini i {
            font-size: 0.8rem !important;
            margin-left: 0.2rem !important;
            margin-right: 0.2rem !important;
        }
    }
    
    /* للشاشات الصغيرة نسبياً */
    @media (max-width: 360px) {
        .content-types-mini .d-flex {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.3rem !important;
        }
        
        .content-type-mini {
            font-size: 0.7rem !important;
            padding: 0.35rem 0.5rem !important;
            min-height: 30px !important;
        }
        
        /* تحسين بطاقات الدروس للشاشات الصغيرة جداً */
        .content-title-mobile {
            font-size: 0.85rem !important;
            -webkit-line-clamp: 1 !important;
            max-height: 1.3em !important;
        }
        
        .mobile-action-btn {
            font-size: 0.7rem !important;
            padding: 0.3rem 0.6rem !important;
            min-width: 50px !important;
        }
        
        .mobile-badges .badge {
            font-size: 0.6rem !important;
            padding: 0.1rem 0.3rem !important;
        }
        
        .mobile-stats small {
            font-size: 0.65rem !important;
        }
        
        .mobile-stats {
            gap: 0.5rem !important;
        }
        
        .mobile-meta-row {
            gap: 0.2rem !important;
        }
    }
    
    .stats-mini .stat-number {
        font-size: 1.2rem;
    }
    
    /* تحسين البطاقة الأولى للهاتف */
    .content-header-message {
        margin-bottom: 1rem !important;
    }
    
    .content-header-row {
        text-align: center !important;
        justify-content: center !important;
    }
    
    .content-header-title {
        font-size: 1rem !important;
        line-height: 1.4 !important;
        text-align: center !important;
        margin-bottom: 0.5rem !important;
        padding: 0.25rem 0.5rem !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
    }
    
    .content-header-title i {
        margin-left: 0.3rem !important;
        margin-right: 0.3rem !important;
    }
    
    .content-header-row .col-12 {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    /* تحسين تناسق الأسطر */
    .content-header-message .alert {
        padding: 1rem 0.5rem !important;
        text-align: center !important;
    }
    
    .content-header-row .col-12:first-child {
        margin-bottom: 0.3rem !important;
    }
    
    .content-header-row .col-12:last-child {
        margin-bottom: 0 !important;
    }
    
    /* تحسين شريط معلومات الأستاذ للهاتف */
    .teacher-info-bar {
        padding: 1rem 0.75rem !important;
        margin-bottom: 1rem !important;
    }
    
    .teacher-info-row {
        text-align: center !important;
    }
    
    .teacher-info-row .col-12 {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
        margin-bottom: 1rem !important;
    }
    
    .teacher-info-row .col-12:last-child {
        margin-bottom: 0.5rem !important;
    }
    
    .teacher-level-badge {
        font-size: 0.9rem !important;
        padding: 0.4rem 0.8rem !important;
        display: inline-block !important;
        margin: 0 auto !important;
    }
    
    .teacher-name {
        font-size: 1rem !important;
        line-height: 1.3 !important;
        text-align: center !important;
        margin-bottom: 0.3rem !important;
        word-wrap: break-word !important;
    }
    
    .teacher-name i {
        margin-left: 0.3rem !important;
        margin-right: 0.3rem !important;
    }
    
    .teacher-badge {
        font-size: 0.7rem !important;
        padding: 0.15rem 0.4rem !important;
        margin-right: 0.3rem !important;
    }
    
    .teacher-specialization {
        font-size: 0.9rem !important;
        text-align: center !important;
        margin-bottom: 0 !important;
    }
    
    .stats-mini {
        justify-content: center !important;
        gap: 1rem !important;
    }
    
    .stat-mini {
        text-align: center !important;
    }
    
    .stats-mini .stat-number {
        font-size: 1.1rem !important;
    }
    
    .teacher-controls {
        margin-top: 1rem !important;
        text-align: center !important;
    }
    
    .teacher-control-btn {
        font-size: 0.85rem !important;
        padding: 0.4rem 0.8rem !important;
        margin: 0.2rem !important;
        display: inline-block !important;
    }
    
    /* تحسين البطاقات للهاتف المحمول فقط - تصميم شريط مدمج */
    .content-item-horizontal {
        margin-bottom: 0.75rem !important;
    }
    
    .content-item-horizontal .card {
        border-radius: 10px !important;
        overflow: hidden !important;
    }
    
    .content-item-horizontal .card-body {
        padding: 0.75rem !important;
        background: linear-gradient(135deg, #f8f9ff 0%, #fff5f8 50%, #f0fff4 100%) !important;
        border: 1px solid rgba(0, 123, 255, 0.1) !important;
        position: relative !important;
        overflow: hidden !important;
    }
    
    .content-item-horizontal .card-body::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 3px !important;
        background: linear-gradient(90deg, #007bff, #28a745, #ffc107, #dc3545, #17a2b8, #6f42c1) !important;
        opacity: 0.6 !important;
    }
    
    /* إخفاء الأيقونة على الهاتف فقط لتوفير مساحة */
    .content-item-horizontal .col-auto {
        display: none !important;
    }
    
    /* تصميم الشريط المدمج للهاتف فقط */
    .content-info-mobile {
        width: 100% !important;
        padding: 0 !important;
    }
    
    /* السطر الأول للهاتف فقط: العنوان + زر العرض */
    .mobile-title-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        margin-bottom: 0.5rem !important;
        gap: 0.5rem !important;
        background: rgba(255, 255, 255, 0.7) !important;
        padding: 0.4rem 0.6rem !important;
        border-radius: 8px !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }
    
    .content-title-mobile {
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        line-height: 1.3 !important;
        margin: 0 !important;
        flex: 1 !important;
        min-width: 0 !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        max-height: 2.6em !important;
    }
    
    .mobile-action-btn {
        flex-shrink: 0 !important;
        font-size: 0.75rem !important;
        padding: 0.4rem 0.8rem !important;
        border-radius: 20px !important;
        white-space: nowrap !important;
        min-width: 60px !important;
        text-align: center !important;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15) !important;
        border: none !important;
        background: linear-gradient(135deg, var(--bs-btn-bg) 0%, color-mix(in srgb, var(--bs-btn-bg) 80%, white) 100%) !important;
        backdrop-filter: blur(10px) !important;
        transition: all 0.3s ease !important;
    }
    
    .mobile-action-btn:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 5px 12px rgba(0, 0, 0, 0.2) !important;
    }
    
    /* السطر الثاني: المعطيات الصغيرة */
    .mobile-meta-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: wrap !important;
        gap: 0.3rem !important;
        background: rgba(248, 249, 255, 0.8) !important;
        padding: 0.3rem 0.5rem !important;
        border-radius: 6px !important;
        border: 1px solid rgba(0, 123, 255, 0.08) !important;
    }
    
    .mobile-badges {
        display: flex !important;
        align-items: center !important;
        gap: 0.25rem !important;
        flex-wrap: wrap !important;
    }
    
    .mobile-badges .badge {
        font-size: 0.65rem !important;
        padding: 0.15rem 0.4rem !important;
        border-radius: 12px !important;
        line-height: 1.2 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        backdrop-filter: blur(5px) !important;
    }
    
    .mobile-badges .badge.bg-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        color: #495057 !important;
        border: 1px solid rgba(108, 117, 125, 0.2) !important;
    }
    
    .mobile-stats {
        display: flex !important;
        align-items: center !important;
        gap: 0.8rem !important;
        flex-shrink: 0 !important;
    }
    
    .mobile-stats small {
        font-size: 0.7rem !important;
        color: #6c757d !important;
        white-space: nowrap !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.2rem !important;
        background: rgba(255, 255, 255, 0.6) !important;
        padding: 0.15rem 0.3rem !important;
        border-radius: 10px !important;
        border: 1px solid rgba(108, 117, 125, 0.1) !important;
        backdrop-filter: blur(3px) !important;
    }
    
    .mobile-stats i {
        font-size: 0.6rem !important;
        opacity: 0.7 !important;
    }
    
    /* إخفاء العناصر غير المرغوب فيها للهاتف */
    .content-badges-mobile,
    .content-meta-mobile {
        display: none !important;
    }
    
    /* التخطيط الجديد للمحتوى */
    .mobile-view .content-item-horizontal .row {
        margin: 0 !important;
    }
    
    .mobile-view .content-item-horizontal .col {
        padding: 0 !important;
    }
    
    /* تأثيرات hover للهاتف */
    .content-item-horizontal {
        transition: all 0.3s ease !important;
    }
    
    .content-item-horizontal:hover .card-body {
        background: linear-gradient(135deg, #f0f4ff 0%, #fff0f5 50%, #f0fff8 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 8px 20px rgba(0, 123, 255, 0.15) !important;
    }
    
    .content-item-horizontal:active {
        transform: scale(0.98) !important;
        transition: transform 0.1s ease !important;
    }
    
    .content-item-horizontal:hover .mobile-title-row {
        background: rgba(255, 255, 255, 0.9) !important;
        transform: scale(1.01) !important;
    }
    
    .content-item-horizontal:hover .mobile-meta-row {
        background: rgba(240, 244, 255, 0.9) !important;
    }
    
    .mobile-action-btn:active {
        transform: scale(0.95) !important;
        transition: transform 0.1s ease !important;
    }
    
    /* تأثير متدرج للبطاقة - للهاتف فقط */
    .content-item-horizontal .card {
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.1) !important;
        transition: all 0.3s ease !important;
        border: 1px solid rgba(0, 123, 255, 0.08) !important;
    }
    
    .content-item-horizontal:hover .card {
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.18) !important;
        border: 1px solid rgba(0, 123, 255, 0.15) !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تفعيل Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            delay: { show: 300, hide: 100 },
            placement: 'top'
        });
    });
    
    // تأثيرات التحويم على بطاقات الأساتذة
    document.querySelectorAll('.teacher-card').forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // تأثيرات التحويم على بطاقات المحتوى
    document.querySelectorAll('.content-item-horizontal').forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // تحسين عرض البطاقات للهاتف المحمول
    function optimizeMobileCards() {
        if (window.innerWidth <= 768) {
            // إضافة class خاص للهاتف المحمول
            document.body.classList.add('mobile-view');
            
            // تحسين ترتيب العناصر
            document.querySelectorAll('.content-item-horizontal').forEach(function(card) {
                const metaSection = card.querySelector('.content-meta-mobile');
                if (metaSection) {
                    // التأكد من أن العناصر في سطر واحد
                    metaSection.style.display = 'flex';
                    metaSection.style.alignItems = 'center';
                    metaSection.style.justifyContent = 'space-between';
                    metaSection.style.flexWrap = 'nowrap';
                }
            });
        } else {
            document.body.classList.remove('mobile-view');
        }
    }
    
    // تشغيل التحسين عند تحميل الصفحة وتغيير حجم النافذة
    optimizeMobileCards();
    window.addEventListener('resize', optimizeMobileCards);
});

// دالة فلترة المحتوى
function filterContent(type) {
    // تحديث الفئة النشطة
    document.querySelectorAll('.content-type-mini').forEach(function(card) {
        card.classList.remove('active');
    });
    
    // تفعيل البطاقة المحددة
    const activeCard = document.querySelector(`[onclick*="${type}"]`);
    if (activeCard) {
        activeCard.classList.add('active');
    }
    
    // إعادة توجيه مع المعامل الجديد
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('type', type);
    window.location.search = urlParams.toString();
}
</script>

<?php include 'includes/footer.php'; ?> 
