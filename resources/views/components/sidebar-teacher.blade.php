<nav class="bg-surface-container-low text-primary flex flex-col h-screen py-8 gap-2 w-64 fixed left-0 top-0 rounded-r-3xl shadow-2xl shadow-primary-dim/10 hidden md:flex z-40 border-r border-slate-100">
    <div class="px-8 mb-8">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-white/90 flex items-center justify-center p-1.5 shadow-lg shadow-primary/25">
                <x-school-logo class="h-8 w-8" />
            </div>
            <h1 class="font-headline text-base font-extrabold text-slate-900">Portal Guru</h1>
        </div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">TK Wonoayu Madiun</p>
    </div>
    
    <div class="flex-1 overflow-y-auto px-4 flex flex-col gap-1.5">
        
        <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1 mt-2">Menu Utama</p>
        
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
        
        <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('guru.attendance.*') || request()->routeIs('guru.attendance.index') ? 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/30 scale-[1.02]' : 'text-slate-600 hover:bg-slate-100 rounded-2xl' }} transition-all duration-300" href="{{ route('guru.attendance.index') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.attendance.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">event_available</span>
            <span class="font-bold text-xs">Absensi</span>
        </a>
        
        <div class="my-4 border-t border-slate-100"></div>
        <p class="px-4 text-[10px] font-black text-sky-200 uppercase tracking-[0.2em] mb-1">Modul Penilaian</p>
        
        <a class="flex items-center gap-3 px-4 py-2.5 rounded-2xl {{ request()->routeIs('guru.daily') ? 'bg-primary text-white shadow-lg shadow-sky-900/35 ring-1 ring-sky-300/40' : 'bg-slate-900/35 text-slate-300 hover:bg-slate-800/85 hover:text-white' }} transition-all duration-300" href="{{ route('guru.daily') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.daily') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">event_note</span>
            <span class="font-bold text-xs">Penilaian Harian</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-2.5 rounded-2xl {{ request()->routeIs('guru.anecdotal') ? 'bg-primary text-white shadow-lg shadow-sky-900/35 ring-1 ring-sky-300/40' : 'bg-slate-900/35 text-slate-300 hover:bg-slate-800/85 hover:text-white' }} transition-all duration-300" href="{{ route('guru.anecdotal') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.anecdotal') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">history_edu</span>
            <span class="font-bold text-xs">Catatan Anekdot</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-2.5 rounded-2xl {{ request()->routeIs('guru.development-narrative') ? 'bg-primary text-white shadow-lg shadow-sky-900/35 ring-1 ring-sky-300/40' : 'bg-slate-900/35 text-slate-300 hover:bg-slate-800/85 hover:text-white' }} transition-all duration-300" href="{{ route('guru.development-narrative') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.development-narrative') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">description</span>
            <span class="font-bold text-xs">Narasi Perkembangan</span>
        </a>

    </div>
    
    <div class="px-6 mt-auto">
        <div class="flex flex-col gap-1 border-t border-slate-100 pt-4">
            <a class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('guru.settings') ? 'text-primary font-black' : 'text-slate-400 hover:text-slate-900' }} transition-colors text-xs font-bold" href="{{ route('guru.settings') }}">
                <span class="material-symbols-outlined text-[18px]">settings</span>
                Pengaturan
            </a>
            <a class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('guru.settings') && request()->query('tab') === 'telegram' ? 'text-primary font-black' : 'text-slate-400 hover:text-slate-900' }} transition-colors text-xs font-bold" href="{{ route('guru.settings', ['tab' => 'telegram']) }}">
                <span class="material-symbols-outlined text-[18px]">send</span>
                Telegram
            </a>
            <form action="{{ route('auth.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-rose-600 transition-colors text-xs font-bold">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    Keluar
                </button>
            </form>
        </div>
    </div>
</nav>
