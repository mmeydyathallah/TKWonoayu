<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Login - TK Wonoayu Madiun</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Manrope:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary-fixed": "#fdc825",
                        "background": "#f8f9fb",
                        "secondary": "#136e27",
                        "tertiary-fixed-dim": "#edba10",
                        "on-surface-variant": "#596064",
                        "inverse-surface": "#0b0f10",
                        "outline-variant": "#acb3b7",
                        "on-primary-fixed-variant": "#003461",
                        "on-tertiary-fixed": "#403000",
                        "primary-fixed-dim": "#599ef1",
                        "inverse-primary": "#68abff",
                        "surface-container-lowest": "#ffffff",
                        "on-error": "#fff7f6",
                        "surface-container": "#eaeff2",
                        "surface-tint": "#0060ad",
                        "on-secondary": "#eaffe4",
                        "surface-container-highest": "#dce4e8",
                        "surface": "#f8f9fb",
                        "surface-variant": "#dce4e8",
                        "on-primary": "#f8f8ff",
                        "surface-container-low": "#f0f4f7",
                        "error-container": "#fa746f",
                        "on-surface": "#2c3437",
                        "tertiary-container": "#fdc825",
                        "surface-bright": "#f8f9fb",
                        "tertiary": "#775b00",
                        "on-primary-fixed": "#000c1e",
                        "error": "#a83836",
                        "error-dim": "#67040d",
                        "on-secondary-fixed-variant": "#0c6a24",
                        "secondary-dim": "#00611e",
                        "on-background": "#2c3437",
                        "primary-fixed": "#68abff",
                        "on-secondary-fixed": "#004a15",
                        "secondary-container": "#9ff79f",
                        "on-tertiary-fixed-variant": "#634c00",
                        "tertiary-dim": "#685000",
                        "primary": "#0060ad",
                        "surface-dim": "#d4dbdf",
                        "on-tertiary-container": "#584300",
                        "on-error-container": "#6e0a12",
                        "on-tertiary": "#fff8f0",
                        "secondary-fixed": "#9ff79f",
                        "surface-container-high": "#e3e9ed",
                        "primary-dim": "#005498",
                        "secondary-fixed-dim": "#91e892",
                        "on-primary-container": "#002b52",
                        "primary-container": "#68abff",
                        "inverse-on-surface": "#9a9d9f",
                        "outline": "#747c80",
                        "on-secondary-container": "#005f1d"
                    },
                    "borderRadius": {
                        "DEFAULT": "1rem",
                        "lg": "2rem",
                        "xl": "3rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Plus Jakarta Sans"],
                        "body": ["Manrope"],
                        "label": ["Manrope"]
                    }
                }
            }
        }
    </script>
