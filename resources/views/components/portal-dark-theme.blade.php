@once
    <style>
        html.dark body {
            background: #020617 !important;
            color: #e5eefb !important;
        }

        html.dark main {
            background:
                radial-gradient(circle at top left, rgba(14, 165, 233, 0.13), transparent 30rem),
                radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.1), transparent 28rem),
                #020617 !important;
        }

        html.dark [class~="bg-surface"],
        html.dark [class~="bg-background"],
        html.dark [class~="bg-surface-container-lowest"],
        html.dark [class~="bg-surface-container-low"],
        html.dark [class~="bg-surface-container"],
        html.dark [class~="bg-surface-container-high"],
        html.dark [class~="bg-surface-container-highest"],
        html.dark [class~="bg-white"],
        html.dark [class~="bg-slate-50"],
        html.dark [class~="bg-slate-100"],
        html.dark [class~="bg-slate-200"] {
            background-color: rgba(15, 23, 42, 0.94) !important;
        }

        html.dark [class~="bg-white/70"],
        html.dark [class~="bg-white/80"],
        html.dark [class~="bg-white/90"],
        html.dark [class~="bg-slate-50/50"],
        html.dark [class~="bg-slate-50/70"],
        html.dark [class~="bg-slate-50/20"] {
            background-color: rgba(15, 23, 42, 0.82) !important;
        }

        html.dark [class~="glass-panel"] {
            background-color: rgba(15, 23, 42, 0.82) !important;
        }

        html.dark [class~="bg-primary"] {
            background: linear-gradient(135deg, #0ea5e9, #2563eb) !important;
            color: #f8fafc !important;
            box-shadow: 0 14px 28px rgba(14, 165, 233, 0.26) !important;
        }

        html.dark [class~="bg-primary/10"] {
            background-color: rgba(14, 165, 233, 0.18) !important;
        }

        html.dark [class~="text-primary"] {
            color: #38bdf8 !important;
        }

        html.dark [class~="text-on-primary"],
        html.dark [class~="text-white"] {
            color: #f8fafc !important;
        }

        html.dark [class~="bg-primary-container"],
        html.dark [class~="bg-secondary-container"],
        html.dark [class~="bg-tertiary-container"] {
            background-color: rgba(56, 189, 248, 0.16) !important;
            color: #e0f2fe !important;
        }

        html.dark [class~="bg-emerald-50"],
        html.dark [class~="bg-green-50"] {
            background-color: rgba(16, 185, 129, 0.16) !important;
        }

        html.dark [class~="bg-amber-50"],
        html.dark [class~="bg-yellow-50"] {
            background-color: rgba(245, 158, 11, 0.18) !important;
        }

        html.dark [class~="bg-rose-50"],
        html.dark [class~="bg-red-50"] {
            background-color: rgba(244, 63, 94, 0.16) !important;
        }

        html.dark [class~="bg-blue-50"] {
            background-color: rgba(59, 130, 246, 0.16) !important;
        }

        html.dark [class~="text-on-surface"],
        html.dark [class~="text-slate-900"],
        html.dark [class~="text-slate-800"],
        html.dark [class~="text-slate-700"] {
            color: #e5eefb !important;
        }

        html.dark [class~="text-on-surface-variant"],
        html.dark [class~="text-outline-variant"],
        html.dark [class~="text-slate-600"],
        html.dark [class~="text-slate-500"],
        html.dark [class~="text-slate-400"] {
            color: #a9b8cc !important;
        }

        html.dark [class~="text-emerald-700"],
        html.dark [class~="text-green-700"],
        html.dark [class~="text-emerald-800"] {
            color: #6ee7b7 !important;
        }

        html.dark [class~="text-amber-700"],
        html.dark [class~="text-amber-800"],
        html.dark [class~="text-yellow-700"] {
            color: #fcd34d !important;
        }

        html.dark [class~="text-rose-600"],
        html.dark [class~="text-rose-700"],
        html.dark [class~="text-red-600"],
        html.dark [class~="text-red-700"] {
            color: #fda4af !important;
        }

        html.dark [class~="text-blue-600"],
        html.dark [class~="text-blue-700"] {
            color: #93c5fd !important;
        }

        html.dark [class~="border-slate-50"],
        html.dark [class~="border-slate-100"],
        html.dark [class~="border-slate-200"],
        html.dark [class~="border-outline-variant/10"],
        html.dark [class~="border-outline-variant/15"],
        html.dark [class~="border-outline-variant/20"] {
            border-color: rgba(148, 163, 184, 0.22) !important;
        }

        html.dark [class~="border-emerald-100"],
        html.dark [class~="border-emerald-200"] {
            border-color: rgba(52, 211, 153, 0.32) !important;
        }

        html.dark [class~="border-amber-100"],
        html.dark [class~="border-amber-200"] {
            border-color: rgba(251, 191, 36, 0.34) !important;
        }

        html.dark [class~="border-rose-100"],
        html.dark [class~="border-rose-200"],
        html.dark [class~="border-red-200"] {
            border-color: rgba(251, 113, 133, 0.32) !important;
        }

        html.dark input,
        html.dark select,
        html.dark textarea {
            background-color: rgba(15, 23, 42, 0.95) !important;
            color: #e5eefb !important;
            border-color: rgba(148, 163, 184, 0.26) !important;
        }

        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #64748b !important;
        }

        html.dark table thead,
        html.dark tr:hover {
            background-color: rgba(30, 41, 59, 0.78) !important;
        }

        html.dark nav {
            background-color: rgba(2, 6, 23, 0.96) !important;
            border-color: rgba(56, 189, 248, 0.22) !important;
        }

        html.dark nav a:not([class*="bg-primary"]),
        html.dark nav button {
            color: #cbd5e1 !important;
        }

        html.dark nav a:not([class*="bg-primary"]):hover,
        html.dark nav button:hover {
            background-color: rgba(30, 41, 59, 0.95) !important;
            color: #f8fafc !important;
        }

        html.dark header {
            border-color: rgba(56, 189, 248, 0.18) !important;
        }

        html.dark button:not([class*="bg-primary"]):not([class*="bg-amber"]):not([class*="bg-rose"]),
        html.dark a[class*="border"] {
            border-color: rgba(148, 163, 184, 0.28) !important;
        }

        html.dark .rounded-2xl,
        html.dark .rounded-3xl,
        html.dark .rounded-\[2\.5rem\],
        html.dark .rounded-\[2rem\] {
            border-color: rgba(148, 163, 184, 0.2) !important;
        }
    </style>
@endonce
