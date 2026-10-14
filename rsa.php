<?>
<?php
// تحديد مسار ملف الإعدادات الخاص بـ OpenSSL في XAMPP بداخل مجلد php
$opensslConfigPath = 'C:/xampp/php/extras/openssl/openssl.cnf';

// إذا لم يكن موجوداً هنا، جرب مسار مجلد apache
if (!file_exists($opensslConfigPath)) {
    $opensslConfigPath = 'C:/xampp/apache/bin/openssl.cnf';
}

// إعداد متغير البيئة ومصفوفة التكوين
if (file_exists($opensslConfigPath)) {
    putenv("OPENSSL_CONF=" . $opensslConfigPath);
}

$config = array(
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
    "config" => $opensslConfigPath
);

// توليد المفاتيح
$res = openssl_pkey_new($config);

if (!$res) {
    echo "خطأ في إنشاء المفتاح: " . openssl_error_string();
    exit;
}

// استخراج المفتاح الخاص
openssl_pkey_export($res, $privateKey, null, $config);

// استخراج المفتاح العام
$pubKey = openssl_pkey_get_details($res);
if (!$pubKey) {
    echo "خطأ في استخراج تفاصيل المفتاح العام.";
    exit;
}
$publicKey = $pubKey["key"];

// الرسالة والتوقيع
$data = "Test Message for Digital Signature";

//  توقيع الرسالة بالمفتاح الخاص
openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);

//  التحقق من التوقيع بالمفتاح العام
$result = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);

if ($result === 1) {
    echo "التوقيع تم " ;
} elseif ($result === 0) {
    echo "التوقيع لم يتم";
} else {
    echo " حدث خطأ أثناء التحقق: " . openssl_error_string();
}
?>