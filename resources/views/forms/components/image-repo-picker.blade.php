<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $isMultiple = $field->isMultiple();
        $pickerToken = $field->getPickerToken();
        $pickerUrl = $field->getImageRepoUrl();
        $repoOrigin = $field->getRepoOrigin();
        $statePath = $field->getStatePath();
    @endphp

    <div
        x-data="{
            state: $wire.entangle('{{ $statePath }}'),
            showModal: false,
            nonce: null,
            iframeLoaded: false,
            pickerToken: @js($pickerToken),
            pickerUrl: @js($pickerUrl),
            repoOrigin: @js($repoOrigin),
            isMultiple: @js($isMultiple),

            openPicker() {
                if (!this.pickerToken) {
                    alert('Image repository is not available. Please check configuration.');
                    return;
                }
                this.nonce = crypto.randomUUID();
                this.iframeLoaded = false;
                this.showModal = true;

                this.$nextTick(() => {
                    const iframe = this.$refs.pickerIframe;
                    if (iframe) {
                        iframe.src = this.pickerUrl;
                    }
                });
            },

            closePicker() {
                this.showModal = false;
                const iframe = this.$refs.pickerIframe;
                if (iframe) {
                    iframe.src = 'about:blank';
                }
            },

            handleMessage(event) {
                if (event.origin !== this.repoOrigin) return;

                if (event.data && event.data.type === 'pickerReady') {
                    this.iframeLoaded = true;
                    const iframe = this.$refs.pickerIframe;
                    if (iframe && iframe.contentWindow) {
                        iframe.contentWindow.postMessage({
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
            @endif
        </div>

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

        {{-- Modal with iframe --}}
        <template x-teleport="body">
            <div
                x-show="showModal"
                x-transition.opacity
                @keydown.escape.window="closePicker()"
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                style="display: none;"
            >
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/50" @click="closePicker()"></div>

                {{-- Modal content --}}
                <div class="relative w-full max-w-6xl h-[85vh] bg-white dark:bg-gray-900 rounded-xl shadow-2xl overflow-hidden flex flex-col">
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 shrink-0">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Select Image from Repository
                        </h3>
                        <button
                            type="button"
                            @click="closePicker()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    {{-- Loading indicator --}}
                    <div x-show="!iframeLoaded" class="flex-1 flex items-center justify-center">
                        <div class="text-gray-500 dark:text-gray-400">
                            <svg class="animate-spin h-8 w-8 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Loading image repository...
                        </div>
                    </div>

                    {{-- Iframe --}}
                    <iframe
                        x-ref="pickerIframe"
                        x-show="iframeLoaded"
                        class="flex-1 w-full border-0"
                    ></iframe>
                </div>
            </div>
        </template>
    </div>
</x-dynamic-component>
