<?php
/**
 * تحقيق شامل حول مشكلة تضاعف الملفات في Google Drive
 */

require_once 'includes/config.php';
require_once 'includes/connection.php';

echo "<h2>🔍 تحقيق مشكلة تضاعف الملفات</h2>";

echo "<div style='max-width: 1000px; margin: 20px auto; font-family: Arial;'>";

try {
    // 1. فحص جدول content_files للملفات المحفوظة
    echo "<h3>1️⃣ فحص الملفات في content_files</h3>";
    
    $stmt = $pdo->prepare("
        SELECT 
            cf.id, cf.content_id, cf.file_name, cf.file_path, 
            cf.google_drive_id, cf.storage_type, cf.is_primary, cf.created_at,
            c.title as content_title
        FROM content_files cf
        JOIN content c ON cf.content_id = c.id  
        ORDER BY cf.content_id DESC, cf.created_at DESC
        LIMIT 20
    ");
    
    $stmt->execute();
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($files) {
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; overflow-x: auto;'>";
        echo "<table style='width: 100%; border-collapse: collapse; font-size: 12px;'>";
        echo "<tr style='background: #e9ecef;'>";
        echo "<th style='border: 1px solid #ddd; padding: 8px;'>المحتوى</th>";
        echo "<th style='border: 1px solid #ddd; padding: 8px;'>اسم الملف</th>";
        echo "<th style='border: 1px solid #ddd; padding: 8px;'>Google Drive ID</th>";
        echo "<th style='border: 1px solid #ddd; padding: 8px;'>التخزين</th>";
        echo "<tr>";
        
        foreach ($files as $file) {
            echo "<tr>";
            echo "<td style='border: 1px solid #ddd; padding: 6px;'>";
            echo "#{$file['content_id']}<br>";
            echo "<small>" . htmlspecialchars(substr($file['content_title'], 0, 30)) . "</small>";
            echo "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 6px;'>" . htmlspecialchars($file['file_name']) . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 6px;'>";
            if ($file['google_drive_id']) {
                echo "<span style='color: green;'>" . substr($file['google_drive_id'], 0, 15) . "...</span>";
            } else {
                echo "<span style='color: gray;'>لا يوجد</span>";
            }
            echo "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 6px;'>{$file['storage_type']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }
    
    // 2. فحص جدول content للملفات المحفوظة فيه
    echo "<h3>2️⃣ فحص الملفات في جدول content</h3>";
    
    $stmt = $pdo->prepare("
        SELECT 
            id, title, file_path, google_drive_id, storage_type, created_at
        FROM content 
        WHERE file_path IS NOT NULL OR google_drive_id IS NOT NULL
        ORDER BY created_at DESC
        LIMIT 10
    ");
    
    $stmt->execute();
    $content_files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($content_files) {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>📋 المحتويات التي لها ملفات في الجدول الرئيسي:</strong><br><br>";
        foreach ($content_files as $content) {
            echo "<strong>#{$content['id']}</strong> - " . htmlspecialchars($content['title']) . "<br>";
            echo "• مسار الملف: <code>{$content['file_path']}</code><br>";
            echo "• Google Drive ID: <code>{$content['google_drive_id']}</code><br><br>";
        }
        echo "</div>";
    }
    
    // 3. البحث عن التضاعف
    echo "<h3>3️⃣ تحليل التضاعف</h3>";
    
    $stmt = $pdo->prepare("
        SELECT 
            c.id as content_id,
            c.title,
            c.file_path as content_file_path,
            c.google_drive_id as content_drive_id,
            COUNT(cf.id) as files_count
        FROM content c
        LEFT JOIN content_files cf ON c.id = cf.content_id
        WHERE (c.file_path IS NOT NULL OR c.google_drive_id IS NOT NULL)
        GROUP BY c.id
        HAVING files_count > 0
        ORDER BY c.created_at DESC
        LIMIT 10
    ");
    
    $stmt->execute();
    $duplicated = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($duplicated) {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>🔍 المحتويات التي قد تحتوي على ملفات مكررة:</strong><br><br>";
        foreach ($duplicated as $dup) {
            echo "<strong>#{$dup['content_id']}</strong> - " . htmlspecialchars($dup['title']) . "<br>";
            echo "• في جدول content: ملف موجود<br>";
            echo "• في جدول content_files: {$dup['files_count']} ملف<br>";
            echo "• <strong style='color: red;'>إجمالي: " . (1 + $dup['files_count']) . " ملف!</strong><br><br>";
        }
        echo "</div>";
    }
    
    // 4. إحصائيات
    echo "<h3>4️⃣ إحصائيات</h3>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM content_files");
    $stmt->execute();
    $files_in_cf = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM content WHERE file_path IS NOT NULL OR google_drive_id IS NOT NULL");
    $stmt->execute();
    $files_in_content = $stmt->fetchColumn();
    
    echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>📊 الإحصائيات:</strong><br><br>";
    echo "• عدد الملفات في content_files: <strong>{$files_in_cf}</strong><br>";
    echo "• عدد المحتويات التي لها ملفات في content: <strong>{$files_in_content}</strong><br>";
    
    if ($files_in_content > 0 && $files_in_cf > 0) {
        echo "• <strong style='color: red;'>مشكلة تضاعف محتملة!</strong><br>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 15px; border: 1px solid red; border-radius: 5px;'>";
    echo "<strong>❌ خطأ:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "</div>";
?> 