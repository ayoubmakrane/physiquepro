<?php
http_response_code(500);
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خطأ في الخادم - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cairo', Arial, sans-serif;
        }
        
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            margin: 20px;
        }
        
        .error-icon {
            font-size: 6rem;
            color: #ff6b6b;
            margin-bottom: 1rem;
        }
        
        .error-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .error-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .btn-home {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.3s ease;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-retry {
            background: transparent;
            border: 2px solid #ff6b6b;
            color: #ff6b6b;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            margin-left: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-retry:hover {
            background: #ff6b6b;
            color: white;
        }
        
        .suggestions {
            margin-top: 2rem;
            text-align: right;
        }
        
        .suggestions ul {
            list-style: none;
            padding: 0;
        }
        
        .suggestions li {
            margin: 0.5rem 0;
            color: #666;
        }
        
        .suggestions a {
            color: #ff6b6b;
            text-decoration: none;
        }
        
        .suggestions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <h1 class="error-title">500</h1>
        
        <p class="error-message">
            عذراً، حدث خطأ في الخادم.<br>
            نحن نعمل على إصلاح هذه المشكلة في أسرع وقت ممكن.
        </p>
        
        <div class="mb-3">
            <a href="javascript:location.reload()" class="btn-retry">
                <i class="fas fa-redo me-2"></i>
                إعادة المحاولة
            </a>
            
            <a href="<?= SITE_URL ?>" class="btn-home">
                <i class="fas fa-home me-2"></i>
                الصفحة الرئيسية
            </a>
        </div>
        
        <div class="suggestions">
            <h5>في حالة استمرار المشكلة:</h5>
            <ul>
                <li><i class="fas fa-envelope me-2"></i> <a href="<?= SITE_URL ?>/contact.php">تواصل مع الدعم الفني</a></li>
                <li><i class="fas fa-clock me-2"></i> جرب مرة أخرى خلال بضع دقائق</li>
                <li><i class="fas fa-refresh me-2"></i> امسح ذاكرة التخزين المؤقت للمتصفح</li>
            </ul>
        </div>
    </div>
    
    <script>
        // تسجيل الخطأ للتحليل
        console.error('500 Internal Server Error - Page:', window.location.href);
        
        // إعادة المحاولة التلقائية بعد 30 ثانية (اختياري)
        setTimeout(function() {
            if (confirm('هل تريد إعادة تحميل الصفحة تلقائياً؟')) {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html> 