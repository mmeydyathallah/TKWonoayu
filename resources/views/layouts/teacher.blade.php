<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="theme-color" content="#020617"/>
    <link rel="icon" href="{{ asset('images/logo-tk.png') }}" type="image/png"/>
    <title>{{ $title ?? 'TK Wonoayu Madiun - Teacher Portal' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Manrope', sans-serif; background-color: #f8f9fb; color: #2c3437; }
        h1, h2, h3, h4, h5, h6, .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @yield('styles')
</head>
<body data-theme="tkwonoayu" class="bg-surface text-on-surface flex min-h-screen">
    <x-portal-dark-theme />

    {{-- SideNavBar --}}
    <x-sidebar-teacher />

    {{-- Main Content Area --}}
    <main class="relative z-10 flex-1 md:ml-64 p-4 md:p-8 overflow-y-auto">
        {{-- Top App Bar (Mobile Only) --}}
        <header class="md:hidden flex justify-between items-center mb-8 bg-surface-container-lowest p-4 rounded-xl shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/90 p-1.5 flex items-center justify-center">
                    <x-school-logo class="h-7 w-7" />
                </div>
                <h1 class="font-headline font-bold text-primary">TK Wonoayu</h1>
            </div>
            <button id="mobileMenuOpenBtn" type="button" class="text-on-surface" aria-label="Buka menu">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </header>

        {{-- Mobile Drawer --}}
        <div id="mobileMenuOverlay" class="md:hidden fixed inset-0 bg-black/40 opacity-0 pointer-events-none transition-opacity duration-300 z-40"></div>
        <aside id="mobileMenu" class="md:hidden fixed inset-y-0 left-0 z-50 w-72 max-w-[85%] bg-surface-container-lowest shadow-2xl transform -translate-x-full transition-transform duration-300 overflow-y-auto">
            <div class="flex items-center justify-between px-4 py-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/90 flex items-center justify-center p-1.5 shadow">
                        <x-school-logo class="h-8 w-8" />
                    </div>
                    <div>
                        <p class="font-headline text-base font-bold text-slate-900">Portal Guru</p>
                        <p class="text-xs text-slate-500">TK Wonoayu</p>
                    </div>
                </div>
                <button id="mobileMenuCloseBtn" type="button" class="p-2 rounded-full text-slate-600 hover:bg-slate-100" aria-label="Tutup menu">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex flex-col gap-2 p-4">
                <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.dashboard') ? 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/30 scale-[1.02]' : 'text-slate-600 hover:bg-slate-100 rounded-2xl' }} transition-all duration-300" href="{{ route('guru.dashboard') }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">dashboard</span>
                    <span class="font-bold text-xs">Dashboard</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.students.*') ? 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/30 scale-[1.02]' : 'text-slate-600 hover:bg-slate-100 rounded-2xl' }} transition-all duration-300" href="{{ route('guru.students.index') }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.students.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">group</span>
                    <span class="font-bold text-xs">Database Siswa</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.agenda') ? 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/30 scale-[1.02]' : 'text-slate-600 hover:bg-slate-100 rounded-2xl' }} transition-all duration-300" href="{{ route('guru.agenda') }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.agenda') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">calendar_month</span>
                    <span class="font-bold text-xs">Agenda Sekolah</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.attendance.*') ? 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/30 scale-[1.02]' : 'text-slate-600 hover:bg-slate-100 rounded-2xl' }} transition-all duration-300" href="{{ route('guru.attendance.index') }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.attendance.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">event_available</span>
                    <span class="font-bold text-xs">Absensi</span>
                </a>
                <div class="my-4 border-t border-slate-700/80"></div>
                <p class="px-4 text-[10px] font-black text-sky-200 uppercase tracking-[0.2em] mb-1">Modul Penilaian</p>
                <a class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('guru.daily') ? 'bg-primary text-white shadow-lg shadow-sky-900/35 ring-1 ring-sky-300/40' : 'bg-slate-900/35 text-slate-300 hover:bg-slate-800/85 hover:text-white' }} transition-all duration-300" href="{{ route('guru.daily') }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.daily') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">event_note</span>
                    <span class="font-bold text-xs">Penilaian Harian</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('guru.anecdotal') ? 'bg-primary text-white shadow-lg shadow-sky-900/35 ring-1 ring-sky-300/40' : 'bg-slate-900/35 text-slate-300 hover:bg-slate-800/85 hover:text-white' }} transition-all duration-300" href="{{ route('guru.anecdotal') }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.anecdotal') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">history_edu</span>
                    <span class="font-bold text-xs">Catatan Anekdot</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('guru.development-narrative') ? 'bg-primary text-white shadow-lg shadow-sky-900/35 ring-1 ring-sky-300/40' : 'bg-slate-900/35 text-slate-300 hover:bg-slate-800/85 hover:text-white' }} transition-all duration-300" href="{{ route('guru.development-narrative') }}">
                    <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.development-narrative') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">description</span>
                    <span class="font-bold text-xs">Narasi Perkembangan</span>
                </a>
                <div class="my-4 border-t border-slate-700/80"></div>
                <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.settings') ? 'text-primary font-black' : 'text-slate-500 hover:text-slate-900' }} transition-colors text-xs font-bold" href="{{ route('guru.settings') }}">
                    <span class="material-symbols-outlined text-[18px]">settings</span>
                    Pengaturan
                </a>
                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-slate-500 hover:text-rose-600 transition-colors text-xs font-bold">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        @yield('content')
    </main>

    <script>
        function toggleMobileTeacherMenu(open) {
            const menu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('mobileMenuOverlay');
            if (!menu || !overlay) return;

            if (open) {
                menu.classList.remove('-translate-x-full');
                overlay.classList.remove('pointer-events-none');
                overlay.classList.add('opacity-100');
                document.body.classList.add('overflow-hidden');
            } else {
                menu.classList.add('-translate-x-full');
                overlay.classList.add('pointer-events-none');
                overlay.classList.remove('opacity-100');
                document.body.classList.remove('overflow-hidden');
            }
        }

        document.getElementById('mobileMenuOpenBtn')?.addEventListener('click', function() {
            toggleMobileTeacherMenu(true);
        });
        document.getElementById('mobileMenuCloseBtn')?.addEventListener('click', function() {
            toggleMobileTeacherMenu(false);
        });
        document.getElementById('mobileMenuOverlay')?.addEventListener('click', function() {
            toggleMobileTeacherMenu(false);
        });
    </script>
    @yield('scripts')
</body>
</html>
