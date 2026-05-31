<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="theme-color" content="#020617"/>
    <link rel="icon" href="{{ asset('images/logo-tk.png') }}" type="image/png"/>
    <title>{{ $title ?? 'TK Wonoayu - Portal Wali Murid' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Manrope', sans-serif; background-color: #f8f9fb; color: #2c3437; }
        h1, h2, h3, h4, h5, h6, .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 1rem); }
    </style>
    @yield('styles')
</head>
<body data-theme="tkwonoayu" class="bg-surface text-on-surface antialiased min-h-screen">
    <x-portal-dark-theme />

    {{-- Sidebar Component --}}
    <x-sidebar-parent />

    {{-- Main Content Area --}}
    <main class="relative z-10 md:ml-64 pt-0 min-h-screen">
        {{-- Top App Bar (Mobile) --}}
        <header class="md:hidden fixed top-0 w-full z-50 flex justify-between items-center px-6 py-4 bg-white/70 backdrop-blur-xl rounded-b-[2rem] shadow-[0_4px_20px_rgba(0,96,173,0.08)]">
            <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-xl bg-white/90 p-1.5 flex items-center justify-center">
                    <x-school-logo class="h-7 w-7" />
                </div>
                <div class="text-lg font-black text-primary font-headline tracking-tight">Wonoayu Portal</div>
            </div>
            <div class="flex gap-3 items-center">
                <span class="material-symbols-outlined text-primary">notifications</span>
                <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-primary-container text-sm">person</span>
                </div>
            </div>
        </header>

        <div class="pt-24 md:pt-10 pb-28 md:pb-10 px-6 md:px-10 max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>

    {{-- Bottom Nav (Mobile) --}}
    <nav class="md:hidden fixed bottom-0 left-0 w-full z-50 grid grid-cols-6 items-center px-3 py-3 bg-white rounded-t-[2.5rem] pb-safe shadow-[0_-10px_40px_rgba(0,0,0,0.06)]">
        <a href="{{ route('wali.dashboard') }}" class="flex flex-col items-center justify-center p-2 rounded-full {{ request()->routeIs('wali.dashboard') ? 'text-primary' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">home</span>
            <span class="text-[10px] font-bold mt-1">Beranda</span>
        </a>
        <a href="{{ route('wali.report') }}" class="flex flex-col items-center justify-center p-2 rounded-full {{ request()->routeIs('wali.report') ? 'text-primary' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.report') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">assessment</span>
            <span class="text-[10px] font-bold mt-1">Laporan</span>
        </a>
        <a href="{{ route('wali.gallery') }}" class="flex flex-col items-center justify-center p-2 rounded-full {{ request()->routeIs('wali.gallery') ? 'text-primary' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.gallery') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">palette</span>
            <span class="text-[10px] font-bold mt-1">Galeri</span>
        </a>
        <a href="{{ route('wali.attendance') }}" class="flex flex-col items-center justify-center p-2 rounded-full {{ request()->routeIs('wali.attendance') ? 'text-primary' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.attendance') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">event_available</span>
            <span class="text-[10px] font-bold mt-1">Absensi</span>
        </a>
        <a href="{{ route('wali.profile') }}" class="flex flex-col items-center justify-center p-2 rounded-full {{ request()->routeIs('wali.profile') ? 'text-primary' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.profile') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">child_care</span>
            <span class="text-[10px] font-bold mt-1">Profil</span>
        </a>
        <a href="{{ route('wali.telegram') }}" class="flex flex-col items-center justify-center p-2 rounded-full {{ request()->routeIs('wali.telegram') ? 'text-primary' : 'text-on-surface-variant' }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.telegram') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">send</span>
            <span class="text-[10px] font-bold mt-1">Telegram</span>
        </a>
    </nav>

    @yield('scripts')
</body>
</html>
