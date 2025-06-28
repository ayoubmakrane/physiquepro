<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

$page_title = 'اتصل بنا';
$message = '';
$message_type = '';

// إنشاء جدول الرسائل إذا لم يكن موجوداً
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            user_id INT DEFAULT NULL,
            status ENUM('new', 'read', 'replied') DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            replied_at TIMESTAMP NULL DEFAULT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} catch (PDOException $e) {
    // في حالة وجود خطأ، تجاهله
}

// معالجة إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $subject = clean_input($_POST['subject'] ?? '');
    $msg = clean_input($_POST['message'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;
    
    // التحقق من البيانات
    if (empty($name) || empty($email) || empty($subject) || empty($msg)) {
        $message = 'يرجى ملء جميع الحقول المطلوبة';
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'يرجى إدخال بريد إلكتروني صحيح';
        $message_type = 'danger';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (name, email, subject, message, phone, user_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $email, $subject, $msg, $phone, $user_id]);
            
            $message = 'تم إرسال رسالتك بنجاح! سنقوم بالرد عليك في أقرب وقت ممكن.';
            $message_type = 'success';
            
            // إعادة تعيين البيانات
            $name = $email = $subject = $msg = $phone = '';
            
        } catch (PDOException $e) {
            $message = 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة لاحقاً.';
            $message_type = 'danger';
        }
    }
}

include 'includes/header.php';
?>

<!-- CSS للتحسينات المحمولة -->
<link rel="stylesheet" href="assets/css/mobile-optimizations.css">

<!-- JavaScript للتحسينات المحمولة -->
<script src="assets/js/mobile-enhancements.js"></script>

<style>
.contact-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 60vh;
    padding: 2rem 0;
}

.contact-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.contact-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    text-align: center;
}

.contact-info {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 15px;
    margin-bottom: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.5rem;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: rgba(102, 126, 234, 0.1);
    transform: translateX(-5px);
}

.info-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 1rem;
    color: white;
}

.form-floating > label {
    color: #6c757d;
}

.btn-send {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 25px;
    padding: 0.75rem 2rem;
    color: white;
    transition: all 0.3s ease;
}

.btn-send:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.social-contact {
    text-align: center;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 15px;
    margin-top: 1rem;
}

.social-contact a {
    display: inline-block;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    line-height: 50px;
    color: white;
    margin: 0 0.5rem;
    transition: all 0.3s ease;
}

.social-contact a:hover {
    transform: scale(1.1);
}

