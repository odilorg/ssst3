<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $isMultiple = $field->isMultiple();
        $pickerToken = $field->getPickerToken();
        $pickerUrl = $field->getImageRepoUrl();
        $repoOrigin = $field->getRepoOrigin();
        $statePath = $field->getStatePath();
        $targetField = $field->getTargetField();
        $currentUrl = null;
        if ($targetField) {
            try {
                $record = $field->getRecord();
                if ($record) {
                    $val = $record->getAttribute($targetField);
                    if ($val && is_string($val) && str_starts_with($val, 'http')) {
                        $currentUrl = $val;
                    }
                }
            } catch (\Throwable $e) {}

            // For repeater items (path field), map UUID key to array index
            // statePath: data.gallery_images.{uuid}.path_from_repo
            if (!$currentUrl && $targetField === 'path' && $record) {
                try {
                    if (preg_match('/gallery_images\.([^.]+)\./', $statePath, $m)) {
                        $uuid = $m[1];
                        $livewire = $field->getLivewire();
                        // Get all repeater keys in order to find this UUID's position
                        $allItems = data_get($livewire, 'data.gallery_images', []);
                        $keys = is_array($allItems) ? array_keys($allItems) : [];
                        $position = array_search($uuid, $keys);

                        if ($position !== false) {
                            $galleryImages = $record->gallery_images ?? [];
                            // gallery_images is a 0-indexed array on the model
                            $item = array_values($galleryImages)[$position] ?? null;
                            $val = $item['path'] ?? null;
                            if ($val && is_string($val) && str_starts_with($val, 'http')) {
                                $currentUrl = $val;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $currentUrl = null;
                }
            }
        }
    @endphp

    <div
        x-data="{
            state: $wire.entangle('{{ $statePath }}'),
            pickerToken: @js($pickerToken),
            pickerUrl: @js($pickerUrl),
            repoOrigin: @js($repoOrigin),
            isMultiple: @js($isMultiple),
            currentUrl: @js($currentUrl),
            nonce: null,
            modalEl: null,
            iframeEl: null,

            openPicker() {
                if (!this.pickerToken) {
                    alert('Image repository is not available. Please check configuration.');
                    return;
                }
                this.nonce = crypto.randomUUID();

                if (!this.modalEl) {
                    this.createModal();
                }
                this.modalEl.style.display = 'flex';
                this.iframeEl.src = this.pickerUrl;
                this.modalEl.querySelector('.picker-spinner').style.display = 'flex';
                this.iframeEl.style.display = 'none';
            },

            createModal() {
                const modal = document.createElement('div');
                Object.assign(modal.style, {
                    display: 'none', position: 'fixed', inset: '0',
                    zIndex: '999999', alignItems: 'center',
                    justifyContent: 'center', padding: '1rem'
                });

                const backdrop = document.createElement('div');
                Object.assign(backdrop.style, {
                    position: 'fixed', inset: '0', background: 'rgba(0,0,0,0.5)'
                });
                backdrop.addEventListener('click', () => this.closePicker());
                modal.appendChild(backdrop);

                const panel = document.createElement('div');
                Object.assign(panel.style, {
                    position: 'relative', width: '100%', maxWidth: '72rem',
                    height: '85vh', background: '#1a1a2e', borderRadius: '0.75rem',
                    boxShadow: '0 25px 50px -12px rgba(0,0,0,0.5)',
                    overflow: 'hidden', display: 'flex', flexDirection: 'column'
                });
                modal.appendChild(panel);

                const header = document.createElement('div');
                Object.assign(header.style, {
                    display: 'flex', alignItems: 'center', justifyContent: 'space-between',
                    padding: '0.75rem 1rem', borderBottom: '1px solid #374151', flexShrink: '0'
                });
                const title = document.createElement('h3');
                Object.assign(title.style, { fontSize: '1.125rem', fontWeight: '600', color: '#f3f4f6' });
                title.textContent = 'Select Image from Repository';
                header.appendChild(title);
                const closeBtn = document.createElement('button');
                closeBtn.type = 'button';
                Object.assign(closeBtn.style, { color: '#9ca3af', cursor: 'pointer', background: 'none', border: 'none', padding: '4px', fontSize: '1.5rem', lineHeight: '1' });
                closeBtn.textContent = '\u00D7';
                closeBtn.addEventListener('click', () => this.closePicker());
                header.appendChild(closeBtn);
                panel.appendChild(header);

                const spinner = document.createElement('div');
                spinner.className = 'picker-spinner';
                Object.assign(spinner.style, {
                    flex: '1', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#9ca3af'
                });
                spinner.textContent = 'Loading image repository...';
                panel.appendChild(spinner);

                const iframe = document.createElement('iframe');
                Object.assign(iframe.style, { flex: '1', width: '100%', border: '0', display: 'none' });
                panel.appendChild(iframe);

                document.body.appendChild(modal);
                this.modalEl = modal;
                this.iframeEl = iframe;

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.modalEl && this.modalEl.style.display === 'flex') {
                        this.closePicker();
                    }
                });
            },

            closePicker() {
                if (this.modalEl) {
                    this.modalEl.style.display = 'none';
                }
                if (this.iframeEl) {
                    this.iframeEl.src = 'about:blank';
                }
            },

            handleMessage(event) {
                if (event.origin !== this.repoOrigin) return;

                if (event.data && event.data.type === 'pickerReady') {
                    if (this.iframeEl) {
                        this.iframeEl.style.display = 'block';
                    }
                    if (this.modalEl) {
                        this.modalEl.querySelector('.picker-spinner').style.display = 'none';
                    }
                    if (this.iframeEl && this.iframeEl.contentWindow) {
                        this.iframeEl.contentWindow.postMessage({
                            type: 'authToken',
                            token: this.pickerToken,
                            nonce: this.nonce,
                        }, this.repoOrigin);
                    }
                }

                if (event.data && event.data.type === 'imageSelected') {
                    if (event.data.nonce !== this.nonce) return;

                    const url = event.data.url;

                    if (this.isMultiple) {
                        let current = Array.isArray(this.state) ? [...this.state] : [];
                        if (!current.includes(url)) {
                            current.push(url);
                        }
                        this.state = current;
                    } else {
                        this.state = url;
                        this.closePicker();
                    }
                }
            },

            removeImage(index) {
                if (this.isMultiple) {
                    let current = Array.isArray(this.state) ? [...this.state] : [];
                    current.splice(index, 1);
                    this.state = current;
                } else {
                    this.state = null;
                }
            },

            getThumb(url) {
                if (!url || typeof url !== 'string') return '';
                return url.replace(/\/large\.webp$/, '/thumb.webp');
            },

            init() {
                window.addEventListener('message', (e) => this.handleMessage(e));
            },

            destroy() {
                window.removeEventListener('message', (e) => this.handleMessage(e));
                if (this.modalEl) {
                    this.modalEl.remove();
                    this.modalEl = null;
                }
            }
        }"
        x-init="init()"
        wire:ignore
        class="space-y-2"
    >
        {{-- Preview --}}
        <div class="flex flex-wrap gap-2">
            @if($isMultiple)
                <template x-for="(url, index) in (Array.isArray(state) ? state : [])" :key="index">
                    <div class="relative group w-24 h-24 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <img :src="getThumb(url)" class="w-full h-full object-cover" :alt="'Image ' + (index + 1)">
                        <button
                            type="button"
                            @click="removeImage(index)"
                            class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-lg p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </template>
            @else
                <template x-if="state && typeof state === 'string'">
                    <div class="relative group w-32 h-24 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <img :src="getThumb(state)" class="w-full h-full object-cover" alt="Selected image">
                        <button
                            type="button"
                            @click="removeImage(0)"
                            class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-lg p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </template>
                {{-- Show current external URL from record when picker state is empty --}}
                <template x-if="(!state || typeof state !== 'string') && currentUrl">
                    <div class="relative w-48 h-32 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        <img :src="getThumb(currentUrl)" class="w-full h-full object-cover" alt="Current image from repository">
                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs px-2 py-1">Current image</div>
                    </div>
                </template>
            @endif
        </div>

        {{-- Debug: show state path for troubleshooting --}}
        @if($targetField === 'path')
            <div class="text-xs text-gray-500 break-all">statePath: {{ $statePath }} | currentUrl: {{ $currentUrl ?? 'null' }}</div>
        @endif

        {{-- Button --}}
        <button
            type="button"
            @click="openPicker()"
            class="fi-btn fi-btn-size-sm inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium shadow-sm ring-1 ring-gray-950/10 bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-white/20 dark:hover:bg-gray-700"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Choose from Image Repository
        </button>
    </div>
</x-dynamic-component>
