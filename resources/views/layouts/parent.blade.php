<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'TK Wonoayu - Portal Wali Murid' }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-tint": "#0060ad", "tertiary": "#775b00", "tertiary-container": "#fdc825",
                        "surface-dim": "#d4dbdf", "on-background": "#2c3437", "surface-container-lowest": "#ffffff",
                        "primary-dim": "#005498", "on-secondary-fixed": "#004a15", "on-tertiary-fixed-variant": "#634c00",
                        "primary-fixed": "#68abff", "surface-container-highest": "#dce4e8", "on-tertiary": "#fff8f0",
                        "primary": "#0060ad", "primary-fixed-dim": "#599ef1", "on-primary": "#f8f8ff",
                        "surface-bright": "#f8f9fb", "secondary-fixed-dim": "#91e892", "on-secondary": "#eaffe4",
                        "surface-container-low": "#f0f4f7", "secondary-fixed": "#9ff79f", "outline-variant": "#acb3b7",
                        "surface-container-high": "#e3e9ed", "inverse-on-surface": "#9a9d9f", "error": "#a83836",
                        "tertiary-fixed-dim": "#edba10", "primary-container": "#68abff", "error-container": "#fa746f",
                        "secondary-dim": "#00611e", "inverse-primary": "#68abff", "secondary": "#136e27",
                        "on-secondary-fixed-variant": "#0c6a24", "surface-variant": "#dce4e8", "on-surface": "#2c3437",
                        "on-secondary-container": "#005f1d", "on-tertiary-container": "#584300",
                        "surface-container": "#eaeff2", "on-surface-variant": "#596064", "outline": "#747c80",
                        "on-primary-container": "#002b52", "secondary-container": "#9ff79f", "on-primary-fixed": "#000c1e",
                        "on-error-container": "#6e0a12", "background": "#f8f9fb", "error-dim": "#67040d",
                        "on-tertiary-fixed": "#403000", "inverse-surface": "#0b0f10", "tertiary-dim": "#685000",
                        "on-error": "#fff7f6", "on-primary-fixed-variant": "#003461", "tertiary-fixed": "#fdc825",
                        "surface": "#f8f9fb"
                    },
                    "borderRadius": { "DEFAULT": "1rem", "lg": "2rem", "xl": "3rem", "full": "9999px" },
                    "fontFamily": { "headline": ["Plus Jakarta Sans"], "body": ["Manrope"], "label": ["Manrope"] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Manrope', sans-serif; background-color: #f8f9fb; color: #2c3437; }
        h1, h2, h3, h4, h5, h6, .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 1rem); }
    </style>
    @yield('styles')
</head>
<body class="bg-surface text-on-surface antialiased min-h-screen">
    <x-portal-dark-theme />

    {{-- Sidebar Component --}}
    <x-sidebar-parent />

    {{-- Main Content Area --}}
    <main class="relative z-10 md:ml-64 pt-0 min-h-screen">
        {{-- Top App Bar (Mobile) --}}
        <header class="md:hidden fixed top-0 w-full z-50 flex justify-between items-center px-6 py-4 bg-white/70 backdrop-blur-xl rounded-b-[2rem] shadow-[0_4px_20px_rgba(0,96,173,0.08)]">
            <div class="text-lg font-black text-primary font-headline tracking-tight">Wonoayu Portal</div>
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
    <nav class="md:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-3 bg-white rounded-t-[2.5rem] pb-safe shadow-[0_-10px_40px_rgba(0,0,0,0.06)]">
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
    </nav>

    @yield('scripts')
</body>
</html>