/* تحسينات الهاتف المحمول لصفحة الاتصال */
@media (max-width: 768px) {
    .contact-container {
        padding: 1rem 0 !important;
    }
    
    .container {
        padding: 0 10px !important;
    }
    
    .contact-header {
        padding: 1.5rem !important;
    }
    
    .contact-header h1 {
        font-size: 1.4rem !important;
    }
    
    .contact-header h1 i {
        font-size: 1.3rem !important;
        margin-left: 0.5rem !important;
    }
    
    .contact-header p {
        font-size: 0.9rem !important;
    }
    
    .contact-info {
        padding: 1rem !important;
        margin-bottom: 1rem !important;
        border-radius: 12px !important;
    }
    
    .contact-info h4 {
        font-size: 1.1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .info-item {
        margin-bottom: 0.75rem !important;
        padding: 0.75rem !important;
        border-radius: 8px !important;
    }
    
    .info-item:hover {
        transform: translateX(-3px) !important;
    }
    
    .info-icon {
        width: 40px !important;
        height: 40px !important;
        font-size: 1rem !important;
        margin-left: 0.75rem !important;
    }
    
    .info-item h6 {
        font-size: 0.9rem !important;
        margin-bottom: 0.25rem !important;
    }
    
    .info-item p {
        font-size: 0.85rem !important;
    }
    
    .row.g-4 {
        gap: 1rem !important;
    }
    
    .col-lg-4 {
        margin-bottom: 1rem !important;
    }
    
    .card-body {
        padding: 1.25rem !important;
    }
    
    .form-floating > label {
        font-size: 0.9rem !important;
    }
    
    .form-control {
        padding: 0.6rem 0.75rem !important;
        font-size: 0.9rem !important;
    }
    
    .btn-send {
        padding: 0.6rem 1.5rem !important;
        font-size: 0.9rem !important;
        border-radius: 20px !important;
    }
    
    .social-contact {
        padding: 1rem !important;
        border-radius: 12px !important;
        margin-top: 1rem !important;
    }
    
    .social-contact h5 {
        font-size: 1rem !important;
        margin-bottom: 1rem !important;
    }
    
    .social-contact a {
        width: 40px !important;
        height: 40px !important;
        line-height: 40px !important;
        font-size: 1rem !important;
        margin: 0 0.25rem !important;
    }
    
    .contact-card {
        margin-bottom: 1rem !important;
        border-radius: 15px !important;
    }
}

@media (max-width: 576px) {
    .contact-container {
        padding: 0.5rem 0 !important;
    }
    
    .container {
        padding: 0 5px !important;
    }
    
    .contact-header {
        padding: 1rem !important;
        border-radius: 15px !important;
    }
    
    .contact-header h1 {
        font-size: 1.2rem !important;
    }
    
    .contact-header h1 i {
        font-size: 1.1rem !important;
    }
    
    .contact-header p {
        font-size: 0.85rem !important;
    }
    
    .contact-info {
        padding: 0.75rem !important;
        border-radius: 10px !important;
    }
    
    .contact-info h4 {
        font-size: 1rem !important;
    }
    
    .contact-info h4 i {
        font-size: 0.9rem !important;
    }
    
    .info-item {
        padding: 0.6rem !important;
        margin-bottom: 0.6rem !important;
        border-radius: 6px !important;
    }
    
    .info-icon {
        width: 35px !important;
        height: 35px !important;
        font-size: 0.9rem !important;
        margin-left: 0.6rem !important;
    }
    
    .info-item h6 {
        font-size: 0.85rem !important;
    }
    
    .info-item p {
        font-size: 0.8rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .form-floating > label {
        font-size: 0.85rem !important;
    }
    
    .form-control {
        padding: 0.5rem 0.6rem !important;
        font-size: 0.85rem !important;
    }
    
    .btn-send {
        padding: 0.5rem 1.2rem !important;
        font-size: 0.85rem !important;
        border-radius: 18px !important;
    }
    
    .social-contact {
        padding: 0.75rem !important;
        border-radius: 10px !important;
    }
    
    .social-contact h5 {
        font-size: 0.95rem !important;
    }
    
    .social-contact a {
        width: 35px !important;
        height: 35px !important;
        line-height: 35px !important;
        font-size: 0.9rem !important;
        margin: 0 0.2rem !important;
    }
    
    .contact-card {
        border-radius: 12px !important;
    }
    
    .row.g-4 {
        gap: 0.5rem !important;
    }
}
</style>

<div class="contact-container">
    <div class="container">
        <!-- Header -->
        <div class="contact-card">
            <div class="contact-header">
                <h1><i class="fas fa-envelope me-3"></i>اتصل بنا</h1>
                <p class="mb-0">نحن هنا لمساعدتك! لا تتردد في التواصل معنا</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- معلومات الاتصال -->
            <div class="col-lg-4">
                <div class="contact-info">
                    <h4 class="mb-3"><i class="fas fa-info-circle me-2"></i>معلومات الاتصال</h4>
                    
                    <div class="info-item">
                        <div class="info-icon bg-primary">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">العنوان</h6>
                            <p class="mb-0">المغرب</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon bg-success">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">البريد الإلكتروني</h6>
                            <p class="mb-0">contact@physique-chimie.ma</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">ساعات العمل</h6>
                            <p class="mb-0">الأحد - الخميس: 9:00 - 17:00</p>
                        </div>
                    </div>
                </div>

                <!-- الشبكات الاجتماعية -->
                <div class="social-contact">
                    <h5 class="mb-3">تابعنا على</h5>
                    <a href="#" style="background: #1877f2;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" style="background: #1da1f2;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="background: #ff0000;"><i class="fab fa-youtube"></i></a>
                    <a href="#" style="background: #e4405f;"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- نموذج الاتصال -->
            <div class="col-lg-8">
                <div class="contact-card">
                    <div class="p-4">
                        <h4 class="mb-4"><i class="fas fa-paper-plane me-2"></i>أرسل لنا رسالة</h4>
                        
                        <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                        <label for="name">الاسم الكامل *</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                        <label for="email">البريد الإلكتروني *</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                                        <label for="phone">رقم الهاتف (اختياري)</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="subject" name="subject" required>
                                            <option value="">اختر الموضوع</option>
                                            <option value="استفسار عام">استفسار عام</option>
                                            <option value="مشكلة تقنية">مشكلة تقنية</option>
                                            <option value="اقتراح محتوى">اقتراح محتوى</option>
                                            <option value="شكوى">شكوى</option>
                                            <option value="طلب مساعدة">طلب مساعدة</option>
                                            <option value="أخرى">أخرى</option>
                                        </select>
                                        <label for="subject">الموضوع *</label>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="message" name="message" 
                                                  style="height: 120px;" required><?php echo htmlspecialchars($msg ?? ''); ?></textarea>
                                        <label for="message">الرسالة *</label>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" class="btn btn-send">
                                        <i class="fas fa-paper-plane me-2"></i>إرسال الرسالة
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
