<?php
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
    <title>ZeNote</title>
    <link rel="icon" type="image/svg+xml" href="logo.svg">
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        .ql-toolbar.ql-snow { border: none; border-bottom: 1px solid #f3f4f6; }
        .ql-container.ql-snow { border: none !important; }
        .ql-editor { font-size: 1.1rem; line-height: 1.6; padding: 2rem; }
        .ql-editor.ql-blank::before { left: 2rem; color: #9ca3af; font-style: normal; }
        
        /* Dark Mode Overrides */
        .dark .ql-toolbar.ql-snow { border-bottom-color: #374151; background-color: #1f2937; }
        .dark .ql-toolbar.ql-snow .ql-stroke { stroke: #9ca3af; }
        .dark .ql-toolbar.ql-snow .ql-fill { fill: #9ca3af; }
        .dark .ql-toolbar.ql-snow .ql-picker { color: #9ca3af; }
        .dark .ql-editor { color: #e5e7eb; }
        .dark .ql-editor.ql-blank::before { color: #6b7280; }

        /* Apple-style scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
        
        .dark ::-webkit-scrollbar-thumb { background: #4b5563; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</head>
<body class="bg-white dark:bg-gray-900 h-screen flex overflow-hidden text-gray-900 dark:text-gray-100 selection:bg-gray-200 dark:selection:bg-gray-700">

    <!-- Sidebar -->
    <aside id="sidebar" class="w-full md:w-80 border-r border-gray-100 dark:border-gray-800 flex flex-col bg-gray-50/50 dark:bg-gray-900/50 absolute md:relative z-20 h-full transition-transform duration-300 ease-in-out">
        <div class="p-6 flex flex-col space-y-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold tracking-tight dark:text-white">ZeNote</h1>
                <div class="flex items-center space-x-2">
                    <button id="settingsBtn" title="Settings" class="p-2 hover:bg-white dark:hover:bg-gray-800 rounded-full transition-colors border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.25 2.25c.414 0 .75.336.75.75v.512c.578.115 1.128.322 1.63.614l.362-.362a.75.75 0 011.06 0l.53.53a.75.75 0 010 1.06l-.362.362c.292.502.499 1.052.614 1.63h.512c.414 0 .75.336.75.75v.75c0 .414-.336.75-.75.75h-.512a6.73 6.73 0 01-.614 1.63l.362.362a.75.75 0 010 1.06l-.53.53a.75.75 0 01-1.06 0l-.362-.362a6.73 6.73 0 01-1.63.614v.512a.75.75 0 01-.75.75h-.75a.75.75 0 01-.75-.75v-.512a6.73 6.73 0 01-1.63-.614l-.362.362a.75.75 0 01-1.06 0l-.53-.53a.75.75 0 010-1.06l.362-.362a6.73 6.73 0 01-.614-1.63H3.75a.75.75 0 01-.75-.75v-.75c0-.414.336-.75.75-.75h.512c.115-.578.322-1.128.614-1.63l-.362-.362a.75.75 0 010-1.06l.53-.53a.75.75 0 011.06 0l.362.362c.502-.292 1.052-.499 1.63-.614V3a.75.75 0 01.75-.75h.75zM12 9.75a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"/>
                        </svg>
                    </button>
                    <button id="themeToggleBtn" title="Toggle Theme" class="p-2 hover:bg-white dark:hover:bg-gray-800 rounded-full transition-colors border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                        <!-- Sun Icon -->
                        <svg id="sunIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <!-- Moon Icon -->
                        <svg id="moonIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                    <button id="newNoteBtn" title="New Note" class="p-2 hover:bg-white dark:hover:bg-gray-800 rounded-full transition-colors border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                    <button id="logoutBtn" title="Logout" class="p-2 hover:bg-white dark:hover:bg-gray-800 rounded-full transition-colors border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </div>
            </div>
            <div class="mt-2 flex items-center space-x-2">
                <select id="notebookFilter" class="w-full md:w-auto md:max-w-[200px] flex-1 text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-3 py-1.5 text-gray-700 dark:text-gray-200 focus:ring-0 focus:border-gray-300 dark:focus:border-gray-600 truncate"></select>
                <button id="newNotebookBtn" title="New Notebook" class="px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7.5A2.5 2.5 0 015.5 5h4l2 2H19a2 2 0 012 2v6.5A2.5 2.5 0 0118.5 18h-13A2.5 2.5 0 013 15.5v-8z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10.5v4m2-2h-4"/>
                    </svg>
                    <span id="newNotebookBtnLabel" class="text-xs">新建笔记本</span>
                </button>
            </div>
            <!-- Global Search -->
            <div class="relative">
                <input type="text" id="globalSearch" placeholder="Search" 
                    class="w-full bg-gray-200/50 dark:bg-gray-800 border-none rounded-lg py-1.5 pl-8 pr-3 text-sm placeholder-gray-500 dark:placeholder-gray-400 focus:bg-white dark:focus:bg-gray-700 focus:ring-0 transition-colors dark:text-gray-200">
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div id="noteList" class="flex-1 overflow-y-auto px-4 space-y-1 pb-6">
            <!-- Notes will be loaded here -->
        </div>
    </aside>

    <!-- Main Content -->
    <main id="mainContent" class="flex-1 flex-col relative bg-white dark:bg-gray-900 hidden md:flex w-full min-h-0">
        <!-- Editor Header/Toolbar -->
        <div class="border-b border-gray-100 dark:border-gray-800 flex items-center justify-between px-4 md:px-8 py-3 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm sticky top-0 z-10">
            <div class="flex items-center w-full">
                <!-- Back Button (Mobile) -->
                <button id="backBtn" class="mr-3 md:hidden p-2 text-gray-500 hover:text-black dark:hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                
                <div class="w-full">
                    <input type="text" id="noteTitle" placeholder="Title" 
                        class="text-xl md:text-2xl font-bold bg-transparent border-none focus:ring-0 w-full placeholder-gray-300 dark:placeholder-gray-600 p-0 dark:text-white">
                    <p id="noteTime" class="text-xs text-gray-400 dark:text-gray-500 mt-1 hidden"></p>
                </div>
            </div>
            
            <div class="flex items-center space-x-2 md:space-x-4 shrink-0">
                <span id="saveStatus" class="text-xs text-gray-400 dark:text-gray-500"></span>
                <select id="noteNotebookSelect" class="text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-3 py-1.5 text-gray-700 dark:text-gray-200 focus:ring-0 focus:border-gray-300 dark:focus:border-gray-600 max-w-[180px] truncate"></select>
                <button id="deleteBtn" class="p-2 text-gray-400 hover:text-red-500 transition-colors hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>

        <!-- Rich Text Editor Container -->
        <div id="editor" class="flex-1 min-h-0 overflow-y-auto dark:text-gray-200"></div>
    </main>

    <div id="notebookModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div id="notebookModalBackdrop" class="absolute inset-0 bg-black/30"></div>
        <div class="relative w-[90%] max-w-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl p-6">
            <div class="text-center">
                <h2 id="notebookModalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">新建笔记本</h2>
                <p id="notebookModalSubtitle" class="text-xs text-gray-400 dark:text-gray-500 mt-1">输入名称即可创建</p>
            </div>
            <div class="mt-4">
                <input id="notebookNameInput" type="text" class="w-full bg-gray-100 dark:bg-gray-800 border border-transparent focus:border-gray-300 dark:focus:border-gray-700 rounded-xl px-4 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-0" />
            </div>
            <div class="mt-5 flex items-center justify-between">
                <button id="notebookCancelBtn" class="px-4 py-2 text-sm rounded-full border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">取消</button>
                <button id="notebookCreateBtn" class="px-5 py-2 text-sm rounded-full bg-black text-white dark:bg-white dark:text-black hover:opacity-90 transition-opacity">创建</button>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="fixed bottom-6 right-6 z-50 group">
        <div id="fabMenu" class="absolute bottom-full right-0 mb-4 flex flex-col space-y-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
            <button id="formatJsonBtn" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center whitespace-nowrap" title="Format JSON">
                <span class="mr-2 text-xs font-medium">JSON</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
            <button id="insertTimeBtn" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center whitespace-nowrap" title="Insert Time">
                <span class="mr-2 text-xs font-medium">Time</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </button>
        </div>
        <button class="bg-black dark:bg-white text-white dark:text-black p-4 rounded-full shadow-xl hover:scale-105 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        </button>
    </div>

    <script>
        let currentNoteId = null;
        let quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Start writing...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'link', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }]
                ]
            }
        });

        // Add Editor Search UI
        const editorContainer = document.querySelector('#editor').parentElement;
        const searchBarHTML = `
            <div id="editorSearchBar" class="hidden absolute top-16 right-8 z-20 bg-white shadow-lg border border-gray-200 rounded-lg p-2 flex items-center space-x-2">
                <input type="text" id="editorSearchInput" placeholder="Find in note..." class="text-sm border-gray-200 rounded px-2 py-1 focus:ring-black focus:border-black">
                <button id="searchBtn" class="p-1 hover:bg-gray-100 rounded text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                <span id="searchCount" class="text-xs text-gray-400 min-w-[30px] text-center"></span>
                <button id="prevMatchBtn" class="p-1 hover:bg-gray-100 rounded text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                </button>
                <button id="nextMatchBtn" class="p-1 hover:bg-gray-100 rounded text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <button id="closeSearchBtn" class="p-1 hover:bg-gray-100 rounded text-gray-500 ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        `;
        editorContainer.insertAdjacentHTML('beforeend', searchBarHTML);

        const editorSearchBar = document.getElementById('editorSearchBar');
        const editorSearchInput = document.getElementById('editorSearchInput');
        const searchCountEl = document.getElementById('searchCount');
        let searchMatches = [];
        let currentMatchIndex = -1;

        // Custom Editor Search Logic
        function toggleEditorSearch() {
            // Re-select elements in case they were removed (though here they are static relative to editor)
            const searchBar = document.getElementById('editorSearchBar');
            if (!searchBar) return;
            
            searchBar.classList.toggle('hidden');
            if (!searchBar.classList.contains('hidden')) {
                const input = document.getElementById('editorSearchInput');
                if (input) {
                    input.focus();
                    // if (input.value) performEditorSearch(); // Don't auto-search on open
                }
            } else {
                clearSearchHighlights();
            }
        }

        function clearSearchHighlights() {
            quill.formatText(0, quill.getLength(), 'background', false);
            searchMatches = [];
            currentMatchIndex = -1;
            searchCountEl.textContent = '';
        }

        function performEditorSearch() {
            clearSearchHighlights();
            const term = editorSearchInput.value;
            if (!term) return;

            const text = quill.getText();
            let index = text.toLowerCase().indexOf(term.toLowerCase());
            while (index !== -1) {
                searchMatches.push({ index, length: term.length });
                index = text.toLowerCase().indexOf(term.toLowerCase(), index + 1);
            }

            if (searchMatches.length > 0) {
                searchMatches.forEach(match => {
                    quill.formatText(match.index, match.length, 'background', '#fef08a'); // yellow-200
                });
                currentMatchIndex = 0;
                highlightCurrentMatch();
            } else {
                searchCountEl.textContent = '0/0';
            }
        }

        function highlightCurrentMatch() {
            if (currentMatchIndex === -1) return;
            
            // Reset all to yellow
            searchMatches.forEach(match => {
                quill.formatText(match.index, match.length, 'background', '#fef08a');
            });

            // Highlight current to orange
            const match = searchMatches[currentMatchIndex];
            quill.formatText(match.index, match.length, 'background', '#fdba74'); // orange-300
            
            // Scroll to match
            quill.setSelection(match.index, match.length);
            
            searchCountEl.textContent = `${currentMatchIndex + 1}/${searchMatches.length}`;
        }

        // editorSearchInput.addEventListener('input', performEditorSearch); // Removed auto-search
        editorSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                if (searchMatches.length === 0) {
                     performEditorSearch();
                } else {
                    if (e.shiftKey) {
                        document.getElementById('prevMatchBtn').click();
                    } else {
                        document.getElementById('nextMatchBtn').click();
                    }
                }
            }
        });

        // Use event delegation for dynamically added elements
        document.addEventListener('click', function(e) {
            if (e.target.closest('#searchBtn')) {
                performEditorSearch();
            }
            if (e.target.closest('#nextMatchBtn')) {
                if (searchMatches.length === 0) return;
                currentMatchIndex = (currentMatchIndex + 1) % searchMatches.length;
                highlightCurrentMatch();
            }
            if (e.target.closest('#prevMatchBtn')) {
                if (searchMatches.length === 0) return;
                currentMatchIndex = (currentMatchIndex - 1 + searchMatches.length) % searchMatches.length;
                highlightCurrentMatch();
            }
            if (e.target.closest('#closeSearchBtn')) {
                editorSearchBar.classList.add('hidden');
                clearSearchHighlights();
            }
        });

        // Add keyboard shortcut for search (Ctrl+F / Cmd+F)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                if (!mainContent.classList.contains('hidden') || window.innerWidth >= 768) {
                   toggleEditorSearch();
                }
            }
        });


        const noteListEl = document.getElementById('noteList');
        const noteTitleEl = document.getElementById('noteTitle');
        const noteTimeEl = document.getElementById('noteTime');
        const saveStatusEl = document.getElementById('saveStatus');
        const deleteBtn = document.getElementById('deleteBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const backBtn = document.getElementById('backBtn');
        const globalSearchEl = document.getElementById('globalSearch');
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const sunIcon = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');
        const globalSearchInput = document.getElementById('globalSearch');
        const noteTitleInput = document.getElementById('noteTitle');
        const editorPlaceholder = document.querySelector('.ql-editor');
        const notebookFilterEl = document.getElementById('notebookFilter');
        const newNotebookBtn = document.getElementById('newNotebookBtn');
        const newNotebookBtnLabel = document.getElementById('newNotebookBtnLabel');
        const noteNotebookSelectEl = document.getElementById('noteNotebookSelect');
        const notebookModal = document.getElementById('notebookModal');
        const notebookModalBackdrop = document.getElementById('notebookModalBackdrop');
        const notebookModalTitle = document.getElementById('notebookModalTitle');
        const notebookModalSubtitle = document.getElementById('notebookModalSubtitle');
        const notebookNameInput = document.getElementById('notebookNameInput');
        const notebookCancelBtn = document.getElementById('notebookCancelBtn');
        const notebookCreateBtn = document.getElementById('notebookCreateBtn');
 
         // Language Logic
         let currentLang = localStorage.getItem('lang') || 'cn';
         
        // Autosave controls (declare early to allow use in functions above)
        var suppressAutoSave = false;
        var autoSaveTimeout = null;
        var blockAutoSaveUntilUser = false;
 
        const uiTexts = {
            cn: {
                searchPlaceholder: '搜索',
                titlePlaceholder: '标题',
                editorPlaceholder: '开始写作...',
                newNote: '新建笔记',
                logout: '退出登录',
                deleteConfirm: '删除此笔记？',
                saving: '保存中...',
                saved: '已保存',
                notebookAll: '全部笔记',
                notebookNone: '未选择笔记本',
                newNotebook: '新建笔记本',
                notebookCreateTitle: '新建笔记本',
                notebookCreateSubtitle: '输入名称即可创建',
                notebookCreatePlaceholder: '笔记本名称',
                notebookCancel: '取消',
                notebookCreate: '创建'
            },
            en: {
                searchPlaceholder: 'Search',
                titlePlaceholder: 'Title',
                editorPlaceholder: 'Start writing...',
                newNote: 'New Note',
                logout: 'Logout',
                deleteConfirm: 'Delete this note?',
                saving: 'Saving...',
                saved: 'Saved',
                notebookAll: 'All Notes',
                notebookNone: 'No Notebook',
                newNotebook: 'New Notebook',
                notebookCreateTitle: 'New Notebook',
                notebookCreateSubtitle: 'Enter a name to create',
                notebookCreatePlaceholder: 'Notebook name',
                notebookCancel: 'Cancel',
                notebookCreate: 'Create'
            }
        };

        let notebooks = [];
        let selectedNotebookId = null;
        let currentNoteNotebookId = null;

         const imageDBPromise = new Promise((resolve) => {
             const req = indexedDB.open('ZeNoteImages', 1);
             req.onupgradeneeded = () => {
                 const db = req.result;
                 if (!db.objectStoreNames.contains('images')) {
                     db.createObjectStore('images', { keyPath: 'id' });
                 }
             };
             req.onsuccess = () => resolve(req.result);
             req.onerror = () => resolve(null);
         });

         async function saveImage(id, dataURL) {
             const db = await imageDBPromise;
             if (!db) return;
             return new Promise((resolve, reject) => {
                 const tx = db.transaction('images', 'readwrite');
                 tx.objectStore('images').put({ id, dataURL });
                 tx.oncomplete = () => resolve();
                 tx.onerror = (e) => reject(e);
             });
         }

         async function getImage(id) {
             const db = await imageDBPromise;
             if (!db) return null;
             return new Promise((resolve) => {
                 const tx = db.transaction('images', 'readonly');
                 const req = tx.objectStore('images').get(id);
                 req.onsuccess = () => resolve(req.result ? req.result.dataURL : null);
                 req.onerror = () => resolve(null);
             });
         }

         async function hashDataURL(dataURL) {
             const enc = new TextEncoder();
             const bytes = enc.encode(dataURL);
             const digest = await crypto.subtle.digest('SHA-256', bytes);
             const arr = Array.from(new Uint8Array(digest));
             return arr.map(b => b.toString(16).padStart(2, '0')).join('').slice(0, 12);
         }
         async function hashString(str) {
             const enc = new TextEncoder();
             const bytes = enc.encode(str);
             const digest = await crypto.subtle.digest('SHA-256', bytes);
             const arr = Array.from(new Uint8Array(digest));
             return arr.map(b => b.toString(16).padStart(2, '0')).join('').slice(0, 12);
         }

         async function ensureImageIdsAndCache() {
             const imgs = quill.root.querySelectorAll('img');
             for (const img of imgs) {
                 let id = img.getAttribute('data-image-id');
                 const src = img.getAttribute('src') || '';
                 if (!id) {
                     const alt = img.getAttribute('alt') || '';
                     const m = alt.match(/image-([a-f0-9]{12})/);
                     if (m) {
                         id = m[1];
                         img.setAttribute('data-image-id', id);
                     } else {
                         id = src ? (src.startsWith('data:') ? await hashDataURL(src) : await hashString(src)) : Math.random().toString(36).slice(2, 14);
                         img.setAttribute('data-image-id', id);
                         img.setAttribute('alt', `image-${id}`);
                     }
                     if (src && src.startsWith('data:')) {
                         await saveImage(id, src);
                     }
                 } else {
                     const alt = img.getAttribute('alt') || '';
                     if (!alt || !alt.includes(id)) {
                         img.setAttribute('alt', `image-${id}`);
                     }
                 }
             }
         }

         async function resolveImages(noteId) {
             suppressAutoSave = true;
             quill.off('text-change');
             if (autoSaveTimeout) {
                 clearTimeout(autoSaveTimeout);
                 autoSaveTimeout = null;
             }
             try {
                 const imgs = quill.root.querySelectorAll('img');
                 for (const img of imgs) {
                     let id = img.getAttribute('data-image-id');
                     const src = img.getAttribute('src') || '';
                     if (!id) {
                         const alt = img.getAttribute('alt') || '';
                         const m = alt.match(/image-([a-f0-9]{12})/);
                         if (m) {
                             id = m[1];
                             img.setAttribute('data-image-id', id);
                         } else {
                             id = src ? (src.startsWith('data:') ? await hashDataURL(src) : await hashString(src)) : Math.random().toString(36).slice(2, 14);
                             img.setAttribute('data-image-id', id);
                             img.setAttribute('alt', `image-${id}`);
                         }
                     }
                     const cached = await getImage(id);
                     if (cached) {
                         if (src !== cached) img.setAttribute('src', cached);
                         continue;
                     }
                     try {
                         const r = await fetch(`api.php?action=get_image&image_id=${encodeURIComponent(id)}`);
                         const d = await r.json();
                         if (d && d.src) {
                             img.setAttribute('src', d.src);
                             await saveImage(id, d.src);
                             continue;
                         }
                     } catch (_) {}
                     if (src && src.startsWith('data:')) {
                         await saveImage(id, src);
                     }
                 }
             } finally {
                 suppressAutoSave = false;
                 quill.on('text-change', triggerAutoSave);
                 if (autoSaveTimeout) {
                     clearTimeout(autoSaveTimeout);
                     autoSaveTimeout = null;
                 }
             }
         }

         function updateLanguageUI(lang) {
             const t = uiTexts[lang];
             currentLang = lang;
             localStorage.setItem('lang', lang);
             
             globalSearchInput.placeholder = t.searchPlaceholder;
             noteTitleInput.placeholder = t.titlePlaceholder;
             document.querySelector('.ql-editor').dataset.placeholder = t.editorPlaceholder;
             const qlEditor = document.querySelector('.ql-editor');
             if (qlEditor) qlEditor.setAttribute('data-placeholder', t.editorPlaceholder);

             document.getElementById('newNoteBtn').title = t.newNote;
             document.getElementById('logoutBtn').title = t.logout;
            newNotebookBtn.title = t.newNotebook;
            if (newNotebookBtnLabel) newNotebookBtnLabel.textContent = t.newNotebook;
            if (notebookModalTitle) notebookModalTitle.textContent = t.notebookCreateTitle;
            if (notebookModalSubtitle) notebookModalSubtitle.textContent = t.notebookCreateSubtitle;
            if (notebookNameInput) notebookNameInput.placeholder = t.notebookCreatePlaceholder;
            if (notebookCancelBtn) notebookCancelBtn.textContent = t.notebookCancel;
            if (notebookCreateBtn) notebookCreateBtn.textContent = t.notebookCreate;
            renderNotebookOptions();
         }

         updateLanguageUI(currentLang);
 
         // Dark Mode Logic
        const isDarkMode = localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);

        function updateTheme(dark) {
            if (dark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
        }

        updateTheme(isDarkMode);

        themeToggleBtn.addEventListener('click', () => {
            const isDark = document.documentElement.classList.contains('dark');
            updateTheme(!isDark);
        });
        document.getElementById('settingsBtn').addEventListener('click', () => {
            window.location.href = 'settings.php';
        });

        let page = 1;
        let loading = false;
        let hasMore = true;
        let searchTimeout;

        async function fetchNotebooks() {
            const res = await fetch('api.php?action=get_notebooks');
            const data = await res.json();
            notebooks = data.notebooks || [];
            renderNotebookOptions();
        }

        function renderNotebookOptions() {
            const t = uiTexts[currentLang];
            if (notebookFilterEl) {
                const opts = [
                    `<option value="" ${selectedNotebookId === null ? 'selected' : ''}>${t.notebookAll}</option>`
                ].concat(notebooks.map(n => `<option value="${n.id}" ${selectedNotebookId == n.id ? 'selected' : ''}>${n.name}</option>`));
                notebookFilterEl.innerHTML = opts.join('');
            }
            if (noteNotebookSelectEl) {
                const opts2 = [
                    `<option value="0" ${currentNoteNotebookId === null ? 'selected' : ''}>${t.notebookNone}</option>`
                ].concat(notebooks.map(n => `<option value="${n.id}" ${currentNoteNotebookId == n.id ? 'selected' : ''}>${n.name}</option>`));
                noteNotebookSelectEl.innerHTML = opts2.join('');
            }
        }

        function openNotebookModal() {
            if (!notebookModal) return;
            notebookModal.classList.remove('hidden');
            notebookModal.classList.add('flex');
            if (notebookNameInput) {
                notebookNameInput.value = '';
                notebookNameInput.focus();
            }
        }

        function closeNotebookModal() {
            if (!notebookModal) return;
            notebookModal.classList.add('hidden');
            notebookModal.classList.remove('flex');
            if (notebookNameInput) notebookNameInput.value = '';
        }

        async function submitNotebookCreate() {
            const name = notebookNameInput ? notebookNameInput.value.trim() : '';
            if (!name) return;
            const res = await fetch('api.php?action=create_notebook', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name })
            });
            if (res.ok) {
                const d = await res.json().catch(() => null);
                await fetchNotebooks();
                if (d && d.id) {
                    selectedNotebookId = d.id;
                    if (!currentNoteId) currentNoteNotebookId = d.id;
                    renderNotebookOptions();
                    fetchNotes(true);
                }
                closeNotebookModal();
            }
        }

        if (newNotebookBtn) {
            newNotebookBtn.addEventListener('click', openNotebookModal);
        }
        if (notebookCancelBtn) {
            notebookCancelBtn.addEventListener('click', closeNotebookModal);
        }
        if (notebookModalBackdrop) {
            notebookModalBackdrop.addEventListener('click', closeNotebookModal);
        }
        if (notebookCreateBtn) {
            notebookCreateBtn.addEventListener('click', submitNotebookCreate);
        }
        if (notebookNameInput) {
            notebookNameInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    submitNotebookCreate();
                }
                if (e.key === 'Escape') {
                    closeNotebookModal();
                }
            });
        }

        if (notebookFilterEl) {
            notebookFilterEl.addEventListener('change', () => {
                selectedNotebookId = notebookFilterEl.value ? parseInt(notebookFilterEl.value) : null;
                fetchNotes(true);
            });
        }

        if (noteNotebookSelectEl) {
            noteNotebookSelectEl.addEventListener('change', async () => {
                const nbId = noteNotebookSelectEl.value === '0' ? null : parseInt(noteNotebookSelectEl.value);
                currentNoteNotebookId = nbId;
                if (!currentNoteId) return;
                await fetch('api.php?action=set_note_notebook', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: currentNoteId, notebook_id: nbId })
                });
                fetchNotes(true);
            });
        }

        // Mobile UI Helpers
        function showEditor() {
            if (window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
                // sidebar.classList.add('hidden'); // Optional: hide completely if animation not needed
                mainContent.classList.remove('hidden');
                mainContent.classList.add('flex');
            }
        }

        function showList() {
            if (window.innerWidth < 768) {
                sidebar.classList.remove('-translate-x-full');
                // sidebar.classList.remove('hidden');
                mainContent.classList.add('hidden');
                mainContent.classList.remove('flex');
                // Reset selection if needed, but keeping it selected is fine
            }
        }

        // FAB Logic
        document.getElementById('insertTimeBtn').addEventListener('click', () => {
            const now = new Date();
            const timeStr = now.getFullYear() + '-' + 
                String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                String(now.getDate()).padStart(2, '0') + ' ' + 
                String(now.getHours()).padStart(2, '0') + ':' + 
                String(now.getMinutes()).padStart(2, '0') + ':' + 
                String(now.getSeconds()).padStart(2, '0');
            
            const range = quill.getSelection(true);
            quill.insertText(range.index, timeStr);
            quill.setSelection(range.index + timeStr.length);
            saveNote(); // Trigger save
        });

        document.getElementById('formatJsonBtn').addEventListener('click', () => {
            const text = quill.getText();
            try {
                // Try to find JSON in the text. Simple approach: assume whole text or try to parse
                // If selection exists, format selection. Else format whole doc if it looks like JSON
                const range = quill.getSelection();
                let textToFormat = text;
                let startIndex = 0;
                let length = text.length;

                if (range && range.length > 0) {
                    textToFormat = quill.getText(range.index, range.length);
                    startIndex = range.index;
                    length = range.length;
                }

                const jsonObj = JSON.parse(textToFormat);
                const formatted = JSON.stringify(jsonObj, null, 4);
                
                // Replace text with formatted code block
                quill.deleteText(startIndex, length);
                quill.insertText(startIndex, formatted);
                quill.setSelection(startIndex, formatted.length);
                quill.format('code-block', true);
                saveNote(); // Trigger save
            } catch (e) {
                alert('Invalid JSON');
            }
        });

        // Fetch notes with pagination and search
        async function fetchNotes(reset = false) {
            if (reset) {
                page = 1;
                hasMore = true;
                noteListEl.scrollTop = 0;
            }
            
            if (loading || !hasMore) return;
            loading = true;

            const query = globalSearchEl.value.trim();
            try {
                let url = `api.php?action=get_notes&page=${page}&limit=20&q=${encodeURIComponent(query)}`;
                if (selectedNotebookId !== null) {
                    url += `&notebook_id=${selectedNotebookId}`;
                }
                const res = await fetch(url);
                const data = await res.json();
                
                if (reset) {
                    noteListEl.innerHTML = '';
                }

                if (data.notes.length < 20) {
                    hasMore = false;
                }

                renderNoteList(data.notes, true); // true = append
                page++;
            } catch (err) {
                console.error('Failed to fetch notes:', err);
            } finally {
                loading = false;
            }
        }

        globalSearchEl.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchNotes(true);
            }, 300);
        });

        // Infinite Scroll
        noteListEl.addEventListener('scroll', () => {
            if (noteListEl.scrollTop + noteListEl.clientHeight >= noteListEl.scrollHeight - 50) {
                fetchNotes();
            }
        });

        function renderNoteList(notes, append = false) {
            const html = notes.map(note => `
                <div id="note-item-${note.id}" onclick="loadNote(${note.id})" class="note-item p-4 rounded-xl cursor-pointer transition-all group border relative ${currentNoteId == note.id ? 'bg-white dark:bg-gray-800 shadow-sm border-gray-100 dark:border-gray-700' : 'border-transparent hover:bg-gray-100/50 dark:hover:bg-gray-800/50'}">
                    <div class="flex justify-between items-start">
                        <h3 class="font-semibold text-sm mb-1 truncate flex-1 dark:text-gray-200">${note.title || 'Untitled'}</h3>
                        ${note.is_pinned ? '<svg class="w-3 h-3 text-yellow-500 ml-2 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4H17V2H7V4H8V12L6 14V16H11V22H13V16H18V14L16 12Z" /></svg>' : ''}
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">${note.preview || 'No content'}</p>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-[10px] text-gray-300 dark:text-gray-600">${new Date(note.updated_at).toLocaleString()}</p>
                        <button onclick="event.stopPropagation(); togglePin(${note.id})" class="opacity-0 group-hover:opacity-100 p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all" title="${note.is_pinned ? 'Unpin' : 'Pin'}">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4H17V2H7V4H8V12L6 14V16H11V22H13V16H18V14L16 12Z" /></svg>
                        </button>
                    </div>
                </div>
            `).join('');

            if (append) {
                noteListEl.insertAdjacentHTML('beforeend', html);
            } else {
                noteListEl.innerHTML = html;
            }
        }

        function updateSidebarSelection(id) {
            document.querySelectorAll('.note-item').forEach(el => {
                if (el.id === `note-item-${id}`) {
                    el.classList.remove('border-transparent', 'hover:bg-gray-100/50', 'dark:hover:bg-gray-800/50');
                    el.classList.add('bg-white', 'dark:bg-gray-800', 'shadow-sm', 'border-gray-100', 'dark:border-gray-700');
                } else {
                    el.classList.add('border-transparent', 'hover:bg-gray-100/50', 'dark:hover:bg-gray-800/50');
                    el.classList.remove('bg-white', 'dark:bg-gray-800', 'shadow-sm', 'border-gray-100', 'dark:border-gray-700');
                }
            });
        }

        async function loadNote(id) {
            if (currentNoteId === id) return;
            currentNoteId = id;
            updateSidebarSelection(id); // Immediate UI update
            blockAutoSaveUntilUser = true;
            
            // Optional: Show loading state in editor or keep old content until new one loads
            // For now, we clear it to indicate change
            noteTitleEl.value = 'Loading...';
            quill.enable(false); // Disable editing while loading
            
            try {
                suppressAutoSave = true;
                if (autoSaveTimeout) {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = null;
                }
                const res = await fetch(`api.php?action=get_note&id=${id}`);
                const data = await res.json();
                
                // Prevent race condition if user switched again
                if (currentNoteId !== id) return;

                noteTitleEl.value = data.note.title;
                noteTimeEl.textContent = new Date(data.note.updated_at).toLocaleString();
                noteTimeEl.classList.remove('hidden');
                
                quill.setContents([], 'api');
                quill.clipboard.dangerouslyPasteHTML(0, data.note.content || '', 'api');
                await resolveImages(id);
                quill.enable(true);
                deleteBtn.classList.remove('hidden');
                currentNoteNotebookId = data.note.notebook_id || null;
                renderNotebookOptions();
                
                showEditor(); // Switch to editor view on mobile
            } catch (err) {
                console.error(err);
                quill.enable(true);
            } finally {
                suppressAutoSave = false;
                if (autoSaveTimeout) {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = null;
                }
            }
        }

        function createNewNote() {
            currentNoteId = null;
            blockAutoSaveUntilUser = true;
            noteTitleEl.value = '';
            noteTimeEl.textContent = '';
            noteTimeEl.classList.add('hidden');
            quill.root.innerHTML = '';
            deleteBtn.classList.add('hidden');
            showEditor(); // Switch to editor view on mobile
            noteTitleEl.focus();
            currentNoteNotebookId = selectedNotebookId;
            renderNotebookOptions();
        }

        async function togglePin(id) {
            const res = await fetch('api.php?action=toggle_pin', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            if (res.ok) {
                fetchNotes(true); // Reset list to reflect pin changes
            }
        }

        async function saveNote() {
            if (suppressAutoSave) return;
            if (!noteTitleEl.value && !quill.root.innerText.trim()) return;

            saveStatusEl.textContent = uiTexts[currentLang].saving;
            await ensureImageIdsAndCache();
            const res = await fetch('api.php?action=save_note', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: currentNoteId,
                    title: noteTitleEl.value || 'Untitled',
                    content: quill.root.innerHTML,
                    notebook_id: currentNoteNotebookId ?? null
                })
            });
            const result = await res.json();
            if (result.success) {
                currentNoteId = result.id;
                saveStatusEl.textContent = uiTexts[currentLang].saved;
                noteTimeEl.textContent = new Date().toLocaleString();
                noteTimeEl.classList.remove('hidden');
                deleteBtn.classList.remove('hidden');
                
                // Only refresh list if it's a new note or title/preview might have changed
                // For simplicity, we can just update the current item in DOM if it exists, 
                // but reloading the list ensures sort order is correct. 
                // However, reloading resets scroll. Ideally we update DOM.
                // For now, let's just reload the first page if it's a new note, or do nothing if update.
                // Actually, if we update, the time changes, so order might change.
                // Let's keep it simple: if new note, reload list. If update, maybe just update DOM?
                // Given the requirement "side bar sort by updated_at", we should reload.
                // To avoid jumping, we could just reload if it's a new note.
                // Let's stick to reloading for correctness for now, user asked for optimization not UX perfection on save yet.
                // Wait, user complained about "accumulating text". 
                // Let's just fetchNotes(true) for now to be safe, or just fetchNotes() if we want to keep position?
                // Actually, if we save, the note jumps to top. So we should probably reset.
                fetchNotes(true);
                
                setTimeout(() => saveStatusEl.textContent = '', 2000);
            }
        }

        async function deleteNote() {
            if (!currentNoteId || !confirm(uiTexts[currentLang].deleteConfirm)) return;

            const res = await fetch('api.php?action=delete_note', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: currentNoteId })
            });
            if (res.ok) {
                createNewNote();
                fetchNotes(true);
            }
        }

        // Auto-save logic
        const triggerAutoSave = (delta, oldDelta, source) => {
            if (source === 'api') return; // Ignore changes from API (loading note)
            if (suppressAutoSave) return; // Ignore programmatic image replacements
            if (blockAutoSaveUntilUser) return;
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(saveNote, 1000);
        };

        noteTitleEl.addEventListener('input', () => {
            blockAutoSaveUntilUser = false;
            triggerAutoSave(null, null, 'user');
        });
        quill.on('text-change', triggerAutoSave);
        quill.root.addEventListener('keydown', () => { blockAutoSaveUntilUser = false; });
        quill.root.addEventListener('paste', () => { blockAutoSaveUntilUser = false; });
        quill.root.addEventListener('drop', () => { blockAutoSaveUntilUser = false; });

        document.getElementById('newNoteBtn').addEventListener('click', createNewNote);
        document.getElementById('deleteBtn').addEventListener('click', deleteNote);
        backBtn.addEventListener('click', showList);
        document.getElementById('logoutBtn').addEventListener('click', async () => {
            await fetch('api.php?action=logout');
            window.location.href = 'login.php';
        });

        // Initial load
        (async () => {
            await fetchNotebooks();
            fetchNotes();
        })();

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(err => {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>
</body>
</html>
