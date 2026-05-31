<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="theme-color" content="#020617"/>
    <link rel="icon" href="{{ asset('images/logo-tk.png') }}" type="image/png"/>
    <title>Login - TK Wonoayu Madiun</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#0ea5e9',
                        'primary-dim': '#0284c7',
                        'on-surface': '#e5eefb',
                        'on-surface-variant': '#a9b8cc',
                    },
                    borderRadius: {
                        DEFAULT: '1rem',
                        lg: '1.5rem',
                        xl: '2rem',
                        full: '9999px',
                    },
                    fontFamily: {
                        headline: ['Plus Jakarta Sans'],
                        body: ['Manrope'],
                    },
                },
            },
        };
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .login-card {
                @apply border border-white/15 bg-slate-950/72 backdrop-blur-2xl shadow-[0_34px_90px_-28px_rgba(0,0,0,0.72)];
            }

            .login-control {
                @apply w-full rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-white placeholder:text-slate-400 outline-none transition-all focus:border-sky-300/70 focus:bg-white/15 focus:ring-4 focus:ring-sky-400/10;
            }

            .login-primary {
                @apply bg-gradient-to-br from-sky-400 to-blue-700 text-white shadow-lg shadow-sky-950/35;
            }
        }
    </style>
</head>
<body class="min-h-screen overflow-hidden bg-slate-950 font-body text-white">
    <x-portal-video-background />

    <main class="relative z-10 flex min-h-screen items-center justify-center px-5 py-8">
        <section class="login-card w-full max-w-md rounded-[2rem] p-7 sm:p-8">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center">
                    <x-school-logo class="h-16 w-16" />
                </div>
                <p class="mb-2 text-xs font-black uppercase tracking-[0.28em] text-sky-200">TK Wonoayu Madiun</p>
                <h1 class="font-headline text-3xl font-black tracking-tight text-white">Selamat Datang</h1>
                <p class="mt-3 text-sm font-medium leading-relaxed text-slate-300">
                    Masuk ke portal guru atau wali murid untuk mengakses layanan sekolah.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-300/30 bg-rose-500/15 p-4">
                    <p class="text-sm font-semibold text-rose-100">
                        {{ $errors->first('username') ?? 'Login gagal. Periksa kembali username/email dan password Anda.' }}
                    </p>
                </div>
            @endif

            <div class="relative mb-7 flex rounded-2xl border border-white/10 bg-white/10 p-1">
                <div class="absolute inset-y-1 left-1 w-[calc(50%-0.25rem)] rounded-xl bg-white shadow-sm transition-all duration-300 pointer-events-none" id="roleIndicator"></div>
                <button class="role-btn z-10 flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-black transition-colors" data-role="guru" type="button" onclick="selectRole('guru')">
                    <span class="material-symbols-outlined text-[20px]" data-icon="person_4">person_4</span>
                    <span>Guru</span>
                </button>
                <button class="role-btn z-10 flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-black transition-colors" data-role="wali_murid" type="button" onclick="selectRole('wali_murid')">
                    <span class="material-symbols-outlined text-[20px]" data-icon="family_restroom">family_restroom</span>
                    <span>Wali</span>
                </button>
            </div>

            <form action="{{ route('auth.handle') }}" class="space-y-6" method="post">
                @csrf
                <input id="roleInput" name="role" type="hidden" value="guru"/>

                <div class="space-y-2">
                    <label class="ml-1 block text-sm font-bold text-slate-100" for="username">Nama Pengguna / Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 pointer-events-none">
                            <span class="material-symbols-outlined" data-icon="person">person</span>
                        </span>
                        <input class="login-control pl-12" id="username" name="username" placeholder="Masukkan nama pengguna" type="text"/>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="ml-1 block text-sm font-bold text-slate-100" for="password">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 pointer-events-none">
                            <span class="material-symbols-outlined" data-icon="lock">lock</span>
                        </span>
                        <input class="login-control pl-12 pr-12" id="password" name="password" placeholder="Password" type="password"/>
                        <button class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition-colors hover:text-white" type="button">
                            <span class="material-symbols-outlined" data-icon="visibility_off">visibility_off</span>
                        </button>
                    </div>
                </div>

                <button class="login-primary mt-2 flex w-full items-center justify-center gap-2 rounded-2xl py-4 text-base font-black transition-all duration-300 hover:scale-[1.01] hover:shadow-xl" type="submit">
                    Masuk
                    <span class="material-symbols-outlined text-[20px]" data-icon="arrow_forward">arrow_forward</span>
                </button>
            </form>

            <p class="mt-7 text-center text-xs font-semibold text-slate-400">Pilih peran sebelum masuk ke portal.</p>
        </section>
    </main>

    <script>
        function selectRole(role) {
            document.getElementById('roleInput').value = role;

            document.querySelectorAll('.role-btn').forEach((btn) => {
                btn.classList.remove('text-slate-950');
                btn.classList.add('text-slate-300');
            });

            const activeBtn = document.querySelector(`[data-role="${role}"]`);
            activeBtn.classList.remove('text-slate-300');
            activeBtn.classList.add('text-slate-950');

            const indicator = document.getElementById('roleIndicator');
            indicator.style.transform = role === 'guru'
                ? 'translateX(0)'
                : 'translateX(calc(100% + 0.25rem))';
        }

        window.addEventListener('DOMContentLoaded', function() {
            selectRole('guru');
        });
    </script>
</body>
</html>
