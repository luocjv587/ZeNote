<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeNote - Login</title>
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="manifest" href="manifest.json">">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen selection:bg-gray-300 relative">
    
    <!-- Language Switcher -->
    <div class="absolute top-4 right-4 flex space-x-2 text-sm">
        <button id="langCn" class="font-medium text-black">中文</button>
        <span class="text-gray-300">|</span>
        <button id="langEn" class="text-gray-500 hover:text-black">English</button>
    </div>

    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">ZeNote</h1>
            <p id="subTitle" class="text-gray-500 mt-2">随时随地记录想法。</p>
        </div>

        <div id="alert" class="hidden mb-4 p-3 text-sm rounded-lg bg-red-50 text-red-600"></div>

        <form id="authForm" class="space-y-6">
            <div>
                <label id="lblUsername" for="username" class="block text-sm font-medium text-gray-700">用户名</label>
                <input type="text" id="username" name="username" required 
                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2 focus:border-black focus:bg-white focus:ring-0 transition-colors">
            </div>
            <div>
                <label id="lblPassword" for="password" class="block text-sm font-medium text-gray-700">密码</label>
                <input type="password" id="password" name="password" required 
                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-2 focus:border-black focus:bg-white focus:ring-0 transition-colors">
            </div>

            <button type="submit" id="submitBtn"
                class="w-full rounded-lg bg-black px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                登录
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            <button id="toggleMode" class="text-gray-500 hover:text-black transition-colors underline decoration-gray-300 underline-offset-4">
                创建新账号
            </button>
        </div>
    </div>

    <script>
        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js');
            });
        }

        let isLogin = true;
        let currentLang = localStorage.getItem('lang') || 'cn'; // Load from cache or default cn

        const texts = {
            cn: {
                title: 'ZeNote',
                subtitle: '随时随地记录想法。',
                username: '用户名',
                password: '密码',
                signin: '登录',
                signup: '注册',
                createAccount: '创建新账号',
                haveAccount: '已有账号？去登录',
                success: '账号创建成功！请登录。',
                error: '发生错误'
            },
            en: {
                title: 'ZeNote',
                subtitle: 'Capture your thoughts.',
                username: 'Username',
                password: 'Password',
                signin: 'Sign In',
                signup: 'Sign Up',
                createAccount: 'Create an account',
                haveAccount: 'Already have an account? Sign in',
                success: 'Account created! Please sign in.',
                error: 'An error occurred'
            }
        };

        const form = document.getElementById('authForm');
        const submitBtn = document.getElementById('submitBtn');
        const toggleBtn = document.getElementById('toggleMode');
        const alertBox = document.getElementById('alert');
        const langCnBtn = document.getElementById('langCn');
        const langEnBtn = document.getElementById('langEn');
        const subTitleEl = document.getElementById('subTitle');
        const lblUsername = document.getElementById('lblUsername');
        const lblPassword = document.getElementById('lblPassword');

        function updateLang(lang) {
            currentLang = lang;
            localStorage.setItem('lang', lang); // Save to cache
            const t = texts[lang];
            
            subTitleEl.textContent = t.subtitle;
            lblUsername.textContent = t.username;
            lblPassword.textContent = t.password;
            
            if (isLogin) {
                submitBtn.textContent = t.signin;
                toggleBtn.textContent = t.createAccount;
            } else {
                submitBtn.textContent = t.signup;
                toggleBtn.textContent = t.haveAccount;
            }

            if (lang === 'cn') {
                langCnBtn.classList.add('font-medium', 'text-black');
                langCnBtn.classList.remove('text-gray-500');
                langEnBtn.classList.add('text-gray-500');
                langEnBtn.classList.remove('font-medium', 'text-black');
            } else {
                langEnBtn.classList.add('font-medium', 'text-black');
                langEnBtn.classList.remove('text-gray-500');
                langCnBtn.classList.add('text-gray-500');
                langCnBtn.classList.remove('font-medium', 'text-black');
            }
        }

        langCnBtn.addEventListener('click', () => updateLang('cn'));
        langEnBtn.addEventListener('click', () => updateLang('en'));

        // Initialize language
        updateLang(currentLang);

        toggleBtn.addEventListener('click', () => {
            isLogin = !isLogin;
            const t = texts[currentLang];
            submitBtn.textContent = isLogin ? t.signin : t.signup;
            toggleBtn.textContent = isLogin ? t.createAccount : t.haveAccount;
            alertBox.classList.add('hidden');
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            data.action = isLogin ? 'login' : 'register';

            try {
                const res = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await res.json();

                if (res.ok) {
                    if (isLogin) {
                        window.location.href = 'index.php';
                    } else {
                        // Auto login after register or ask to login
                        isLogin = true;
                        const t = texts[currentLang];
                        submitBtn.textContent = t.signin;
                        toggleBtn.textContent = t.createAccount;
                        alertBox.textContent = t.success;
                        alertBox.classList.remove('hidden', 'bg-red-50', 'text-red-600');
                        alertBox.classList.add('bg-green-50', 'text-green-600');
                        alertBox.classList.remove('hidden');
                        form.reset();
                    }
                } else {
                    throw new Error(result.error || texts[currentLang].error);
                }
            } catch (err) {
                alertBox.textContent = err.message;
                alertBox.classList.remove('hidden', 'bg-green-50', 'text-green-600');
                alertBox.classList.add('bg-red-50', 'text-red-600');
                alertBox.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
