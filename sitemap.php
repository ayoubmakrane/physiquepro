<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

$page_title = 'خريطة الموقع';



include 'includes/header.php';
?>

<style>
.sitemap-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 80vh;
    padding: 2rem 0;
}

.sitemap-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    padding: 3rem 2rem;
    text-align: center;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.sitemap-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.sitemap-section h3 {
    color: #667eea;
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.sitemap-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.sitemap-link {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 10px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.sitemap-link:hover {
    background: #e9ecef;
    color: #667eea;
    transform: translateX(-5px);
    border-color: #667eea;
}

.sitemap-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 1rem;
    color: white;
}



.breadcrumb-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.quick-access {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    margin-top: 2rem;
}

.quick-access-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.quick-access-item {
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 1rem;
    text-decoration: none;
    color: white;
    transition: all 0.3s ease;
}

.quick-access-item:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-2px);
}
</style>

<div class="sitemap-container">
    <div class="container">
        <!-- Header -->
        <div class="sitemap-header">
            <h1><i class="fas fa-sitemap me-3"></i>خريطة الموقع</h1>
            <p class="mb-0">دليل شامل لجميع صفحات ومحتويات منصة الفيزياء والكيمياء التعليمية</p>
        </div>



        <!-- الصفحات الرئيسية -->
        <div class="sitemap-section">
            <h3><i class="fas fa-home me-2"></i>الصفحات الرئيسية</h3>
            <div class="sitemap-links">
                <a href="<?= SITE_URL ?>/index.php" class="sitemap-link">
                    <div class="sitemap-icon bg-primary">
                        <i class="fas fa-home"></i>
                    </div>
                    <div>
                        <strong>الصفحة الرئيسية</strong>
                        <small class="d-block text-muted">نظرة عامة على المنصة</small>
                    </div>
                </a>
                
                <a href="<?= SITE_URL ?>/teachers_lessons.php" class="sitemap-link">
                    <div class="sitemap-icon bg-info">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <strong>دروس الأساتذة</strong>
                        <small class="d-block text-muted">جميع الدروس والتجارب</small>
                    </div>
                </a>
                
                <a href="<?= SITE_URL ?>/all_teachers.php" class="sitemap-link">
                    <div class="sitemap-icon bg-warning">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <strong>محتوى الأساتذة</strong>
                        <small class="d-block text-muted">دروس الأساتذة المسجلين</small>
                    </div>
                </a>
                
                <a href="<?= SITE_URL ?>/login.php" class="sitemap-link">
                    <div class="sitemap-icon bg-success">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div>
                        <strong>تسجيل الدخول</strong>
                        <small class="d-block text-muted">دخول للحساب الشخصي</small>
                    </div>
                </a>
                
                <a href="<?= SITE_URL ?>/register.php" class="sitemap-link">
                    <div class="sitemap-icon bg-secondary">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <strong>تسجيل جديد</strong>
                        <small class="d-block text-muted">إنشاء حساب جديد</small>
                    </div>
                </a>
            </div>
        </div>

        <!-- المستويات التعليمية -->
        <div class="sitemap-section">
            <h3><i class="fas fa-graduation-cap me-2"></i>المستويات التعليمية</h3>
            <div class="sitemap-links">
                <a href="level.php?level=tc" class="sitemap-link">
                    <div class="sitemap-icon bg-primary">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <strong>الجذع المشترك العلمي</strong>
                        <small class="d-block text-muted">محتوى السنة الأولى ثانوي</small>
                    </div>
                </a>
                
                <a href="level.php?level=1bac" class="sitemap-link">
                    <div class="sitemap-icon bg-warning">
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <div>
                        <strong>الأولى باكالوريا</strong>
                        <small class="d-block text-muted">محتوى السنة الثانية ثانوي</small>
                    </div>
                </a>
                
                <a href="level.php?level=2bac" class="sitemap-link">
                    <div class="sitemap-icon bg-danger">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div>
                        <strong>الثانية باكالوريا</strong>
                        <small class="d-block text-muted">محتوى السنة الثالثة ثانوي</small>
                    </div>
                </a>
            </div>
        </div>

        <!-- المواد العلمية -->
        <div class="sitemap-section">
            <h4 class="sitemap-title">
                <i class="fas fa-book"></i>
                المحتوى التعليمي
            </h4>
            <div class="sitemap-links">
                <a href="teachers_lessons.php" class="sitemap-link">
                    <i class="fas fa-chalkboard-teacher"></i>
                    دروس الأساتذة
                </a>
                <a href="level.php?level=tc" class="sitemap-link">
                    <i class="fas fa-layer-group"></i>
                    الجذع المشترك
                </a>
                <a href="level.php?level=1bac" class="sitemap-link">
                    <i class="fas fa-layer-group"></i>
                    الأولى باك
                </a>
                <a href="level.php?level=2bac" class="sitemap-link">
                    <i class="fas fa-layer-group"></i>
                    الثانية باك
                </a>
            </div>
        </div>

        <!-- أنواع المحتوى -->
        <div class="sitemap-section">
            <h3><i class="fas fa-layer-group me-2"></i>أنواع المحتوى</h3>
            <div class="sitemap-links">
                <a href="teachers_lessons.php?type=lessons" class="sitemap-link">
                    <div class="sitemap-icon bg-info">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div>
                        <strong>الدروس</strong>
                        <small class="d-block text-muted">جميع الدروس المتاحة</small>
                    </div>
                </a>
                
                <a href="teachers_lessons.php?type=experiments" class="sitemap-link">
                    <div class="sitemap-icon bg-success">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <div>
                        <strong>التجارب</strong>
                        <small class="d-block text-muted">جميع التجارب المتاحة</small>
                    </div>
                </a>
                
                <a href="teachers_lessons.php?type=exams" class="sitemap-link">
                    <div class="sitemap-icon bg-warning">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <strong>الامتحانات</strong>
                        <small class="d-block text-muted">جميع الامتحانات المتاحة</small>
                    </div>
                </a>
                
                <a href="teachers_lessons.php?type=exercises" class="sitemap-link">
                    <div class="sitemap-icon bg-danger">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                    <div>
                        <strong>التمارين</strong>
                        <small class="d-block text-muted">جميع التمارين المتاحة</small>
                    </div>
                </a>
                
                <a href="teachers_lessons.php?type=interactive" class="sitemap-link">
                    <div class="sitemap-icon bg-dark">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <div>
                        <strong>الدروس التفاعلية</strong>
                        <small class="d-block text-muted">جميع الدروس التفاعلية</small>
                    </div>
                </a>
            </div>
        </div>

        <!-- المدرسون -->
        <div class="sitemap-section">
            <h3><i class="fas fa-chalkboard-teacher me-2"></i>الأساتذة</h3>
            <div class="sitemap-links">
                <a href="all_teachers.php" class="sitemap-link">
                    <div class="sitemap-icon bg-success">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <strong>جميع الأساتذة</strong>
                        <small class="d-block text-muted">تصفح ملفات المدرسين</small>
                    </div>
                </a>
                
                <a href="teachers_lessons.php" class="sitemap-link">
                    <div class="sitemap-icon bg-info">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <strong>دروس الأساتذة</strong>
                        <small class="d-block text-muted">جميع الدروس والتجارب</small>
                    </div>
                </a>
            </div>
        </div>

        <!-- الدعم والمساعدة -->
        <div class="sitemap-section">
            <h3><i class="fas fa-headset me-2"></i>الدعم والمساعدة</h3>
            <div class="sitemap-links">
                <a href="help.php" class="sitemap-link">
                    <div class="sitemap-icon bg-info">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div>
                        <strong>مركز المساعدة</strong>
                        <small class="d-block text-muted">أسئلة شائعة ودليل المستخدم</small>
                    </div>
                </a>
                
                <a href="contact.php" class="sitemap-link">
                    <div class="sitemap-icon bg-primary">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <strong>اتصل بنا</strong>
                        <small class="d-block text-muted">نموذج التواصل والاستفسارات</small>
                    </div>
                </a>
            </div>
        </div>

        <!-- الصفحات القانونية -->
        <div class="sitemap-section">
            <h3><i class="fas fa-gavel me-2"></i>الصفحات القانونية</h3>
            <div class="sitemap-links">
                <a href="privacy.php" class="sitemap-link">
                    <div class="sitemap-icon bg-success">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <strong>سياسة الخصوصية</strong>
                        <small class="d-block text-muted">كيفية حماية بياناتك</small>
                    </div>
                </a>
                
                <a href="terms.php" class="sitemap-link">
                    <div class="sitemap-icon bg-warning">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <strong>الشروط والأحكام</strong>
                        <small class="d-block text-muted">شروط استخدام المنصة</small>
                    </div>
                </a>
            </div>
        </div>

        <!-- وصول سريع -->
        <div class="quick-access">
            <h4><i class="fas fa-rocket me-2"></i>وصول سريع</h4>
            <p class="mb-3">الصفحات الأكثر زيارة في المنصة</p>
            
            <div class="quick-access-grid">
                <a href="index.php" class="quick-access-item">
                    <i class="fas fa-home fa-2x mb-2"></i>
                    <div>الرئيسية</div>
                </a>
                <a href="teachers_lessons.php" class="quick-access-item">
                    <i class="fas fa-book fa-2x mb-2"></i>
                    <div>دروس الأساتذة</div>
                </a>
                <a href="level.php?level=tc" class="quick-access-item">
                    <i class="fas fa-star fa-2x mb-2"></i>
                    <div>الجذع المشترك</div>
                </a>
                <a href="teachers_lessons.php?type=lessons" class="quick-access-item">
                    <i class="fas fa-book-open fa-2x mb-2"></i>
                    <div>الدروس</div>
                </a>
                <a href="teachers_lessons.php?type=experiments" class="quick-access-item">
                    <i class="fas fa-microscope fa-2x mb-2"></i>
                    <div>التجارب</div>
                </a>
                <a href="help.php" class="quick-access-item">
                    <i class="fas fa-question-circle fa-2x mb-2"></i>
                    <div>المساعدة</div>
                </a>
            </div>
        </div>

        <!-- التصفح السريع -->
        <div class="quick-access">
            <h6 class="mb-3">
                <i class="fas fa-bolt me-2"></i>
                تصفح سريع
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <a href="teachers_lessons.php" class="quick-access-item">
                        <i class="fas fa-chalkboard-teacher"></i>
                        دروس الأساتذة
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="level.php?level=tc" class="quick-access-item">
                        <i class="fas fa-book-open"></i>
                        الجذع المشترك
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="level.php?level=1bac" class="quick-access-item">
                        <i class="fas fa-flask"></i>
                        الأولى باك
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="level.php?level=2bac" class="quick-access-item">
                        <i class="fas fa-file-alt"></i>
                        الثانية باك
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
