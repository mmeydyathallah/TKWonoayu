<nav class="bg-surface-container-low text-primary flex flex-col h-screen py-8 gap-2 w-64 fixed left-0 top-0 rounded-r-3xl shadow-2xl shadow-primary-dim/10 hidden md:flex z-40 border-r border-slate-100">
    <div class="px-8 mb-8">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined text-[18px]">school</span>
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
        
        <div class="my-4 border-t border-slate-100"></div>
        <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Modul Penilaian</p>
        
        <a class="flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('guru.daily') ? 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/30' : 'text-slate-600 hover:bg-slate-100 rounded-2xl' }} transition-all duration-300" href="{{ route('guru.daily') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.daily') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">event_note</span>
            <span class="font-bold text-xs">Penilaian Harian</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('guru.checklist') ? 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/30' : 'text-slate-600 hover:bg-slate-100 rounded-2xl' }} transition-all duration-300" href="{{ route('guru.checklist') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.checklist') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">fact_check</span>
            <span class="font-bold text-xs">Penilaian Ceklis</span>
        </a>
        
        <a class="flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('guru.panel') ? 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/30' : 'text-slate-600 hover:bg-slate-100 rounded-2xl' }} transition-all duration-300" href="{{ route('guru.panel') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.panel') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">forum</span>
            <span class="font-bold text-xs">Penilaian Percakapan</span>
        </a>
        
        <a class="flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('guru.artworks') ? 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/30' : 'text-slate-600 hover:bg-slate-100 rounded-2xl' }} transition-all duration-300" href="{{ route('guru.artworks') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.artworks') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">palette</span>
            <span class="font-bold text-xs">Hasil Karya</span>
        </a>
        
        <a class="flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('guru.anecdotal') ? 'bg-primary text-white rounded-2xl shadow-lg shadow-primary/30' : 'text-slate-600 hover:bg-slate-100 rounded-2xl' }} transition-all duration-300" href="{{ route('guru.anecdotal') }}">
            <span class="material-symbols-outlined text-[20px]" style="{{ request()->routeIs('guru.anecdotal') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">history_edu</span>
            <span class="font-bold text-xs">Catatan Anekdot</span>
        </a>

    </div>
    
    <div class="px-6 mt-auto">
        <div class="flex flex-col gap-1 border-t border-slate-100 pt-4">
            <a class="flex items-center gap-3 px-4 py-2 {{ request()->routeIs('guru.settings') ? 'text-primary font-black' : 'text-slate-400 hover:text-slate-900' }} transition-colors text-xs font-bold" href="{{ route('guru.settings') }}">
                <span class="material-symbols-outlined text-[18px]">settings</span>
                Pengaturan
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
