<?php
require_once 'config.php';
require_once 'auth.php';

// Get the current page/route
$page = $_GET['page'] ?? '';
$handle = $_GET['handle'] ?? '';

// If handle is provided, show user profile
if (!empty($handle) && empty($page)) {
    $page = 'profile';
}

// Default to home page
if (empty($page)) {
    $page = 'home';
}

/**
 * Render HTML head with RTL support
 */
function renderHead($title = '', $description = '') {
    $appName = APP_NAME;
    $pageTitle = empty($title) ? $appName : "$title - $appName";
    $metaDescription = empty($description) ? "أداة مجانية ومفتوحة المصدر لإنشاء صفحة الروابط الشخصية" : $description;
    
    echo "<!DOCTYPE html>
<html lang='ar' dir='rtl'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$pageTitle}</title>
    <meta name='description' content='{$metaDescription}'>
    
    <!-- Open Graph -->
    <meta property='og:title' content='{$pageTitle}'>
    <meta property='og:description' content='{$metaDescription}'>
    <meta property='og:type' content='website'>
    <meta property='og:url' content='" . APP_URL . $_SERVER['REQUEST_URI'] . "'>
    
    <!-- Twitter Card -->
    <meta name='twitter:card' content='summary_large_image'>
    <meta name='twitter:title' content='{$pageTitle}'>
    <meta name='twitter:description' content='{$metaDescription}'>
    
    <link rel='stylesheet' href='/styles.css'>
    <link rel='preconnect' href='https://fonts.googleapis.com'>
    <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
</head>
<body class='bg-pattern'>";
}

/**
 * Render navigation
 */
function renderNavigation($isProfilePage = false) {
    $isLoggedIn = isLoggedIn();
    $loginText = $isLoggedIn ? 'لوحة التحكم' : 'تسجيل الدخول';
    $loginLink = $isLoggedIn ? '/admin' : '/login';
    
    if ($isProfilePage) {
        echo "<nav class='navbar'>
            <div class='container flex justify-between items-center'>
                <a href='/' class='nav-brand'>" . APP_NAME . "</a>
                <a href='{$loginLink}' class='btn btn-secondary'>{$loginText}</a>
            </div>
        </nav>";
    } else {
        echo "<nav class='navbar'>
            <div class='container flex justify-between items-center'>
                <a href='/' class='nav-brand'>" . APP_NAME . "</a>
                <a href='{$loginLink}' class='btn btn-secondary'>{$loginText}</a>
            </div>
        </nav>";
    }
}

/**
 * Render home page
 */
