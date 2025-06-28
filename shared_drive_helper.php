<?php
/**
 * أداة مساعدة لإعداد Shared Drive
 */

require_once 'includes/google_drive_config.php';

echo "<h2>🛠️ أداة إعداد Shared Drive</h2>";

$credentials = GoogleDriveConfig::getCredentials();

echo "<div style='max-width: 900px; margin: 20px auto; font-family: Arial;'>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>⚠️ مشكلة في الوصول للـ Shared Drive</strong><br><br>";
echo "المعرف المحدد: <code>1crENXhWCSRxnMSNCSMU9hZayImCkaf_o</code><br>";
echo "خطأ: <strong>Shared drive not found</strong><br><br>";
echo "<strong>الأسباب المحتملة:</strong><br>";
echo "• Service Account لم يتم إضافته للـ Shared Drive<br>";
echo "• معرف الـ Shared Drive غير صحيح<br>";
echo "• الـ Shared Drive تم حذفه أو تغيير اسمه<br>";
echo "</div>";

echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>📋 خطوات الحل:</strong><br><br>";

echo "<strong>الخطوة 1: إنشاء Shared Drive جديد</strong><br>";
echo "1. افتح <a href='https://drive.google.com/drive/shared-drives' target='_blank' style='color: #0066cc;'>Google Drive Shared Drives</a><br>";
echo "2. انقر على 'New' ثم 'Shared drive'<br>";
echo "3. أدخل اسم: <strong>PhysiquePro Storage</strong><br>";
echo "4. انقر 'Create'<br><br>";

echo "<strong>الخطوة 2: إضافة Service Account</strong><br>";
echo "1. في الـ Shared Drive الجديد، انقر بزر الماوس الأيمن<br>";
echo "2. اختر 'Manage members'<br>";
echo "3. انقر 'Add members'<br>";
echo "4. أضف هذا العنوان: <br>";
echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 3px; margin: 5px 0; font-family: monospace;'>";
echo "physiqueprofiles@physiquepro-files.iam.gserviceaccount.com";
echo "</div>";
echo "5. امنح صلاحية: <strong>Content Manager</strong><br>";
echo "6. انقر 'Send'<br><br>";

echo "<strong>الخطوة 3: نسخ المعرف الصحيح</strong><br>";
echo "1. انقر على الـ Shared Drive الجديد لفتحه<br>";
echo "2. انسخ المعرف من شريط العنوان<br>";
echo "3. المعرف يكون بعد '/drive/folders/' في الرابط<br>";
echo "4. مثال: https://drive.google.com/drive/folders/<strong>ABC123XYZ789</strong><br>";
echo "5. المعرف هو: <strong>ABC123XYZ789</strong><br>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>🔄 استخدم النموذج أدناه لتحديث المعرف:</strong><br><br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shared_drive_id'])) {
    $newId = trim($_POST['shared_drive_id']);
    
    if (!empty($newId)) {
        // تحديث الإعدادات
        $configFile = 'includes/google_drive_config.php';
        $content = file_get_contents($configFile);
        $pattern = "/const MAIN_FOLDER_ID = '[^']*';/";
        $replacement = "const MAIN_FOLDER_ID = '{$newId}';";
        $newContent = preg_replace($pattern, $replacement, $content);
        
        if (file_put_contents($configFile, $newContent)) {
            echo "<div style='color: green; padding: 15px; border: 1px solid green; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ تم تحديث معرف الـ Shared Drive بنجاح!<br>";
            echo "المعرف الجديد: <code>{$newId}</code><br><br>";
            echo "<a href='test_new_shared_drive.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧪 اختبار الآن</a>";
            echo "</div>";
        } else {
            echo "<div style='color: red; padding: 15px; border: 1px solid red; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ فشل في تحديث الإعدادات";
            echo "</div>";
        }
    } else {
        echo "<div style='color: red; padding: 15px; border: 1px solid red; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ يرجى إدخال معرف صحيح";
        echo "</div>";
    }
}

echo "<form method='POST' style='margin-top: 20px;'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>معرف الـ Shared Drive الجديد:</label>";
echo "<input type='text' name='shared_drive_id' placeholder='أدخل معرف الـ Shared Drive هنا' style='width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;' required>";
echo "</div>";
echo "<button type='submit' style='background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer;'>🔄 تحديث المعرف</button>";
echo "</form>";
echo "</div>";

echo "<div style='background: #e8f4f8; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
echo "<strong>💡 نصائح هامة:</strong><br>";
echo "• تأكد من إضافة Service Account قبل تحديث المعرف<br>";
echo "• استخدم صلاحية 'Content Manager' أو أعلى<br>";
echo "• تأكد من نسخ المعرف بشكل صحيح (بدون مسافات)<br>";
echo "• إذا استمرت المشكلة، أنشئ Shared Drive جديد تماماً<br>";
echo "</div>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='diagnose_new_service_account.php' style='color: #007bff; margin: 0 10px;'>🔍 تشخيص المشكلة</a>";
echo "<a href='test_new_shared_drive.php' style='color: #007bff; margin: 0 10px;'>🧪 اختبار الوصول</a>";
echo "</div>";

echo "</div>";
?> 