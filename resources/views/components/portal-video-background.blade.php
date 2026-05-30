@once
    <style>
        .portal-video-background {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
            background: #020617;
        }

        .portal-video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: saturate(1.05) brightness(0.45);
        }

        .portal-video-background::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(2, 6, 23, 0.92), rgba(15, 23, 42, 0.78)),
                rgba(2, 6, 23, 0.54);
            backdrop-filter: blur(1px);
        }

        html.dark body {
            background: #020617 !important;
            color: #e5eefb !important;
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
            background-color: rgba(15, 23, 42, 0.84) !important;
        }

        html.dark [class~="bg-white/70"],
        html.dark [class~="bg-white/80"],
        html.dark [class~="bg-white/90"],
        html.dark [class~="bg-slate-50/50"],
        html.dark [class~="bg-slate-50/70"],
        html.dark [class~="bg-slate-50/20"] {
            background-color: rgba(15, 23, 42, 0.72) !important;
        }

        html.dark [class~="glass-panel"] {
            background-color: rgba(15, 23, 42, 0.78) !important;
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

        html.dark [class~="border-slate-50"],
        html.dark [class~="border-slate-100"],
        html.dark [class~="border-slate-200"],
        html.dark [class~="border-outline-variant/10"],
        html.dark [class~="border-outline-variant/15"],
        html.dark [class~="border-outline-variant/20"] {
            border-color: rgba(148, 163, 184, 0.22) !important;
        }

        html.dark input,
        html.dark select,
        html.dark textarea {
            background-color: rgba(15, 23, 42, 0.9) !important;
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
    </style>
@endonce

<div class="portal-video-background" aria-hidden="true">
    <video autoplay muted loop playsinline preload="metadata">
        <source src="{{ asset('videos/portal-background.mp4') }}" type="video/mp4">
    </video>
</div>