function renderHomePage() {
    renderHead();
    renderNavigation();
    
    echo "<main>
        <div class='container mx-auto px-4 py-16'>
            <!-- Hero Section -->
            <div class='text-center mb-12'>
                <div class='mb-6'>
                    <a href='https://github.com/aldoyh/librelinks' target='_blank' class='btn btn-ghost'>
                        ⭐ ضع نجمة على GitHub
                    </a>
                </div>
                <h1 class='text-4xl md:text-6xl font-bold mb-4'>
                    <span class='block'>أداة الروابط المجانية</span>
                    <span class='hero-title block'>مفتوحة المصدر</span>
                </h1>
                <p class='text-lg text-gray-500 max-w-2xl mx-auto mb-8'>
                    " . APP_NAME . " هي أداة مفتوحة المصدر لإنشاء صفحة روابط شخصية تساعدك على إدارة روابطك بسهولة وتحويل تواجدك الرقمي.
                </p>
                <div class='flex justify-center'>
                    <a href='/register' class='btn btn-primary text-lg px-8 py-3'>
                        ابدأ الآن
                    </a>
                </div>
            </div>
            
            <!-- Features Section -->
            <div class='grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16'>
                <div class='card text-center'>
                    <h3 class='font-semibold text-lg mb-2'>خصص صفحتك</h3>
                    <p class='text-gray-500'>يمكنك تخصيص صفحتك بسهولة بالألوان والقوالب الجميلة</p>
                </div>
                <div class='card text-center'>
                    <h3 class='font-semibold text-lg mb-2'>تتبع الإحصائيات</h3>
                    <p class='text-gray-500'>احصل على إحصائيات مفيدة حول ملفك الشخصي مثل المشاهدات والنقرات</p>
                </div>
                <div class='card text-center'>
                    <h3 class='font-semibold text-lg mb-2'>روابط قابلة للمشاركة</h3>
                    <p class='text-gray-500'>شارك ملفك الشخصي في كل مكان برابط واحد مخصص لك</p>
                </div>
                <div class='card text-center'>
                    <h3 class='font-semibold text-lg mb-2'>محور على الخصوصية</h3>
                    <p class='text-gray-500'>جميع روابطك تخصك، نحن لا نبيع بياناتك</p>
                </div>
            </div>
            
            <!-- Demo Image -->
            <div class='text-center mb-16'>
                <div class='relative max-w-4xl mx-auto'>
                    <div class='bg-slate-900 rounded-lg p-8 text-white'>
                        <h2 class='text-2xl font-bold mb-4'>مثال على صفحة ليبر لينكس</h2>
                        <p class='text-gray-300'>أنشئ وخصص صفحة الروابط الخاصة بك في دقائق ✨</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class='footer'>
        <div class='container mx-auto px-4'>
            <p class='text-gray-400'>
                صنع بواسطة 
                <a href='https://github.com/aldoyh' target='_blank' class='text-emerald-400 hover:text-emerald-300'>
                    @aldoyh
                </a>
            </p>
            <div class='flex justify-center gap-4 mt-4'>
                <a href='https://github.com/aldoyh' target='_blank' class='text-white hover:text-gray-300'>GitHub</a>
                <a href='#' target='_blank' class='text-white hover:text-gray-300'>Twitter</a>
            </div>
        </div>
    </footer>";
    
    echo "<script src='/app.js'></script>
</body>
</html>";
}

/**
 * Render profile page
 */
