@once
<div id="avatarCropperModal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/70 px-4 py-6">
    <div class="w-full max-w-lg rounded-2xl bg-surface-container-lowest p-5 shadow-2xl ring-1 ring-outline-variant/20">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-black text-on-surface">Posisikan Foto Siswa</h3>
                <p class="mt-1 text-xs font-bold text-on-surface-variant">Geser gambar dan atur zoom sebelum menekan Confirm.</p>
            </div>
            <button type="button" id="avatarCropCancelIcon" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="mt-5 flex justify-center">
            <canvas id="avatarCropCanvas" width="320" height="320" class="h-80 w-80 max-w-full cursor-grab rounded-2xl bg-surface-container-high ring-1 ring-outline-variant/20 active:cursor-grabbing"></canvas>
        </div>

        <div class="mt-5 space-y-2">
            <label class="text-xs font-black uppercase tracking-widest text-on-surface-variant">Zoom</label>
            <input id="avatarCropZoom" type="range" min="1" max="3" step="0.01" value="1" class="range range-primary range-sm">
        </div>

        <div class="mt-6 flex flex-wrap justify-end gap-3">
            <button type="button" id="avatarCropCancel" class="rounded-full border border-outline-variant/30 px-5 py-2 text-sm font-black text-on-surface-variant hover:bg-surface-container-high">
                Batal
            </button>
            <button type="button" id="avatarCropConfirm" class="rounded-full bg-primary px-5 py-2 text-sm font-black text-on-primary shadow-lg shadow-primary/20 hover:bg-primary/90">
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
            ctx.fillStyle = '#e2e8f0';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            clampOffsets();
            ctx.drawImage(image, offsetX, offsetY, image.width * scale, image.height * scale);
            ctx.save();
            ctx.strokeStyle = 'rgba(255,255,255,0.9)';
            ctx.lineWidth = 2;
            ctx.strokeRect(1, 1, canvas.width - 2, canvas.height - 2);
            ctx.restore();
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
            scale = minScale * Number(zoomInput.value);
            offsetX = centerX - ((centerX - offsetX) / previousScale) * scale;
            offsetY = centerY - ((centerY - offsetY) / previousScale) * scale;
            draw();
        });

        canvas.addEventListener('pointerdown', (event) => {
            canvas.setPointerCapture(event.pointerId);
            dragStart = { x: event.clientX, y: event.clientY, offsetX, offsetY };
        });

        canvas.addEventListener('pointermove', (event) => {
            if (!dragStart) return;
            offsetX = dragStart.offsetX + event.clientX - dragStart.x;
            offsetY = dragStart.offsetY + event.clientY - dragStart.y;
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
