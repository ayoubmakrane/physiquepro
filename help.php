<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

$page_title = 'مساعدة';

include 'includes/header.php';
?>

<!-- CSS للتحسينات المحمولة -->
<link rel="stylesheet" href="assets/css/mobile-optimizations.css">

<!-- JavaScript للتحسينات المحمولة -->
<script src="assets/js/mobile-enhancements.js"></script>

<style>
.help-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 80vh;
    padding: 2rem 0;
}

.help-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.help-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    text-align: center;
}

.help-categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.category-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.category-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.faq-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.accordion-button {
    background: #f8f9fa;
    border: none;
    border-radius: 10px !important;
    font-weight: 600;
    color: #495057;
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.accordion-body {
    background: #fafafa;
    border-radius: 0 0 10px 10px;
}

.search-help {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.contact-help {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    margin-top: 2rem;
}

/* تحسينات الهاتف المحمول للمساعدة */
@media (max-width: 768px) {
    .help-container {
        padding: 1rem 0 !important;
    }
    
    .container {
        padding: 0 10px !important;
    }
    
    .help-header {
        padding: 1.5rem !important;
        margin-bottom: 1rem !important;
    }
    
    .help-header h1 {
        font-size: 1.5rem !important;
    }
    
    .help-header p {
        font-size: 0.9rem !important;
    }
    
    .search-help {
        padding: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .search-help h4 {
        font-size: 1.1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .input-group .form-control {
        font-size: 0.9rem !important;
        padding: 0.6rem !important;
    }
    
    .input-group .btn {
        padding: 0.6rem 1rem !important;
        font-size: 0.9rem !important;
    }
    
    .help-categories {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .category-card {
        padding: 1.25rem !important;
    }
    
    .category-icon {
        width: 50px !important;
        height: 50px !important;
        font-size: 1.2rem !important;
        margin-bottom: 0.75rem !important;
    }
    
    .category-card h5 {
        font-size: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .category-card ul li {
        margin-bottom: 0.5rem !important;
    }
    
    .category-card ul li a {
        font-size: 0.9rem !important;
        padding: 0.25rem 0 !important;
        display: block !important;
    }
    
    .category-card ul li i {
        font-size: 0.8rem !important;
    }
    
    .faq-section {
        padding: 1.5rem !important;
    }
    
    .faq-section h3 {
        font-size: 1.2rem !important;
        margin-bottom: 1.5rem !important;
    }
    
    .accordion-item {
        margin-bottom: 0.75rem !important;
    }
    
    .accordion-button {
        padding: 0.75rem !important;
        font-size: 0.9rem !important;
    }
    
    .accordion-body {
        padding: 1rem !important;
        font-size: 0.9rem !important;
        line-height: 1.5 !important;
    }
    
    .contact-help {
        padding: 1.5rem !important;
        margin-top: 1rem !important;
    }
    
    .contact-help h4 {
        font-size: 1.1rem !important;
    }
    
    .contact-help p {
        font-size: 0.9rem !important;
    }
    
    .contact-help .btn {
        padding: 0.6rem 1.2rem !important;
        font-size: 0.9rem !important;
    }
}

@media (max-width: 576px) {
    .help-container {
        padding: 0.5rem 0 !important;
    }
    
    .container {
        padding: 0 5px !important;
    }
    
    .help-header {
        padding: 1rem !important;
        border-radius: 10px !important;
    }
    
    .help-header h1 {
        font-size: 1.3rem !important;
    }
    
    .help-header h1 i {
        font-size: 1.2rem !important;
    }
    
    .help-header p {
        font-size: 0.85rem !important;
    }
    
    .search-help {
        padding: 0.75rem !important;
        border-radius: 10px !important;
    }
    
    .search-help h4 {
        font-size: 1rem !important;
    }
    
    .search-help h4 i {
        font-size: 0.9rem !important;
    }
    
    .input-group .form-control {
        font-size: 0.85rem !important;
        padding: 0.5rem !important;
    }
    
    .input-group .btn {
        padding: 0.5rem 0.8rem !important;
        font-size: 0.85rem !important;
    }
    
    .category-card {
        padding: 1rem !important;
        border-radius: 10px !important;
    }
    
    .category-icon {
        width: 45px !important;
        height: 45px !important;
        font-size: 1.1rem !important;
    }
    
    .category-card h5 {
        font-size: 0.95rem !important;
        margin-bottom: 0.75rem !important;
    }
    
    .category-card ul li a {
        font-size: 0.85rem !important;
    }
    
    .faq-section {
        padding: 1rem !important;
        border-radius: 10px !important;
    }
    
    .faq-section h3 {
        font-size: 1.1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .faq-section h3 i {
        font-size: 1rem !important;
    }
    
    .accordion-button {
        padding: 0.6rem !important;
        font-size: 0.85rem !important;
        border-radius: 8px !important;
    }
    
    .accordion-body {
        padding: 0.75rem !important;
        font-size: 0.85rem !important;
    }
    
    .contact-help {
        padding: 1rem !important;
        border-radius: 10px !important;
    }
    
    .contact-help h4 {
        font-size: 1rem !important;
    }
    
    .contact-help p {
        font-size: 0.85rem !important;
    }
    
    .contact-help .btn {
        padding: 0.5rem 1rem !important;
        font-size: 0.85rem !important;
    }
}
</style>

<div class="help-container">
    <div class="container">
        <!-- Header -->
        <div class="help-card">
            <div class="help-header">
                <h1><i class="fas fa-question-circle me-3"></i>مركز المساعدة</h1>
                <p class="mb-0">نحن هنا لمساعدتك في استخدام المنصة والاستفادة من جميع ميزاتها</p>
            </div>
        </div>

        <!-- البحث -->
        <div class="search-help">
            <h4 class="mb-3"><i class="fas fa-search me-2"></i>ابحث عن إجابة</h4>
            <div class="input-group">
                <input type="text" class="form-control form-control-lg" placeholder="اكتب سؤالك هنا..." id="helpSearch">
                <button class="btn btn-primary btn-lg" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- فئات المساعدة -->
        <div class="help-categories">
            <!-- البدء -->
            <div class="category-card">
                <div class="category-icon bg-success">
                    <i class="fas fa-play"></i>
                </div>
                <h5 class="text-center mb-3">البدء مع المنصة</h5>
                <ul class="list-unstyled">
                    <li><a href="#getting-started" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>كيفية إنشاء حساب</a></li>
                    <li><a href="#getting-started" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>تسجيل الدخول</a></li>
                    <li><a href="#getting-started" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>استكشاف المحتوى</a></li>
                </ul>
            </div>

            <!-- للطلاب -->
            <div class="category-card">
                <div class="category-icon bg-primary">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h5 class="text-center mb-3">للطلاب</h5>
                <ul class="list-unstyled">
                    <li><a href="#students" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>الوصول للدروس</a></li>
                    <li><a href="#students" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>حل التمارين</a></li>
                    <li><a href="#students" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>تتبع التقدم</a></li>
                </ul>
            </div>

            <!-- للأساتذة -->
            <div class="category-card">
                <div class="category-icon bg-warning">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h5 class="text-center mb-3">للأساتذة</h5>
                <ul class="list-unstyled">
                    <li><a href="#teachers" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>إنشاء المحتوى</a></li>
                    <li><a href="#teachers" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>إدارة الطلاب</a></li>
                    <li><a href="#teachers" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>تقييم الأداء</a></li>
                </ul>
            </div>

            <!-- المشاكل التقنية -->
            <div class="category-card">
                <div class="category-icon bg-danger">
                    <i class="fas fa-tools"></i>
                </div>
                <h5 class="text-center mb-3">المشاكل التقنية</h5>
                <ul class="list-unstyled">
                    <li><a href="#technical" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>مشاكل تسجيل الدخول</a></li>
                    <li><a href="#technical" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>بطء التحميل</a></li>
                    <li><a href="#technical" class="text-decoration-none"><i class="fas fa-chevron-left me-2"></i>أخطاء النظام</a></li>
                </ul>
            </div>
        </div>

        <!-- الأسئلة الشائعة -->
        <div class="faq-section">
            <h3 class="mb-4"><i class="fas fa-question-circle me-2"></i>الأسئلة الشائعة</h3>
            
            <div class="accordion" id="faqAccordion">
                <!-- السؤال 1 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            كيف يمكنني إنشاء حساب جديد؟
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>لإنشاء حساب جديد:</p>
                            <ol>
                                <li>انقر على زر "تسجيل" في أعلى الصفحة</li>
                                <li>املأ النموذج بالمعلومات المطلوبة</li>
                                <li>اختر نوع حسابك (طالب أو أستاذ)</li>
                                <li>انقر على "إنشاء حساب"</li>
                                <li>تحقق من بريدك الإلكتروني لتفعيل الحساب</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- السؤال 2 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            نسيت كلمة المرور، ماذا أفعل؟
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>لاستعادة كلمة المرور:</p>
                            <ol>
                                <li>اذهب إلى صفحة تسجيل الدخول</li>
                                <li>انقر على "نسيت كلمة المرور؟"</li>
                                <li>أدخل بريدك الإلكتروني</li>
                                <li>تحقق من بريدك الإلكتروني للحصول على رابط إعادة التعيين</li>
                                <li>اتبع التعليمات في البريد الإلكتروني</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- السؤال 3 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            كيف يمكنني الوصول للدروس؟
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>للوصول للدروس:</p>
                            <ul>
                                <li>سجل دخولك إلى حسابك</li>
                                <li>اختر المستوى التعليمي المناسب من القائمة الرئيسية</li>
                                <li>حدد المادة (فيزياء أو كيمياء)</li>
                                <li>تصفح الدروس المتاحة واختر ما يناسبك</li>
                                <li>يمكنك أيضاً استخدام البحث للعثور على درس معين</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- السؤال 4 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            كيف يمكن للأساتذة إضافة محتوى جديد؟
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>للأساتذة لإضافة محتوى:</p>
                            <ol>
                                <li>سجل دخولك كأستاذ</li>
                                <li>اذهب إلى لوحة التحكم</li>
                                <li>انقر على "إضافة محتوى جديد"</li>
                                <li>املأ تفاصيل الدرس أو التجربة</li>
                                <li>أضف الملفات والصور المرفقة</li>
                                <li>احفظ ونشر المحتوى</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- السؤال 5 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            هل المنصة مجانية؟
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>نعم، منصة الفيزياء والكيمياء التعليمية مجانية تماماً لجميع المستخدمين. نهدف إلى توفير تعليم عالي الجودة للجميع في المغرب.</p>
                        </div>
                    </div>
                </div>

                <!-- السؤال 6 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                            كيف يمكنني الإبلاغ عن مشكلة؟
                        </button>
                    </h2>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>للإبلاغ عن مشكلة:</p>
                            <ul>
                                <li>استخدم صفحة <a href="contact.php">اتصل بنا</a></li>
                                <li>اختر "مشكلة تقنية" كموضوع</li>
                                <li>وصف المشكلة بالتفصيل</li>
                                <li>أرفق لقطة شاشة إن أمكن</li>
                                <li>سنرد عليك في أقرب وقت ممكن</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- السؤال 7 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                            هل يمكنني استخدام المنصة على الهاتف؟
                        </button>
                    </h2>
                    <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>نعم، المنصة متوافقة مع جميع الأجهزة:</p>
                            <ul>
                                <li>الهواتف الذكية (Android & iOS)</li>
                                <li>الأجهزة اللوحية</li>
                                <li>أجهزة الكمبيوتر</li>
                                <li>أجهزة الكمبيوتر المحمولة</li>
                            </ul>
                            <p>التصميم متجاوب ويتكيف مع حجم شاشتك.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- دليل المستخدم -->
        <div class="faq-section mt-4">
            <h3 class="mb-4"><i class="fas fa-book me-2"></i>دليل المستخدم</h3>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="category-icon bg-info mx-auto mb-3">
                            <i class="fas fa-video"></i>
                        </div>
                        <h5>فيديوهات تعليمية</h5>
                        <p class="text-muted">شاهد فيديوهات توضح كيفية استخدام المنصة</p>
                        <a href="#" class="btn btn-outline-primary">مشاهدة</a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="category-icon bg-secondary mx-auto mb-3">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h5>دليل PDF</h5>
                        <p class="text-muted">حمل دليل المستخدم الكامل بصيغة PDF</p>
                        <a href="#" class="btn btn-outline-secondary">تحميل</a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="category-icon bg-success mx-auto mb-3">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h5>مجتمع المساعدة</h5>
                        <p class="text-muted">انضم لمجتمع المستخدمين للحصول على المساعدة</p>
                        <a href="#" class="btn btn-outline-success">انضمام</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- التواصل -->
        <div class="contact-help">
            <h4><i class="fas fa-headset me-2"></i>هل تحتاج لمساعدة إضافية؟</h4>
            <p class="mb-3">
                إذا لم تجد الإجابة التي تبحث عنها، فريق الدعم جاهز لمساعدتك
            </p>
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <i class="fas fa-envelope fa-2x mb-2"></i>
                    <p>البريد الإلكتروني</p>
                    <small>contact@physique-chimie.ma</small>
                </div>
                <div class="col-md-4 mb-3">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <p>ساعات العمل</p>
                    <small>الأحد - الخميس: 9:00 - 17:00</small>
                </div>
                <div class="col-md-4 mb-3">
                    <i class="fas fa-reply fa-2x mb-2"></i>
                    <p>وقت الاستجابة</p>
                    <small>خلال 24 ساعة</small>
                </div>
            </div>
            <a href="contact.php" class="btn btn-light btn-lg mt-3">
                <i class="fas fa-paper-plane me-2"></i>تواصل معنا
            </a>
        </div>
    </div>
</div>

<script>
// البحث في الأسئلة الشائعة
document.getElementById('helpSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const accordionItems = document.querySelectorAll('.accordion-item');
    
    accordionItems.forEach(item => {
        const button = item.querySelector('.accordion-button');
        const body = item.querySelector('.accordion-body');
        const text = (button.textContent + ' ' + body.textContent).toLowerCase();
        
        if (text.includes(searchTerm) || searchTerm === '') {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?> 
