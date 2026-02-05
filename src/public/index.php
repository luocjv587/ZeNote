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
    <!-- Highlight.js (for syntax highlighting) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    
    <!-- KaTeX (for formulas) -->
    <link href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>

    <!-- Quill Rich Text Editor (v2.0) -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill-table-better@1/dist/quill-table-better.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-table-better@1/dist/quill-table-better.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quilljs-markdown@1.2.0/dist/quilljs-markdown-common-style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quilljs-markdown@1.2.0/dist/quilljs-markdown.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
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
                <button id="newNotebookBtn" title="Manage" class="px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7.5A2.5 2.5 0 015.5 5h4l2 2H19a2 2 0 012 2v6.5A2.5 2.5 0 0118.5 18h-13A2.5 2.5 0 013 15.5v-8z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10.5v4m2-2h-4"/>
                    </svg>
                    <span id="newNotebookBtnLabel" class="text-xs">管理</span>
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
                
                <!-- History Button -->
                <button id="historyBtn" class="p-2 text-gray-400 hover:text-black dark:hover:text-white transition-colors hidden" title="History">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </button>

                <!-- Restore Button (Trash Mode) -->
                <button id="restoreBtn" class="p-2 text-green-500 hover:text-green-600 transition-colors hidden" title="Restore">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>

                <!-- Delete Forever Button (Trash Mode) -->
                <button id="deleteForeverBtn" class="p-2 text-red-500 hover:text-red-600 transition-colors hidden" title="Delete Forever">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>

                <button id="deleteBtn" class="p-2 text-gray-400 hover:text-red-500 transition-colors hidden" title="Move to Trash">
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
            
            <!-- Create Section -->
            <div class="mt-4 flex space-x-2">
                <input id="notebookNameInput" type="text" class="flex-1 bg-gray-100 dark:bg-gray-800 border border-transparent focus:border-gray-300 dark:focus:border-gray-700 rounded-xl px-4 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-0" />
                <button id="notebookCreateBtn" class="px-4 py-2 text-sm rounded-xl bg-black text-white dark:bg-white dark:text-black hover:opacity-90 transition-opacity whitespace-nowrap">创建</button>
            </div>

            <!-- List Section -->
            <div class="mt-6 border-t border-gray-100 dark:border-gray-800 pt-4">
                <div id="notebookManagementList" class="max-h-48 overflow-y-auto space-y-2 pr-1">
                    <!-- Items injected here -->
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-5 flex justify-end">
                <button id="notebookCancelBtn" class="px-4 py-2 text-sm rounded-full border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors w-full">关闭</button>
            </div>
        </div>
    </div>

    <div id="historyModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div id="historyModalBackdrop" class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
        <div class="relative w-[90%] max-w-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl p-6 max-h-[80vh] flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Version History</h2>
                <button id="closeHistoryBtn" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="historyList" class="flex-1 overflow-y-auto space-y-2 pr-2">
                <!-- History items will be injected here -->
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
            <button id="calculateBtn" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center whitespace-nowrap" title="Calculate">
                <span class="mr-2 text-xs font-medium">Calc</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </button>
            <button id="insertTimeBtn" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center whitespace-nowrap" title="Insert Time">
                <span class="mr-2 text-xs font-medium">Time</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </button>
            <button id="exportPdfBtn" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-3 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center whitespace-nowrap" title="Export to PDF">
                <span class="mr-2 text-xs font-medium">PDF</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </button>
        </div>
        <button class="bg-black dark:bg-white text-white dark:text-black p-4 rounded-full shadow-xl hover:scale-105 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        </button>
    </div>

    <script>
        let currentNoteId = null;
        let isTrashMode = false;
        // Register Table Module
        if (typeof QuillTableBetter !== 'undefined') {
            Quill.register({
                'modules/table-better': QuillTableBetter
            }, true);
        }

        let quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Start writing...',
            modules: {
                syntax: true,
                table: false,
                'table-better': {
                    language: 'en_US',
                    menus: ['column', 'row', 'merge', 'table', 'cell', 'wrap', 'copy', 'delete'],
                    toolbarTable: true
                },
                keyboard: {
                    bindings: QuillTableBetter.keyboardBindings
                },
                toolbar: [
                    [{ 'font': [] }, { 'size': [] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'direction': 'rtl' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video', 'formula'],
                    ['clean'],
                    ['table-better']
                ]
            }
        });

        // Initialize Markdown Module
        if (typeof QuillMarkdown !== 'undefined') {
            new QuillMarkdown(quill, {});
        }

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

        // Add keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl+S / Cmd+S to Save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveNote();
            }
            // Ctrl+F / Cmd+F to Search
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
        const notebookManagementList = document.getElementById('notebookManagementList');
 
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
                newNotebook: '管理',
                notebookCreateTitle: '管理笔记本',
                notebookCreateSubtitle: '新建或管理现有笔记本',
                notebookCreatePlaceholder: '笔记本名称',
                notebookCancel: '关闭',
                notebookCreate: '创建',
                trashBin: '🗑️ 废纸篓',
                historyTitle: '版本历史',
                restore: '恢复',
                deleteForever: '永久删除',
                deleteForeverConfirm: '永久删除此笔记？无法撤销。',
                restoreVersionConfirm: '恢复此版本？当前内容将保存为新的历史版本。',
                noHistory: '无历史记录',
                noSummary: '无摘要',
                moveToTrash: '移至废纸篓',
                deleteNotebookConfirm: '确定删除此笔记本吗？其中的笔记将被移出该笔记本。'
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
                newNotebook: 'Manage',
                notebookCreateTitle: 'Manage Notebooks',
                notebookCreateSubtitle: 'Create new or manage existing',
                notebookCreatePlaceholder: 'Notebook name',
                notebookCancel: 'Close',
                notebookCreate: 'Create',
                trashBin: '🗑️ Trash Bin',
                historyTitle: 'Version History',
                restore: 'Restore',
                deleteForever: 'Delete Forever',
                deleteForeverConfirm: 'Permanently delete this note? This cannot be undone.',
                restoreVersionConfirm: 'Restore this version? Current content will be saved as a new history version.',
                noHistory: 'No history available',
                noSummary: 'No summary',
                moveToTrash: 'Move to Trash',
                deleteNotebookConfirm: 'Delete this notebook? Notes inside will be moved out.'
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
            
            // Update Trash/History UI
            document.getElementById('historyBtn').title = t.historyTitle;
            document.getElementById('restoreBtn').title = t.restore;
            document.getElementById('deleteForeverBtn').title = t.deleteForever;
            document.getElementById('deleteBtn').title = t.moveToTrash;
            const historyModalTitle = document.querySelector('#historyModal h2');
            if (historyModalTitle) historyModalTitle.textContent = t.historyTitle;
            
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
                const trashSelected = isTrashMode ? 'selected' : '';
                const allSelected = (selectedNotebookId === null && !isTrashMode) ? 'selected' : '';
                
                const opts = [
                    `<option value="" ${allSelected}>${t.notebookAll}</option>`,
                    `<option value="trash" ${trashSelected}>${t.trashBin}</option>`
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

        function renderNotebookManagementList() {
            if (!notebookManagementList) return;
            if (notebooks.length === 0) {
                notebookManagementList.innerHTML = '<p class="text-center text-xs text-gray-400 py-4">No notebooks</p>';
                return;
            }
            
            notebookManagementList.innerHTML = notebooks.map(n => `
                <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg group">
                    <span class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-[200px]">${n.name}</span>
                    <button onclick="deleteNotebook(${n.id})" class="text-gray-400 hover:text-red-500 p-1 rounded transition-colors opacity-0 group-hover:opacity-100" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            `).join('');
        }

        window.deleteNotebook = async function(id) {
            if (!confirm(uiTexts[currentLang].deleteNotebookConfirm)) return;
            
            const res = await fetch('api.php?action=delete_notebook', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            
            if (res.ok) {
                await fetchNotebooks();
                renderNotebookManagementList();
                
                if (currentNoteNotebookId == id) {
                    currentNoteNotebookId = null;
                    renderNotebookOptions();
                }
                
                if (selectedNotebookId == id) {
                    selectedNotebookId = null;
                    fetchNotes(true);
                }
            }
        };

        function openNotebookModal() {
            if (!notebookModal) return;
            notebookModal.classList.remove('hidden');
            notebookModal.classList.add('flex');
            if (notebookNameInput) {
                notebookNameInput.value = '';
                notebookNameInput.focus();
            }
            renderNotebookManagementList();
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
                     renderNotebookOptions();
                }
                if (notebookNameInput) notebookNameInput.value = '';
                renderNotebookManagementList();
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
                const val = notebookFilterEl.value;
                if (val === 'trash') {
                    isTrashMode = true;
                    selectedNotebookId = null;
                } else {
                    isTrashMode = false;
                    selectedNotebookId = val ? parseInt(val) : null;
                }
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
                // Push history state for mobile back button support
                history.pushState({ view: 'editor' }, null, '');
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

        document.getElementById('exportPdfBtn').addEventListener('click', () => {
            const element = document.querySelector('.ql-editor');
            const opt = {
                margin:       1,
                filename:     (document.getElementById('noteTitle').value || 'note') + '.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        });

        document.getElementById('calculateBtn').addEventListener('click', () => {
            const range = quill.getSelection();
            if (!range || range.length === 0) return;
            
            const text = quill.getText(range.index, range.length);
            // Allow numbers, operators (+, -, *, /), parentheses, dots, and spaces
            if (/^[0-9+\-*/().\s]+$/.test(text)) {
                try {
                    // Safe evaluation for basic math
                    const result = new Function('return ' + text)();
                    // Check if result is a number and finite
                    if (typeof result === 'number' && isFinite(result)) {
                        const newText = text + ' = ' + result;
                        quill.deleteText(range.index, range.length);
                        quill.insertText(range.index, newText);
                        quill.setSelection(range.index + newText.length);
                        saveNote();
                    }
                } catch (e) {
                    console.error('Calculation error', e);
                }
            }
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
                if (isTrashMode) {
                    url += '&trash=1';
                } else if (selectedNotebookId !== null) {
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
            if (currentNoteId === id) {
                showEditor();
                return;
            }
            currentNoteId = id;
            updateSidebarSelection(id); 
            blockAutoSaveUntilUser = true;
            
            noteTitleEl.value = 'Loading...';
            quill.enable(false);
            
            // Reset buttons
            document.getElementById('historyBtn').classList.add('hidden');
            document.getElementById('restoreBtn').classList.add('hidden');
            document.getElementById('deleteForeverBtn').classList.add('hidden');
            document.getElementById('deleteBtn').classList.add('hidden');
            
            try {
                suppressAutoSave = true;
                if (autoSaveTimeout) {
                    clearTimeout(autoSaveTimeout);
                    autoSaveTimeout = null;
                }
                const res = await fetch(`api.php?action=get_note&id=${id}`);
                const data = await res.json();
                
                if (currentNoteId !== id) return;

                noteTitleEl.value = data.note.title;
                noteTimeEl.textContent = new Date(data.note.updated_at).toLocaleString();
                noteTimeEl.classList.remove('hidden');
                
                quill.setContents([], 'api');
                quill.clipboard.dangerouslyPasteHTML(0, data.note.content || '', 'api');
                await resolveImages(id);
                
                currentNoteNotebookId = data.note.notebook_id || null;
                renderNotebookOptions();

                if (isTrashMode) {
                    quill.enable(false);
                    noteTitleEl.disabled = true;
                    document.getElementById('restoreBtn').classList.remove('hidden');
                    document.getElementById('deleteForeverBtn').classList.remove('hidden');
                } else {
                    quill.enable(true);
                    noteTitleEl.disabled = false;
                    document.getElementById('deleteBtn').classList.remove('hidden');
                    document.getElementById('historyBtn').classList.remove('hidden');
                }
                
                showEditor(); 
                
                if (window.innerWidth < 768) {
                    noteTitleEl.blur();
                    quill.blur();
                }
            } catch (err) {
                console.error(err);
                if (!isTrashMode) quill.enable(true);
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
            quill.setContents([]);
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
            if (suppressAutoSave || isTrashMode) return;
            if (!noteTitleEl.value && !quill.root.innerText.trim()) return;

            saveStatusEl.textContent = uiTexts[currentLang].saving;
            await ensureImageIdsAndCache();

            // Fix: Remove Quill 2.0 syntax highlighter UI artifacts (.ql-ui) before saving
            // This prevents the language list ("PlainBashC++...") from being saved into the note content
            const clone = quill.root.cloneNode(true);
            clone.querySelectorAll('.ql-ui').forEach(el => el.remove());
            const cleanContent = clone.innerHTML;

            const res = await fetch('api.php?action=save_note', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: currentNoteId,
                    title: noteTitleEl.value || 'Untitled',
                    content: cleanContent,
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
        
        // Handle back button click
        backBtn.addEventListener('click', () => {
            if (history.state && history.state.view === 'editor') {
                history.back();
            } else {
                showList();
            }
        });
        
        // Handle mobile hardware back button
        window.addEventListener('popstate', (event) => {
             // If we are popping back from editor (state is null or different), show list
             if (!event.state || event.state.view !== 'editor') {
                 showList();
             }
        });

        document.getElementById('logoutBtn').addEventListener('click', async () => {
            await fetch('api.php?action=logout');
            window.location.href = 'login.php';
        });

        // Initial load
        (async () => {
            await fetchNotebooks();
            fetchNotes();
        })();

        // Trash & History Logic
        async function restoreNote() {
            if (!currentNoteId) return;
            const res = await fetch('api.php?action=restore_note', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: currentNoteId })
            });
            if (res.ok) {
                createNewNote();
                fetchNotes(true);
            }
        }

        async function deleteForever() {
            if (!currentNoteId || !confirm(uiTexts[currentLang].deleteForeverConfirm)) return;
            const res = await fetch('api.php?action=delete_note', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: currentNoteId, force: true })
            });
            if (res.ok) {
                createNewNote();
                fetchNotes(true);
            }
        }

        async function showHistory() {
            if (!currentNoteId) return;
            const res = await fetch(`api.php?action=get_history&id=${currentNoteId}`);
            const data = await res.json();
            const historyList = document.getElementById('historyList');
            
            if (!data.history || data.history.length === 0) {
                historyList.innerHTML = '<p class="text-gray-500 text-center py-4">' + uiTexts[currentLang].noHistory + '</p>';
            } else {
                historyList.innerHTML = data.history.map(h => `
                    <div class="flex justify-between items-center p-3 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-800">
                        <div>
                            <p class="text-sm font-medium dark:text-gray-200">${new Date(h.created_at.replace(' ', 'T') + 'Z').toLocaleString()}</p>
                            <p class="text-xs text-gray-500 truncate max-w-[200px]">${h.summary || uiTexts[currentLang].noSummary}</p>
                        </div>
                        <button onclick="restoreVersion(${h.id})" class="text-xs bg-black text-white dark:bg-white dark:text-black px-3 py-1.5 rounded-full hover:opacity-80">${uiTexts[currentLang].restore}</button>
                    </div>
                `).join('');
            }
            
            document.getElementById('historyModal').classList.remove('hidden');
            document.getElementById('historyModal').classList.add('flex');
        }

        function closeHistory() {
            document.getElementById('historyModal').classList.add('hidden');
            document.getElementById('historyModal').classList.remove('flex');
        }

        window.restoreVersion = async function(historyId) {
            if (!confirm(uiTexts[currentLang].restoreVersionConfirm)) return;
            
            // First get the history detail
            const res = await fetch(`api.php?action=get_history_detail&history_id=${historyId}`);
            const data = await res.json();
            
            if (data.history) {
                // Update current note content
                noteTitleEl.value = data.history.title;
                quill.setContents([]);
                quill.clipboard.dangerouslyPasteHTML(0, data.history.content, 'api');
                
                // Trigger save to persist this restoration as current version
                await saveNote();
                
                closeHistory();
            }
        };

        document.getElementById('restoreBtn').addEventListener('click', restoreNote);
        document.getElementById('deleteForeverBtn').addEventListener('click', deleteForever);
        document.getElementById('historyBtn').addEventListener('click', showHistory);
        document.getElementById('closeHistoryBtn').addEventListener('click', closeHistory);
        document.getElementById('historyModalBackdrop').addEventListener('click', closeHistory);

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
