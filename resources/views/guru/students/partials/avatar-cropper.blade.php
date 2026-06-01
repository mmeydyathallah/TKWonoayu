@once
<style>
    .avatar-crop-stage {
        background:
            linear-gradient(45deg, rgba(148, 163, 184, 0.18) 25%, transparent 25%),
            linear-gradient(-45deg, rgba(148, 163, 184, 0.18) 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, rgba(148, 163, 184, 0.18) 75%),
            linear-gradient(-45deg, transparent 75%, rgba(148, 163, 184, 0.18) 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0;
    }
    .avatar-crop-guide {
        box-shadow: 0 0 0 999px rgba(15, 23, 42, 0.48), inset 0 0 0 2px rgba(255, 255, 255, 0.95);
    }
</style>

<div id="avatarCropperModal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/75 px-4 py-6 backdrop-blur-sm">
    <div class="w-full max-w-xl rounded-[1.5rem] bg-surface-container-lowest p-5 shadow-2xl ring-1 ring-white/10 md:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-xl font-black text-on-surface">Posisikan Foto Siswa</h3>
                <p class="mt-1 text-xs font-bold text-on-surface-variant">Area bulat adalah pratinjau foto profil yang akan tampil.</p>
            </div>
            <button type="button" id="avatarCropCancelIcon" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="mt-5 flex justify-center">
            <div class="avatar-crop-stage relative aspect-square w-full max-w-[360px] overflow-hidden rounded-[1.25rem] bg-surface-container-high ring-1 ring-outline-variant/20">
                <canvas id="avatarCropCanvas" width="360" height="360" class="h-full w-full touch-none cursor-grab active:cursor-grabbing"></canvas>
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                    <div class="avatar-crop-guide h-[76%] w-[76%] rounded-full"></div>
                </div>
                <div class="pointer-events-none absolute bottom-3 left-1/2 -translate-x-1/2 rounded-full bg-slate-950/65 px-3 py-1 text-[11px] font-black text-white shadow-lg">
                    Geser foto untuk mengatur posisi
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl bg-surface-container-high/70 p-4">
            <div class="mb-3 flex items-center justify-between">
                <label class="text-xs font-black uppercase tracking-widest text-on-surface-variant">Zoom</label>
                <span id="avatarCropZoomValue" class="rounded-full bg-surface-container-lowest px-2.5 py-1 text-[11px] font-black text-primary">100%</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">remove</span>
                <input id="avatarCropZoom" type="range" min="1" max="3" step="0.01" value="1" class="range range-primary range-sm">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">add</span>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap justify-end gap-3">
            <button type="button" id="avatarCropCancel" class="rounded-full border border-outline-variant/30 px-5 py-2.5 text-sm font-black text-on-surface-variant hover:bg-surface-container-high">
                Batal
            </button>
            <button type="button" id="avatarCropConfirm" class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2.5 text-sm font-black text-on-primary shadow-lg shadow-primary/20 hover:bg-primary/90">
                <span class="material-symbols-outlined text-[18px]">check</span>
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('avatarCropperModal');
        const canvas = document.getElementById('avatarCropCanvas');
        const zoomInput = document.getElementById('avatarCropZoom');
        const zoomValue = document.getElementById('avatarCropZoomValue');
        const confirmButton = document.getElementById('avatarCropConfirm');
        const cancelButton = document.getElementById('avatarCropCancel');
        const cancelIconButton = document.getElementById('avatarCropCancelIcon');
        const ctx = canvas.getContext('2d');

        let activeInput = null;
        let activePreview = null;
        let activeIcon = null;
        let originalFile = null;
        let image = new Image();
        let imageUrl = '';
        let scale = 1;
        let minScale = 1;
        let offsetX = 0;
        let offsetY = 0;
        let dragStart = null;

        function fitImage() {
            minScale = Math.max(canvas.width / image.width, canvas.height / image.height);
            scale = minScale;
            zoomInput.value = '1';
            zoomValue.textContent = '100%';
            offsetX = (canvas.width - image.width * scale) / 2;
            offsetY = (canvas.height - image.height * scale) / 2;
            draw();
        }

        function clampOffsets() {
            const width = image.width * scale;
            const height = image.height * scale;
            offsetX = width <= canvas.width ? (canvas.width - width) / 2 : Math.min(0, Math.max(canvas.width - width, offsetX));
            offsetY = height <= canvas.height ? (canvas.height - height) / 2 : Math.min(0, Math.max(canvas.height - height, offsetY));
        }

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.fillStyle = '#e2e8f0';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            clampOffsets();
            ctx.drawImage(image, offsetX, offsetY, image.width * scale, image.height * scale);
        }

        function canvasPoint(event) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: (event.clientX - rect.left) * (canvas.width / rect.width),
                y: (event.clientY - rect.top) * (canvas.height / rect.height),
            };
        }

        function closeModal({ clearInput = false } = {}) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            if (imageUrl) URL.revokeObjectURL(imageUrl);
            imageUrl = '';
            if (clearInput && activeInput) activeInput.value = '';
            activeInput = null;
            activePreview = null;
            activeIcon = null;
            originalFile = null;
            dragStart = null;
        }

        function updateFileInput(blob) {
            const extension = blob.type === 'image/png' ? 'png' : 'jpg';
            const baseName = originalFile?.name?.replace(/\.[^.]+$/, '') || 'avatar';
            const croppedFile = new File([blob], `${baseName}-cropped.${extension}`, { type: blob.type });
            const transfer = new DataTransfer();
            transfer.items.add(croppedFile);
            activeInput.files = transfer.files;
        }

        window.openAvatarCropper = function(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            if (!file.type.startsWith('image/')) return;

            activeInput = input;
            activePreview = document.getElementById(input.dataset.previewTarget || 'avatarPreview');
            activeIcon = document.getElementById(input.dataset.iconTarget || 'avatarIcon');
            originalFile = file;
            imageUrl = URL.createObjectURL(file);
            image = new Image();
            image.onload = () => {
                fitImage();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };
            image.src = imageUrl;
        };

        zoomInput.addEventListener('input', () => {
            const previousScale = scale;
            const centerX = canvas.width / 2;
            const centerY = canvas.height / 2;
            const zoom = Number(zoomInput.value);
            scale = minScale * zoom;
            zoomValue.textContent = `${Math.round(zoom * 100)}%`;
            offsetX = centerX - ((centerX - offsetX) / previousScale) * scale;
            offsetY = centerY - ((centerY - offsetY) / previousScale) * scale;
            draw();
        });

        canvas.addEventListener('pointerdown', (event) => {
            canvas.setPointerCapture(event.pointerId);
            const point = canvasPoint(event);
            dragStart = { x: point.x, y: point.y, offsetX, offsetY };
        });

        canvas.addEventListener('pointermove', (event) => {
            if (!dragStart) return;
            const point = canvasPoint(event);
            offsetX = dragStart.offsetX + point.x - dragStart.x;
            offsetY = dragStart.offsetY + point.y - dragStart.y;
            draw();
        });

        canvas.addEventListener('pointerup', () => { dragStart = null; });
        canvas.addEventListener('pointercancel', () => { dragStart = null; });

        confirmButton.addEventListener('click', () => {
            canvas.toBlob((blob) => {
                if (!blob || !activeInput) return;
                updateFileInput(blob);
                if (activePreview) {
                    activePreview.src = URL.createObjectURL(blob);
                    activePreview.classList.remove('hidden');
                }
                if (activeIcon) activeIcon.classList.add('hidden');
                closeModal();
            }, 'image/jpeg', 0.9);
        });

        cancelButton.addEventListener('click', () => closeModal({ clearInput: true }));
        cancelIconButton.addEventListener('click', () => closeModal({ clearInput: true }));
    })();
</script>
@endonce
