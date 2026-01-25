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
                <div class="flex space-x-2">
                    <button id="langToggleBtn" class="p-2 text-sm font-medium hover:bg-white dark:hover:bg-gray-800 rounded-full transition-colors border border-transparent hover:border-gray-200 dark:hover:border-gray-700 dark:text-gray-300">
                        CN
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
    <main id="mainContent" class="flex-1 flex-col relative bg-white dark:bg-gray-900 hidden md:flex w-full">
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
                <button id="deleteBtn" class="p-2 text-gray-400 hover:text-red-500 transition-colors hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>

        <!-- Rich Text Editor Container -->
        <div id="editor" class="flex-1 dark:text-gray-200"></div>
    </main>

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
         const langToggleBtn = document.getElementById('langToggleBtn');
         const sunIcon = document.getElementById('sunIcon');
         const moonIcon = document.getElementById('moonIcon');
         const globalSearchInput = document.getElementById('globalSearch');
         const noteTitleInput = document.getElementById('noteTitle');
         const editorPlaceholder = document.querySelector('.ql-editor');
 
         // Language Logic
         let currentLang = localStorage.getItem('lang') || 'cn';
         
         const uiTexts = {
             cn: {
                 searchPlaceholder: '搜索',
                 titlePlaceholder: '标题',
                 editorPlaceholder: '开始写作...',
                 newNote: '新建笔记',
                 logout: '退出登录',
                 deleteConfirm: '删除此笔记？',
                 saving: '保存中...',
                 saved: '已保存'
             },
             en: {
                 searchPlaceholder: 'Search',
                 titlePlaceholder: 'Title',
                 editorPlaceholder: 'Start writing...',
                 newNote: 'New Note',
                 logout: 'Logout',
                 deleteConfirm: 'Delete this note?',
                 saving: 'Saving...',
                 saved: 'Saved'
             }
         };

         function updateLanguageUI(lang) {
             const t = uiTexts[lang];
             currentLang = lang;
             localStorage.setItem('lang', lang);
             
             // Update Button Text
             langToggleBtn.textContent = lang.toUpperCase();
             
             // Update Placeholders
             globalSearchInput.placeholder = t.searchPlaceholder;
             noteTitleInput.placeholder = t.titlePlaceholder;
             // Quill placeholder is tricky to update dynamically via API, so we use dataset or CSS
             document.querySelector('.ql-editor').dataset.placeholder = t.editorPlaceholder;
             // But Quill uses ::before content: attr(data-placeholder), so updating the attribute works if configured
             // Re-initializing is heavy. Let's try direct DOM manipulation of the data attribute if Quill supports it
             // Quill sets data-placeholder on .ql-editor
             const qlEditor = document.querySelector('.ql-editor');
             if (qlEditor) qlEditor.setAttribute('data-placeholder', t.editorPlaceholder);

             // Update Tooltips
             document.getElementById('newNoteBtn').title = t.newNote;
             document.getElementById('logoutBtn').title = t.logout;
         }

         langToggleBtn.addEventListener('click', () => {
             const newLang = currentLang === 'cn' ? 'en' : 'cn';
             updateLanguageUI(newLang);
         });

         // Initialize Language
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

        let page = 1;
        let loading = false;
        let hasMore = true;
        let searchTimeout;

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
                const res = await fetch(`api.php?action=get_notes&page=${page}&limit=20&q=${encodeURIComponent(query)}`);
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
            
            // Optional: Show loading state in editor or keep old content until new one loads
            // For now, we clear it to indicate change
            noteTitleEl.value = 'Loading...';
            quill.enable(false); // Disable editing while loading
            
            try {
                const res = await fetch(`api.php?action=get_note&id=${id}`);
                const data = await res.json();
                
                // Prevent race condition if user switched again
                if (currentNoteId !== id) return;

                noteTitleEl.value = data.note.title;
                noteTimeEl.textContent = new Date(data.note.updated_at).toLocaleString();
                noteTimeEl.classList.remove('hidden');
                
                quill.setContents([], 'api');
                quill.clipboard.dangerouslyPasteHTML(0, data.note.content || '', 'api');
                quill.enable(true);
                deleteBtn.classList.remove('hidden');
                
                showEditor(); // Switch to editor view on mobile
            } catch (err) {
                console.error(err);
                quill.enable(true);
            }
        }

        function createNewNote() {
            currentNoteId = null;
            noteTitleEl.value = '';
            noteTimeEl.textContent = '';
            noteTimeEl.classList.add('hidden');
            quill.root.innerHTML = '';
            deleteBtn.classList.add('hidden');
            showEditor(); // Switch to editor view on mobile
            noteTitleEl.focus();
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
            if (!noteTitleEl.value && !quill.root.innerText.trim()) return;

            saveStatusEl.textContent = uiTexts[currentLang].saving;
            const res = await fetch('api.php?action=save_note', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: currentNoteId,
                    title: noteTitleEl.value || 'Untitled',
                    content: quill.root.innerHTML
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
        let autoSaveTimeout;
        const triggerAutoSave = (delta, oldDelta, source) => {
            if (source === 'api') return; // Ignore changes from API (loading note)
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(saveNote, 1000);
        };

        noteTitleEl.addEventListener('input', () => triggerAutoSave(null, null, 'user'));
        quill.on('text-change', triggerAutoSave);

        document.getElementById('newNoteBtn').addEventListener('click', createNewNote);
        document.getElementById('deleteBtn').addEventListener('click', deleteNote);
        backBtn.addEventListener('click', showList);
        document.getElementById('logoutBtn').addEventListener('click', async () => {
            await fetch('api.php?action=logout');
            window.location.href = 'login.php';
        });

        // Initial load
        fetchNotes();
    </script>
</body>
</html>