function renderProfilePage($handle) {
    $user = getUserByHandle($handle);
    
    if (!$user) {
        http_response_code(404);
        renderHead('المستخدم غير موجود');
        echo "<div class='container mx-auto px-4 py-16 text-center'>
            <h1 class='text-2xl font-bold mb-4'>المستخدم غير موجود</h1>
            <p class='text-gray-500 mb-8'>الصفحة التي تبحث عنها غير موجودة.</p>
            <a href='/' class='btn btn-primary'>العودة للرئيسية</a>
        </div>
        </body></html>";
        return;
    }
    
    // Increment views
    incrementUserViews($user['id']);
    
    // Get user links
    $links = Database::getLinks($user['id']);
    usort($links, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    
    $pageTitle = $user['name'] ?: $user['handle'];
    $pageDescription = $user['bio'] ?: "صفحة {$pageTitle} على " . APP_NAME;
    
    renderHead($pageTitle, $pageDescription);
    renderNavigation(true);
    
    // Get theme colors
    $theme = $user['themePalette'];
    $backgroundColor = $theme['palette'][0] ?? '#FFFFFF';
    $cardColor = $theme['palette'][1] ?? '#F2F2F2';
    $textColor = $theme['palette'][2] ?? '#1F2937';
    $buttonColor = $theme['palette'][3] ?? '#6170F8';
    
    echo "<style>
        .profile-theme {
            background-color: {$backgroundColor};
            color: {$textColor};
        }
        .profile-card {
            background-color: {$cardColor};
        }
        .profile-button {
            background-color: {$buttonColor};
            color: white;
        }
    </style>";
    
    echo "<main class='profile-theme min-h-screen'>
        <div class='profile-container'>
            <!-- Profile Header -->
            <div class='text-center mb-8'>
                <div class='profile-avatar bg-gray-200 mb-4'></div>
                <h1 class='profile-name'>{$user['name']}</h1>
                <p class='profile-bio'>{$user['bio']}</p>
                <div class='text-sm text-gray-500'>
                    {$user['totalViews']} مشاهدة
                </div>
            </div>
            
            <!-- Links -->
            <div class='space-y-4'>";
    
    foreach ($links as $link) {
        if (!$link['archived']) {
            echo "<a href='{$link['url']}' 
                     target='_blank' 
                     class='link-card profile-button block text-center py-4 px-6 font-medium'
                     data-link-id='{$link['id']}'
                     onclick='trackClick(\"{$link['id']}\")'>
                {$link['title']}
            </a>";
        }
    }
    
    echo "    </div>
        </div>
    </main>";
    
    echo "<script src='/app.js'></script>
</body>
</html>";
}

/**
 * Render login page
 */
function renderLoginPage() {
    if (isLoggedIn()) {
        redirect('/admin');
    }
    
    renderHead('تسجيل الدخول');
    renderNavigation();
    
    echo "<main class='container mx-auto px-4 py-16'>
        <div class='max-w-md mx-auto'>
            <div class='card'>
                <h1 class='text-2xl font-bold text-center mb-6'>تسجيل الدخول</h1>
                <form id='login-form'>
                    <div class='form-group'>
                        <label class='form-label'>البريد الإلكتروني</label>
                        <input type='email' name='email' class='form-input' required>
                    </div>
                    <button type='submit' class='btn btn-primary w-full'>
                        تسجيل الدخول
                    </button>
                </form>
                <div class='text-center mt-4'>
                    <p class='text-gray-500'>
                        ليس لديك حساب؟ 
                        <a href='/register' class='text-blue-500 hover:text-blue-600'>إنشاء حساب جديد</a>
                    </p>
                </div>
            </div>
        </div>
    </main>";
    
    echo "<script src='/app.js'></script>
</body>
</html>";
}

/**
 * Render register page
 */
function renderRegisterPage() {
    if (isLoggedIn()) {
        redirect('/admin');
    }
    
    renderHead('إنشاء حساب جديد');
    renderNavigation();
    
    echo "<main class='container mx-auto px-4 py-16'>
        <div class='max-w-md mx-auto'>
            <div class='card'>
                <h1 class='text-2xl font-bold text-center mb-6'>إنشاء حساب جديد</h1>
                <form id='register-form'>
                    <div class='form-group'>
                        <label class='form-label'>الاسم</label>
                        <input type='text' name='name' class='form-input' required>
                    </div>
                    <div class='form-group'>
                        <label class='form-label'>اسم المستخدم</label>
                        <input type='text' name='handle' class='form-input' placeholder='username' required>
                        <small class='text-gray-500'>سيكون رابطك: " . APP_URL . "/username</small>
                    </div>
                    <div class='form-group'>
                        <label class='form-label'>البريد الإلكتروني</label>
                        <input type='email' name='email' class='form-input' required>
                    </div>
                    <button type='submit' class='btn btn-primary w-full'>
                        إنشاء الحساب
                    </button>
                </form>
                <div class='text-center mt-4'>
                    <p class='text-gray-500'>
                        لديك حساب؟ 
                        <a href='/login' class='text-blue-500 hover:text-blue-600'>تسجيل الدخول</a>
                    </p>
                </div>
            </div>
        </div>
    </main>";
    
    echo "<script src='/app.js'></script>
</body>
</html>";
}

/**
 * Render admin page
 */
function renderAdminPage() {
    if (!isLoggedIn()) {
        redirect('/login');
    }
    
    $user = getCurrentUser();
    $links = Database::getLinks($user['id']);
    usort($links, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    
    renderHead('لوحة التحكم');
    renderNavigation();
    
    echo "<main class='container mx-auto px-4 py-8'>
        <div class='max-w-4xl mx-auto'>
            <!-- Header -->
            <div class='flex justify-between items-center mb-8'>
                <h1 class='text-3xl font-bold'>مرحباً، {$user['name']}</h1>
                <div class='flex gap-4'>
                    <a href='/{$user['handle']}' target='_blank' class='btn btn-ghost'>
                        معاينة الصفحة
                    </a>
                    <button onclick='copyToClipboard(\"" . APP_URL . "/{$user['handle']}\")' class='btn btn-secondary'>
                        نسخ الرابط
                    </button>
                    <a href='/api.php?action=logout' class='btn btn-ghost text-red-500'>
                        تسجيل الخروج
                    </a>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class='grid md:grid-cols-3 gap-6 mb-8'>
                <div class='card text-center'>
                    <h3 class='text-2xl font-bold text-blue-500'>{$user['totalViews']}</h3>
                    <p class='text-gray-500'>مشاهدة</p>
                </div>
                <div class='card text-center'>
                    <h3 class='text-2xl font-bold text-green-500'>" . count($links) . "</h3>
                    <p class='text-gray-500'>رابط</p>
                </div>
                <div class='card text-center'>
                    <h3 class='text-2xl font-bold text-purple-500'>" . array_sum(array_column($links, 'clicks')) . "</h3>
                    <p class='text-gray-500'>نقرة</p>
                </div>
            </div>
            
            <!-- Profile Settings -->
            <div class='card mb-8'>
                <h2 class='text-xl font-bold mb-4'>إعدادات الملف الشخصي</h2>
                <form id='profile-form' class='grid md:grid-cols-2 gap-4'>
                    <div class='form-group'>
                        <label class='form-label'>الاسم</label>
                        <input type='text' id='profile-name' name='name' class='form-input' value='{$user['name']}'>
                    </div>
                    <div class='form-group'>
                        <label class='form-label'>اسم المستخدم</label>
                        <input type='text' id='profile-handle' name='handle' class='form-input' value='{$user['handle']}'>
                    </div>
                    <div class='form-group md:col-span-2'>
                        <label class='form-label'>النبذة الشخصية</label>
                        <textarea id='profile-bio' name='bio' class='form-input' rows='3'>{$user['bio']}</textarea>
                    </div>
                    <div class='md:col-span-2'>
                        <button type='button' onclick='updateProfile()' class='btn btn-primary'>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Links Management -->
            <div class='card'>
                <div class='flex justify-between items-center mb-4'>
                    <h2 class='text-xl font-bold'>إدارة الروابط</h2>
                    <button onclick='addLink()' class='btn btn-primary'>
                        إضافة رابط جديد
                    </button>
                </div>
                
                <div id='links-container'>";
    
    foreach ($links as $link) {
        echo "<div class='link-card flex justify-between items-center' data-link-id='{$link['id']}'>
            <div class='flex-1'>
                <h3 class='font-medium'>{$link['title']}</h3>
                <p class='text-gray-500 text-sm'>{$link['url']}</p>
                <small class='text-gray-400'>{$link['clicks']} نقرة</small>
            </div>
            <div class='flex gap-2'>
                <button onclick='editLink(\"{$link['id']}\", \"{$link['title']}\", \"{$link['url']}\")' 
                        class='btn btn-ghost text-blue-500'>تعديل</button>
                <button onclick='deleteLink(\"{$link['id']}\")' 
                        class='btn btn-ghost text-red-500'>حذف</button>
            </div>
        </div>";
    }
    
    if (empty($links)) {
        echo "<div class='text-center py-8 text-gray-500'>
            <p>لا توجد روابط حتى الآن</p>
            <button onclick='addLink()' class='btn btn-primary mt-4'>إضافة أول رابط</button>
        </div>";
    }
    
    echo "        </div>
            </div>
        </div>
    </main>";
    
    echo "<script>
        function editLink(linkId, title, url) {
            const newTitle = prompt('العنوان الجديد:', title);
            const newUrl = prompt('الرابط الجديد:', url);
            
            if (newTitle && newUrl) {
                updateLink(linkId, newTitle, newUrl);
            }
        }
    </script>
    <script src='/app.js'></script>
</body>
</html>";
}

// Route to appropriate page
switch ($page) {
    case 'home':
        renderHomePage();
        break;
    case 'login':
        renderLoginPage();
        break;
    case 'register':
        renderRegisterPage();
        break;
    case 'admin':
        renderAdminPage();
        break;
    case 'profile':
        renderProfilePage($handle);
        break;
    default:
        http_response_code(404);
        renderHead('الصفحة غير موجودة');
        echo "<div class='container mx-auto px-4 py-16 text-center'>
            <h1 class='text-2xl font-bold mb-4'>الصفحة غير موجودة</h1>
            <p class='text-gray-500 mb-8'>الصفحة التي تبحث عنها غير موجودة.</p>
            <a href='/' class='btn btn-primary'>العودة للرئيسية</a>
        </div>
        </body></html>";
        break;
}
?>