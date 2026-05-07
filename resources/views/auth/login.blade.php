<x-layouts.guest :title="'Masuk - Kopi Tembalang'">
    <div class="min-h-screen flex flex-col md:flex-row bg-[#FAFAFA]">
        
        {{-- LEFT PANEL: Visual Branding --}}
        <div class="hidden md:flex md:w-1/2 lg:w-7/12 bg-slate-900 relative overflow-hidden items-center justify-center p-20">
            {{-- Soft Glow Decor --}}
            <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-[#D4E971] opacity-[0.08] blur-[120px] rounded-full"></div>
            
            <div class="relative z-10 text-center">
                <img src="{{ asset('images/assets/images/Logo_Kopi_Tembalang.jpeg') }}" 
                     class="h-32 w-32 rounded-[2.5rem] mx-auto mb-10 shadow-2xl border-4 border-slate-800 object-cover" alt="Logo">
                
                <h1 class="text-6xl font-black text-white italic leading-none tracking-tighter mb-6">
                    KOPI<br><span class="text-[#D4E971] not-italic">TEMBALANG.</span>
                </h1>
                
                <p class="text-white/40 text-xs font-bold max-w-xs mx-auto leading-relaxed uppercase tracking-[0.2em]">
                    Selamat Datang kembali di sistem manajemen operasional harian.
                </p>
            </div>
        </div>

        {{-- RIGHT PANEL: Login Form --}}
        <div class="flex-1 flex items-center justify-center p-6 md:p-12 relative bg-pattern">
            
            {{-- Form Card --}}
            <div class="w-full max-w-md bg-white p-8 md:p-10 rounded-[3rem] shadow-xl shadow-slate-200/60 border border-slate-50 relative z-10">
                
                {{-- Header Form --}}
                <div class="mb-10 text-center md:text-left">
                    <div class="md:hidden mb-6 flex justify-center">
                        <img src="{{ asset('images/assets/images/Logo_Kopi_Tembalang.jpeg') }}" class="h-16 w-16 rounded-2xl shadow-lg" alt="Logo">
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tighter italic">
                        Selamat <span class="text-[#D4E971] not-italic">Datang.</span>
                    </h2>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-1">Silahkan masuk ke akun anda</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-4 tracking-[0.2em]">Email Akses</label>
                        <div class="mt-1 relative">
                            <input type="email" name="email" :value="old('email')" required autofocus
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-[#D4E971]/50 outline-none transition-all font-bold text-slate-700 placeholder:text-slate-300"
                                placeholder="nama@kopitembalang.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 ml-4" />
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-4 tracking-[0.2em]">Kata Sandi</label>
                        <div class="mt-1 relative">
                            <input :type="show ? 'text' : 'password'" name="password" required
                                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-[#D4E971]/50 outline-none transition-all font-bold text-slate-700 placeholder:text-slate-300"
                                placeholder="••••••••••••">
                            
                            {{-- Toggle Eye --}}
                            <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-600 transition-colors">
                                <template x-if="!show">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </template>
                                <template x-if="show">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </template>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-2">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded-lg border-slate-200 text-slate-900 focus:ring-[#D4E971]">
                            <span class="ml-3 text-[10px] font-bold uppercase text-slate-400 tracking-widest group-hover:text-slate-600 transition-colors">Ingat Saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[10px] font-black uppercase text-slate-300 hover:text-[#D4E971] transition-colors tracking-widest">Lupa?</a>
                        @endif
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-slate-900 text-[#D4E971] py-5 rounded-[1.5rem] font-black uppercase tracking-[0.3em] text-[10px] shadow-xl shadow-slate-200 hover:bg-black active:scale-[0.97] transition-all duration-300">
                            Masuk Sekarang
                        </button>
                    </div>
                </form>

                <div class="mt-12 pt-8 border-t border-slate-50 text-center">
                    <p class="text-[9px] font-bold text-slate-200 uppercase tracking-[0.4em]">
                        &copy; {{ date('Y') }} • Kopi Tembalang
                    </p>
                </div>
            </div>
        </div>

    </div>
</x-layouts.guest>