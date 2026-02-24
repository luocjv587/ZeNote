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
    <title>ZeNote - Tile</title>
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Highlight.js (for Quill syntax module) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <!-- Quill 1.x -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <!-- Helpers: PDF, JSON big integer, Markdown render -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/json-bigint@1.0.0/dist/json-bigint.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
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
<body class="bg-white dark:bg-gray-900 h-screen flex overflow-hidden text-gray-900 dark:text-gray-100 selection:bg-gray-200 dark:selection:bg-gray-700">
    <div id="aiModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div id="aiModalBackdrop" class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
        <div class="relative w-[90%] max-w-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl p-6 flex flex-col max-h-[85vh]">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    AI Assistant
                </h2>
                <button id="closeAiModalBtn" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto space-y-4 pr-1">
                <div id="aiContextPreview" class="hidden">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Selected Context</label>
                    <div id="aiContextText" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-sm text-gray-600 dark:text-gray-400 border border-gray-100 dark:border-gray-700 italic max-h-32 overflow-y-auto"></div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Instruction</label>
                    <textarea id="aiPromptInput" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-black focus:border-black dark:text-white min-h-[100px]" placeholder="Ask AI to polish, translate, or summarize..."></textarea>
                </div>
                <div id="aiResultArea" class="hidden">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">AI Response</label>
                    <div id="aiResponseText" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-sm text-gray-800 dark:text-gray-200 border border-gray-100 dark:border-gray-700 whitespace-pre-wrap"></div>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button id="aiInsertBtn" class="hidden px-4 py-2 text-sm rounded-full border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Insert to Bottom
                </button>
                <button id="aiSubmitBtn" class="px-5 py-2 text-sm rounded-full bg-black text-white dark:bg-white dark:text-black hover:opacity-90 transition-opacity flex items-center justify-center">
                    <span>Ask AI</span>
                </button>
            </div>
        </div>
    </div>
    <aside id="tileSidebar" class="w-64 md:w-64 border-r border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 flex flex-col fixed md:relative inset-y-0 left-0 z-30 transform -translate-x-full md:translate-x-0 transition-transform">
        <div class="p-5 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <button id="closeSidebarBtn" class="md:hidden p-2 hover:bg-white dark:hover:bg-gray-800 rounded-full border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <h1 class="text-xl font-bold tracking-tight">ZeNote</h1>
            </div>
            <div class="flex items-center space-x-2">
                <button id="settingsBtn" class="p-2 hover:bg-white dark:hover:bg-gray-800 rounded-full border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
                <button id="logoutBtn" class="p-2 hover:bg-white dark:hover:bg-gray-800 rounded-full border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </div>
        </div>
        <div class="px-5">
            <input type="text" id="globalSearch" placeholder="Search" class="w-full bg-gray-200/50 dark:bg-gray-800 border-none rounded-lg py-1.5 px-3 text-sm placeholder-gray-500 dark:placeholder-gray-400 focus:bg-white dark:focus:bg-gray-700 focus:ring-0 transition-colors dark:text-gray-200">
        </div>
        <div id="notebookList" class="flex-1 overflow-y-auto p-4 space-y-1">
        </div>
    </aside>
    <div id="tileBackdrop" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-20 hidden md:hidden"></div>
    <main class="flex-1 overflow-y-auto md:ml-64">
        <div class="flex items-center justify-between px-6 pt-6">
            <div class="flex items-center space-x-2">
                <button id="openSidebarBtn" class="md:hidden px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full text-xs">分类</button>
                <button id="allBtn" class="px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full text-xs">全部</button>
                <button id="favoritesBtn" class="px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full text-xs">收藏</button>
                <button id="trashBtn" class="px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full text-xs">废纸篓</button>
            </div>
            <button id="newNoteBtn" class="px-3 py-1.5 bg-black text-white dark:bg-white dark:text-black rounded-full text-xs">新建笔记</button>
        </div>
        <div id="tileContainer" class="px-6 py-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"></div>
        <div id="tileEditorPane" class="hidden px-6 py-6">
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center space-x-2">
                        <button id="tileBackBtn" class="px-2 py-1.5 rounded-full border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-xs">返回</button>
                        <input id="tileTitleInput" type="text" placeholder="标题" class="w-56 sm:w-72 md:w-96 bg-transparent border-none focus:ring-0 text-sm placeholder-gray-400 dark:placeholder-gray-500">
                    </div>
                    <div class="flex items-center space-x-2">
                        <span id="tileSaveStatus" class="text-xs text-gray-400"></span>
                        <button id="tileSaveBtn" class="px-3 py-1.5 bg-black text-white dark:bg-white dark:text-black rounded-full text-xs">保存</button>
                    </div>
                </div>
                <div class="p-4">
                    <div id="tileEditor" class="min-h-[50vh]"></div>
                </div>
            </div>
        </div>
    </main>
    <div class="fixed bottom-6 right-6 z-50 group">
        <div id="fabMenu" class="absolute bottom-full right-0 mb-4 flex flex-col space-y-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
            <button id="aiBtn" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center whitespace-nowrap" title="AI Assistant">
                <span class="mr-2 text-xs font-medium">AI</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </button>
            <button id="formatJsonBtn" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center whitespace-nowrap" title="Format JSON">
                <span class="mr-2 text-xs font-medium">JSON</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path></svg>
            </button>
            <button id="calculateBtn" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center whitespace-nowrap" title="Calculate">
                <span class="mr-2 text-xs font-medium">Calc</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6M9 12h6M9 17h6"></path></svg>
            </button>
            <button id="insertTimeBtn" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center whitespace-nowrap" title="Insert Time">
                <span class="mr-2 text-xs font-medium">Time</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </button>
            <button id="exportPdfBtn" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center whitespace-nowrap" title="Export PDF">
                <span class="mr-2 text-xs font-medium">PDF</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11V3m0 8l-3-3m3 3l3-3m-9 5h12v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6z"></path></svg>
            </button>
        </div>
        <button class="bg-black dark:bg-white text-white dark:text-black p-4 rounded-full shadow-xl hover:scale-105 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        </button>
    </div>
    <script>
        const settingsBtn = document.getElementById('settingsBtn');
        const logoutBtn = document.getElementById('logoutBtn');
        const globalSearchEl = document.getElementById('globalSearch');
        const notebookListEl = document.getElementById('notebookList');
        const tileContainer = document.getElementById('tileContainer');
        const allBtn = document.getElementById('allBtn');
        const favoritesBtn = document.getElementById('favoritesBtn');
        const trashBtn = document.getElementById('trashBtn');
        const newNoteBtn = document.getElementById('newNoteBtn');
        const tileEditorPane = document.getElementById('tileEditorPane');
        const tileBackBtn = document.getElementById('tileBackBtn');
        const tileTitleInput = document.getElementById('tileTitleInput');
        const tileSaveBtn = document.getElementById('tileSaveBtn');
        const tileSaveStatus = document.getElementById('tileSaveStatus');
        const aiBtn = document.getElementById('aiBtn');
        const aiModal = document.getElementById('aiModal');
        const aiModalBackdrop = document.getElementById('aiModalBackdrop');
        const closeAiModalBtn = document.getElementById('closeAiModalBtn');
        const aiContextPreview = document.getElementById('aiContextPreview');
        const aiContextText = document.getElementById('aiContextText');
        const aiPromptInput = document.getElementById('aiPromptInput');
        const aiSubmitBtn = document.getElementById('aiSubmitBtn');
        const aiResultArea = document.getElementById('aiResultArea');
        const aiResponseText = document.getElementById('aiResponseText');
        const aiInsertBtn = document.getElementById('aiInsertBtn');
        let quillTile = null;
        let currentTileNoteId = null;
        let currentTileNotebookId = null;
        let tileCurrentSelection = '';

        let notebooks = [];
        let selectedNotebookId = null;
        let isTrashMode = false;
        let isFavoriteMode = false;
        let page = 1;
        let hasMore = true;
        let loading = false;
        let searchTimeout;

        settingsBtn.addEventListener('click', () => window.location.href = 'settings.php');
        logoutBtn.addEventListener('click', async () => {
            await fetch('api.php?action=logout');
            window.location.href = 'login.php';
        });

        async function fetchNotebooks() {
            const res = await fetch('api.php?action=get_notebooks');
            const data = await res.json();
            notebooks = data.notebooks || [];
            renderNotebookList();
        }

        function renderNotebookList() {
            const items = [{ id: null, name: '全部' }].concat(notebooks);
            notebookListEl.innerHTML = items.map(n => `
                <button data-id="${n.id === null ? '' : n.id}" class="w-full flex justify-between items-center text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all ${selectedNotebookId == n.id ? 'ring-1 ring-black dark:ring-white' : ''}">
                    <span class="truncate">${n.name}</span>
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            `).join('');
        }

        notebookListEl.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-id]');
            if (!btn) return;
            const val = btn.dataset.id;
            selectedNotebookId = val ? parseInt(val) : null;
            isTrashMode = false;
            isFavoriteMode = false;
            resetAndFetch();
            closeSidebar();
        });

        allBtn.addEventListener('click', () => {
            selectedNotebookId = null;
            isTrashMode = false;
            isFavoriteMode = false;
            resetAndFetch();
        });
        favoritesBtn.addEventListener('click', () => {
            selectedNotebookId = null;
            isTrashMode = false;
            isFavoriteMode = true;
            resetAndFetch();
        });
        trashBtn.addEventListener('click', () => {
            selectedNotebookId = null;
            isTrashMode = true;
            isFavoriteMode = false;
            resetAndFetch();
        });

        // Mobile sidebar controls
        const tileSidebar = document.getElementById('tileSidebar');
        const tileBackdrop = document.getElementById('tileBackdrop');
        const openSidebarBtn = document.getElementById('openSidebarBtn');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');

        function openSidebar() {
            tileSidebar.classList.remove('-translate-x-full');
            tileBackdrop.classList.remove('hidden');
        }
        function closeSidebar() {
            tileSidebar.classList.add('-translate-x-full');
            tileBackdrop.classList.add('hidden');
        }
        if (openSidebarBtn) openSidebarBtn.addEventListener('click', openSidebar);
        if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
        if (tileBackdrop) tileBackdrop.addEventListener('click', closeSidebar);

        function showGrid() {
            tileContainer.classList.remove('hidden');
            tileEditorPane.classList.add('hidden');
        }
        function showEditor() {
            tileContainer.classList.add('hidden');
            tileEditorPane.classList.remove('hidden');
        }

        function initEditorIfNeeded() {
            if (quillTile) return;
            quillTile = new Quill('#tileEditor', {
                theme: 'snow',
                placeholder: '开始写作...',
                modules: {
                    syntax: true,
                    toolbar: [
                        [{ font: [] }, { size: [] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ script: 'sub' }, { script: 'super' }],
                        [{ header: 1 }, { header: 2 }, 'blockquote', 'code-block'],
                        [{ list: 'ordered' }, { list: 'bullet' }, { list: 'check' }],
                        [{ indent: '-1' }, { indent: '+1' }],
                        [{ direction: 'rtl' }],
                        [{ align: [] }],
                        ['link', 'image', 'video', 'formula'],
                        ['clean']
                    ]
                }
            });
        }

        newNoteBtn.addEventListener('click', () => {
            initEditorIfNeeded();
            currentTileNoteId = null;
            tileTitleInput.value = '';
            quillTile.setContents([]);
            showEditor();
        });

        globalSearchEl.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => resetAndFetch(), 300);
        });

        async function fetchNotes() {
            if (loading || !hasMore) return;
            loading = true;
            const q = globalSearchEl.value.trim();
            let url = `api.php?action=get_notes&page=${page}&limit=20&q=${encodeURIComponent(q)}`;
            if (isTrashMode) url += '&trash=1';
            else if (isFavoriteMode) url += '&favorites=1';
            else if (selectedNotebookId !== null) url += `&notebook_id=${selectedNotebookId}`;
            const res = await fetch(url);
            const data = await res.json();
            renderTiles(data.notes || []);
            if ((data.notes || []).length < 20) hasMore = false;
            page++;
            loading = false;
        }

        function renderTiles(notes) {
            const html = notes.map(n => `
                <div class="group border border-gray-200 dark:border-gray-800 rounded-2xl bg-white dark:bg-gray-900 p-4 hover:shadow-sm transition-shadow cursor-pointer" data-id="${n.id}">
                    <div class="flex justify-between items-start">
                        <h3 class="font-semibold text-sm mb-1 truncate ${!n.title ? 'text-gray-400 dark:text-gray-500 italic' : ''}">${n.title || n.preview || '新建笔记'}</h3>
                        <div class="flex items-center space-x-1 ml-2 shrink-0">
                            ${n.is_favorite == 1 ? '<svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>' : ''}
                            ${n.is_pinned ? '<svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4H17V2H7V4H8V12L6 14V16H11V22H13V16H18V14L16 12Z" /></svg>' : ''}
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3">${n.preview || '暂无内容'}</p>
                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-[10px] text-gray-300 dark:text-gray-600">${new Date(n.updated_at).toLocaleString()}</p>
                        <span class="opacity-0 group-hover:opacity-100 text-[10px] text-gray-400">点击打开</span>
                    </div>
                </div>
            `).join('');
            tileContainer.insertAdjacentHTML('beforeend', html);
        }

        function resetAndFetch() {
            page = 1;
            hasMore = true;
            loading = false;
            tileContainer.innerHTML = '';
            fetchNotes();
        }

        tileContainer.addEventListener('click', async (e) => {
            const card = e.target.closest('[data-id]');
            if (!card) return;
            const id = parseInt(card.dataset.id);
            if (!id || isNaN(id)) return;
            initEditorIfNeeded();
            currentTileNoteId = id;
            try {
                const res = await fetch(`api.php?action=get_note&id=${id}`);
                const d = await res.json();
                const note = d.note || {};
                tileTitleInput.value = note.title || '';
                currentTileNotebookId = note.notebook_id ?? null;
                quillTile.root.innerHTML = note.content || '';
                showEditor();
            } catch (err) {}
        });

        tileBackBtn.addEventListener('click', () => {
            showGrid();
        });

        async function saveTileNote() {
            if (!quillTile) return;
            if (!tileTitleInput.value && !quillTile.root.innerText.trim()) return;
            tileSaveStatus.textContent = '保存中...';
            const clone = quillTile.root.cloneNode(true);
            clone.querySelectorAll('.ql-ui').forEach(el => el.remove());
            const cleanContent = clone.innerHTML;
            const res = await fetch('api.php?action=save_note', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: currentTileNoteId,
                    title: tileTitleInput.value,
                    content: cleanContent,
                    notebook_id: selectedNotebookId ?? currentTileNotebookId ?? null
                })
            });
            const result = await res.json().catch(() => ({}));
            if (result && result.success) {
                currentTileNoteId = result.id;
                tileSaveStatus.textContent = '已保存';
                setTimeout(() => tileSaveStatus.textContent = '', 2000);
                resetAndFetch();
            } else {
                tileSaveStatus.textContent = '保存失败';
                setTimeout(() => tileSaveStatus.textContent = '', 2000);
            }
        }

        tileSaveBtn.addEventListener('click', saveTileNote);

        const insertTimeBtn = document.getElementById('insertTimeBtn');
        const exportPdfBtn = document.getElementById('exportPdfBtn');
        const calculateBtn = document.getElementById('calculateBtn');
        const formatJsonBtn = document.getElementById('formatJsonBtn');

        if (insertTimeBtn) {
            insertTimeBtn.addEventListener('click', () => {
                initEditorIfNeeded();
                if (!quillTile) return;
                const now = new Date();
                const timeStr = now.getFullYear() + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    String(now.getDate()).padStart(2, '0') + ' ' +
                    String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0') + ':' +
                    String(now.getSeconds()).padStart(2, '0');
                const range = quillTile.getSelection(true) || { index: quillTile.getLength(), length: 0 };
                quillTile.insertText(range.index, timeStr);
                quillTile.setSelection(range.index + timeStr.length);
                saveTileNote();
            });
        }

        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', () => {
                const element = document.querySelector('#tileEditor .ql-editor');
                if (!element) return;
                const opt = {
                    margin: 1,
                    filename: (tileTitleInput.value || 'note') + '.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
                };
                html2pdf().set(opt).from(element).save();
            });
        }

        if (calculateBtn) {
            calculateBtn.addEventListener('click', () => {
                initEditorIfNeeded();
                if (!quillTile) return;
                const range = quillTile.getSelection();
                if (!range || range.length === 0) return;
                const text = quillTile.getText(range.index, range.length);
                if (/^[0-9+\-*/().\s]+$/.test(text)) {
                    try {
                        const result = new Function('return ' + text)();
                        if (typeof result === 'number' && isFinite(result)) {
                            const newText = text + ' = ' + result;
                            quillTile.deleteText(range.index, range.length);
                            quillTile.insertText(range.index, newText);
                            quillTile.setSelection(range.index + newText.length);
                            saveTileNote();
                        }
                    } catch (e) {}
                }
            });
        }

        if (formatJsonBtn) {
            formatJsonBtn.addEventListener('click', () => {
                initEditorIfNeeded();
                if (!quillTile) return;
                const text = quillTile.getText();
                try {
                    const range = quillTile.getSelection();
                    let textToFormat = text;
                    let startIndex = 0;
                    let length = text.length;
                    if (range && range.length > 0) {
                        textToFormat = quillTile.getText(range.index, range.length);
                        startIndex = range.index;
                        length = range.length;
                    }
                    let parser;
                    if (window.JSONbig) {
                        if (typeof window.JSONbig === 'function') {
                            parser = window.JSONbig({ storeAsString: true });
                        } else {
                            parser = null;
                        }
                    }
                    if (!parser) {
                        textToFormat = textToFormat.replace(/:\s*(-?\d{16,})(?=\s*[,}\]])/g, ': "$1"');
                        parser = JSON;
                    }
                    const jsonObj = parser.parse(textToFormat);
                    const formatted = parser.stringify(jsonObj, null, 4).replace(/ /g, '\u00A0');
                    quillTile.deleteText(startIndex, length);
                    quillTile.insertText(startIndex, formatted);
                    quillTile.setSelection(startIndex, formatted.length);
                    quillTile.format('code-block', true);
                    saveTileNote();
                } catch (e) {
                    alert('Invalid JSON');
                }
            });
        }

        function openTileAiModal() {
            initEditorIfNeeded();
            if (!quillTile) return;
            const range = quillTile.getSelection();
            tileCurrentSelection = '';
            if (range && range.length > 0) {
                tileCurrentSelection = quillTile.getText(range.index, range.length);
            }
            if (tileCurrentSelection && tileCurrentSelection.trim()) {
                aiContextPreview.classList.remove('hidden');
                aiContextText.textContent = tileCurrentSelection.trim();
            } else {
                aiContextPreview.classList.add('hidden');
                aiContextText.textContent = '';
            }
            aiResultArea.classList.add('hidden');
            aiInsertBtn.classList.add('hidden');
            aiResponseText.innerHTML = '';
            aiPromptInput.value = '';
            aiModal.classList.remove('hidden');
            aiModal.classList.add('flex');
            aiPromptInput.focus();
        }

        function closeTileAiModal() {
            aiModal.classList.add('hidden');
            aiModal.classList.remove('flex');
        }

        if (aiBtn) {
            aiBtn.addEventListener('click', openTileAiModal);
        }
        if (closeAiModalBtn) {
            closeAiModalBtn.addEventListener('click', closeTileAiModal);
        }
        if (aiModalBackdrop) {
            aiModalBackdrop.addEventListener('click', closeTileAiModal);
        }
        if (aiSubmitBtn) {
            aiSubmitBtn.addEventListener('click', async () => {
                initEditorIfNeeded();
                if (!quillTile) return;
                const prompt = aiPromptInput.value.trim();
                const context = tileCurrentSelection || quillTile.getText().trim();
                if (!prompt) return;
                aiSubmitBtn.disabled = true;
                aiSubmitBtn.textContent = 'Thinking...';
                aiResultArea.classList.remove('hidden');
                aiResponseText.textContent = 'Loading...';
                try {
                    const res = await fetch('ai_handler.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ prompt, context })
                    });
                    const data = await res.json();
                    let text = data.text || '';
                    if (window.marked && typeof window.marked.parse === 'function') {
                        aiResponseText.innerHTML = window.marked.parse(text);
                    } else {
                        aiResponseText.textContent = text;
                    }
                    aiInsertBtn.classList.remove('hidden');
                } catch (e) {
                    aiResponseText.textContent = 'AI request failed';
                } finally {
                    aiSubmitBtn.disabled = false;
                    aiSubmitBtn.textContent = 'Ask AI';
                }
            });
        }
        if (aiInsertBtn) {
            aiInsertBtn.addEventListener('click', () => {
                initEditorIfNeeded();
                if (!quillTile) return;
                const text = aiResponseText.textContent || '';
                if (!text) return;
                const length = quillTile.getLength();
                quillTile.insertText(length - 1, '\n\n' + text);
                quillTile.setSelection(length + text.length + 1);
                saveTileNote();
                closeTileAiModal();
            });
        }

        document.addEventListener('scroll', () => {
            if (loading || !hasMore) return;
            const nearBottom = window.scrollY + window.innerHeight >= document.body.scrollHeight - 100;
            if (nearBottom) fetchNotes();
        });

        (async () => {
            await fetchNotebooks();
            fetchNotes();
        })();
    </script>
</body>
</html>
