<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Language Handling
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ne'; // Default to Nepali

// Translation Dictionary
$trans = [
    'en' => [
        'gov_nepal' => 'Government of Nepal',
        'mun_name' => 'Tarkeshwor Municipality',
        'office_loc' => 'Office of Municipal Executive, Dharmasthali, Kathmandu',
        'home' => 'Home',
        'report_issue' => 'Report an Issue',
        'admin_login' => 'Management Login',
        'slogan' => 'Healthy, Developed and Prosperous Tarkeshwor',
        'identify' => 'Identify Issue',
        'submit' => 'Submit Report',
        'solved' => 'Get Solved',
        'nepali' => 'Nepali',
        'english' => 'English',
        'report_btn' => 'Report an Issue Now',
        'hero_title' => 'Tarkeshwor Municipality',
        'hero_subtitle' => 'Citizen Issue Reporting Portal',
        'ward' => 'Ward No.',
        'category' => 'Category',
        'description' => 'Description',
        'landmark' => 'Landmark / Location Detail',
        'photo' => 'Add Photo Evidence',
        'submit_to_mun' => 'SUBMIT REPORT TO MUNICIPALITY',
        'pin_location' => 'Pin Exact Location on Map',
        'management_login' => 'Management Login',
        'username' => 'Username',
        'password' => 'Password',
        'login_btn' => 'LOGIN TO DASHBOARD',
        'official_portal' => 'Official Management Portal',
        'desc_identify' => 'Notice a pothole, broken light, or garbage? Tell us about it.',
        'desc_submit' => 'Use our simple form, snap a photo, and pin the location on the map.',
        'desc_solved' => 'Track the status of your report as our team works to resolve it.',
    ],
    'ne' => [
        'gov_nepal' => 'नेपाल सरकार',
        'mun_name' => 'तारकेश्वर नगरपालिका',
        'office_loc' => 'नगर कार्यपालिकाको कार्यालय, धर्मस्थली, काठमाडौँ',
        'home' => 'गृहपृष्ठ',
        'report_issue' => 'गुनासो दर्ता',
        'admin_login' => 'व्यवस्थापन लगइन',
        'slogan' => 'स्वस्थ, समुन्नत र समृद्ध तारकेश्वर नगरपालिका',
        'identify' => 'समस्या पहिचान',
        'submit' => 'प्रतिवेदन बुझाउनुहोस्',
        'solved' => 'समाधान पाउनुहोस्',
        'nepali' => 'नेपाली',
        'english' => 'English',
        'report_btn' => 'अहिले रिपोर्ट गर्नुहोस्',
        'hero_title' => 'तारकेश्वर नगरपालिका',
        'hero_subtitle' => 'नागरिक गुनासो दर्ता पोर्टल',
        'ward' => 'वडा नं.',
        'category' => 'वर्ग',
        'description' => 'विवरण',
        'landmark' => 'नजिकको स्थान / ठेगाना',
        'photo' => 'फोटो प्रमाण थप्नुहोस्',
        'submit_to_mun' => 'नगरपालिकामा रिपोर्ट बुझाउनुहोस्',
        'pin_location' => 'नक्सामा सही स्थान छान्नुहोस्',
        'management_login' => 'व्यवस्थापन लगइन',
        'username' => 'प्रयोगकर्ता नाम',
        'password' => 'पासवर्ड',
        'login_btn' => 'ड्यासबोर्डमा लगइन गर्नुहोस्',
        'official_portal' => 'आधिकारिक व्यवस्थापन पोर्टल',
        'contact' => 'सम्पर्क जानकारी',
        'links' => 'महत्वपूर्ण लिङ्कहरू',
        'all_rights' => 'सबै अधिकार सुरक्षित',
        'desc_identify' => 'सडकमा खाल्डो, बत्ती नवलने वा फोहोर देख्नुभयो? हामीलाई जानकारी दिनुहोस्।',
        'desc_submit' => 'हाम्रो सजिलो फारम प्रयोग गर्नुहोस्, फोटो खिच्नुहोस् र नक्सामा स्थान छान्नुहोस्।',
        'desc_solved' => 'हाम्रो टोलीले समस्या समाधान गर्दा तपाईं आफ्नो रिपोर्टको अवस्था हेर्न सक्नुहुन्छ।',
    ]
];

// Helper function for translations
if (!function_exists('__')) {
    function __($key)
    {
        global $trans, $lang;
        return isset($trans[$lang][$key]) ? $trans[$lang][$key] : $key;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
    if (isset($pageTitleKey)) {
        echo __($pageTitleKey) . " - " . __($lang == 'en' ? 'mun_name' : 'mun_name');
    } else {
        echo isset($pageTitle) ? $pageTitle : __('mun_name');
    }
    ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gov-blue: #004a89;
            --gov-red: #ef1c24;
        }

        body {
            font-family: 'Mukta', sans-serif;
        }

        .gov-top-bar {
            background: var(--gov-red);
            color: white;
            font-size: 14px;
            padding: 5px 0;
        }

        .gov-header {
            background: white;
            padding: 15px 0;
            border-bottom: 3px solid var(--gov-blue);
        }

        .gov-emblem {
            height: 80px;
        }

        .gov-title {
            color: var(--gov-red);
            font-weight: bold;
            line-height: 1.2;
        }

        .gov-subtitle {
            color: var(--gov-blue);
            font-size: 18px;
            font-weight: bold;
        }

        .navbar-gov {
            background: var(--gov-blue) !important;
        }

        .navbar-gov .nav-link {
            color: white !important;
            font-weight: bold;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
        }

        .navbar-gov .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .hero-banner {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://tarakeshwormun.gov.np/sites/tarakeshwormun.gov.np/files/slider/slider1.jpg') no-repeat center center;
            background-size: cover;
            height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }
    </style>
</head>

<body>

    <!-- Top Bar -->
    <div class="gov-top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div><?php echo __('gov_nepal'); ?> | Government of Nepal</div>
            <div class="small">
                <?php $current_url = strtok($_SERVER["REQUEST_URI"], '?'); ?>
                <a href="<?php echo $current_url; ?>?lang=en" class="text-white text-decoration-none me-2 <?php if ($lang == 'en')
                       echo 'fw-bold border-bottom'; ?>">English</a>
                |
                <a href="<?php echo $current_url; ?>?lang=ne" class="text-white text-decoration-none ms-2 <?php if ($lang == 'ne')
                       echo 'fw-bold border-bottom'; ?>">नेपाली</a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="gov-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/23/Emblem_of_Nepal.svg" alt="Nepal Emblem"
                    class="gov-emblem me-3">
                <div>
                    <div class="gov-title fs-5"><?php echo $trans['ne']['mun_name']; ?></div>
                    <div class="gov-subtitle"><?php echo $trans['en']['mun_name']; ?></div>
                    <div class="text-muted small"><?php echo __('office_loc'); ?></div>
                </div>
                <div class="ms-auto d-none d-md-block text-end">
                    <div class="fw-bold text-primary"><?php echo __('slogan'); ?></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-gov py-0">
        <div class="container">
            <button class="navbar-toggler my-2" type="button" data-bs-toggle="collapse" data-bs-target="#govNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="govNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><?php echo __('home'); ?></a></li>
                    <li class="nav-item"><a class="nav-link"
                            href="report_issue.php"><?php echo __('report_issue'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_login.php"><?php echo __('admin_login'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>