<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

$page_title = 'سياسة الخصوصية';

include 'includes/header.php';
?>

<style>
.privacy-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 80vh;
    padding: 2rem 0;
}

.privacy-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.privacy-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    text-align: center;
}

.privacy-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.privacy-section h3 {
    color: #667eea;
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.privacy-section h4 {
    color: #495057;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.privacy-list {
    margin-right: 1rem;
}

.privacy-list li {
    margin-bottom: 0.5rem;
    color: #6c757d;
}

.highlight-box {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    border-left: 4px solid #667eea;
    margin: 1rem 0;
}

.contact-privacy {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
}
</style>

<div class="privacy-container">
    <div class="container">
        <!-- Header -->
        <div class="privacy-card">
            <div class="privacy-header">
                <h1><i class="fas fa-shield-alt me-3"></i>سياسة الخصوصية</h1>
                <p class="mb-0">نحن نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية</p>
                <small class="text-white-50">آخر تحديث: يناير 2025</small>
            </div>
        </div>

        <!-- مقدمة -->
        <div class="privacy-section">
            <h3><i class="fas fa-info-circle me-2"></i>مقدمة</h3>
            <p>
                مرحباً بك في منصة الفيزياء والكيمياء التعليمية. نحن نقدر ثقتك بنا ونلتزم بحماية 
                خصوصيتك وأمان بياناتك الشخصية. توضح هذه السياسة كيفية جمعنا واستخدامنا وحمايتنا 
                للمعلومات التي تقدمها لنا عند استخدام موقعنا الإلكتروني وخدماتنا.
            </p>
            
            <div class="highlight-box">
                <strong><i class="fas fa-exclamation-triangle text-warning me-2"></i>ملاحظة مهمة:</strong>
                باستخدام موقعنا، فإنك توافق على جمع واستخدام المعلومات وفقاً لهذه السياسة.
            </div>
        </div>

        <!-- المعلومات التي نجمعها -->
        <div class="privacy-section">
            <h3><i class="fas fa-database me-2"></i>المعلومات التي نجمعها</h3>
            
            <h4>1. المعلومات الشخصية</h4>
            <ul class="privacy-list">
                <li>الاسم الكامل</li>
                <li>عنوان البريد الإلكتروني</li>
                <li>رقم الهاتف (اختياري)</li>
                <li>المستوى التعليمي</li>
                <li>معلومات الحساب (اسم المستخدم وكلمة المرور المشفرة)</li>
            </ul>

            <h4>2. المعلومات التقنية</h4>
            <ul class="privacy-list">
                <li>عنوان IP الخاص بك</li>
                <li>نوع المتصفح والجهاز</li>
                <li>أوقات الزيارة والصفحات المُشاهدة</li>
                <li>ملفات تعريف الارتباط (Cookies)</li>
            </ul>

            <h4>3. المحتوى التعليمي</h4>
            <ul class="privacy-list">
                <li>الدروس والتجارب التي تقوم بإنشائها (للأساتذة)</li>
                <li>التفاعلات والتعليقات</li>
                <li>سجل التقدم التعليمي</li>
            </ul>
        </div>

        <!-- كيفية استخدام المعلومات -->
        <div class="privacy-section">
            <h3><i class="fas fa-cogs me-2"></i>كيفية استخدام المعلومات</h3>
            
            <p>نستخدم المعلومات التي نجمعها للأغراض التالية:</p>
            
            <ul class="privacy-list">
                <li><strong>تقديم الخدمات:</strong> إنشاء وإدارة حسابك وتخصيص تجربة التعلم</li>
                <li><strong>التواصل:</strong> إرسال الإشعارات والتحديثات المهمة</li>
                <li><strong>التحسين:</strong> تطوير وتحسين المنصة وخدماتها</li>
                <li><strong>الأمان:</strong> مراقبة ومنع الأنشطة المشبوهة</li>
                <li><strong>الدعم:</strong> تقديم المساعدة التقنية والإجابة على استفساراتك</li>
                <li><strong>الإحصائيات:</strong> تحليل استخدام الموقع بشكل مجهول</li>
            </ul>
        </div>

        <!-- مشاركة المعلومات -->
        <div class="privacy-section">
            <h3><i class="fas fa-share-alt me-2"></i>مشاركة المعلومات</h3>
            
            <div class="highlight-box">
                <strong><i class="fas fa-lock text-success me-2"></i>التزامنا:</strong>
                لن نبيع أو نؤجر أو نتاجر بمعلوماتك الشخصية مع أطراف ثالثة لأغراض تجارية.
            </div>
            
            <p>قد نشارك معلوماتك في الحالات المحدودة التالية:</p>
            
            <ul class="privacy-list">
                <li><strong>الموافقة:</strong> عندما تعطي موافقة صريحة</li>
                <li><strong>القانون:</strong> عند الحاجة للامتثال للقوانين والتشريعات</li>
                <li><strong>الأمان:</strong> لحماية حقوقنا وحقوق المستخدمين الآخرين</li>
                <li><strong>مقدمي الخدمات:</strong> مع شركاء موثوقين يساعدوننا في تشغيل المنصة</li>
            </ul>
        </div>

        <!-- أمان البيانات -->
        <div class="privacy-section">
            <h3><i class="fas fa-shield-virus me-2"></i>أمان البيانات</h3>
            
            <p>نطبق إجراءات أمنية صارمة لحماية بياناتك:</p>
            
            <ul class="privacy-list">
                <li><strong>التشفير:</strong> جميع كلمات المرور مشفرة</li>
                <li><strong>HTTPS:</strong> اتصال آمن ومشفر</li>
                <li><strong>التحديثات:</strong> تحديث أنظمة الأمان بانتظام</li>
                <li><strong>الوصول المحدود:</strong> فقط الموظفون المخولون يمكنهم الوصول للبيانات</li>
                <li><strong>النسخ الاحتياطية:</strong> نسخ احتياطية آمنة لحماية بياناتك</li>
            </ul>
        </div>

        <!-- حقوقك -->
        <div class="privacy-section">
            <h3><i class="fas fa-user-shield me-2"></i>حقوقك</h3>
            
            <p>لديك الحقوق التالية بخصوص بياناتك الشخصية:</p>
            
            <ul class="privacy-list">
                <li><strong>الوصول:</strong> طلب نسخة من بياناتك الشخصية</li>
                <li><strong>التصحيح:</strong> تصحيح أي معلومات غير دقيقة</li>
                <li><strong>الحذف:</strong> طلب حذف بياناتك في ظروف معينة</li>
                <li><strong>التحديد:</strong> تقييد معالجة بياناتك</li>
                <li><strong>النقل:</strong> الحصول على بياناتك بتنسيق قابل للنقل</li>
                <li><strong>الاعتراض:</strong> الاعتراض على معالجة بياناتك</li>
            </ul>
        </div>

        <!-- ملفات تعريف الارتباط -->
        <div class="privacy-section">
            <h3><i class="fas fa-cookie-bite me-2"></i>ملفات تعريف الارتباط (Cookies)</h3>
            
            <p>نستخدم ملفات تعريف الارتباط لتحسين تجربتك:</p>
            
            <ul class="privacy-list">
                <li><strong>الضرورية:</strong> مطلوبة لعمل الموقع بشكل صحيح</li>
                <li><strong>الوظيفية:</strong> لحفظ تفضيلاتك وإعداداتك</li>
                <li><strong>التحليلية:</strong> لفهم كيفية استخدام الموقع</li>
                <li><strong>الأداء:</strong> لتحسين سرعة وأداء الموقع</li>
            </ul>
            
            <div class="highlight-box">
                <strong><i class="fas fa-info-circle text-info me-2"></i>ملاحظة:</strong>
                يمكنك التحكم في ملفات تعريف الارتباط من خلال إعدادات متصفحك.
            </div>
        </div>

        <!-- الأطفال -->
        <div class="privacy-section">
            <h3><i class="fas fa-child me-2"></i>خصوصية الأطفال</h3>
            
            <p>
                منصتنا مخصصة للطلاب من جميع الأعمار. بالنسبة للمستخدمين تحت سن 16 عاماً، 
                نطلب موافقة ولي الأمر قبل جمع أي معلومات شخصية. نلتزم بحماية خصوصية 
                الأطفال ولا نجمع معلومات غير ضرورية منهم.
            </p>
        </div>

        <!-- تحديث السياسة -->
        <div class="privacy-section">
            <h3><i class="fas fa-sync-alt me-2"></i>تحديث السياسة</h3>
            
            <p>
                قد نقوم بتحديث سياسة الخصوصية من وقت لآخر. سنقوم بإشعارك بأي تغييرات 
                جوهرية عبر البريد الإلكتروني أو من خلال إشعار على موقعنا. ننصحك بمراجعة 
                هذه الصفحة بانتظام للاطلاع على أي تحديثات.
            </p>
        </div>

        <!-- التواصل -->
        <div class="contact-privacy">
            <h4><i class="fas fa-envelope me-2"></i>تواصل معنا</h4>
            <p class="mb-3">
                إذا كان لديك أي أسئلة حول سياسة الخصوصية أو تريد ممارسة حقوقك، 
                يرجى التواصل معنا:
            </p>
            <div class="row text-center">
                <div class="col-md-6">
                    <p><i class="fas fa-envelope me-2"></i>contact@physique-chimie.ma</p>
                </div>
                <div class="col-md-6">
                    <a href="contact.php" class="btn btn-light">
                        <i class="fas fa-paper-plane me-2"></i>نموذج الاتصال
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
