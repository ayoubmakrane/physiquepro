<?php
session_start();
require_once 'includes/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("خطأ في الاتصال بقاعدة البيانات");
}

$page_title = 'جميع الأساتذة';
include 'includes/header.php';

$teachers_query = "SELECT u.id, u.username, u.full_name, u.created_at,
                         COUNT(DISTINCT c.id) as content_count,
                         COUNT(DISTINCT CASE WHEN ct.code = 'lessons' THEN c.id END) as lessons_count,
                         COUNT(DISTINCT CASE WHEN ct.code = 'exercises' THEN c.id END) as exercises_count,
                         COUNT(DISTINCT CASE WHEN ct.code = 'experiments' THEN c.id END) as experiments_count,
                         COUNT(DISTINCT CASE WHEN ct.code = 'exams' THEN c.id END) as exams_count,
                         COUNT(DISTINCT CASE WHEN ct.code = 'assignments' THEN c.id END) as assignments_count,
                         COUNT(DISTINCT CASE WHEN ct.code = 'interactive_lessons' THEN c.id END) as interactive_count,
                         COALESCE(SUM(c.views_count), 0) as total_views
                  FROM users u
                  LEFT JOIN content c ON u.id = c.teacher_id AND c.status = 'published'
                  LEFT JOIN content_types ct ON c.type_id = ct.id
                  WHERE u.user_type = 'teacher' AND u.status = 'active'
                  GROUP BY u.id, u.username, u.full_name, u.created_at
                  HAVING content_count > 0
                  ORDER BY content_count DESC, total_views DESC";

$stmt = $pdo->prepare($teachers_query);
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_teachers = count($teachers);
$total_content = array_sum(array_column($teachers, 'content_count'));
$total_views = array_sum(array_column($teachers, 'total_views'));
?>

<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 0;
    margin-bottom: 3rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
}

.page-header h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
}

.page-header p {
    font-size: 1.3rem;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}

.stats-overview {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 3rem;
    border: 1px solid rgba(0,123,255,0.1);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.stat-card {
    text-align: center;
    padding: 2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    border-left: 5px solid #007bff;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 60px;
    height: 60px;
    background: rgba(0,123,255,0.1);
    border-radius: 50%;
    transform: translate(20px, -20px);
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,123,255,0.2);
}

.stat-number {
    font-size: 2.8rem;
    font-weight: 800;
    color: #007bff;
    display: block;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #495057;
    font-size: 1.1rem;
    font-weight: 600;
}

.teachers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 2.5rem;
    margin-bottom: 3rem;
}

.teacher-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-left: 6px solid #007bff;
    display: block;
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.teacher-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #007bff, #0056b3, #007bff);
}

.teacher-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,123,255,0.2);
    text-decoration: none;
    color: inherit;
}

.teacher-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1.5rem;
    box-shadow: 0 10px 25px rgba(0,123,255,0.3);
    position: relative;
}

.teacher-avatar::after {
    content: '';
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    border: 2px solid rgba(0,123,255,0.2);
    border-radius: 50%;
}

.teacher-name {
    font-size: 1.4rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.teacher-title {
    color: #007bff;
    font-weight: 600;
    font-size: 1rem;
    text-align: center;
    margin-bottom: 1.5rem;
}

.teacher-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    color: #6c757d;
    padding: 0.8rem;
    background: rgba(0,123,255,0.05);
    border-radius: 10px;
}

.teacher-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(0,123,255,0.05) 0%, rgba(0,123,255,0.02) 100%);
    border-radius: 15px;
    border: 1px solid rgba(0,123,255,0.1);
}

.teacher-stat {
    text-align: center;
    padding: 0.8rem;
    background: white;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.teacher-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.1);
}

.teacher-stat-number {
    display: block;
    font-size: 1.4rem;
    font-weight: 800;
    color: #007bff;
    line-height: 1;
    margin-bottom: 0.3rem;
}

.teacher-stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 600;
}

.teacher-cta {
    text-align: center;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 1.2rem 1.5rem;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.teacher-cta::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.teacher-card:hover .teacher-cta::before {
    left: 100%;
}

.navigation-buttons {
    text-align: center;
    margin-bottom: 3rem;
}

.nav-btn {
    margin: 0 0.8rem;
    padding: 1rem 2rem;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 700;
    display: inline-block;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.nav-btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
}

.nav-btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(108,117,125,0.3);
}

.nav-btn:hover {
    transform: translateY(-3px);
    text-decoration: none;
    color: white;
}

.nav-btn-primary:hover {
    box-shadow: 0 8px 25px rgba(0,123,255,0.4);
}

.nav-btn-secondary:hover {
    box-shadow: 0 8px 25px rgba(108,117,125,0.4);
}

