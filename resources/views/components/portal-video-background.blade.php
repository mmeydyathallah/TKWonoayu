@once
    <style>
        .portal-video-background {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
            background: #0f172a;
        }

        .portal-video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: saturate(1.05) brightness(0.72);
        }

        .portal-video-background::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(248, 249, 251, 0.86), rgba(248, 249, 251, 0.72)),
                rgba(248, 249, 251, 0.36);
            backdrop-filter: blur(1px);
        }
    </style>
@endonce

<div class="portal-video-background" aria-hidden="true">
    <video autoplay muted loop playsinline preload="metadata">
        <source src="{{ asset('videos/portal-background.mp4') }}" type="video/mp4">
    </video>
</div>
