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
            filter: saturate(1.08) brightness(1.05);
        }

        .portal-video-background::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(2, 6, 23, 0.38), rgba(15, 23, 42, 0.2)),
                rgba(2, 6, 23, 0.12);
            backdrop-filter: none;
        }

    </style>
@endonce

<div class="portal-video-background" aria-hidden="true">
    <video autoplay muted loop playsinline preload="metadata">
        <source src="{{ asset('videos/portal-background.mp4') }}" type="video/mp4">
    </video>
</div>