.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 5rem;
    margin-bottom: 2rem;
    opacity: 0.3;
    color: #007bff;
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2.2rem;
    }
    
    .teachers-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .teacher-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .teacher-meta {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .nav-btn {
        display: block;
        margin: 0.5rem 0;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.teacher-card {
    animation: fadeInUp 0.6s ease forwards;
}
</style>

<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-users me-3"></i>جميع الأساتذة</h1>
        <p>تصفح جميع أساتذة الفيزياء والكيمياء المتاحين على المنصة</p>
    </div>
</div>

<div class="container">
    <!-- إحصائيات عامة -->
    <?php if ($total_teachers > 0): ?>
    <div class="stats-overview">
        <h3 class="text-center mb-4" style="color: #2c3e50; font-weight: 700;">
            <i class="fas fa-chart-bar me-2 text-primary"></i>إحصائيات المنصة
        </h3>
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?php echo $total_teachers; ?></span>
                <div class="stat-label">أستاذ نشط</div>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $total_content; ?></span>
                <div class="stat-label">محتوى تعليمي</div>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo number_format($total_views); ?></span>
                <div class="stat-label">إجمالي المشاهدات</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- أزرار التنقل -->
    <div class="navigation-buttons">
        <a href="index.php" class="nav-btn nav-btn-secondary">
            <i class="fas fa-home me-2"></i>الصفحة الرئيسية
        </a>
        <a href="level.php" class="nav-btn nav-btn-primary">
            <i class="fas fa-layer-group me-2"></i>تصفح حسب المستويات
        </a>
    </div>

    <!-- شبكة الأساتذة -->
    <?php if (!empty($teachers)): ?>
        <div class="teachers-grid">
            <?php foreach ($teachers as $index => $teacher): ?>
                <a href="teachers_lessons.php?teacher_id=<?php echo $teacher['id']; ?>&level=all&type=lessons" 
                   class="teacher-card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <div class="teacher-avatar">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    
                    <div class="teacher-name">
                        <?php 
                        $display_name = !empty($teacher['full_name']) ? $teacher['full_name'] : $teacher['username'];
                        echo htmlspecialchars($display_name);
                        ?>
                    </div>
                    
                    <div class="teacher-title">
                        <i class="fas fa-flask me-1"></i>
                        أستاذ الفيزياء والكيمياء
                    </div>
                    
                    <div class="teacher-meta">
                        <span>
                            <i class="fas fa-calendar-alt me-1"></i>
                            انضم <?php echo date('Y', strtotime($teacher['created_at'])); ?>
                        </span>
                        <span>
                            <i class="fas fa-eye me-1"></i>
                            <?php echo number_format($teacher['total_views']); ?> مشاهدة
                        </span>
                    </div>
                    
                    <div class="teacher-stats">
                        <div class="teacher-stat">
                            <span class="teacher-stat-number"><?php echo $teacher['lessons_count']; ?></span>
                            <small class="teacher-stat-label">دروس</small>
                        </div>
                        <div class="teacher-stat">
                            <span class="teacher-stat-number"><?php echo $teacher['experiments_count']; ?></span>
                            <small class="teacher-stat-label">تجارب</small>
                        </div>
                        <div class="teacher-stat">
                            <span class="teacher-stat-number"><?php echo $teacher['exercises_count']; ?></span>
                            <small class="teacher-stat-label">تمارين</small>
                        </div>
                        <div class="teacher-stat">
                            <span class="teacher-stat-number"><?php echo $teacher['exams_count']; ?></span>
                            <small class="teacher-stat-label">امتحانات</small>
                        </div>
                        <div class="teacher-stat">
                            <span class="teacher-stat-number"><?php echo $teacher['assignments_count']; ?></span>
                            <small class="teacher-stat-label">فروض</small>
                        </div>
                        <div class="teacher-stat">
                            <span class="teacher-stat-number"><?php echo $teacher['interactive_count']; ?></span>
                            <small class="teacher-stat-label">تفاعلية</small>
                        </div>
                    </div>

                    <div class="teacher-cta">
                        <i class="fas fa-arrow-left me-2"></i>
                        عرض المحتوى (<?php echo $teacher['content_count']; ?> محتوى)
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>لا يوجد أساتذة متاحون حالياً</h3>
            <p>لم يتم العثور على أي أستاذ لديه محتوى منشور</p>
            <a href="index.php" class="nav-btn nav-btn-primary mt-3">
                <i class="fas fa-home me-2"></i>العودة للرئيسية
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تأثير تدريجي لظهور البطاقات
    const teacherCards = document.querySelectorAll('.teacher-card');
    
    // إضافة تأثير hover متقدم
    teacherCards.forEach((card, index) => {
        // تأثيرات التفاعل
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-12px) scale(1.03)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // تأثير على بطاقات الإحصائيات
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.05)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // تأثير على أزرار التنقل
    const navButtons = document.querySelectorAll('.nav-btn');
    navButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px) scale(1.05)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?> 
