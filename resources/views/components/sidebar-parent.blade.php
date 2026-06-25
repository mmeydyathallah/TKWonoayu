<nav class="bg-surface-container-low text-primary flex flex-col h-screen py-8 gap-2 w-64 fixed left-0 top-0 rounded-r-xl shadow-2xl shadow-primary-dim/10 hidden md:flex z-40">
    <div class="px-6 mb-8">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-white/90 flex items-center justify-center p-1.5 shadow-md shadow-primary/25">
                <x-school-logo class="h-8 w-8" />
            </div>
            <div>
                <h1 class="font-headline text-base font-extrabold text-on-primary-container leading-tight">Wali Murid</h1>
                <p class="text-xs text-on-surface-variant">TK Wonoayu Madiun</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-4 flex flex-col gap-2">
        <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('wali.dashboard') ? 'bg-primary text-on-primary rounded-full shadow-lg shadow-primary/30 scale-105' : 'text-on-surface-variant hover:bg-surface-container-high rounded-full' }} transition-all duration-300" href="{{ route('wali.dashboard') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">home</span>
            <span class="font-medium text-sm">Beranda</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('wali.report') ? 'bg-primary text-on-primary rounded-full shadow-lg shadow-primary/30 scale-105' : 'text-on-surface-variant hover:bg-surface-container-high rounded-full' }} transition-all duration-300" href="{{ route('wali.report') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.report') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">assessment</span>
            <span class="font-medium text-sm">Laporan Perkembangan</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('wali.gallery') ? 'bg-primary text-on-primary rounded-full shadow-lg shadow-primary/30 scale-105' : 'text-on-surface-variant hover:bg-surface-container-high rounded-full' }} transition-all duration-300" href="{{ route('wali.gallery') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.gallery') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">palette</span>
            <span class="font-medium text-sm">Galeri Aktivitas</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('wali.agenda') ? 'bg-primary text-on-primary rounded-full shadow-lg shadow-primary/30 scale-105' : 'text-on-surface-variant hover:bg-surface-container-high rounded-full' }} transition-all duration-300" href="{{ route('wali.agenda') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.agenda') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">calendar_month</span>
            <span class="font-medium text-sm">Agenda Sekolah</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('wali.attendance') ? 'bg-primary text-on-primary rounded-full shadow-lg shadow-primary/30 scale-105' : 'text-on-surface-variant hover:bg-surface-container-high rounded-full' }} transition-all duration-300" href="{{ route('wali.attendance') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.attendance') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">event_available</span>
            <span class="font-medium text-sm">Absensi Anak</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('wali.profile') ? 'bg-primary text-on-primary rounded-full shadow-lg shadow-primary/30 scale-105' : 'text-on-surface-variant hover:bg-surface-container-high rounded-full' }} transition-all duration-300" href="{{ route('wali.profile') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.profile') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">child_care</span>
            <span class="font-medium text-sm">Profil Anak</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('wali.telegram') ? 'bg-primary text-on-primary rounded-full shadow-lg shadow-primary/30 scale-105' : 'text-on-surface-variant hover:bg-surface-container-high rounded-full' }} transition-all duration-300" href="{{ route('wali.telegram') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.telegram') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">send</span>
            <span class="font-medium text-sm">Telegram</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('wali.feedback.*') ? 'bg-primary text-on-primary rounded-full shadow-lg shadow-primary/30 scale-105' : 'text-on-surface-variant hover:bg-surface-container-high rounded-full' }} transition-all duration-300" href="{{ route('wali.feedback.index') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('wali.feedback.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">feedback</span>
            <span class="font-medium text-sm">Feedback Guru</span>
        </a>
    </div>

    <div class="px-6 mt-auto">
        <div class="flex flex-col gap-1 border-t border-outline-variant/15 pt-4">
            <a class="flex items-center gap-3 px-4 py-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors text-sm" href="{{ route('login') }}">
                <span class="material-symbols-outlined">logout</span>
                Keluar
            </a>
        </div>
    </div>
</nav>
