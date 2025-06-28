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

$page_title = 'إنشاء حساب جديد';

// تهيئة المتغيرات
$username = '';
$email = '';
$full_name = '';
$user_type = 'student';

// معالجة النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التحقق من رمز CSRF
    if (!validate_csrf()) {
        show_message('خطأ في رمز الأمان. يرجى المحاولة مرة أخرى.', 'danger');
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $full_name = sanitize($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $user_type = sanitize($_POST['user_type'] ?? 'student');
        
        // التحقق من صحة المدخلات
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'اسم المستخدم مطلوب';
        } elseif (strlen($username) < 3) {
            $errors[] = 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'اسم المستخدم يجب أن يحتوي على أحرف وأرقام وشرطة سفلية فقط';
        }
        
        if (empty($email)) {
            $errors[] = 'البريد الإلكتروني مطلوب';
        } elseif (!validate_email($email)) {
            $errors[] = 'البريد الإلكتروني غير صحيح';
        }
        
        if (empty($full_name)) {
            $errors[] = 'الاسم الكامل مطلوب';
        }
        
        if (empty($password)) {
            $errors[] = 'كلمة المرور مطلوبة';
        } elseif (strlen($password) < 8) {
            $errors[] = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
        } elseif (function_exists('is_strong_password') && !is_strong_password($password)) {
            $errors[] = 'كلمة المرور ضعيفة. يجب أن تحتوي على الأقل 8 أحرف، حرف كبير، حرف صغير، رقم وحرف خاص.';
        }
        
        if ($password !== $confirm_password) {
            $errors[] = 'كلمة المرور وتأكيدها غير متطابقتين';
        }
        
        // التأكد من صحة نوع المستخدم
        if (!in_array($user_type, ['student', 'teacher'])) {
            $user_type = 'student';
        }
        
        // التحقق من وجود اسم المستخدم أو البريد الإلكتروني
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                if ($stmt->fetch()) {
                    $errors[] = 'اسم المستخدم أو البريد الإلكتروني موجود بالفعل';
                }
            } catch (PDOException $e) {
                error_log("خطأ في التحقق من وجود المستخدم: " . $e->getMessage());
                $errors[] = 'خطأ في النظام. يرجى المحاولة لاحقاً';
            }
        }
        
        // إنشاء الحساب إذا لم توجد أخطاء
        if (empty($errors)) {
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $status = 'active'; // جميع المستخدمين يتم تفعيلهم مباشرة
                $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
                $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                
                // تقسيم الاسم الكامل إلى first_name و last_name
                $name_parts = explode(' ', trim($full_name), 2);
                $first_name = $name_parts[0] ?? '';
                $last_name = $name_parts[1] ?? '';
                
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name, full_name, user_type, status, created_at) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$username, $email, $hashed_password, $first_name, $last_name, $full_name, $user_type, $status]);
                
                // تسجيل نشاط إنشاء الحساب للتدقيق
                error_log("تم إنشاء حساب جديد: $username ($user_type) من عنوان IP: $ip_address");
                
                show_message('تم إنشاء حسابك بنجاح! يمكنك الآن تسجيل الدخول والاستفادة من جميع خدمات المنصة.', 'success');
                
                // مسح النموذج
                $username = '';
                $email = '';
                $full_name = '';
                
            } catch (PDOException $e) {
                error_log("خطأ في إنشاء الحساب: " . $e->getMessage());
                show_message('خطأ في إنشاء الحساب. يرجى المحاولة لاحقاً.', 'danger');
            }
        } else {
            show_message(implode('<br>', $errors), 'danger');
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
    
    .register-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: calc(100vh - 140px);
        display: flex;
        align-items: center;
        padding: 40px 0;
    }
    
    .register-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 650px;
        width: 100%;
        margin: auto;
    }
    
    .register-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
    }
    
    .register-header::before {
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
    
    .register-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: rgba(255,255,255,0.9);
    }
    
    .register-body {
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
    
    .btn-register {
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
    
    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .login-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    
    .login-link:hover {
        color: #764ba2;
        text-decoration: underline;
    }
    
    .form-select {
        border: 2px solid #e9ecef;
        border-right: none;
        padding: 12px;
        transition: all 0.3s ease;
    }
    
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .form-text {
        background: #fff3cd;
        padding: 10px;
        border-radius: 8px;
        border-right: 4px solid #ffc107;
        margin-top: 5px;
    }
    

    
    .alert {
        border-radius: 15px;
        border: none;
        padding: 1rem 1.5rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .register-container {
            padding: 20px 0;
        }
        
        .register-container .container {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }
        
        .register-card {
            margin: 0 auto;
            max-width: calc(100vw - 40px);
        }
        
        .register-header {
            padding: 1.5rem;
        }
        
        .register-icon {
            font-size: 3rem;
        }
        
        .register-body {
            padding: 2rem 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .register-container .container {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        
        .register-card {
            max-width: calc(100vw - 30px);
        }
        
        .register-body {
            padding: 1.5rem 1rem;
        }
    }
</style>

<div class="register-container">
    <div class="container">
        <div class="register-card">
            <div class="register-header">
                <div class="register-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>إنشاء حساب جديد</h2>
                <p>انضم إلى منصة الفيزياء والكيمياء</p>
            </div>
            
            <div class="register-body">

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
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">الاسم الكامل</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo htmlspecialchars($full_name); ?>" required>
                                    <div class="invalid-feedback">يرجى إدخال الاسم الكامل</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">اسم المستخدم</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-at"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($username); ?>" required>
                                    <div class="invalid-feedback">يرجى إدخال اسم المستخدم</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email); ?>" required>
                                <div class="invalid-feedback">يرجى إدخال بريد إلكتروني صحيح</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="user_type" class="form-label">نوع الحساب</label>
                            <select class="form-select" id="user_type" name="user_type" required>
                                <option value="student" <?php echo ($user_type === 'student') ? 'selected' : ''; ?>>طالب</option>
                                <option value="teacher" <?php echo ($user_type === 'teacher') ? 'selected' : ''; ?>>أستاذ</option>
                            </select>
                            <div class="form-text">
                                <small class="text-info">ملاحظة: جميع الحسابات يتم تفعيلها مباشرة بعد التسجيل</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="invalid-feedback">يرجى إدخال كلمة المرور</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <div class="invalid-feedback">يرجى تأكيد كلمة المرور</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-register text-white">
                                <i class="fas fa-user-plus me-2"></i>
                                إنشاء الحساب
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <p class="mb-0">لديك حساب بالفعل؟ <a href="login.php" class="login-link">تسجيل الدخول</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Bootstrap form validation
(function() {
    'use strict';
    
    // تفعيل تحقق Bootstrap
    var forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // تحقق من تطابق كلمة المرور
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('كلمة المرور غير متطابقة');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
    
    // تأثيرات إضافية
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.parentNode.style.transform = 'scale(1)';
        });
    });
})();
</script>

<?php include 'includes/footer.php'; ?> 
