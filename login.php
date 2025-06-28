<?php
session_start(); // بدء الجلسة لضمان عمل رموز الأمان
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';
// تضمين ملف التحسينات الأمنية
if (file_exists('includes/security_fixes.php')) {
    require_once 'includes/security_fixes.php';
}

// إذا كان المستخدم مسجل دخول بالفعل، توجيه للصفحة الرئيسية
if (is_logged_in()) {
    redirect('index.php');
}

$page_title = 'تسجيل الدخول';

// معالجة رسائل من الكوكيز
if (isset($_COOKIE['logout_message'])) {
    show_message($_COOKIE['logout_message'], 'success');
    setcookie('logout_message', '', time() - 3600, '/');
}

// معالجة النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // فحص Rate Limiting قبل المعالجة - زيادة الحد الأقصى إلى 5 محاولات في 15 دقيقة
    if (!rate_limit_check('login', 5, 900)) {
        $retry_time = RateLimiter::getRetryTime('login', 900);
        $retry_minutes = ceil($retry_time / 60);
        show_message("تم تجاوز الحد المسموح من محاولات تسجيل الدخول. يرجى المحاولة مرة أخرى بعد $retry_minutes دقيقة.", 'danger');
        // تسجيل محاولة محتملة للاختراق
        error_log("محاولات متعددة لتسجيل الدخول من IP: " . $_SERVER['REMOTE_ADDR'] . " - تم منع المحاولة");
    }
    // التحقق من رمز CSRF
    elseif (!validate_csrf()) {
        rate_limit_record('login');
        show_message('خطأ في رمز الأمان. يرجى المحاولة مرة أخرى.', 'danger');
        // تسجيل محاولة محتملة للتلاعب بالنموذج
        error_log("محاولة تسجيل دخول مع رمز CSRF غير صالح من IP: " . $_SERVER['REMOTE_ADDR']);
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) ? true : false;
        
        // التحقق من صحة المدخلات
        if (empty($username) || empty($password)) {
            rate_limit_record('login');
            show_message('يرجى إدخال اسم المستخدم وكلمة المرور', 'danger');
        } else {
            try {
                // البحث عن المستخدم
                $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    // تسجيل الدخول بنجاح
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['user_type'] = $user['user_type'];
                    
                    // تجديد معرف الجلسة لمنع اختطاف الجلسة
                    session_regenerate_id(true);
                    
                    // تحديث آخر دخول (نسخة مبسطة)
                    // إذا كان العمودان موجودين، يمكن إضافة last_login و last_ip لاحقاً
                    // $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    // $stmt->execute([$user['id']]);
                    
                    // تسجيل نجاح تسجيل الدخول للتدقيق
                    error_log("تسجيل دخول ناجح: {$user['username']} ({$user['user_type']}) من IP: $ip_address");
                    
                    // تسجيل كوكي التذكر إذا طُلب
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $token_hash = hash('sha256', $token);
                        $expires = time() + (86400 * 30); // 30 يوم
                        
                        // حفظ الرمز المشفر في قاعدة البيانات (معلق مؤقتاً حتى إضافة الأعمدة)
                        // $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, token_expires = FROM_UNIXTIME(?) WHERE id = ?");
                        // $stmt->execute([$token_hash, $expires, $user['id']]);
                        
                        // تعيين الكوكي للمتصفح
                        setcookie('remember_token', $token, $expires, "/", "", isset($_SERVER['HTTPS']), true);
                    }
                    
                    // مسح محاولات تسجيل الدخول عند النجاح
                    RateLimiter::clearAttempts('login');
                    
                    // التوجيه حسب نوع المستخدم
                    if ($user['user_type'] == 'admin') {
                        redirect('admin/dashboard.php');
                    } elseif ($user['user_type'] == 'teacher') {
                        redirect('teacher/dashboard.php');
                    } else {
                        redirect('index.php');
                    }
                } else {
                    // تسجيل المحاولة الفاشلة
                    rate_limit_record('login');
                    
                    // تسجيل محاولة فاشلة للتدقيق
                    error_log("محاولة تسجيل دخول فاشلة: $username من IP: " . $_SERVER['REMOTE_ADDR']);
                    
                    // استخدام نفس رسالة الخطأ بغض النظر عن سبب الفشل (أمان)
                    show_message('اسم المستخدم أو كلمة المرور غير صحيحة', 'danger');
                }
            } catch (PDOException $e) {
                rate_limit_record('login');
                error_log("خطأ في تسجيل الدخول: " . $e->getMessage());
                show_message('خطأ في النظام. يرجى المحاولة لاحقاً', 'danger');
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Bootstrap RTL -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }
    
    .login-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: calc(100vh - 140px);
        display: flex;
        align-items: center;
        padding: 40px 0;
    }
    
    .login-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 500px;
        width: 100%;
        margin: auto;
    }
    
    .login-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
    }
    
    .login-header::before {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 20px solid transparent;
        border-right: 20px solid transparent;
        border-top: 20px solid #764ba2;
    }
    
    .login-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: rgba(255,255,255,0.9);
    }
    
    .login-body {
        padding: 3rem 2rem 2rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .input-group-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        min-width: 45px;
        justify-content: center;
    }
    
    .form-control {
        border: 2px solid #e9ecef;
        border-right: none;
        padding: 12px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-login {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 15px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .register-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    
    .register-link:hover {
        color: #764ba2;
        text-decoration: underline;
    }
    
    .toggle-password {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }
    
    .toggle-password:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        color: white;
    }
    
    .alert {
        border-radius: 15px;
        border: none;
        padding: 1rem 1.5rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .login-container {
            padding: 20px 0;
        }
        
        .login-container .container {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }
        
        .login-card {
            margin: 0 auto;
            max-width: calc(100vw - 40px);
        }
        
        .login-header {
            padding: 1.5rem;
        }
        
        .login-icon {
            font-size: 3rem;
        }
        
        .login-body {
            padding: 2rem 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .login-container .container {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        
        .login-card {
            max-width: calc(100vw - 30px);
        }
        
        .login-body {
            padding: 1.5rem 1rem;
        }
    }
</style>

<div class="login-container">
    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <h2>تسجيل الدخول</h2>
                <p>مرحباً بك في منصة الفيزياء والكيمياء</p>
            </div>
            
            <div class="login-body">

                    <?php 
                    // عرض الرسائل
                    $message = get_message();
                    if ($message): 
                    ?>
                        <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="needs-validation" novalidate>
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">اسم المستخدم أو البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required autofocus>
                                <div class="invalid-feedback">
                                    يرجى إدخال اسم المستخدم
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="invalid-feedback">
                                    يرجى إدخال كلمة المرور
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">تذكرني</label>
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-login text-white w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            تسجيل الدخول
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">ليس لديك حساب؟ <a href="register.php" class="register-link">إنشاء حساب جديد</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// إظهار/إخفاء كلمة المرور
document.querySelector('.toggle-password')?.addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// تنشيط Bootstrap validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<?php include 'includes/footer.php'; ?> 
