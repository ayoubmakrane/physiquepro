<?php
// بدء الجلسة لضمان عمل وظائف المستخدم
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * الصفحة الرئيسية لموقع الفيزياء والكيمياء - محسنة حسب نوع المستخدم
 */

// تضمين الملفات الأساسية
require_once 'includes/config.php';
require_once 'includes/connection.php';
require_once 'includes/functions.php';

// تحديد نوع المستخدم الحالي
$user_type = 'guest'; // افتراضي: زائر
$user_id = null;
$user_name = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['username'] ?? 'المستخدم';
    
    if (isset($_SESSION['user_type'])) {
        switch ($_SESSION['user_type']) {
            case 'admin':
                $user_type = 'admin';
                break;
            case 'teacher':
                $user_type = 'teacher';
                break;
            case 'student':
                $user_type = 'student';
                break;
            default:
                $user_type = 'student';
        }
    }
}

$page_title = 'الصفحة الرئيسية';

// تضمين ملف الهيدر
include 'includes/header.php';
?>

<div class="container-fluid">
    <style>
        /* تحميل الخطوط المناسبة */
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap');
        @import url('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
        
        body {
            font-family: 'Cairo', 'Tajawal', sans-serif;
            background: #f8f9fa;
        }
        
        /* تحسينات الاستجابة للجوال */
        @media (max-width: 768px) {
            body {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .hero-section {
                padding: 30px 0 !important;
                margin: 0 -15px !important;
                width: calc(100% + 30px) !important;
                position: relative !important;
                left: -15px !important;
            }
            
            .hero-section .container {
                padding: 0 1rem !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
            
            .hero-section h1 {
                font-size: 1.8rem !important;
            }
            
            .hero-section p.lead {
                font-size: 1rem !important;
            }
            
            .section-title {
                font-size: 2rem !important;
            }
            
            .feature-card {
                padding: 1.5rem !important;
            }
            
            .btn-hero {
                padding: 8px 16px !important;
                font-size: 0.9rem !important;
                margin-bottom: 10px !important;
                display: inline-block !important;
            }
            
            .feature-icon {
                font-size: 2.5rem !important;
            }
            
            /* تحسينات بطاقات الإحصائيات للهاتف - للمشرف */
            .stats-card {
                padding: 0.8rem !important;
                border-radius: 10px !important;
                min-height: auto !important;
                margin-bottom: 1rem !important;
            }
            
            .stats-number {
                font-size: 1.4rem !important;
                margin-bottom: 0.3rem !important;
                font-weight: 700 !important;
            }
            
            .stats-card h6 {
                font-size: 0.75rem !important;
                margin-bottom: 0.5rem !important;
                line-height: 1.2 !important;
            }
            
            .stats-card .btn {
                padding: 4px 8px !important;
                font-size: 0.65rem !important;
                border-radius: 6px !important;
            }
            
            .stats-card small {
                font-size: 0.65rem !important;
            }
            
            /* بطاقات الإجراءات السريعة للمشرف */
            .quick-action-card {
                padding: 1rem !important;
                text-align: center !important;
                background: white !important;
                border-radius: 10px !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
                cursor: pointer !important;
                transition: all 0.3s ease !important;
                margin-bottom: 1rem !important;
            }
            
            .quick-action-card i {
                font-size: 1.5rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            .quick-action-card h6 {
                font-size: 0.75rem !important;
                margin-bottom: 0 !important;
                line-height: 1.2 !important;
            }
            
            /* تحسينات بطاقات الإحصائيات للهاتف - نفس تصميم أنواع المحتوى */
            .stats-card-enhanced {
                padding: 1rem !important;
                border-radius: 15px !important;
                background: white !important;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
                border: none !important;
                height: 100% !important;
                min-height: 120px !important;
                overflow: hidden !important;
                position: relative !important;
                z-index: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                text-align: center !important;
            }
            
            .feature-icon-enhanced {
                flex-shrink: 0 !important;
                font-size: 1.8rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            .stats-number-enhanced {
                flex-shrink: 0 !important;
                font-size: 1.4rem !important;
                margin: 0.3rem 0 !important;
                font-weight: 700 !important;
            }
            
            .stats-card-enhanced h6 {
                flex-shrink: 0 !important;
                font-size: 0.8rem !important;
                margin: 0.3rem 0 !important;
                line-height: 1.2 !important;
                font-weight: 600 !important;
            }
            
            .stats-card-enhanced small {
                flex-grow: 1 !important;
                font-size: 0.65rem !important;
                margin-bottom: 0.5rem !important;
                line-height: 1.2 !important;
            }
            
            /* تأثيرات تفاعلية للإحصائيات - مطابقة لأنواع المحتوى */
            .stats-card-enhanced::after {
                content: '' !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
                background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%) !important;
                z-index: -1 !important;
                transform: scaleX(0) !important;
                transform-origin: 0 50% !important;
                transition: transform 0.5s ease-out !important;
                border-radius: 15px !important;
            }
            
            .stats-card-enhanced:hover::after {
                transform: scaleX(1) !important;
            }
            
            .stats-card-enhanced:hover {
                transform: translateY(-5px) !important;
                box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important;
            }
            
            .stats-card-enhanced:hover .feature-icon-enhanced {
                transform: scale(1.1) !important;
            }
            
            /* تحسينات خاصة للإحصائيات الشخصية للأستاذ - مربعة */
            .teacher-stats-section .stats-card-enhanced {
                aspect-ratio: 1 !important;
                min-height: 150px !important;
            }
            
            /* تخطيط الإحصائيات - بطاقتين في السطر للهاتف */
            .teacher-stats-section .col-3 {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }
            
            /* تحسينات المستويات للهاتف - مربعة ومتناسقة */
            .level-card-custom {
                margin-bottom: 1rem !important;
                aspect-ratio: 1 !important;
                height: auto !important;
                display: flex !important;
                flex-direction: column !important;
            }
            
            .level-card-custom > div {
                padding: 1rem !important;
                height: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                text-align: center !important;
            }
            
            .level-icon-circle {
                width: 50px !important;
                height: 50px !important;
                margin: 0 auto 0.8rem auto !important;
            }
            
            .level-icon-circle i {
                font-size: 1.2rem !important;
            }
            
            .level-card-custom h5 {
                font-size: 0.85rem !important;
                margin-bottom: 0.5rem !important;
                line-height: 1.2 !important;
                flex-shrink: 0 !important;
            }
            
            .level-card-custom p {
                font-size: 0.7rem !important;
                margin-bottom: 0.8rem !important;
                line-height: 1.3 !important;
                flex-grow: 1 !important;
                display: -webkit-box !important;
                -webkit-line-clamp: 3 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
            }
            
            .level-btn {
                padding: 6px 12px !important;
                font-size: 0.7rem !important;
                margin-top: auto !important;
                flex-shrink: 0 !important;
                border-radius: 20px !important;
            }
            
            /* توسيط المستوى الثالث عندما يكون وحيداً في السطر */
            .row .col-6:nth-child(3):last-child {
                margin: 0 auto !important;
                float: none !important;
            }
            
            /* تحسينات بطاقات أنواع المحتوى للهاتف - 3 في السطر */
            .content-types-mobile .col-6 {
                flex: 0 0 33.333333% !important;
                max-width: 33.333333% !important;
            }
            
            .content-types-mobile .feature-card {
                padding: 1rem !important;
                min-height: 120px !important;
            }
            
            .content-types-mobile .feature-icon {
                font-size: 1.8rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            .content-types-mobile h6 {
                font-size: 0.8rem !important;
                margin-bottom: 0.3rem !important;
                line-height: 1.2 !important;
            }
            
            .content-types-mobile p {
                font-size: 0.65rem !important;
                margin-bottom: 0.5rem !important;
                line-height: 1.2 !important;
            }
            
            .content-types-mobile .small {
                font-size: 0.6rem !important;
            }
            
            /* تحسينات بطاقات المميزات للهاتف */
            .features-mobile .feature-card {
                padding: 1.2rem !important;
                min-height: 140px !important;
            }
            
            .features-mobile .feature-icon {
                font-size: 2rem !important;
                margin-bottom: 0.8rem !important;
            }
            
            .features-mobile h6 {
                font-size: 0.9rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            .features-mobile p {
                font-size: 0.75rem !important;
                line-height: 1.3 !important;
            }
            
            /* تحسينات بطاقات الأساتذة للهاتف */
            .teachers-grid-homepage {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 1rem !important;
            }
            
            .teacher-card-homepage {
                padding: 1rem !important;
            }
            
            .teacher-avatar-homepage {
                width: 50px !important;
                height: 50px !important;
                font-size: 1.2rem !important;
            }
            
            .teacher-name-homepage {
                font-size: 0.8rem !important;
                line-height: 1.2 !important;
            }
            
            .teacher-level-homepage {
                font-size: 0.7rem !important;
            }
            
            .teacher-stats-homepage {
                gap: 0.5rem !important;
            }
            
            .teacher-stat-number-homepage {
                font-size: 0.9rem !important;
            }
            
            .teacher-stat-label-homepage {
                font-size: 0.6rem !important;
            }
            
            .teacher-cta-homepage {
                font-size: 0.7rem !important;
            }
            
            /* تحسينات العناوين الرئيسية للهاتف */
            .section-title {
                font-size: 1.8rem !important;
                margin-bottom: 1rem !important;
            }
            
            .section-title::after {
                width: 60px !important;
                height: 3px !important;
            }
            
            /* تحسينات الأزرار الرئيسية */
            .btn-lg {
                padding: 0.8rem 1.5rem !important;
                font-size: 0.9rem !important;
            }
            
            /* تحسينات النصوص الوصفية */
            .fs-5 {
                font-size: 1rem !important;
            }
            
            /* تحسينات Hero Section للهاتف */
            .hero-welcome-container {
                width: 100% !important;
                padding: 1rem 0.5rem !important;
                margin: 0 !important;
                text-align: center !important;
            }
            
            .hero-title-spectacular {
                margin-bottom: 1.5rem !important;
                width: 100% !important;
                padding: 0 0.5rem !important;
            }
            
            .title-line-1 {
                font-size: 1.1rem !important;
                margin-bottom: 0.3rem !important;
            }
            
            .title-line-2 {
                font-size: 1.8rem !important;
                margin-bottom: 0.3rem !important;
            }
            
            .title-line-3 {
                font-size: 0.9rem !important;
            }
            
            .lead-spectacular {
                font-size: 1rem !important;
                margin-bottom: 1.5rem !important;
                width: 100% !important;
                padding: 0 0.5rem !important;
            }
            
            .features-preview {
                margin-bottom: 2rem !important;
            }
            
            .feature-bubble {
                padding: 0.5rem 0.8rem !important;
                font-size: 0.75rem !important;
                margin: 0.25rem !important;
            }
            
            .hero-actions-spectacular {
                width: 100% !important;
                padding: 0 0.5rem !important;
                margin: 1.5rem 0 !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 1rem !important;
            }
            
            .btn-spectacular {
                padding: 0.8rem 1.2rem !important;
                font-size: 0.85rem !important;
                margin: 0.5rem auto !important;
                width: 95% !important;
                max-width: 100% !important;
            }
            
            .btn-icon {
                width: 40px !important;
                height: 40px !important;
                font-size: 1.3rem !important;
            }
            
            .btn-main {
                font-size: 1rem !important;
            }
            
            .btn-sub {
                font-size: 0.8rem !important;
            }
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            border-radius: 0 0 20px 20px;
        }
        
        /* التأكد من تحميل بوتستراب RTL بشكل صحيح */
        .section-title, p, h1, h2, h3, h4, h5, h6 {
            font-family: 'Cairo', 'Tajawal', sans-serif !important;
        }
        
        /* إضافة أشكال عائمة للخلفية */
        .hero-section::before,
        .hero-section::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            animation: floatBubble 15s infinite alternate ease-in-out;
        }
        
        .hero-section::before {
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }
        
        .hero-section::after {
            bottom: -100px;
            right: -100px;
            width: 200px;
            height: 200px;
            animation-delay: 5s;
        }
        
        @keyframes floatBubble {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, 20px) scale(1.1); }
            100% { transform: translate(10px, 5px) scale(1); }
        }
        
        /* تعريف تأثيرات الحركة للعناصر */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hero-section.user-logged {
            padding: 30px 0;
        }
        
        .hero-section h1 {
            font-size: 2.8rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            animation: fadeInDown 1s ease-out;
        }
        
        .hero-section p.lead {
            animation: fadeInUp 1s ease-out;
            animation-delay: 0.3s;
            animation-fill-mode: both;
            max-width: 700px;
            margin: 0 auto 1.5rem auto;
            font-size: 1.2rem;
        }
        
        .hero-section.user-logged h1 {
            font-size: 2rem;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
            min-height: 140px;
            overflow: hidden;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .feature-card .feature-icon {
            flex-shrink: 0;
            font-size: 2rem !important;
            margin-bottom: 0.5rem !important;
        }
        
        .feature-card h5 {
            flex-shrink: 0;
            margin: 0.5rem 0;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .feature-card p {
            flex-grow: 1;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            line-height: 1.3;
        }
        
        .feature-card .feature-badge {
            display: none;
        }
        
        .feature-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            z-index: -1;
            transform: scaleX(0);
            transform-origin: 0 50%;
            transition: transform 0.5s ease-out;
            border-radius: 20px;
        }
        
        .feature-card:hover::after {
            transform: scaleX(1);
        }
        
        .feature-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.2) rotate(5deg);
        }
        
        /* تحسين تأثيرات المستويات */
        .level-card {
            position: relative;
            overflow: hidden;
        }
        
        .level-card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            z-index: -1;
            transform: scaleY(0);
            transform-origin: top;
            transition: transform 0.5s ease-out;
        }
        
        .level-card:hover .level-card-overlay {
            transform: scaleY(1);
        }
        
        .btn-hero {
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 50px;
            margin: 0 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            animation: fadeInUp 1s ease-out;
            animation-delay: 0.6s;
            animation-fill-mode: both;
        }
        
        .btn-hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            z-index: -1;
            transform: scaleX(0);
            transform-origin: 0 50%;
            transition: transform 0.5s ease-out;
        }
        
        .btn-hero:hover::after {
            transform: scaleX(1);
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        
        /* أنماط مخصصة لكل نوع مستخدم */
        .admin-dashboard {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }
        
        .teacher-dashboard {
            background: linear-gradient(135deg, #4834d4 0%, #686de0 100%);
        }
        
        .student-dashboard {
            background: linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%);
        }
        
        .guest-dashboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: none;
            height: 100%;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .quick-action-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .user-welcome {
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        /* أنماط الرسالة الترحيبية المحسنة للطلاب */
        .student-welcome-enhanced {
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .student-welcome-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        .student-avatar {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            animation: pulse 2s infinite;
        }
        
        .student-avatar i {
            font-size: 2.2rem;
            color: white;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .welcome-title {
            margin: 1.2rem 0 0.8rem 0;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .greeting {
            display: block;
            font-size: 1.4rem;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }
        
        .student-name {
            display: block;
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }
        
        .welcome-subtitle {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.95);
            margin-bottom: 0.6rem;
            font-weight: 500;
        }
        
        .highlight-physics {
            color: #00bcd4;
            font-weight: 700;
            text-shadow: 0 1px 3px rgba(0, 188, 212, 0.5);
        }
        
        .highlight-chemistry {
            color: #4caf50;
            font-weight: 700;
            text-shadow: 0 1px 3px rgba(76, 175, 80, 0.5);
        }
        
        .welcome-description {
            font-size: 1rem;
            color: rgba(255,255,255,0.8);
            margin-bottom: 1.5rem;
            font-weight: 400;
        }
        
        .welcome-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-start-learning {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 25px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }
        
        .btn-start-learning:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
        
        .btn-browse-teachers {
            background: transparent;
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 25px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .btn-browse-teachers:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.5);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255,255,255,0.2);
        }
        
        @media (max-width: 768px) {
            .student-welcome-enhanced {
                padding: 1.5rem 1rem;
            }
            
            .welcome-title {
                font-size: 1.6rem;
            }
            
            .greeting {
                font-size: 1.1rem;
            }
            
            .welcome-subtitle {
                font-size: 1rem;
            }
            
            .welcome-description {
                font-size: 0.9rem;
            }
            
            .welcome-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-start-learning,
            .btn-browse-teachers {
                width: 100%;
                max-width: 280px;
            }
        }
        
        .bg-gradient {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        /* أنماط خاصة بصفحة الطالب */
        .teacher-avatar-preview {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .teacher-preview-card {
            transition: all 0.3s ease;
        }
        
        .teacher-preview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        /* أنماط البطاقات المصغرة للمميزات */
        .mini-feature-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
            min-height: 140px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .mini-feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .mini-feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .mini-feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .mini-feature-card i {
            font-size: 2rem;
            transition: all 0.3s ease;
        }
        
        .mini-feature-card:hover i {
            transform: scale(1.2);
        }
        
        /* تحسين أنماط البطاقات الرئيسية */
        .feature-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 2;
        }
        
        .feature-badge .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* تأثيرات حركية للبطاقات */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .feature-card {
            animation: slideInUp 0.6s ease-out;
            animation-fill-mode: both;
        }
        
        .feature-card:nth-child(1) { animation-delay: 0.1s; }
        .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .feature-card:nth-child(3) { animation-delay: 0.3s; }
        .feature-card:nth-child(4) { animation-delay: 0.4s; }
        
        .mini-feature-card {
            animation: slideInUp 0.4s ease-out;
            animation-fill-mode: both;
        }
        
        .mini-feature-card:nth-child(1) { animation-delay: 0.5s; }
        .mini-feature-card:nth-child(2) { animation-delay: 0.6s; }
        .mini-feature-card:nth-child(3) { animation-delay: 0.7s; }
        .mini-feature-card:nth-child(4) { animation-delay: 0.8s; }
        .mini-feature-card:nth-child(5) { animation-delay: 0.9s; }
        .mini-feature-card:nth-child(6) { animation-delay: 1.0s; }
        
        .teacher-stats-preview {
            padding: 1rem 0;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .content-types-preview {
            text-align: center;
        }
        
        .interactive-preview {
            background: linear-gradient(135deg, #e3f2fd 0%, #f8f9ff 100%);
            border: 2px solid #e1f5fe;
        }
        
        .interactive-feature {
            padding: 1rem;
            text-align: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .interactive-feature i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .interactive-lesson-card {
            position: relative;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }
        
        .lesson-type-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .lesson-meta {
            padding-top: 1rem;
            border-top: 1px solid #f0f0f0;
            margin-top: 1rem;
        }
        
        .level-stats {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
        }
        
        .stat-item {
            padding: 0.5rem;
        }
        
        .content-type-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            margin: 0.125rem;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .badge-lessons { background: #e3f2fd; color: #1976d2; }
        .badge-exercises { background: #fff3e0; color: #f57c00; }
        .badge-exams { background: #ffebee; color: #d32f2f; }
        .badge-experiments { background: #e8f5e8; color: #388e3c; }
        
        /* تحسينات خاصة بصفحة الأستاذ */
        .teacher-stats-section {
            background: #ffffff;
            position: relative;
            overflow: hidden;
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
        }
        
        .teacher-stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(248,249,250,0.3);
            opacity: 0.5;
        }
        
        .stats-card-enhanced {
            background: #ffffff;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .stats-card-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }
        
        .stats-card-enhanced:hover::before {
            left: 100%;
        }
        
        .stats-card-enhanced:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            border-color: #dee2e6;
        }
        
        .stats-number-enhanced {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #495057, #6c757d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }
        
        .feature-card-enhanced {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 25px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 
                0 10px 30px rgba(0,0,0,0.1),
                inset 0 1px 0 rgba(255,255,255,0.6);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card-enhanced::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            animation: rotate 6s linear infinite;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .feature-card-enhanced:hover::after {
            opacity: 1;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .feature-card-enhanced:hover {
            transform: translateY(-15px);
            box-shadow: 
                0 25px 50px rgba(0,0,0,0.15),
                inset 0 1px 0 rgba(255,255,255,0.8);
        }
        
        .feature-icon-enhanced {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #667eea, #764ba2, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradientShift 3s ease-in-out infinite;
            position: relative;
            z-index: 2;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .teacher-card-enhanced {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .teacher-card-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .teacher-card-enhanced:hover::before {
            transform: translateX(0);
        }
        
        .teacher-card-enhanced:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .popular-content-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .popular-content-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #ffecd2);
            border-radius: 22px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .popular-content-card:hover::before {
            opacity: 1;
        }
        
        .popular-content-card:hover {
            transform: translateY(-10px) scale(1.02);
        }
        
        .ranking-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .section-title-enhanced {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #667eea, #764ba2, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradientShift 4s ease-in-out infinite;
            text-align: center;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        
        /* أنماط إضافية للصفحة الرئيسية للزوار */
        .level-card {
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease-in-out;
            z-index: 1;
        }
        
        .level-card:hover {
            transform: translateY(-15px);
        }
        
        .level-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: height 0.3s ease;
            z-index: -1;
        }
        
        .level-card:hover::before {
            height: 10px;
        }
        
        .btn-level {
            transition: all 0.3s ease;
            transform: translateY(5px);
            opacity: 0.9;
        }
        
        .level-card:hover .btn-level {
            transform: translateY(0);
            opacity: 1;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .features-card {
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            border-radius: 15px;
        }
        
        .features-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .feature-icon-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: all 0.3s ease;
        }
        
        .feature-icon-wrapper i {
            font-size: 2.5rem;
            transition: all 0.3s ease;
        }
        
        .features-card:hover .feature-icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }
        
        .feature-list {
            list-style: none;
            padding-left: 5px;
        }
        
        .feature-list li {
            margin-bottom: 8px;
            position: relative;
            padding-left: 5px;
        }
        
        .register-cta {
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.25);
            transition: all 0.3s ease;
        }
        
        .register-cta:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
        }
        
        /* تحسينات إضافية للهواتف */
        @media (max-width: 576px) {
            .hero-section {
                padding: 40px 0;
                border-radius: 0 0 15px 15px;
            }
            
            .hero-section h1 {
                font-size: 1.6rem !important;
            }
            
            .hero-section p.lead {
                font-size: 0.9rem !important;
                margin-bottom: 1rem !important;
            }
            
            .btn-hero {
                padding: 8px 15px !important;
                font-size: 0.85rem !important;
                margin: 5px !important;
                display: inline-block !important;
            }
            
            .section-title {
                font-size: 1.8rem !important;
            }
            
            .feature-card {
                padding: 1.25rem !important;
            }
            
            .feature-icon-wrapper {
                width: 60px;
                height: 60px;
            }
            
            .feature-icon-wrapper i {
                font-size: 1.8rem;
            }
            
            .register-cta {
                padding: 10px 20px !important;
                font-size: 1rem !important;
            }
        }
        
        /* أنماط مخصصة لكل نوع مستخدم */
        
        /* أنماط البطاقات المخصصة للمستويات */
        .level-card-custom {
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            transform: translateY(0);
        }
        
        .level-card-custom:hover {
            transform: translateY(-15px) !important;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15) !important;
        }
        
        .level-card-student {
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            transform: translateY(0);
        }
        
        .level-card-student:hover {
            transform: translateY(-12px) !important;
        }
        
        /* تأثيرات الأيقونات */
        .level-card-custom:hover .level-icon-circle,
        .level-card-student:hover .level-icon-circle {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3) !important;
        }
        
        .level-icon-circle {
            transition: all 0.3s ease;
        }
        
        /* تأثيرات الأزرار */
        .level-btn:hover {
            transform: translateY(-3px) !important;
        }
        
        .level-btn {
            transition: all 0.3s ease;
        }
        
        /* تأثيرات الأزرار المحاطة */
        .level-btn-outline {
            transition: all 0.3s ease;
        }
        
        .level-btn-outline:hover {
            transform: translateY(-2px) !important;
        }
        
        /* تأثيرات خاصة للأزرار حسب المستوى */
        .level-tc .level-btn-outline:hover {
            background: #4f46e5 !important;
            color: white !important;
        }
        
        .level-1bac .level-btn-outline:hover {
            background: #059669 !important;
            color: white !important;
        }
        
        .level-2bac .level-btn-outline:hover {
            background: #dc2626 !important;
            color: white !important;
        }
        
        /* تأثيرات إضافية للبطاقات */
        .level-card-custom::before,
        .level-card-student::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            border-radius: 15px;
        }
        
        .level-card-custom:hover::before,
        .level-card-student:hover::before {
            opacity: 1;
        }
        
        /* تأثيرات خاصة للمستويات */
        .level-tc:hover {
            box-shadow: 0 25px 50px rgba(79, 70, 229, 0.3) !important;
        }
        
        .level-1bac:hover {
            box-shadow: 0 25px 50px rgba(5, 150, 105, 0.3) !important;
        }
        
        .level-2bac:hover {
            box-shadow: 0 25px 50px rgba(220, 38, 38, 0.3) !important;
        }
        
        /* أنماط البطاقات المصغرة للمميزات */
        .mini-feature-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem 1rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .mini-feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .mini-feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .mini-feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .mini-feature-card i {
            font-size: 2rem;
            transition: all 0.3s ease;
        }
        
        .mini-feature-card:hover i {
            transform: scale(1.2);
        }
        
        /* تحسين أنماط البطاقات الرئيسية */
        .feature-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 2;
        }
        
        .feature-badge .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* تأثيرات حركية للبطاقات */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .feature-card {
            animation: slideInUp 0.6s ease-out;
            animation-fill-mode: both;
        }
        
        .feature-card:nth-child(1) { animation-delay: 0.1s; }
        .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .feature-card:nth-child(3) { animation-delay: 0.3s; }
        .feature-card:nth-child(4) { animation-delay: 0.4s; }
        
        .mini-feature-card {
            animation: slideInUp 0.4s ease-out;
            animation-fill-mode: both;
        }
        
        .mini-feature-card:nth-child(1) { animation-delay: 0.5s; }
        .mini-feature-card:nth-child(2) { animation-delay: 0.6s; }
        .mini-feature-card:nth-child(3) { animation-delay: 0.7s; }
        .mini-feature-card:nth-child(4) { animation-delay: 0.8s; }
        .mini-feature-card:nth-child(5) { animation-delay: 0.9s; }
        .mini-feature-card:nth-child(6) { animation-delay: 1.0s; }

        /* 🌟 تصميم خارق للعادة للرسالة الترحيبية 🌟 */
        
        /* الحاوي الرئيسي للترحيب */
        .hero-welcome-container {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 1rem 0;
        }

        /* العنوان الخارق */
        .hero-title-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .hero-title-spectacular {
            font-family: 'Cairo', 'Tajawal', sans-serif;
            margin: 0;
            line-height: 1.2;
            position: relative;
            z-index: 5;
        }

        .title-line-1, .title-line-2, .title-line-3 {
            display: block;
            opacity: 1;
            transform: none;
        }

        .title-line-1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            animation-delay: 0.2s;
            margin-bottom: 0.5rem;
        }

        .title-line-2 {
            font-size: 3.5rem;
            font-weight: 900;
            animation-delay: 0.5s;
            margin-bottom: 0.5rem;
        }

        .title-line-3 {
            font-size: 1.4rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
            animation-delay: 0.8s;
        }

        .atom-icon {
            display: inline-block;
            font-size: 2rem;
            margin-left: 10px;
            filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.8));
        }
        
        .atom-icon .rotating-atom {
            color: #ffd700;
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.4));
        }

        .gradient-text {
            color: #ffd700 !important;
            background: none !important;
            -webkit-background-clip: unset !important;
            -webkit-text-fill-color: #ffd700 !important;
            background-clip: unset !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
            filter: none !important;
        }

        .subtitle-text {
            /* تم إيقاف التأثير لتحسين الأداء */
        }

        /* الجسيمات العائمة */
        .floating-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            display: none;
            /* تم إخفاء الجسيمات لتحسين الأداء */
        }

        .particle-1 { top: 20%; left: 10%; animation-delay: 0s; }
        .particle-2 { top: 40%; right: 15%; animation-delay: 1s; }
        .particle-3 { top: 60%; left: 20%; animation-delay: 2s; }
        .particle-4 { top: 30%; right: 25%; animation-delay: 3s; }
        .particle-5 { top: 70%; left: 70%; animation-delay: 4s; }

        /* الوصف المتحرك */
        .hero-description-animated {
            margin: 1rem 0;
            /* تم إيقاف التأثير لتحسين الأداء */
        }

        .lead-spectacular {
            font-size: 1.4rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* تم حذف CSS الخاص بـ typing-text */

        /* فقاعات المميزات */
        .features-preview {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin: 1rem 0;
        }

        .feature-bubble {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            /* animation: bubbleFloat 3s ease-in-out infinite; - تم إيقاف الحركة */
            transition: all 0.3s ease;
            cursor: pointer;
        }

        /* .feature-bubble:nth-child(1) { animation-delay: 0s; } - تم إيقاف الحركة */
        /* .feature-bubble:nth-child(2) { animation-delay: 0.5s; } - تم إيقاف الحركة */
        /* .feature-bubble:nth-child(3) { animation-delay: 1s; } - تم إيقاف الحركة */

        .feature-bubble:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .feature-bubble i {
            font-size: 1.2rem;
            color: #ffd700;
        }

        /* الأزرار الخارقة */
        .hero-actions-spectacular {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin: 1.5rem 0;
            flex-wrap: wrap;
        }

        .btn-spectacular {
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.2rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            overflow: hidden;
            border: 2px solid transparent;
            backdrop-filter: blur(10px);
            /* animation: slideInUp 1s ease-out 1.5s both; - تم إيقاف الحركة */
        }

        .btn-primary-spectacular {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            color: #333;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary-spectacular {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.9) 0%, rgba(255, 193, 7, 0.8) 100%);
            color: #333;
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        }

        .btn-spectacular:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            color: #333;
        }

        .btn-primary-spectacular:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.9) 100%);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary-spectacular:hover {
            background: linear-gradient(135deg, rgba(255, 215, 0, 1) 0%, rgba(255, 193, 7, 0.9) 100%);
            box-shadow: 0 15px 35px rgba(255, 215, 0, 0.4);
        }

        .btn-icon {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.1);
        }

        .btn-text {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: right;
        }

        .btn-main {
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .btn-sub {
            font-size: 0.85rem;
            opacity: 0.8;
            font-weight: 500;
        }

        .btn-glow {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            /* transition: left 0.6s ease; - تم إيقاف تأثير الوهج */
        }

        /* .btn-spectacular:hover .btn-glow {
            left: 100%;
        } - تم إيقاف تأثير الوهج المتحرك */



        /* الحركات والتأثيرات */
        @keyframes slideInFromLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes atomRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes floatParticle {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
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

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }

        @keyframes blink {
            0%, 50% { 
                border-left-color: #ffd700; 
                border-left-width: 3px;
            }
            51%, 100% { 
                border-left-color: transparent; 
                border-left-width: 3px;
            }
        }

        @keyframes bubbleFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }



        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* تحسينات الاستجابة للجوال */
        @media (max-width: 768px) {
            .title-line-1 { font-size: 1.4rem; }
            .title-line-2 { font-size: 2.5rem; }
            .title-line-3 { font-size: 1.1rem; }
            
            .lead-spectacular { font-size: 1.1rem; }
            
            .features-preview { gap: 1rem; }
            .feature-bubble { 
                padding: 0.8rem 1.2rem; 
                font-size: 0.8rem;
            }
            
            .hero-actions-spectacular { 
                gap: 1rem; 
                flex-direction: column;
                align-items: center;
            }
            
            .btn-spectacular { 
                padding: 1rem 1.5rem; 
                font-size: 0.9rem;
                width: 100%;
                max-width: 300px;
            }
            

        }

        @media (max-width: 576px) {
            .title-line-1 { font-size: 1.2rem; }
            .title-line-2 { font-size: 2rem; }
            .title-line-3 { font-size: 1rem; }
            
            .lead-spectacular { font-size: 1rem; }
            
            .feature-bubble { 
                padding: 0.6rem 1rem; 
                font-size: 0.75rem;
            }
            
            .btn-spectacular { 
                padding: 0.8rem 1.2rem; 
                font-size: 0.85rem;
            }
            
            .btn-icon { 
                width: 40px; 
                height: 40px; 
                font-size: 1.2rem;
            }
            
            .btn-main { font-size: 1rem; }
            .btn-sub { font-size: 0.8rem; }
            

        }
        
        /* تحسينات إضافية للهواتف الصغيرة جداً */
        @media (max-width: 576px) {
            /* بطاقات الإحصائيات للمشرف - أصغر للهواتف الصغيرة */
            .stats-card {
                padding: 0.6rem !important;
            }
            
            .stats-number {
                font-size: 1.2rem !important;
            }
            
            .stats-card h6 {
                font-size: 0.7rem !important;
            }
            
            .stats-card .btn {
                padding: 3px 6px !important;
                font-size: 0.6rem !important;
            }
            
            /* بطاقات أنواع المحتوى - 3 في السطر مع أحجام أصغر */
            .content-types-mobile .feature-card {
                padding: 0.8rem !important;
                min-height: 110px !important;
            }
            
            .content-types-mobile .feature-icon {
                font-size: 1.5rem !important;
            }
            
            .content-types-mobile h6 {
                font-size: 0.75rem !important;
            }
            
            .content-types-mobile p {
                font-size: 0.6rem !important;
            }
            
            .content-types-mobile .small {
                font-size: 0.55rem !important;
            }
            
            /* بطاقات المميزات - أصغر للهواتف الصغيرة */
            .features-mobile .feature-card {
                padding: 1rem !important;
                min-height: 120px !important;
            }
            
            .features-mobile .feature-icon {
                font-size: 1.8rem !important;
            }
            
            .features-mobile h5 {
                font-size: 0.85rem !important;
            }
            
            .features-mobile p {
                font-size: 0.7rem !important;
            }
            
            /* العناوين الرئيسية */
            .section-title {
                font-size: 1.6rem !important;
            }
            
            /* Hero Section للهواتف الصغيرة */
            .title-line-1 {
                font-size: 1rem !important;
            }
            
            .title-line-2 {
                font-size: 1.6rem !important;
            }
            
            .title-line-3 {
                font-size: 0.8rem !important;
            }
            
            .feature-bubble {
                padding: 0.4rem 0.6rem !important;
                font-size: 0.7rem !important;
            }
            
            .btn-spectacular {
                padding: 0.7rem 1rem !important;
                font-size: 0.8rem !important;
            }
            
            /* بطاقات الأساتذة - أصغر للهواتف الصغيرة */
            .teacher-card-homepage {
                padding: 0.8rem !important;
            }
            
            .teacher-avatar-homepage {
                width: 45px !important;
                height: 45px !important;
                font-size: 1.1rem !important;
            }
            
            .teacher-name-homepage {
                font-size: 0.75rem !important;
            }
            
            .teacher-level-homepage {
                font-size: 0.65rem !important;
            }
            
            .teacher-stat-number-homepage {
                font-size: 0.8rem !important;
            }
            
            .teacher-stat-label-homepage {
                font-size: 0.55rem !important;
            }
        }
        
        /* تحسينات إضافية للهواتف الصغيرة */
        @media (max-width: 480px) {
            .hero-section {
                padding: 20px 0 !important;
                margin: 0 -20px !important;
                width: calc(100% + 40px) !important;
                left: -20px !important;
            }
            
            .hero-welcome-container {
                padding: 0.5rem !important;
                width: 100% !important;
            }
        }
        
        /* تحسينات إضافية لجميع الأجهزة المحمولة */
        @media (max-width: 991px) {
            .hero-section {
                margin-left: -15px !important;
                margin-right: -15px !important;
                width: calc(100vw) !important;
                position: relative !important;
            }
            
            .title-line-1 {
                font-size: 1.1rem !important;
            }
            
            .title-line-2 {
                font-size: 2rem !important;
            }
            
            .title-line-3 {
                font-size: 0.9rem !important;
            }
            
            .feature-bubble {
                padding: 0.4rem 0.7rem !important;
                font-size: 0.7rem !important;
            }
            
            .btn-spectacular {
                padding: 0.9rem 1.5rem !important;
                font-size: 0.9rem !important;
            }
        }
        
        /* إضافة تحسينات للخلفية البنفسجية لتغطي العرض الكامل */
        @media (max-width: 768px) {
            /* إزالة أي هوامش من الصفحة */
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                overflow-x: hidden !important;
            }
            
            /* التأكد من عدم وجود هوامش في الحاوي الرئيسي */
            .container-fluid {
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            
            /* جعل الخلفية البنفسجية تمتد للحواف */
            .hero-section {
                margin-left: calc(-50vw + 50%) !important;
                margin-right: calc(-50vw + 50%) !important;
                width: 100vw !important;
                max-width: 100vw !important;
                position: relative !important;
                left: 0 !important;
                right: 0 !important;
                box-sizing: border-box !important;
            }
        }
        
        /* إيقاف جميع التأثيرات المرئية لتحسين الأداء */
        *, *::before, *::after {
            animation-duration: 0s !important;
            animation-delay: 0s !important;
            transition-duration: 0s !important;
            transition-delay: 0s !important;
        }
        
        /* إيقاف تأثيرات الخلفية المتحركة */
        .hero-section::before,
        .hero-section::after {
            animation: none !important;
        }
    </style>

    <!-- Hero Section - مخصص حسب نوع المستخدم -->
    <section class="hero-section <?php echo $user_type; ?>-dashboard <?php echo $user_type !== 'guest' ? 'user-logged' : ''; ?>">
        <div class="container">
            <?php if ($user_type === 'guest'): ?>
            <!-- رسالة ترحيبية خارقة للعادة -->
            <div class="hero-welcome-container">
                <!-- العنوان الرئيسي مع تأثيرات خاصة -->
                <div class="hero-title-wrapper">
                    <div class="floating-particles">
                        <div class="particle particle-1"></div>
                        <div class="particle particle-2"></div>
                        <div class="particle particle-3"></div>
                        <div class="particle particle-4"></div>
                        <div class="particle particle-5"></div>
                    </div>
                    
                    <h1 class="hero-title-spectacular">
                        <span class="title-line-1">
                            <span class="atom-icon"><i class="fas fa-atom rotating-atom"></i></span>
                            <span class="title-text">مرحباً بكم في عالم</span>
                        </span>
                        <span class="title-line-2">
                            <span class="gradient-text">الفيزياء والكيمياء</span>
                        </span>
                        <span class="title-line-3">
                            <span class="subtitle-text">🌟 منصة التعلم الذكية 🌟</span>
                        </span>
                    </h1>
                </div>

                <!-- الوصف التفاعلي -->
                <div class="hero-description-animated">
                    <div class="features-preview">
                        <div class="feature-bubble">
                            <i class="fas fa-microscope"></i>
                            <span>تجارب تفاعلية</span>
                        </div>
                        <div class="feature-bubble">
                            <i class="fas fa-brain"></i>
                            <span>تعلم ذكي</span>
                        </div>
                        <div class="feature-bubble">
                            <i class="fas fa-rocket"></i>
                            <span>محتوى متقدم</span>
                        </div>
                    </div>
                </div>

                <!-- أزرار التفاعل المتطورة -->
                <div class="hero-actions-spectacular">
                    <a href="#levels-section" class="btn-spectacular btn-primary-spectacular">
                        <span class="btn-icon">
                            <i class="fas fa-book-open"></i>
                        </span>
                        <span class="btn-text">
                            <span class="btn-main">استكشف المحتوى</span>
                            <span class="btn-sub">آلاف الدروس في انتظارك</span>
                        </span>
                        <div class="btn-glow"></div>
                    </a>
                    
                    <a href="register.php" class="btn-spectacular btn-secondary-spectacular">
                        <span class="btn-icon">
                            <i class="fas fa-user-graduate"></i>
                        </span>
                        <span class="btn-text">
                            <span class="btn-main">انضم إلى المنصة</span>
                            <span class="btn-sub">ابدأ رحلتك التعليمية</span>
                        </span>
                        <div class="btn-glow"></div>
                    </a>
                </div>


            </div>
            <?php elseif ($user_type === 'student'): ?>
                <div class="user-welcome student-welcome-enhanced">
                    <div class="welcome-content">
                        <div class="welcome-icon mb-3">
                            <div class="student-avatar">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                        <h1 class="welcome-title">
                            <span class="greeting">أهلاً وسهلاً</span>
                            <span class="student-name"><?php echo htmlspecialchars($user_name); ?></span>
                        </h1>
                        <p class="welcome-subtitle">
                            منصتك التعليمية لاستكشاف عالم <span class="highlight-physics">الفيزياء</span> و<span class="highlight-chemistry">الكيمياء</span>
                        </p>
                        <p class="welcome-description">
                            رحلة تعلم ممتعة وتفاعلية تنتظرك مع أفضل المحتويات التعليمية
                        </p>
                        <div class="welcome-actions mt-4">
                            <a href="#student-levels" class="btn btn-primary btn-hero btn-start-learning">
                                <i class="fas fa-rocket me-2"></i>
                                ابدأ التعلم الآن
                            </a>
                            <a href="#browse-teachers" class="btn btn-outline-primary btn-hero btn-browse-teachers">
                                <i class="fas fa-chalkboard-teacher me-2"></i>
                                تصفح الأساتذة
                            </a>
                        </div>
                    </div>
                </div>
            <?php elseif ($user_type === 'teacher'): ?>
                <div class="user-welcome">
                    <h1>👨‍🏫 أهلاً وسهلاً بك أستاذ <?php echo htmlspecialchars($user_name); ?></h1>
                    <p class="lead mb-0">منصتك التعليمية لمشاركة المعرفة وإثراء المحتوى العلمي</p>
                    <div class="mt-3">
                        <a href="teacher/add_content.php" class="btn btn-light btn-hero">
                            <i class="fas fa-plus me-2"></i>
                            إضافة محتوى جديد
                        </a>
                        <a href="teacher/my_content.php" class="btn btn-outline-light btn-hero">
                            <i class="fas fa-folder-open me-2"></i>
                            محتوياتي
                        </a>
                    </div>
                </div>
            <?php elseif ($user_type === 'admin'): ?>
                <div class="user-welcome">
                    <h1>👨‍💼 مرحباً مشرف <?php echo htmlspecialchars($user_name); ?></h1>
                    <p class="lead mb-0">إشراف شامل على المنصة والمحتويات</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($user_type === 'guest'): ?>
    <!-- Levels Section للزوار فقط -->
    <section id="levels-section" class="py-5">
        <div class="container">
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <h2 class="section-title">📚 المستويات التعليمية</h2>
                <p class="text-muted fs-5">اختر مستواك التعليمي لتصفح المحتوى المناسب</p>
            </div>
            
            <div class="row g-4">
                <?php
                $levels = [
                    ['id' => 'tc', 'name' => 'الجذع المشترك', 'description' => 'المستوى الأول من التعليم الثانوي التأهيلي', 'icon' => 'fas fa-graduation-cap'],
                    ['id' => '1bac', 'name' => 'الأولى باكالوريا', 'description' => 'المستوى الثاني من التعليم الثانوي التأهيلي', 'icon' => 'fas fa-user-graduate'],
                    ['id' => '2bac', 'name' => 'الثانية باكالوريا', 'description' => 'المستوى الثالث من التعليم الثانوي التأهيلي', 'icon' => 'fas fa-medal']
                ];
                
                // تطبيق نفس نظام الألوان المستخدم في level.php
                $level_colors = [
                    'tc' => [
                        'primary' => '#4f46e5',
                        'secondary' => '#7c3aed',
                        'light' => '#e0e7ff',
                        'gradient' => 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)',
                        'shadow' => 'rgba(79, 70, 229, 0.4)',
                        'btn_class' => 'btn-tc'
                    ],
                    '1bac' => [
                        'primary' => '#059669',
                        'secondary' => '#10b981',
                        'light' => '#d1fae5',
                        'gradient' => 'linear-gradient(135deg, #059669 0%, #10b981 100%)',
                        'shadow' => 'rgba(5, 150, 105, 0.4)',
                        'btn_class' => 'btn-1bac'
                    ],
                    '2bac' => [
                        'primary' => '#dc2626',
                        'secondary' => '#f59e0b',
                        'light' => '#fef3c7',
                        'gradient' => 'linear-gradient(135deg, #dc2626 0%, #f59e0b 100%)',
                        'shadow' => 'rgba(220, 38, 38, 0.4)',
                        'btn_class' => 'btn-2bac'
                    ]
                ];
                
                foreach ($levels as $index => $level):
                    $level_id = $level['id'];
                    $colors = $level_colors[$level_id];
                    $icon = $level['icon'];
                    $delay = ($index + 1) * 150;
                ?>
                <div class="col-lg-4 col-md-6 col-6 animate__animated animate__fadeInUp" style="animation-delay: <?php echo $delay; ?>ms">
                    <div class="level-card-custom level-<?php echo $level_id; ?>" data-level="<?php echo $level_id; ?>" style="
                        background: <?php echo $colors['light']; ?>;
                        border-left: 4px solid <?php echo $colors['primary']; ?>;
                        border-radius: 15px;
                        overflow: hidden;
                        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                        cursor: pointer;
                        text-decoration: none;
                        color: inherit;
                    " onclick="location.href='level.php?level=<?php echo $level['id']; ?>'">
                        <div style="padding: 2rem; text-align: center;">
                            <div class="level-icon-circle" style="
                                width: 80px;
                                height: 80px;
                                border-radius: 50%;
                                background: <?php echo $colors['gradient']; ?>;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto 1rem auto;
                                box-shadow: 0 10px 25px <?php echo $colors['shadow']; ?>;
                            ">
                                <i class="<?php echo $icon; ?>" style="font-size: 2rem; color: white;"></i>
                            </div>
                            <h5 style="margin-bottom: 1rem; color: <?php echo $colors['primary']; ?>; font-weight: bold;">
                                <?php echo htmlspecialchars($level['name']); ?>
                            </h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; font-size: 0.9rem;">
                                <?php echo htmlspecialchars($level['description']); ?>
                            </p>
                            <div class="level-btn" style="
                                background: <?php echo $colors['gradient']; ?>;
                                color: white;
                                padding: 12px 24px;
                                border-radius: 25px;
                                font-weight: 600;
                                display: inline-flex;
                                align-items: center;
                                border: none;
                                text-decoration: none;
                                box-shadow: 0 5px 15px <?php echo $colors['shadow']; ?>;
                            ">
                                <i class="fas fa-arrow-left me-2"></i>
                                تصفح المحتوى
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php elseif ($user_type === 'student'): ?>
    <!-- Student Dashboard - المحتوى حسب المستويات (نفس عرض الزوار) -->
    <section id="student-levels" class="py-5">
        <div class="container">
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <h2 class="section-title">📚 المستويات التعليمية</h2>
                <p class="text-muted fs-5">اختر مستواك التعليمي لتصفح المحتوى المناسب</p>
            </div>
            
            <div class="row g-4">
                <?php
                $levels = [
                    ['id' => 'tc', 'name' => 'الجذع المشترك', 'description' => 'المستوى الأول من التعليم الثانوي التأهيلي', 'icon' => 'fas fa-graduation-cap'],
                    ['id' => '1bac', 'name' => 'الأولى باكالوريا', 'description' => 'المستوى الثاني من التعليم الثانوي التأهيلي', 'icon' => 'fas fa-user-graduate'],
                    ['id' => '2bac', 'name' => 'الثانية باكالوريا', 'description' => 'المستوى الثالث من التعليم الثانوي التأهيلي', 'icon' => 'fas fa-medal']
                ];
                
                // تطبيق نفس نظام الألوان المستخدم في level.php
                $level_colors = [
                    'tc' => [
                        'primary' => '#4f46e5',
                        'secondary' => '#7c3aed',
                        'light' => '#e0e7ff',
                        'gradient' => 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)',
                        'shadow' => 'rgba(79, 70, 229, 0.4)',
                        'btn_class' => 'btn-tc'
                    ],
                    '1bac' => [
                        'primary' => '#059669',
                        'secondary' => '#10b981',
                        'light' => '#d1fae5',
                        'gradient' => 'linear-gradient(135deg, #059669 0%, #10b981 100%)',
                        'shadow' => 'rgba(5, 150, 105, 0.4)',
                        'btn_class' => 'btn-1bac'
                    ],
                    '2bac' => [
                        'primary' => '#dc2626',
                        'secondary' => '#f59e0b',
                        'light' => '#fef3c7',
                        'gradient' => 'linear-gradient(135deg, #dc2626 0%, #f59e0b 100%)',
                        'shadow' => 'rgba(220, 38, 38, 0.4)',
                        'btn_class' => 'btn-2bac'
                    ]
                ];
                
                foreach ($levels as $index => $level):
                    $level_id = $level['id'];
                    $colors = $level_colors[$level_id];
                    $icon = $level['icon'];
                    $delay = ($index + 1) * 150;
                ?>
                <div class="col-lg-4 col-md-6 col-6 animate__animated animate__fadeInUp" style="animation-delay: <?php echo $delay; ?>ms">
                    <div class="level-card-custom level-<?php echo $level_id; ?>" data-level="<?php echo $level_id; ?>" style="
                        background: <?php echo $colors['light']; ?>;
                        border-left: 4px solid <?php echo $colors['primary']; ?>;
                        border-radius: 15px;
                        overflow: hidden;
                        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                        cursor: pointer;
                        text-decoration: none;
                        color: inherit;
                    " onclick="location.href='level.php?level=<?php echo $level['id']; ?>'">
                        <div style="padding: 2rem; text-align: center;">
                            <div class="level-icon-circle" style="
                                width: 80px;
                                height: 80px;
                                border-radius: 50%;
                                background: <?php echo $colors['gradient']; ?>;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto 1rem auto;
                                box-shadow: 0 10px 25px <?php echo $colors['shadow']; ?>;
                            ">
                                <i class="<?php echo $icon; ?>" style="font-size: 2rem; color: white;"></i>
                            </div>
                            <h5 style="margin-bottom: 1rem; color: <?php echo $colors['primary']; ?>; font-weight: bold;">
                                <?php echo htmlspecialchars($level['name']); ?>
                            </h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; font-size: 0.9rem;">
                                <?php echo htmlspecialchars($level['description']); ?>
                            </p>
                            <div class="level-btn" style="
                                background: <?php echo $colors['gradient']; ?>;
                                color: white;
                                padding: 12px 24px;
                                border-radius: 25px;
                                font-weight: 600;
                                display: inline-flex;
                                align-items: center;
                                border: none;
                                text-decoration: none;
                                box-shadow: 0 5px 15px <?php echo $colors['shadow']; ?>;
                            ">
                                <i class="fas fa-arrow-left me-2"></i>
                                تصفح المحتوى
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php endif; ?>

    <?php if ($user_type === 'teacher'): ?>
    <!-- قسم المستويات التعليمية للأستاذ أيضاً -->
    <section id="teacher-levels" class="py-5">
        <div class="container">
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <h2 class="section-title">📚 المستويات التعليمية</h2>
                <p class="text-muted fs-5">تصفح المحتوى التعليمي حسب المستويات</p>
            </div>
            
            <div class="row g-4">
                <?php
                $levels = [
                    ['id' => 'tc', 'name' => 'الجذع المشترك', 'description' => 'المستوى الأول من التعليم الثانوي التأهيلي', 'icon' => 'fas fa-graduation-cap'],
                    ['id' => '1bac', 'name' => 'الأولى باكالوريا', 'description' => 'المستوى الثاني من التعليم الثانوي التأهيلي', 'icon' => 'fas fa-user-graduate'],
                    ['id' => '2bac', 'name' => 'الثانية باكالوريا', 'description' => 'المستوى الثالث من التعليم الثانوي التأهيلي', 'icon' => 'fas fa-medal']
                ];
                
                // تطبيق نفس نظام الألوان المستخدم في level.php
                $level_colors = [
                    'tc' => [
                        'primary' => '#4f46e5',
                        'secondary' => '#7c3aed',
                        'light' => '#e0e7ff',
                        'gradient' => 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)',
                        'shadow' => 'rgba(79, 70, 229, 0.4)',
                        'btn_class' => 'btn-tc'
                    ],
                    '1bac' => [
                        'primary' => '#059669',
                        'secondary' => '#10b981',
                        'light' => '#d1fae5',
                        'gradient' => 'linear-gradient(135deg, #059669 0%, #10b981 100%)',
                        'shadow' => 'rgba(5, 150, 105, 0.4)',
                        'btn_class' => 'btn-1bac'
                    ],
                    '2bac' => [
                        'primary' => '#dc2626',
                        'secondary' => '#f59e0b',
                        'light' => '#fef3c7',
                        'gradient' => 'linear-gradient(135deg, #dc2626 0%, #f59e0b 100%)',
                        'shadow' => 'rgba(220, 38, 38, 0.4)',
                        'btn_class' => 'btn-2bac'
                    ]
                ];
                
                foreach ($levels as $index => $level):
                    $level_id = $level['id'];
                    $colors = $level_colors[$level_id];
                    $icon = $level['icon'];
                    $delay = ($index + 1) * 150;
                ?>
                <div class="col-lg-4 col-md-6 col-6 animate__animated animate__fadeInUp" style="animation-delay: <?php echo $delay; ?>ms">
                    <div class="level-card-custom level-<?php echo $level_id; ?>" data-level="<?php echo $level_id; ?>" style="
                        background: <?php echo $colors['light']; ?>;
                        border-left: 4px solid <?php echo $colors['primary']; ?>;
                        border-radius: 15px;
                        overflow: hidden;
                        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                        cursor: pointer;
                        text-decoration: none;
                        color: inherit;
                    " onclick="location.href='level.php?level=<?php echo $level['id']; ?>'">
                        <div style="padding: 2rem; text-align: center;">
                            <div class="level-icon-circle" style="
                                width: 80px;
                                height: 80px;
                                border-radius: 50%;
                                background: <?php echo $colors['gradient']; ?>;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto 1rem auto;
                                box-shadow: 0 10px 25px <?php echo $colors['shadow']; ?>;
                            ">
                                <i class="<?php echo $icon; ?>" style="font-size: 2rem; color: white;"></i>
                            </div>
                            <h5 style="margin-bottom: 1rem; color: <?php echo $colors['primary']; ?>; font-weight: bold;">
                                <?php echo htmlspecialchars($level['name']); ?>
                            </h5>
                            <p style="color: #6c757d; margin-bottom: 1.5rem; font-size: 0.9rem;">
                                <?php echo htmlspecialchars($level['description']); ?>
                            </p>
                            <div class="level-btn" style="
                                background: <?php echo $colors['gradient']; ?>;
                                color: white;
                                padding: 12px 24px;
                                border-radius: 25px;
                                font-weight: 600;
                                display: inline-flex;
                                align-items: center;
                                border: none;
                                text-decoration: none;
                                box-shadow: 0 5px 15px <?php echo $colors['shadow']; ?>;
                            ">
                                <i class="fas fa-arrow-left me-2"></i>
                                تصفح المحتوى
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- صفحة الأستاذ الرئيسية -->
    
    <!-- إحصائيات الأستاذ -->
    <section class="py-5 teacher-stats-section">
        <div class="container position-relative">
            <div class="text-center mb-5">
                <h2 class="section-title">📊 إحصائياتك الشخصية</h2>
                <p class="text-muted fs-5">نظرة سريعة على إنجازاتك التعليمية</p>
            </div>
            <div class="row g-4">
                <?php
                // جلب إحصائيات شاملة للأستاذ من قاعدة البيانات
                $my_content_count = 0;
                $my_views_count = 0;
                $pending_count = 0;
                $published_count = 0;
                
                try {
                    // عدد محتوياتي الإجمالي
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM content WHERE teacher_id = ?");
                    $stmt->execute([$user_id]);
                    $my_content_count = $stmt->fetchColumn();
                    
                    // عدد المحتويات المنشورة
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM content WHERE teacher_id = ? AND status = 'published'");
                    $stmt->execute([$user_id]);
                    $published_count = $stmt->fetchColumn();
                    
                    // المحتوى في انتظار المراجعة
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM content WHERE teacher_id = ? AND status = 'pending'");
                    $stmt->execute([$user_id]);
                    $pending_count = $stmt->fetchColumn();
                    
                    // حساب المشاهدات (افتراضي حتى يتم إضافة نظام المشاهدات)
                    $my_views_count = $published_count * rand(10, 50);
                    
                } catch (PDOException $e) {
                    // في حالة خطأ، استخدم القيم الافتراضية
                }
                ?>
                
                <div class="col-lg-3 col-md-6 col-3">
                    <div class="stats-card-enhanced">
                        <div class="feature-icon-enhanced text-primary mb-3">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stats-number-enhanced">
                            <?php echo $my_content_count; ?>
                        </div>
                        <h6 class="fw-bold text-dark">إجمالي المحتويات</h6>
                        <small class="text-muted">جميع محتوياتك التعليمية</small>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 col-3">
                    <div class="stats-card-enhanced">
                        <div class="feature-icon-enhanced text-success mb-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stats-number-enhanced">
                            <?php echo $published_count; ?>
                        </div>
                        <h6 class="fw-bold text-dark">المحتويات المنشورة</h6>
                        <small class="text-muted">متاحة للطلاب الآن</small>
                    </div>
                </div>
            
                <div class="col-lg-3 col-md-6 col-3">
                    <div class="stats-card-enhanced">
                        <div class="feature-icon-enhanced text-warning mb-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-number-enhanced">
                            <?php echo $pending_count; ?>
                        </div>
                        <h6 class="fw-bold text-dark">في انتظار المراجعة</h6>
                        <small class="text-muted">قيد المراجعة الإدارية</small>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 col-3">
                    <div class="stats-card-enhanced">
                        <div class="feature-icon-enhanced text-info mb-3">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stats-number-enhanced">
                            <?php echo number_format($my_views_count); ?>
                        </div>
                        <h6 class="fw-bold text-dark">إجمالي المشاهدات</h6>
                        <small class="text-muted">تأثيرك التعليمي</small>
                    </div>
                </div>
                </div>
            </div>
    
    <!-- قسم الزملاء الأساتذة -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <h2 class="section-title">👥 تصفح حسب الزملاء</h2>
                <p class="text-muted fs-5">استكشف محتويات زملائك الأساتذة المتميزين</p>
            </div>
            
            <?php
            try {
                // جلب الأساتذة مع عدد المحتوى لكل منهم (بنفس طريقة عرض الطلاب)
                $stmt = $pdo->prepare("
                    SELECT u.id, u.username, u.full_name,
                           COUNT(DISTINCT c.id) as content_count,
                           COUNT(DISTINCT CASE WHEN ct.code = 'lessons' THEN c.id END) as lessons_count,
                           COUNT(DISTINCT CASE WHEN ct.code = 'experiments' THEN c.id END) as experiments_count
                    FROM users u
                    LEFT JOIN content c ON u.id = c.teacher_id AND c.status = 'published'
                    LEFT JOIN content_types ct ON c.type_id = ct.id
                    WHERE u.user_type = 'teacher' AND u.status = 'active' AND u.id != ?
                    GROUP BY u.id, u.username, u.full_name
                    HAVING content_count > 0
                    ORDER BY content_count DESC
                    LIMIT 8
                ");
                $stmt->execute([$user_id]);
                $teachers = $stmt->fetchAll();
                
                if (!empty($teachers)):
            ?>
                <div class="teachers-grid-homepage">
                    <?php foreach ($teachers as $teacher): ?>
                        <a href="teachers_lessons.php?teacher_id=<?php echo $teacher['id']; ?>&level=all&type=lessons" class="teacher-card-homepage">
                            <div class="teacher-avatar-homepage">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            
                            <div class="teacher-name-homepage">
                                <span class="teacher-title-homepage">زميل</span><br>
                                <?php 
                                $display_name = !empty($teacher['full_name']) ? $teacher['full_name'] : $teacher['username'];
                                echo htmlspecialchars($display_name);
                                ?>
                            </div>
                            
                            <div class="teacher-level-homepage">
                                <i class="fas fa-flask me-1"></i>
                                الفيزياء والكيمياء
                            </div>
                            
                            <div class="teacher-stats-homepage">
                                <div class="teacher-stat-homepage">
                                    <span class="teacher-stat-number-homepage"><?php echo $teacher['content_count']; ?></span>
                                    <small class="teacher-stat-label-homepage">محتوى</small>
                                </div>
                                <div class="teacher-stat-homepage">
                                    <span class="teacher-stat-number-homepage"><?php echo $teacher['lessons_count']; ?></span>
                                    <small class="teacher-stat-label-homepage">دروس</small>
                                </div>
                                <div class="teacher-stat-homepage">
                                    <span class="teacher-stat-number-homepage"><?php echo $teacher['experiments_count']; ?></span>
                                    <small class="teacher-stat-label-homepage">تجارب</small>
                                </div>
                            </div>

                            <div class="teacher-cta-homepage">
                                <i class="fas fa-arrow-left me-2"></i>
                                تصفح المحتوى
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <?php 
                // عرض زر "عرض جميع الأساتذة" فقط إذا كان العدد أكثر من 6
                $total_teachers_query = "SELECT COUNT(DISTINCT u.id) as total_count
                                        FROM users u
                                        LEFT JOIN content c ON u.id = c.teacher_id AND c.status = 'published'
                                        WHERE u.user_type = 'teacher' AND u.status = 'active' AND u.id != ?
                                        AND EXISTS (SELECT 1 FROM content WHERE teacher_id = u.id AND status = 'published')";
                $stmt = $pdo->prepare($total_teachers_query);
                $stmt->execute([$user_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $total_teachers_count = $result['total_count'];
                
                if ($total_teachers_count > 6): ?>
                <div class="text-center mt-4">
                    <a href="all_teachers.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-users me-2"></i>عرض جميع الزملاء (<?php echo $total_teachers_count; ?>)
                    </a>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4>لا يوجد زملاء متاحون حالياً</h4>
                    <p class="text-muted">سيتم إضافة المزيد من الزملاء قريباً</p>
                </div>
            <?php endif; ?>
            <?php } catch (PDOException $e) { ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    خطأ في جلب بيانات الزملاء
                </div>
            <?php } ?>
        </div>
    </section>
    

    <?php endif; ?>

    <?php if ($user_type === 'admin'): ?>
    <!-- Admin Dashboard -->
    <section class="py-5">
        <div class="container">
            <?php
            // جلب إحصائيات شاملة للمشرف
            try {
                $admin_stats_query = "
                    SELECT 
                        (SELECT COUNT(*) FROM content) as total_content,
                        (SELECT COUNT(*) FROM users WHERE user_type != 'admin') as total_users,
                        (SELECT COUNT(*) FROM users WHERE user_type = 'teacher' AND status = 'active') as total_teachers,
                        (SELECT COALESCE(SUM(views_count), 0) FROM content) as total_views,
                        (SELECT COUNT(*) FROM content c JOIN content_types ct ON c.type_id = ct.id WHERE ct.code = 'experiments') as total_experiments
                ";
                $admin_stats_stmt = $pdo->prepare($admin_stats_query);
                $admin_stats_stmt->execute();
                $admin_stats = $admin_stats_stmt->fetch(PDO::FETCH_ASSOC);
                $admin_stats_stmt->closeCursor();
            } catch (Exception $e) {
                $admin_stats = [
                    'total_content' => 0,
                    'total_users' => 0, 
                    'total_teachers' => 0,
                    'total_views' => 0,
                    'total_experiments' => 0
                ];
            }
            ?>
            
            <!-- إحصائيات شاملة للمشرف -->
            <div class="row g-4 mb-5 admin-stats-mobile">
                <div class="col-6 col-md-2">
                    <div class="stats-card">
                        <div class="stats-number text-primary"><?php echo number_format($admin_stats['total_content']); ?></div>
                        <h6 class="text-muted">المحتويات</h6>
                        <a href="admin/dashboard.php" class="btn btn-outline-primary btn-sm mt-2">إدارة</a>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="stats-card">
                        <div class="stats-number text-success"><?php echo number_format($admin_stats['total_users']); ?></div>
                        <h6 class="text-muted">المستخدمين</h6>
                        <a href="admin/users.php" class="btn btn-outline-success btn-sm mt-2">إدارة</a>
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <div class="stats-card">
                        <div class="stats-number text-info"><?php echo number_format($admin_stats['total_teachers']); ?></div>
                        <h6 class="text-muted">الأساتذة</h6>
                        <a href="admin/users.php?user_type=teacher" class="btn btn-outline-info btn-sm mt-2">عرض</a>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="stats-card">
                        <div class="stats-number text-secondary"><?php echo number_format($admin_stats['total_views']); ?></div>
                        <h6 class="text-muted">المشاهدات</h6>
                        <small class="text-muted">إجمالي</small>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="stats-card">
                        <div class="stats-number text-dark"><?php echo number_format($admin_stats['total_experiments']); ?></div>
                        <h6 class="text-muted">التجارب</h6>
                        <a href="admin/dashboard.php" class="btn btn-outline-dark btn-sm mt-2">إدارة</a>
                    </div>
                </div>
            </div>
            
            <!-- إجراءات سريعة للمشرف -->
            <div class="text-center mb-4">
                <h4>⚡ إجراءات إدارية سريعة</h4>
            </div>
            <div class="row g-3 mb-5 admin-actions-mobile">
                <div class="col-6 col-md-2">
                    <div class="quick-action-card" onclick="location.href='admin/dashboard.php'">
                        <i class="fas fa-tachometer-alt text-primary" style="font-size: 2rem;"></i>
                        <h6 class="mt-2 mb-0">لوحة التحكم</h6>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="quick-action-card" onclick="location.href='admin/users.php'">
                        <i class="fas fa-users text-success" style="font-size: 2rem;"></i>
                        <h6 class="mt-2 mb-0">إدارة المستخدمين</h6>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="quick-action-card" onclick="location.href='admin/reports.php'">
                        <i class="fas fa-chart-bar text-warning" style="font-size: 2rem;"></i>
                        <h6 class="mt-2 mb-0">التقارير</h6>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="quick-action-card" onclick="location.href='admin/settings.php'">
                        <i class="fas fa-cog text-info" style="font-size: 2rem;"></i>
                        <h6 class="mt-2 mb-0">إعدادات النظام</h6>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="quick-action-card" onclick="location.href='admin/google_drive_monitor.php'">
                        <i class="fas fa-cloud text-danger" style="font-size: 2rem;"></i>
                        <h6 class="mt-2 mb-0">Google Drive</h6>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="quick-action-card" onclick="location.href='profile.php'">
                        <i class="fas fa-user-shield text-secondary" style="font-size: 2rem;"></i>
                        <h6 class="mt-2 mb-0">الملف الشخصي</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Content Types Section لجميع المستخدمين -->
    <section class="py-5 bg-gradient">
        <div class="container">
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <h2 class="section-title">🎯 أنواع المحتوى</h2>
                <p class="text-muted fs-5">تنوع في المواد التعليمية لتناسب احتياجاتك</p>
            </div>
            
            <div class="row g-4 justify-content-center content-types-mobile">
                <?php
                $content_types = [
                    ['type' => 'lessons', 'icon' => 'fas fa-book-open', 'title' => 'الدروس', 'desc' => 'محتوى تعليمي للدروس والشروحات', 'color' => 'info'],
                    ['type' => 'interactive', 'icon' => 'fas fa-play-circle', 'title' => 'الدروس التفاعلية', 'desc' => 'دروس تفاعلية مع محاكيات وأنشطة', 'color' => 'primary'],
                    ['type' => 'exercises', 'icon' => 'fas fa-pencil-alt', 'title' => 'التمارين', 'desc' => 'تمارين تطبيقية لتعزيز المفاهيم', 'color' => 'warning'],
                    ['type' => 'assignments', 'icon' => 'fas fa-clipboard-check', 'title' => 'الفروض', 'desc' => 'فروض المراقبة المستمرة', 'color' => 'success'],
                    ['type' => 'exams', 'icon' => 'fas fa-file-alt', 'title' => 'الامتحانات', 'desc' => 'امتحانات وطنية ومحلية', 'color' => 'secondary'],
                    ['type' => 'experiments', 'icon' => 'fas fa-vial', 'title' => 'التجارب', 'desc' => 'تجارب مخبرية وأنشطة عملية', 'color' => 'danger']
                ];
                
                foreach ($content_types as $index => $type):
                    $delay = ($index + 1) * 100;
                ?>
                <div class="col-6 col-md-4 col-lg-2 animate__animated animate__zoomIn" style="animation-delay: <?php echo $delay; ?>ms">
                    <a href="level.php?type=<?php echo $type['type']; ?>" class="text-decoration-none">
                        <div class="feature-card text-center">
                            <div class="feature-icon text-<?php echo $type['color']; ?>">
                                <i class="<?php echo $type['icon']; ?>"></i>
                            </div>
                            <h6><?php echo $type['title']; ?></h6>
                            <p class="text-muted small"><?php echo $type['desc']; ?></p>
                            <div class="mt-2 small text-<?php echo $type['color']; ?>">
                                <span>عرض المحتوى</span>
                                <i class="fas fa-chevron-left ms-1"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($user_type === 'guest'): ?>

    <!-- Features Section للزوار فقط -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">✨ مميزات المنصة</h2>
                <p class="text-muted fs-5">اكتشف الإمكانيات المتقدمة لمنصتنا التعليمية</p>
            </div>
            
            <!-- المميزات الرئيسية -->
            <div class="row g-4 mb-5 features-mobile">
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon text-primary mb-3">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <h5 class="fw-bold">دروس تفاعلية</h5>
                        <p class="text-muted">محتوى تفاعلي مع فيديوهات وأنشطة تطبيقية</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon text-success mb-3">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h5 class="fw-bold">تجارب مخبرية</h5>
                        <p class="text-muted">تجارب افتراضية ومحاكاة للتجارب الفيزيائية</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon text-warning mb-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="fw-bold">رسم المبيانات</h5>
                        <p class="text-muted">أدوات متقدمة لرسم وتحليل المبيانات</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon text-info mb-3">
                            <i class="fas fa-video"></i>
                        </div>
                        <h5 class="fw-bold">فصول افتراضية</h5>
                        <p class="text-muted">حضور الحصص والدروس عبر الإنترنت بشكل تفاعلي</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon text-success mb-3">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h5 class="fw-bold">تصميم متجاوب</h5>
                        <p class="text-muted">جميع الأجهزة</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon text-warning mb-3">
                            <i class="fas fa-star"></i>
                        </div>
                        <h5 class="fw-bold">محتوى مجاني</h5>
                        <p class="text-muted">بدون رسوم</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon text-danger mb-3">
                            <i class="fas fa-download"></i>
                        </div>
                        <h5 class="fw-bold">تحميل المواد</h5>
                        <p class="text-muted">PDF & DOC</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="feature-card h-100">
                        <div class="feature-icon text-info mb-3">
                            <i class="fas fa-search"></i>
                        </div>
                        <h5 class="fw-bold">بحث متقدم</h5>
                        <p class="text-muted">سريع ودقيق</p>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <?php endif; ?>

    <!-- Teachers Section للطلاب فقط -->
    <?php if ($user_type === 'student'): ?>
    <section id="browse-teachers" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <h2 class="section-title">👨‍🏫 تصفح حسب الأساتذة</h2>
                <p class="text-muted fs-5">اختر الأستاذ المناسب لتصفح محتواه التعليمي</p>
            </div>
            
            <?php
            // جلب الأساتذة مع عدد المحتوى لكل منهم
            $teachers_query = "SELECT u.id, u.username, u.full_name,
                                     COUNT(DISTINCT c.id) as content_count,
                                     COUNT(DISTINCT CASE WHEN ct.code = 'lessons' THEN c.id END) as lessons_count,
                                     COUNT(DISTINCT CASE WHEN ct.code = 'experiments' THEN c.id END) as experiments_count
                              FROM users u
                              LEFT JOIN content c ON u.id = c.teacher_id AND c.status = 'published'
                              LEFT JOIN content_types ct ON c.type_id = ct.id
                              WHERE u.user_type = 'teacher' AND u.status = 'active'
                              GROUP BY u.id, u.username, u.full_name
                              HAVING content_count > 0
                              ORDER BY content_count DESC
                              LIMIT 8";
            
            $stmt = $pdo->prepare($teachers_query);
            $stmt->execute();
            $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            
            <?php if (!empty($teachers)): ?>
                <div class="teachers-grid-homepage">
                    <?php foreach ($teachers as $teacher): ?>
                        <a href="teachers_lessons.php?teacher_id=<?php echo $teacher['id']; ?>&level=all&type=lessons" class="teacher-card-homepage">
                            <div class="teacher-avatar-homepage">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            
                            <div class="teacher-name-homepage">
                                <span class="teacher-title-homepage">أستاذ</span><br>
                                <?php 
                                $display_name = !empty($teacher['full_name']) ? $teacher['full_name'] : $teacher['username'];
                                echo htmlspecialchars($display_name);
                                ?>
                            </div>
                            
                            <div class="teacher-level-homepage">
                                <i class="fas fa-flask me-1"></i>
                                الفيزياء والكيمياء
                            </div>
                            
                            <div class="teacher-stats-homepage">
                                <div class="teacher-stat-homepage">
                                    <span class="teacher-stat-number-homepage"><?php echo $teacher['content_count']; ?></span>
                                    <small class="teacher-stat-label-homepage">محتوى</small>
                                </div>
                                <div class="teacher-stat-homepage">
                                    <span class="teacher-stat-number-homepage"><?php echo $teacher['lessons_count']; ?></span>
                                    <small class="teacher-stat-label-homepage">دروس</small>
                                </div>
                                <div class="teacher-stat-homepage">
                                    <span class="teacher-stat-number-homepage"><?php echo $teacher['experiments_count']; ?></span>
                                    <small class="teacher-stat-label-homepage">تجارب</small>
                                </div>
                            </div>

                            <div class="teacher-cta-homepage">
                                <i class="fas fa-arrow-left me-2"></i>
                                تصفح المحتوى
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <?php 
                // عرض زر "عرض جميع الأساتذة" فقط إذا كان العدد أكثر من 6
                $total_teachers_query = "SELECT COUNT(DISTINCT u.id) as total_count
                                        FROM users u
                                        LEFT JOIN content c ON u.id = c.teacher_id AND c.status = 'published'
                                        WHERE u.user_type = 'teacher' AND u.status = 'active'
                                        AND EXISTS (SELECT 1 FROM content WHERE teacher_id = u.id AND status = 'published')";
                $stmt = $pdo->prepare($total_teachers_query);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $total_teachers_count = $result['total_count'];
                
                if ($total_teachers_count > 6): ?>
                <div class="text-center mt-4">
                    <a href="all_teachers.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-users me-2"></i>عرض جميع الأساتذة (<?php echo $total_teachers_count; ?>)
                    </a>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4>لا يوجد أساتذة متاحون حالياً</h4>
                    <p class="text-muted">سيتم إضافة الأساتذة قريباً</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <style>
    .teachers-grid-homepage {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .teacher-card-homepage {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border-left: 5px solid #007bff;
        position: relative;
        overflow: hidden;
        transform: scale(0.95);
        opacity: 1;
    }

    .teacher-card-homepage::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }

    .teacher-card-homepage:hover {
        transform: scale(0.98);
        box-shadow: 0 15px 35px rgba(0, 123, 255, 0.2);
        text-decoration: none;
        color: inherit;
    }

    .teacher-avatar-homepage {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
        box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
    }

    .teacher-name-homepage {
        font-size: 1.1rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 0.4rem;
        color: #2d3748;
    }

    .teacher-name-homepage .teacher-title-homepage {
        color: #007bff;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .teacher-level-homepage {
        text-align: center;
        color: #007bff;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 0.85rem;
    }

    .teacher-stats-homepage {
        display: flex;
        justify-content: space-around;
        margin-bottom: 1rem;
        padding: 0.8rem 0;
        background: rgba(0, 123, 255, 0.05);
        border-radius: 12px;
    }

    .teacher-stat-homepage {
        text-align: center;
        flex: 1;
    }

    .teacher-stat-number-homepage {
        display: block;
        font-size: 1.2rem;
        font-weight: 700;
        color: #007bff;
        line-height: 1;
    }

    .teacher-stat-label-homepage {
        font-size: 0.7rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    .teacher-cta-homepage {
        text-align: center;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 0.6rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.3s ease;
    }

    .teacher-card-homepage:hover .teacher-cta-homepage {
        transform: scale(1.05);
    }

    @media (max-width: 768px) {
        .teachers-grid-homepage {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .teacher-card-homepage {
            padding: 1rem;
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 200px;
        }
        
        .teacher-avatar-homepage {
            width: 40px;
            height: 40px;
            font-size: 1rem;
            margin: 0 auto 0.5rem;
        }
        
        .teacher-name-homepage {
            font-size: 0.8rem;
            margin-bottom: 0.3rem;
            line-height: 1.2;
        }
        
        .teacher-name-homepage .teacher-title-homepage {
            font-size: 0.7rem;
        }
        
        .teacher-level-homepage {
            font-size: 0.7rem;
            margin-bottom: 0.5rem;
        }
        
        .teacher-stats-homepage {
            flex-direction: row;
            gap: 0;
            padding: 0.5rem 0;
            margin-bottom: 0.5rem;
        }
        
        .teacher-stat-number-homepage {
            font-size: 0.9rem;
        }
        
        .teacher-stat-label-homepage {
            font-size: 0.6rem;
        }
        
        .teacher-cta-homepage {
            padding: 0.4rem 0.5rem;
            font-size: 0.7rem;
            border-radius: 15px;
        }
    }
    </style>
    <?php endif; ?>

</div>
<!-- إغلاق container-fluid -->

<!-- JavaScript للتأثيرات الخارقة -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    
    // تم إيقاف تأثيرات بطاقات الأساتذة لتحسين الأداء
    const teacherCards = document.querySelectorAll('.teacher-card-homepage');
    teacherCards.forEach((card) => {
        card.style.opacity = '1';
        card.style.transform = 'none';
    });

    // تأثير الجسيمات العائمة المتقدم
    function createAdvancedParticles() {
        const particlesContainer = document.querySelector('.floating-particles');
        if (!particlesContainer) return;
        
        // إضافة جسيمات إضافية
        for (let i = 6; i <= 15; i++) {
            const particle = document.createElement('div');
            particle.className = `particle particle-${i}`;
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 6 + 4}px;
                height: ${Math.random() * 6 + 4}px;
                background: radial-gradient(circle, rgba(255, 255, 255, ${Math.random() * 0.5 + 0.3}) 0%, rgba(255, 255, 255, 0.1) 100%);
                border-radius: 50%;
                top: ${Math.random() * 100}%;
                left: ${Math.random() * 100}%;
                animation: floatParticle ${Math.random() * 4 + 4}s ease-in-out infinite;
                animation-delay: ${Math.random() * 5}s;
            `;
            particlesContainer.appendChild(particle);
        }
    }
    
    // createAdvancedParticles(); // تم إيقافها لتحسين الأداء
    
    // تم حذف دالة typeWriter
    
    // تم حذف الجملة التدريجية نهائياً
    
    // تأثيرات الأزرار المتقدمة - تم إيقافها لجعل الأزرار ثابتة
    /*
    document.querySelectorAll('.btn-spectacular').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.05)';
            
            // إضافة تأثير الموجة
            const ripple = document.createElement('div');
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (rect.width / 2 - size / 2) + 'px';
            ripple.style.top = (rect.height / 2 - size / 2) + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // تأثير الفقاعات التفاعلي
    document.querySelectorAll('.feature-bubble').forEach(bubble => {
        bubble.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.05)';
            this.style.background = 'rgba(255, 255, 255, 0.25)';
            this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.2)';
        });
        
        bubble.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.background = 'rgba(255, 255, 255, 0.15)';
            this.style.boxShadow = 'none';
        });
    });
    */
    
    // تم إيقاف تأثير التدرج المتحرك للنص لتحسين الأداء
    const gradientText = document.querySelector('.gradient-text');
    if (gradientText) {
        // لون ثابت بدلاً من المتحرك
        gradientText.style.background = '#ffd700';
        gradientText.style.backgroundClip = 'text';
        gradientText.style.webkitBackgroundClip = 'text';
        gradientText.style.webkitTextFillColor = 'transparent';
    }
    
    // تم إيقاف تأثير الظلال المتحركة للعنوان لتحسين الأداء
    const heroTitle = document.querySelector('.hero-title-spectacular');
    if (heroTitle) {
        // ظل ثابت بدلاً من المتحرك
        heroTitle.style.textShadow = '0 2px 10px rgba(0,0,0,0.3)';
    }
    
    // تم إيقاف تأثيرات التمرير لتحسين الأداء
    // window.addEventListener('scroll', () => { ... }); // معطل
    
    // تمرير سلس للروابط الداخلية
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            // تأكد من وجود معرف بعد الرمز #
            if (href && href.length > 1) {
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // إضافة CSS للتأثيرات الإضافية
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .hero-welcome-container {
            /* transition معطل لتحسين الأداء */
        }
        
        .particle {
            /* transition معطل لتحسين الأداء */
        }
        
        /* تأثير الوهج للأزرار - تم إيقافه */
        /*
        .btn-spectacular::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            border-radius: inherit;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .btn-spectacular:hover::before {
            opacity: 1;
        }
        */
        
        /* تمرير سلس للصفحة */
        html {
            scroll-behavior: smooth;
        }

    `;
    document.head.appendChild(style);
});
</script>

<?php include 'includes/footer.php'; ?>