<style type="text/tailwindcss">
        @layer utilities {
            .glass-panel {
                @apply bg-surface-container-lowest/80 backdrop-blur-[20px];
            }
            .ghost-border {
                @apply border border-outline-variant/15;
            }
            .soft-shadow {
                @apply shadow-[0_32px_64px_-16px_rgba(44,52,55,0.06)];
            }
            .gradient-primary {
                @apply bg-gradient-to-br from-primary to-primary-container text-on-primary;
            }
            .gradient-tertiary {
                @apply bg-gradient-to-br from-tertiary-container to-tertiary-fixed-dim text-on-tertiary-container;
            }
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface min-h-screen flex items-center justify-center relative overflow-hidden">
<!-- Decorative Background Elements -->
<div class="absolute inset-0 pointer-events-none z-0 overflow-hidden">
<div class="absolute -top-32 -right-32 w-96 h-96 bg-tertiary-container rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
<div class="absolute top-1/2 -left-24 w-72 h-72 bg-secondary-container rounded-full mix-blend-multiply filter blur-3xl opacity-40"></div>
<div class="absolute -bottom-40 right-20 w-[30rem] h-[30rem] bg-primary-container rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
</div>
<!-- Main Container -->
<main class="w-full max-w-6xl px-6 md:px-12 py-12 relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
<!-- Left Side: Graphic / Branding -->
<div class="hidden lg:flex flex-col justify-center items-start space-y-8 pr-12">
<div class="flex items-center gap-4">
<div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center shadow-lg">
<span class="material-symbols-outlined text-on-primary text-3xl" data-icon="school">school</span>
</div>
<h1 class="font-headline text-3xl font-extrabold text-primary">TK Wonoayu <span class="block text-xl font-medium text-on-surface-variant">Madiun</span></h1>
</div>
<div class="relative w-full aspect-square rounded-xl overflow-hidden glass-panel soft-shadow border border-surface-container-high/50 p-6 flex flex-col justify-center items-center text-center">
<img alt="Children playing" class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-overlay" data-alt="soft pastel watercolor illustration of diverse happy children playing together in a bright sunny kindergarten playground, warm cheerful educational mood" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBdlMymS4kA7zsxkpNC7zWiI-zxY2vVFq4YS14kyWd0o1qMMCr5ozEYlVYP-LEgl4uNRZYqClzglYqFSuj0v-IQAtFLVlErCB4c9p9T2DAkz0PiKJEuaNUmjn2xEiF_kUYciJfhM6fXoZVXz_AbKWTb8_Ut2mDyoY-6fVcLy8N9GsXhXdRRStw8dXEoyY68v6RFsIdL36SVTyJxxvqvAvq7XYqQKmkZqIbr_Rc7OzQ4k_-SPH-CHV06G-RitUe56b2LUD1G82yYJWbw"/>
<div class="relative z-10 space-y-4">
<div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-tertiary-container text-on-tertiary-container shadow-lg mb-4">
<span class="material-symbols-outlined text-4xl" data-icon="toys">toys</span>
</div>
<h2 class="font-headline text-2xl font-bold text-on-surface">Tempat Bermain &amp; Belajar</h2>
<p class="text-on-surface-variant font-medium">Membangun karakter anak sejak usia dini dengan penuh keceriaan.</p>
</div>
</div>
</div>
<!-- Right Side: Login Form -->
<div class="w-full max-w-md mx-auto">
<!-- Mobile Logo -->
<div class="lg:hidden flex flex-col items-center gap-3 mb-8 text-center">
<div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center shadow-md">
<span class="material-symbols-outlined text-on-primary text-3xl" data-icon="school">school</span>
</div>
<h1 class="font-headline text-2xl font-extrabold text-primary">TK Wonoayu</h1>
</div>
<div class="glass-panel rounded-lg p-8 soft-shadow ghost-border relative">
<!-- Floating decorative blob -->
<div class="absolute -top-6 -right-6 w-16 h-16 bg-secondary-container rounded-[2rem] rounded-bl-sm z-[-1]"></div>
<div class="mb-8">
<h2 class="font-headline text-2xl font-bold text-on-surface mb-2">Selamat Datang!</h2>
<p class="text-sm text-on-surface-variant">Silakan login dengan akun Anda</p>
</div>
@if ($errors->any())
<div class="bg-error-container/20 border border-error/30 rounded-DEFAULT p-4 mb-6">
<p class="text-sm font-semibold text-error">{{ $errors->first('username') ?? 'Login gagal. Periksa kembali username/email dan password Anda.' }}</p>
</div>
@endif
<!-- Role Selection Toggle -->
<div class="flex p-1 bg-surface-container-high rounded-full mb-8 relative gap-1">
<!-- Sliding background -->
<div class="absolute inset-y-1 left-1 w-[calc(50%-0.25rem)] bg-surface-container-lowest rounded-full shadow-sm transition-all duration-300 pointer-events-none" id="roleIndicator"></div>
<!-- Guru Button -->
<button class="flex-1 py-3 px-6 rounded-full text-sm font-bold z-10 transition-colors flex items-center justify-center gap-2 role-btn" data-role="guru" type="button" onclick="selectRole('guru')">
<span class="material-symbols-outlined text-[20px]" data-icon="person_4">person_4</span>
<span class="hidden sm:inline">Guru</span>
</button>
<!-- Wali Murid Button -->
<button class="flex-1 py-3 px-6 rounded-full text-sm font-bold z-10 transition-colors flex items-center justify-center gap-2 role-btn" data-role="wali_murid" type="button" onclick="selectRole('wali_murid')">
<span class="material-symbols-outlined text-[20px]" data-icon="family_restroom">family_restroom</span>
<span class="hidden sm:inline">Wali</span>
</button>
</div>

<!-- Form -->
<form action="{{ route('auth.handle') }}" class="space-y-6" method="post">
    @csrf
    <!-- Hidden role input -->
    <input id="roleInput" name="role" type="hidden" value="guru"/>

<div class="space-y-2">
<label class="block text-sm font-semibold text-on-surface ml-2" for="username">Nama Pengguna / Email</label>
<div class="relative">
<span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-on-surface-variant">
<span class="material-symbols-outlined" data-icon="person">person</span>
</span>
<input class="w-full bg-surface-container-high border-none rounded-DEFAULT py-4 pl-12 pr-4 text-on-surface placeholder:text-outline-variant focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-colors" id="username" name="username" placeholder="Masukkan nama pengguna" type="text"/>
</div>
</div>
<div class="space-y-2">
<div class="flex justify-between items-center ml-2">
<label class="block text-sm font-semibold text-on-surface" for="password">Kata Sandi</label>
<a class="text-xs font-semibold text-primary hover:text-primary-dim transition-colors" href="#">Lupa Kata Sandi?</a>
</div>
<div class="relative">
<span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-on-surface-variant">
<span class="material-symbols-outlined" data-icon="lock">lock</span>
</span>
<input class="w-full bg-surface-container-high border-none rounded-DEFAULT py-4 pl-12 pr-12 text-on-surface placeholder:text-outline-variant focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-colors" id="password" name="password" placeholder="••••••••" type="password"/>
<button class="absolute inset-y-0 right-0 pr-4 flex items-center text-outline-variant hover:text-on-surface transition-colors" type="button">
<span class="material-symbols-outlined" data-icon="visibility_off">visibility_off</span>
</button>
</div>
</div>
<button class="w-full gradient-primary py-4 rounded-full font-bold text-lg hover:scale-[1.02] hover:shadow-lg transition-all duration-300 mt-4 flex justify-center items-center gap-2" type="submit">
                        Masuk
                        <span class="material-symbols-outlined text-[20px]" data-icon="arrow_forward">arrow_forward</span>
</button>
</form>
<!-- Help Link -->
<div class="mt-8 text-center">
<p class="text-sm text-on-surface-variant">Pilih peran Anda untuk login</p>
</div>
</div>
</div>
</main>

<script>
function selectRole(role) {
    // Update hidden input
    document.getElementById('roleInput').value = role;
    
    // Update button styles
    document.querySelectorAll('.role-btn').forEach(btn => {
        btn.classList.remove('text-primary');
        btn.classList.add('text-on-surface-variant');
    });
    
    const activeBtn = document.querySelector(`[data-role="${role}"]`);
    activeBtn.classList.remove('text-on-surface-variant');
    activeBtn.classList.add('text-primary');
    
    // Update indicator position
    const indicator = document.getElementById('roleIndicator');
    if (role === 'guru') {
        indicator.style.transform = 'translateX(0)';
    } else {
        indicator.style.transform = 'translateX(calc(100% + 0.25rem))';
    }
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', function() {
    selectRole('guru');
});
</script>
</body></html>

