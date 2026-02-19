<?php
ini_set('session.gc_maxlifetime', 604800);
session_set_cookie_params(604800);
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeNote - Settings</title>
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
        .dark ::-webkit-scrollbar-thumb { background: #4b5563; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</head>
<body class="bg-white dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100 selection:bg-gray-200 dark:selection:bg-gray-700">
    <div class="max-w-2xl mx-auto px-6 py-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button id="backBtn" class="p-2 text-gray-500 hover:text-black dark:hover:text-white rounded-full border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">设置</h1>
            </div>
            <div>
                <button id="homeBtn" class="p-2 text-gray-500 hover:text-black dark:hover:text-white rounded-full border border-transparent hover:border-gray-200 dark:hover:border-gray-700" title="首页">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75l9-6 9 6V20a2 2 0 01-2 2H5a2 2 0 01-2-2V9.75z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 22V12h6v10"/></svg>
                </button>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
                <h2 class="text-lg font-semibold">AI Configuration</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 mb-4">配置阿里云百炼 API Key 以使用 AI 功能</p>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">API Key</label>
                        <input type="password" id="apiKeyInput" placeholder="sk-..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm focus:ring-black focus:border-black dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model Name</label>
                        <input type="text" id="modelNameInput" placeholder="qwen-plus" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm focus:ring-black focus:border-black dark:text-white">
                    </div>
                    <div class="flex justify-end">
                        <button id="saveAiSettingsBtn" class="px-5 py-2 text-sm rounded-full bg-black text-white dark:bg-white dark:text-black hover:opacity-90 transition-opacity">
                            保存配置
                        </button>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">导出数据库</h2>
                        <p id="dbSizeHint" class="text-xs text-gray-400 dark:text-gray-500 mt-1">点击下方按钮获取数据库大小并导出</p>
                    </div>
                    <button id="exportBtn" class="px-4 py-2 text-sm rounded-full bg-black text-white dark:bg-white dark:text-black hover:opacity-90 transition-opacity">
                        获取并导出
                    </button>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
                <h2 class="text-lg font-semibold">邮箱备份</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 mb-4">配置 QQ 邮箱，将数据库备份发送到指定邮箱</p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">发件 QQ 邮箱</label>
                        <input type="email" id="qqEmailAccountInput" placeholder="例如：123456@qq.com" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm focus:ring-black focus:border-black dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">授权码</label>
                        <input type="password" id="qqEmailPasswordInput" placeholder="QQ 邮箱 SMTP 授权码" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm focus:ring-black focus:border-black dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">接收邮箱</label>
                        <input type="email" id="qqEmailToInput" placeholder="接收备份的邮箱地址" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-sm focus:ring-black focus:border-black dark:text-white">
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">自动发送数据库备份</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">开启后，每天自动发送数据库文件到上方邮箱</p>
                            <p id="qqEmailLastSentText" class="text-xs text-gray-400 dark:text-gray-500 mt-1 hidden"></p>
                        </div>
                        <button id="qqEmailAutoToggle" type="button" class="relative inline-flex h-6 w-11 items-center rounded-full border border-gray-200 dark:border-gray-700 bg-gray-200 dark:bg-gray-700 transition-colors">
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition-transform translate-x-0"></span>
                        </button>
                    </div>
                    <div class="flex justify-end space-x-3 pt-1">
                        <button id="qqEmailTestBtn" class="px-4 py-2 text-sm rounded-full border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            测试发送
                        </button>
                        <button id="saveEmailSettingsBtn" class="px-5 py-2 text-sm rounded-full bg-black text-white dark:bg-white dark:text-black hover:opacity-90 transition-opacity">
                            保存邮箱配置
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="exportModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div id="exportBackdrop" class="absolute inset-0 bg-black/30"></div>
        <div class="relative w-[90%] max-w-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl p-6">
            <div class="text-center">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">导出数据库</h2>
                <p id="exportInfo" class="text-xs text-gray-400 dark:text-gray-500 mt-1"></p>
            </div>
            <div id="exportStep1" class="mt-5">
                <p class="text-sm text-gray-700 dark:text-gray-300">数据库大小：<span id="dbSizeText">-</span></p>
                <div class="mt-5 flex items-center justify-between">
                    <button id="cancelStep1" class="px-4 py-2 text-sm rounded-full border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">取消</button>
                    <button id="nextStep" class="px-5 py-2 text-sm rounded-full bg-black text-white dark:bg-white dark:text-black hover:opacity-90 transition-opacity">下一步</button>
                </div>
            </div>
            <div id="exportStep2" class="mt-5 hidden">
                <p class="text-sm text-gray-700 dark:text-gray-300">确认下载数据库文件？</p>
                <div class="mt-5 flex items-center justify-between">
                    <button id="backStep" class="px-4 py-2 text-sm rounded-full border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">返回</button>
                    <button id="confirmDownload" class="px-5 py-2 text-sm rounded-full bg-black text-white dark:bg-white dark:text-black hover:opacity-90 transition-opacity">下载</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const exportBtn = document.getElementById('exportBtn');
        const dbSizeHint = document.getElementById('dbSizeHint');
        const exportModal = document.getElementById('exportModal');
        const exportBackdrop = document.getElementById('exportBackdrop');
        const exportInfo = document.getElementById('exportInfo');
        const dbSizeText = document.getElementById('dbSizeText');
        const cancelStep1 = document.getElementById('cancelStep1');
        const nextStep = document.getElementById('nextStep');
        const backStep = document.getElementById('backStep');
        const confirmDownload = document.getElementById('confirmDownload');
        const exportStep1 = document.getElementById('exportStep1');
        const exportStep2 = document.getElementById('exportStep2');

        const apiKeyInput = document.getElementById('apiKeyInput');
        const modelNameInput = document.getElementById('modelNameInput');
        const saveAiSettingsBtn = document.getElementById('saveAiSettingsBtn');
        const qqEmailAccountInput = document.getElementById('qqEmailAccountInput');
        const qqEmailPasswordInput = document.getElementById('qqEmailPasswordInput');
        const qqEmailToInput = document.getElementById('qqEmailToInput');
        const qqEmailAutoToggle = document.getElementById('qqEmailAutoToggle');
        const qqEmailLastSentText = document.getElementById('qqEmailLastSentText');
        const saveEmailSettingsBtn = document.getElementById('saveEmailSettingsBtn');
        const qqEmailTestBtn = document.getElementById('qqEmailTestBtn');

        function updateAutoToggle(enabled) {
            if (!qqEmailAutoToggle) return;
            const knob = qqEmailAutoToggle.querySelector('span');
            if (enabled) {
                qqEmailAutoToggle.classList.remove('bg-gray-200', 'dark:bg-gray-700');
                qqEmailAutoToggle.classList.add('bg-black', 'dark:bg-white');
                if (knob) {
                    knob.classList.remove('translate-x-0');
                    knob.classList.add('translate-x-5');
                }
                qqEmailAutoToggle.dataset.enabled = '1';
            } else {
                qqEmailAutoToggle.classList.add('bg-gray-200', 'dark:bg-gray-700');
                qqEmailAutoToggle.classList.remove('bg-black', 'dark:bg-white');
                if (knob) {
                    knob.classList.add('translate-x-0');
                    knob.classList.remove('translate-x-5');
                }
                qqEmailAutoToggle.dataset.enabled = '0';
            }
        }

        fetch('api.php?action=get_settings')
            .then(res => res.json())
            .then(data => {
                if (data.aliyun_api_key) apiKeyInput.value = data.aliyun_api_key;
                if (data.aliyun_model_name) modelNameInput.value = data.aliyun_model_name;
                if (data.qq_email_account) qqEmailAccountInput.value = data.qq_email_account;
                if (data.qq_email_to) qqEmailToInput.value = data.qq_email_to;
                const enabled = data.qq_email_auto_enabled ? true : false;
                updateAutoToggle(enabled);
                if (data.qq_email_last_sent_at) {
                    qqEmailLastSentText.textContent = '最近发送时间：' + data.qq_email_last_sent_at;
                    qqEmailLastSentText.classList.remove('hidden');
                }
            });

        saveAiSettingsBtn.addEventListener('click', async () => {
            const apiKey = apiKeyInput.value.trim();
            const modelName = modelNameInput.value.trim();
            
            saveAiSettingsBtn.disabled = true;
            saveAiSettingsBtn.textContent = 'Saving...';
            
            try {
                const res = await fetch('api.php?action=save_settings', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({aliyun_api_key: apiKey, aliyun_model_name: modelName})
                });
                
                if (res.ok) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Failed to save settings.');
                }
            } catch (e) {
                console.error(e);
                alert('Error saving settings.');
            } finally {
                saveAiSettingsBtn.disabled = false;
                saveAiSettingsBtn.textContent = '保存配置';
            }
        });

        if (qqEmailAutoToggle) {
            qqEmailAutoToggle.addEventListener('click', () => {
                const current = qqEmailAutoToggle.dataset.enabled === '1';
                updateAutoToggle(!current);
            });
        }

        if (saveEmailSettingsBtn) {
            saveEmailSettingsBtn.addEventListener('click', async () => {
                const account = qqEmailAccountInput.value.trim();
                const password = qqEmailPasswordInput.value.trim();
                const to = qqEmailToInput.value.trim();
                const autoEnabled = qqEmailAutoToggle.dataset.enabled === '1' ? 1 : 0;

                saveEmailSettingsBtn.disabled = true;
                saveEmailSettingsBtn.textContent = '保存中...';

                try {
                    const payload = {
                        aliyun_api_key: apiKeyInput.value.trim(),
                        aliyun_model_name: modelNameInput.value.trim(),
                        qq_email_account: account,
                        qq_email_to: to,
                        qq_email_auto_enabled: autoEnabled
                    };
                    if (password) {
                        payload.qq_email_password = password;
                    }
                    const res = await fetch('api.php?action=save_settings', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(payload)
                    });
                    if (res.ok) {
                        alert('邮箱配置已保存');
                        qqEmailPasswordInput.value = '';
                    } else {
                        alert('保存邮箱配置失败');
                    }
                } catch (e) {
                    console.error(e);
                    alert('保存邮箱配置出错');
                } finally {
                    saveEmailSettingsBtn.disabled = false;
                    saveEmailSettingsBtn.textContent = '保存邮箱配置';
                }
            });
        }

        if (qqEmailTestBtn) {
            qqEmailTestBtn.addEventListener('click', async () => {
                qqEmailTestBtn.disabled = true;
                qqEmailTestBtn.textContent = '测试中...';
                try {
                    const res = await fetch('api.php?action=test_backup_email', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({})
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        alert('测试邮件已发送，请检查邮箱');
                        if (data.last_sent_at) {
                            qqEmailLastSentText.textContent = '最近发送时间：' + data.last_sent_at;
                            qqEmailLastSentText.classList.remove('hidden');
                        }
                    } else {
                        alert(data.error || '测试发送失败');
                    }
                } catch (e) {
                    console.error(e);
                    alert('测试发送出错');
                } finally {
                    qqEmailTestBtn.disabled = false;
                    qqEmailTestBtn.textContent = '测试发送';
                }
            });
        }
        const backBtn = document.getElementById('backBtn');
        const homeBtn = document.getElementById('homeBtn');

        function openExportModal() {
            exportModal.classList.remove('hidden');
            exportModal.classList.add('flex');
            exportStep1.classList.remove('hidden');
            exportStep2.classList.add('hidden');
        }
        function closeExportModal() {
            exportModal.classList.add('hidden');
            exportModal.classList.remove('flex');
        }

        const isDarkMode = localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (isDarkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        backBtn.addEventListener('click', () => history.back());
        homeBtn.addEventListener('click', () => { window.location.href = 'index.php'; });

        exportBtn.addEventListener('click', async () => {
            try {
                const res = await fetch('api.php?action=get_db_info');
                const data = await res.json();
                if (res.ok) {
                    dbSizeText.textContent = data.size_text;
                    dbSizeHint.textContent = '数据库大小：' + data.size_text;
                    exportInfo.textContent = '将下载 SQLite 数据库文件，包含你所有的笔记数据';
                    openExportModal();
                } else {
                    alert('获取数据库信息失败');
                }
            } catch (e) {
                alert('网络错误');
            }
        });

        cancelStep1.addEventListener('click', closeExportModal);
        exportBackdrop.addEventListener('click', closeExportModal);
        nextStep.addEventListener('click', () => {
            exportStep1.classList.add('hidden');
            exportStep2.classList.remove('hidden');
        });
        backStep.addEventListener('click', () => {
            exportStep2.classList.add('hidden');
            exportStep1.classList.remove('hidden');
        });
        confirmDownload.addEventListener('click', () => {
            window.location.href = 'api.php?action=download_db';
            closeExportModal();
        });
    </script>
</body>
</html>
