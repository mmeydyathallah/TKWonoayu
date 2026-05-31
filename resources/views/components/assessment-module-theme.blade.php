@once
    <style>
        html.dark .assessment-header {
            background: rgba(2, 6, 23, 0.88) !important;
            border-color: rgba(56, 189, 248, 0.22) !important;
            box-shadow: 0 18px 36px rgba(2, 6, 23, 0.34) !important;
        }

        html.dark .assessment-module {
            color: #e5eefb;
        }

        html.dark .assessment-module .ambient-shadow {
            box-shadow: 0 16px 36px rgba(2, 6, 23, 0.28) !important;
        }

        html.dark .assessment-module [class~="bg-white"],
        html.dark .assessment-module [class~="bg-white/90"] {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.94), rgba(15, 23, 42, 0.86)) !important;
            border-color: rgba(56, 189, 248, 0.2) !important;
        }

        html.dark .assessment-module [class~="bg-slate-50"],
        html.dark .assessment-module [class~="bg-slate-50/40"],
        html.dark .assessment-module [class~="bg-slate-50/50"],
        html.dark .assessment-module [class~="bg-slate-50/80"],
        html.dark .assessment-module [class~="bg-slate-100"],
        html.dark .assessment-module [class~="bg-slate-200/50"],
        html.dark .assessment-module [class~="from-slate-50/50"] {
            background: rgba(30, 41, 59, 0.72) !important;
        }

        html.dark .assessment-module [class~="border-slate-50"],
        html.dark .assessment-module [class~="border-slate-100"],
        html.dark .assessment-module [class~="border-slate-100/60"],
        html.dark .assessment-module [class~="border-slate-200"],
        html.dark .assessment-module [class~="border-slate-200/60"] {
            border-color: rgba(148, 163, 184, 0.22) !important;
        }

        html.dark .assessment-module input,
        html.dark .assessment-module select,
        html.dark .assessment-module textarea {
            background: rgba(2, 6, 23, 0.78) !important;
            border-color: rgba(148, 163, 184, 0.28) !important;
            color: #e5eefb !important;
        }

        html.dark .assessment-module input:focus,
        html.dark .assessment-module select:focus,
        html.dark .assessment-module textarea:focus {
            border-color: rgba(56, 189, 248, 0.58) !important;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.14) !important;
        }

        html.dark .assessment-module .score-pill {
            background: rgba(15, 23, 42, 0.9);
            border-color: rgba(148, 163, 184, 0.28);
            color: #b6c5da;
        }

        html.dark .assessment-module .score-pill:hover {
            background: rgba(30, 41, 59, 0.94);
            border-color: rgba(56, 189, 248, 0.42);
            color: #f8fafc;
        }

        html.dark .assessment-module .score-radio:checked + .score-pill.pill-BB {
            background: rgba(244, 63, 94, 0.18);
            color: #fda4af;
            border-color: rgba(251, 113, 133, 0.52);
        }

        html.dark .assessment-module .score-radio:checked + .score-pill.pill-MB {
            background: rgba(245, 158, 11, 0.18);
            color: #fcd34d;
            border-color: rgba(251, 191, 36, 0.5);
        }

        html.dark .assessment-module .score-radio:checked + .score-pill.pill-BSH {
            background: rgba(16, 185, 129, 0.18);
            color: #6ee7b7;
            border-color: rgba(52, 211, 153, 0.5);
        }

        html.dark .assessment-module .score-radio:checked + .score-pill.pill-BSB {
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border-color: rgba(96, 165, 250, 0.52);
        }

        html.dark .assessment-module .score-radio:checked + .score-pill.pill-clear {
            background: rgba(100, 116, 139, 0.2);
            color: #cbd5e1;
            border-color: rgba(148, 163, 184, 0.42);
        }

        html.dark .assessment-module .icon-purple { color: #c4b5fd; background: rgba(124, 58, 237, 0.18); }
        html.dark .assessment-module .icon-emerald { color: #6ee7b7; background: rgba(5, 150, 105, 0.18); }
        html.dark .assessment-module .icon-amber { color: #fcd34d; background: rgba(217, 119, 6, 0.18); }
        html.dark .assessment-module .icon-blue { color: #93c5fd; background: rgba(37, 99, 235, 0.2); }
        html.dark .assessment-module .icon-rose { color: #fda4af; background: rgba(225, 29, 72, 0.18); }
        html.dark .assessment-module .icon-orange { color: #fdba74; background: rgba(234, 88, 12, 0.18); }

        html.dark .assessment-module table thead,
        html.dark .assessment-module tbody tr:hover {
            background: rgba(30, 41, 59, 0.86) !important;
        }

        .assessment-module .assessment-form-header {
            border-color: transparent !important;
            color: #f8fafc !important;
        }

        .assessment-module .assessment-form-header h3,
        .assessment-module .assessment-form-header .material-symbols-outlined {
            color: #f8fafc !important;
        }

        .assessment-module .assessment-form-header-panel {
            background: linear-gradient(135deg, #0284c7, #2563eb) !important;
        }

        .assessment-module .assessment-form-header-artwork {
            background: linear-gradient(135deg, #d97706, #ea580c) !important;
        }

        .assessment-module .assessment-form-header-anecdotal {
            background: linear-gradient(135deg, #7c3aed, #db2777) !important;
        }
    </style>
@endonce
