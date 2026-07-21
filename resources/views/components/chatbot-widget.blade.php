<div x-data="chatbot()" @click.outside="isOpen = false" class="fixed right-4 md:right-6 z-[60] font-sans {{ request()->routeIs('product.show') ? 'max-md:bottom-[220px] bottom-20 md:bottom-24' : 'bottom-20 md:bottom-20' }}">
    
    {{-- Chat Button --}}
    <button @click="toggle()" 
            class="w-14 h-14 bg-amber-500 hover:bg-amber-400 text-slate-900 rounded-full shadow-lg flex items-center justify-center transition-transform hover:scale-105">
        <svg x-show="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <svg x-show="isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    {{-- Chat Window --}}
    <div x-show="isOpen" 
         x-transition.opacity.translate.y.10px
         style="display: none; height: 500px; max-height: calc(100svh - 180px);"
         class="absolute bottom-16 right-0 w-[calc(100vw-32px)] sm:w-[350px] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col z-50">
        
        {{-- Header --}}
        <div class="bg-slate-900 text-white p-4 flex items-center gap-3 shrink-0">
            <div class="w-8 h-8 bg-amber-500 rounded-sm flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-slate-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-sm" style="font-family: 'Sora', sans-serif;">Asisten CV Berkah</h3>
                <p class="text-[10px] text-slate-300">Tanya seputar layanan kami</p>
            </div>
        </div>

        {{-- Messages Area --}}
        <div class="flex-1 p-4 overflow-y-auto bg-slate-50 space-y-4 min-h-0" id="chatbot-messages">
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.isUser ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.isUser ? 'bg-amber-500 text-slate-900' : 'bg-white border border-slate-200 text-slate-700'" 
                         class="max-w-[85%] rounded-2xl px-4 py-2.5 text-sm whitespace-pre-wrap shadow-sm" x-html="msg.text">
                    </div>
                </div>
            </template>
            <div x-show="isLoading" class="flex justify-start">
                <div class="bg-white border border-slate-200 rounded-2xl px-4 py-3 shadow-sm flex gap-1">
                    <span class="w-2 h-2 bg-slate-300 rounded-full animate-bounce"></span>
                    <span class="w-2 h-2 bg-slate-300 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                    <span class="w-2 h-2 bg-slate-300 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                </div>
            </div>
        </div>

        {{-- Quick Replies & Input --}}
        <div class="bg-white border-t border-slate-100 p-3 shrink-0">
            {{-- Quick Replies --}}
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide" x-show="showFaqs">
                <template x-for="faq in faqs" :key="faq.id">
                    <button @click="askFaq(faq)" 
                            class="shrink-0 px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 rounded-full text-xs font-medium border border-sky-100 transition whitespace-nowrap"
                            x-text="faq.question">
                    </button>
                </template>
            </div>

            {{-- Input Box --}}
            <form @submit.prevent="submitManual" class="flex items-center gap-2 mt-1">
                <input type="text" x-model="inputText" placeholder="Ketik pertanyaan..." 
                       class="flex-1 border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                <button type="submit" :disabled="!inputText.trim() || isLoading"
                        class="w-10 h-10 bg-slate-900 hover:bg-slate-800 text-white rounded-xl flex items-center justify-center transition disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function chatbot() {
    return {
        isOpen: false,
        messages: [
            { isUser: false, text: 'Halo! Ada yang bisa saya bantu? Silakan pilih pertanyaan di bawah atau ketik langsung pertanyaan Anda.' }
        ],
        faqs: [],
        inputText: '',
        isLoading: false,
        showFaqs: true,
        waNumber: '{{ \App\Models\Setting::getSetting()->wa_number ?? "" }}',

        init() {
            this.fetchFaqs();
        },
        
        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) this.scrollToBottom();
        },

        async fetchFaqs() {
            try {
                const res = await fetch('/chatbot/faqs');
                const data = await res.json();
                if(data && data.length > 0) {
                    this.faqs = data;
                } else {
                    this.useFallbackFaqs();
                }
            } catch (err) {
                console.error('Gagal memuat FAQ:', err);
                this.useFallbackFaqs();
            }
        },

        useFallbackFaqs() {
            let cleanWa = this.waNumber.replace(/\D/g, '');
            if(!cleanWa) cleanWa = '6281234567890';
            let waLink = `https://wa.me/${cleanWa}?text=Halo,%20CS%20CV%20Berkah.%20Saya%20ingin%20bertanya...`;

            this.faqs = [
                { 
                    id: 'fallback-1', 
                    question: 'Bagaimana cara memesannya?', 
                    answer: 'Sangat mudah! 🎉<br><br>1. Cari barang yang Anda inginkan, lalu tekan tombol <b>Tambah ke Keranjang</b>.<br>2. Kalau sudah selesai, pencet gambar <b>Keranjang</b> di pojok kanan atas.<br>3. Terakhir, klik tombol <b>Checkout via WhatsApp</b>.<br><br>Nanti Anda akan langsung terhubung dengan tim CS kami untuk mengatur pembayaran dan pengiriman. Mudah kan? 😊' 
                },
                { 
                    id: 'fallback-2', 
                    question: 'Apakah ada layanan antar?', 
                    answer: 'Ada dong! 🚚<br><br>Kami bisa mengantar pesanan langsung ke rumah atau lokasi proyek Anda. Biaya kirimnya bisa didiskusikan langsung dengan CS kami ya, disesuaikan dengan jarak lokasi Anda!' 
                },
                { 
                    id: 'fallback-3', 
                    question: 'Hubungi CS (Tanya Lainnya)', 
                    answer: `Tentu! Anda bisa langsung berbicara dengan tim CS kami untuk menanyakan harga, ukuran khusus, atau bantuan lainnya.<br><br><a href="${waLink}" target="_blank" class="inline-block bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-lg mt-1 hover:bg-emerald-600 transition">💬 Chat CS via WhatsApp</a>` 
                }
            ];
        },

        async askFaq(faq) {
            this.messages.push({ isUser: true, text: faq.question });
            this.showFaqs = false;
            this.scrollToBottom();
            
            if (faq.answer) {
                this.isLoading = true;
                setTimeout(() => {
                    this.isLoading = false;
                    this.messages.push({ isUser: false, text: faq.answer });
                    this.showFaqs = true;
                    this.scrollToBottom();
                }, 600);
                return;
            }

            this.isLoading = true;
            try {
                const res = await fetch('/chatbot/ask', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ question: faq.question, faq_id: faq.id })
                });
                const data = await res.json();
                this.isLoading = false;
                
                if (data.answer) {
                    this.messages.push({ isUser: false, text: data.answer });
                } else {
                    this.showCSFallback(faq.question);
                }
            } catch (err) {
                this.isLoading = false;
                this.showCSFallback(faq.question);
            }
            this.showFaqs = true;
            this.scrollToBottom();
        },

        showCSFallback(q) {
            let cleanWa = this.waNumber.replace(/\D/g, '');
            if(!cleanWa) cleanWa = '6281234567890';
            let waLink = `https://wa.me/${cleanWa}?text=Halo,%20saya%20ingin%20bertanya:%20${encodeURIComponent(q)}`;
            this.messages.push({ 
                isUser: false, 
                text: `Untuk pertanyaan ini atau konsultasi lebih lanjut, silakan hubungi CS kami secara langsung.<br><br><a href="${waLink}" target="_blank" class="inline-block bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-lg mt-1 hover:bg-emerald-600 transition">Chat CS via WhatsApp</a>`
            });
        },

        submitManual() {
            if (!this.inputText.trim()) return;
            const q = this.inputText.trim();
            this.inputText = '';
            
            this.messages.push({ isUser: true, text: q });
            this.showFaqs = false;
            this.scrollToBottom();
            
            this.isLoading = true;
            setTimeout(() => {
                this.isLoading = false;
                this.showCSFallback(q);
                this.showFaqs = true;
                this.scrollToBottom();
            }, 800);
        },

        scrollToBottom() {
            setTimeout(() => {
                const el = document.getElementById('chatbot-messages');
                if(el) el.scrollTop = el.scrollHeight;
            }, 100);
        }
    }
}
</script>
<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush
