<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

$page_title = 'الشروط والأحكام';

include 'includes/header.php';
?>

<style>
.terms-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 80vh;
    padding: 2rem 0;
}

.terms-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.terms-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    text-align: center;
}

.terms-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.terms-section h3 {
    color: #667eea;
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.terms-section h4 {
    color: #495057;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.terms-list {
    margin-right: 1rem;
}

.terms-list li {
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

.warning-box {
    background: #fff3cd;
    border-radius: 10px;
    padding: 1rem;
    border-left: 4px solid #ffc107;
    margin: 1rem 0;
}

.success-box {
    background: #d1e7dd;
    border-radius: 10px;
    padding: 1rem;
    border-left: 4px solid #198754;
    margin: 1rem 0;
}

.contact-terms {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
}
</style>

<div class="terms-container">
    <div class="container">
        <!-- Header -->
        <div class="terms-card">
            <div class="terms-header">
                <h1><i class="fas fa-file-contract me-3"></i>الشروط والأحكام</h1>
                <p class="mb-0">يرجى قراءة هذه الشروط بعناية قبل استخدام منصتنا</p>
                <small class="text-white-50">آخر تحديث: يناير 2025</small>
            </div>
        </div>

        <!-- الموافقة -->
        <div class="terms-section">
            <h3><i class="fas fa-handshake me-2"></i>الموافقة على الشروط</h3>
            <p>
                باستخدام منصة الفيزياء والكيمياء التعليمية، فإنك توافق على الالتزام بهذه الشروط 
                والأحكام. إذا كنت لا توافق على أي من هذه الشروط، يرجى عدم استخدام موقعنا أو خدماتنا.
            </p>
            
            <div class="warning-box">
                <strong><i class="fas fa-exclamation-triangle text-warning me-2"></i>تنبيه:</strong>
                قد نقوم بتحديث هذه الشروط من وقت لآخر. استمرار استخدامك للموقع يعني موافقتك على التحديثات.
            </div>
        </div>

        <!-- تعريف الخدمة -->
        <div class="terms-section">
            <h3><i class="fas fa-info-circle me-2"></i>تعريف الخدمة</h3>
            <p>
                منصة الفيزياء والكيمياء التعليمية هي موقع إلكتروني يقدم محتوى تعليمي في مجالي 
                الفيزياء والكيمياء للطلاب والأساتذة في المغرب. تشمل خدماتنا:
            </p>
            
            <ul class="terms-list">
                <li>دروس تفاعلية ومحتوى تعليمي</li>
                <li>تجارب علمية وأنشطة تطبيقية</li>
                <li>امتحانات وتقييمات</li>
                <li>منتديات للنقاش والتفاعل</li>
                <li>أدوات للأساتذة لإنشاء وإدارة المحتوى</li>
            </ul>
        </div>

        <!-- حسابات المستخدمين -->
        <div class="terms-section">
            <h3><i class="fas fa-user-cog me-2"></i>حسابات المستخدمين</h3>
            
            <h4>إنشاء الحساب</h4>
            <ul class="terms-list">
                <li>يجب أن تكون المعلومات المقدمة صحيحة ودقيقة</li>
                <li>أنت مسؤول عن الحفاظ على سرية كلمة المرور</li>
                <li>يجب إبلاغنا فوراً عن أي استخدام غير مصرح به لحسابك</li>
                <li>لا يُسمح بإنشاء حسابات متعددة للشخص الواحد</li>
            </ul>

            <h4>أنواع الحسابات</h4>
            <ul class="terms-list">
                <li><strong>الطلاب:</strong> الوصول للمحتوى التعليمي والتفاعل معه</li>
                <li><strong>الأساتذة:</strong> إنشاء وإدارة المحتوى التعليمي</li>
                <li><strong>الإداريون:</strong> إدارة المنصة والمستخدمين</li>
            </ul>
        </div>

        <!-- قواعد الاستخدام -->
        <div class="terms-section">
            <h3><i class="fas fa-rules me-2"></i>قواعد الاستخدام</h3>
            
            <div class="success-box">
                <strong><i class="fas fa-check-circle text-success me-2"></i>السلوك المقبول:</strong>
                نشجع على الاستخدام التعليمي والتفاعل البناء والاحترام المتبادل.
            </div>
            
            <h4>الممنوعات</h4>
            <ul class="terms-list">
                <li>نشر محتوى مسيء أو غير لائق</li>
                <li>انتهاك حقوق الطبع والنشر</li>
                <li>محاولة اختراق أو تعطيل النظام</li>
                <li>إنشاء حسابات وهمية أو استخدام هوية مزيفة</li>
                <li>إرسال رسائل عشوائية أو محتوى إعلاني غير مرغوب فيه</li>
                <li>تحميل ملفات ضارة أو فيروسات</li>
                <li>استخدام المنصة لأغراض تجارية دون إذن</li>
            </ul>
        </div>

        <!-- المحتوى والملكية الفكرية -->
        <div class="terms-section">
            <h3><i class="fas fa-copyright me-2"></i>المحتوى والملكية الفكرية</h3>
            
            <h4>محتوى المنصة</h4>
            <p>
                جميع المواد المنشورة على المنصة محمية بحقوق الطبع والنشر. يُسمح باستخدام 
                المحتوى لأغراض تعليمية شخصية فقط.
            </p>
            
            <h4>المحتوى المقدم من المستخدمين</h4>
            <ul class="terms-list">
                <li>أنت محتفظ بحقوق الملكية للمحتوى الذي تنشره</li>
                <li>تمنحنا ترخيصاً لاستخدام وعرض المحتوى على المنصة</li>
                <li>يجب أن يكون المحتوى أصلياً أو لديك الحق في نشره</li>
                <li>نحتفظ بالحق في حذف أي محتوى ينتهك هذه الشروط</li>
            </ul>
        </div>

        <!-- الخصوصية وحماية البيانات -->
        <div class="terms-section">
            <h3><i class="fas fa-shield-alt me-2"></i>الخصوصية وحماية البيانات</h3>
            <p>
                نلتزم بحماية خصوصيتك وبياناتك الشخصية وفقاً لسياسة الخصوصية الخاصة بنا. 
                يرجى مراجعة <a href="privacy.php">سياسة الخصوصية</a> للحصول على معلومات تفصيلية.
            </p>
            
            <div class="highlight-box">
                <strong><i class="fas fa-info-circle text-info me-2"></i>مهم:</strong>
                باستخدام المنصة، توافق على جمع واستخدام بياناتك وفقاً لسياسة الخصوصية.
            </div>
        </div>

        <!-- المسؤولية والضمانات -->
        <div class="terms-section">
            <h3><i class="fas fa-balance-scale me-2"></i>المسؤولية والضمانات</h3>
            
            <h4>إخلاء المسؤولية</h4>
            <ul class="terms-list">
                <li>المحتوى التعليمي مقدم "كما هو" دون ضمانات</li>
                <li>لا نضمن دقة أو اكتمال جميع المعلومات</li>
                <li>لا نتحمل مسؤولية أي أضرار ناتجة عن استخدام المنصة</li>
                <li>قد تحدث انقطاعات في الخدمة لأغراض الصيانة</li>
            </ul>

            <h4>مسؤولية المستخدم</h4>
            <ul class="terms-list">
                <li>أنت مسؤول عن استخدامك للمنصة</li>
                <li>يجب عليك التحقق من صحة المعلومات المستخدمة في دراستك</li>
                <li>أنت مسؤول عن أي محتوى تنشره أو تشاركه</li>
            </ul>
        </div>

        <!-- إنهاء الخدمة -->
        <div class="terms-section">
            <h3><i class="fas fa-times-circle me-2"></i>إنهاء الخدمة</h3>
            
            <h4>إنهاء الحساب</h4>
            <p>يمكنك إنهاء حسابك في أي وقت عبر التواصل معنا. سيتم حذف بياناتك وفقاً لسياسة الخصوصية.</p>
            
            <h4>إيقاف الحساب</h4>
            <p>نحتفظ بالحق في إيقاف أو حذف حسابك في حالة انتهاك هذه الشروط دون إشعار مسبق.</p>
        </div>

        <!-- القانون المعمول به -->
        <div class="terms-section">
            <h3><i class="fas fa-gavel me-2"></i>القانون المعمول به</h3>
            <p>
                تخضع هذه الشروط والأحكام للقانون المغربي. أي نزاعات تنشأ سيتم حلها 
                وفقاً للقانون المغربي والمحاكم المختصة في المغرب.
            </p>
        </div>

        <!-- التواصل -->
        <div class="contact-terms">
            <h4><i class="fas fa-envelope me-2"></i>تواصل معنا</h4>
            <p class="mb-3">
                إذا كان لديك أي أسئلة حول هذه الشروط والأحكام، يرجى التواصل معنا:
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
