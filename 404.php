<?php
http_response_code(404);
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة غير موجودة - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #667eea;
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
            background: linear-gradient(45deg, #667eea, #764ba2);
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
            color: #667eea;
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
            <i class="fas fa-search"></i>
        </div>
        
        <h1 class="error-title">404</h1>
        
        <p class="error-message">
            عذراً، الصفحة التي تبحث عنها غير موجودة.<br>
            ربما تم نقلها أو حذفها، أو أن الرابط غير صحيح.
        </p>
        
        <a href="<?= SITE_URL ?>" class="btn-home">
            <i class="fas fa-home me-2"></i>
            العودة للصفحة الرئيسية
        </a>
        
        <div class="suggestions">
            <h5>يمكنك أيضاً:</h5>
            <ul>
                <li><i class="fas fa-book me-2"></i> <a href="<?= SITE_URL ?>/teachers_lessons.php">تصفح الدروس</a></li>
                <li><i class="fas fa-users me-2"></i> <a href="<?= SITE_URL ?>/all_teachers.php">عرض جميع الأساتذة</a></li>
                <li><i class="fas fa-question-circle me-2"></i> <a href="<?= SITE_URL ?>/help.php">مركز المساعدة</a></li>
                <li><i class="fas fa-envelope me-2"></i> <a href="<?= SITE_URL ?>/contact.php">التواصل معنا</a></li>
            </ul>
        </div>
    </div>
    
    <script>
        // إرسال تقرير عن الرابط المكسور (اختياري)
        if (document.referrer) {
            console.log('404 Error - Referrer:', document.referrer);
        }
    </script>
</body>
</html> 