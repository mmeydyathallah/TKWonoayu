<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="theme-color" content="#020617"/>
    <link rel="icon" href="{{ asset('images/logo-tk.png') }}" type="image/png"/>
    <title>Login - TK Wonoayu Madiun</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Manrope', sans-serif;
        }

        .font-headline {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .login-page-main {
            position: relative;
            z-index: 20;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }

        .login-card {
            width: 100%;
            max-width: 28rem;
            border-radius: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(2, 6, 23, 0.82);
            color: #ffffff;
            padding: 1.75rem;
            box-shadow: 0 34px 90px -28px rgba(0, 0, 0, 0.72);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }

        @media (min-width: 640px) {
            .login-card {
                padding: 2rem;
            }
        }

        .login-control {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            padding: 1rem 1rem 1rem 3.25rem;
            outline: none;
            transition: border-color 160ms ease, background 160ms ease, box-shadow 160ms ease;
        }

        .login-control::placeholder {
            color: #94a3b8;
        }

        .login-control:focus {
            border-color: rgba(125, 211, 252, 0.75);
            background: rgba(255, 255, 255, 0.16);
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.12);
        }

        .login-primary {
            background: linear-gradient(135deg, #38bdf8, #1d4ed8);
            color: #ffffff;
            box-shadow: 0 18px 36px rgba(8, 47, 73, 0.35);
        }

        .login-primary:hover {
            box-shadow: 0 24px 48px rgba(8, 47, 73, 0.45);
        }
    </style>
</head>
<body data-theme="tkwonoayu" class="min-h-screen overflow-hidden bg-slate-950 font-body text-white">
    <x-portal-video-background />

    <main class="login-page-main">
        <section class="login-card">
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
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                            <span class="material-symbols-outlined" data-icon="person">person</span>
                        </span>
                        <input class="login-control" id="username" name="username" placeholder="Masukkan nama pengguna" type="text"/>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="ml-1 block text-sm font-bold text-slate-100" for="password">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                            <span class="material-symbols-outlined" data-icon="lock">lock</span>
                        </span>
                        <input class="login-control pr-12" id="password" name="password" placeholder="Password" type="password"/>
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
