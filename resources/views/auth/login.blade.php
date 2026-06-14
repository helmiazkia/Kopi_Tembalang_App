<x-layouts.guest :title="'Masuk - Kopi Tembalang'">

    {{-- ══════════════════════════════════════════
         LEFT PANEL — Dark Branding
    ══════════════════════════════════════════ --}}
    <div class="panel-left">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="grid-lines"></div>

        <div class="panel-brand">
            <img src="{{ asset('images/assets/images/Logo_Kopi_Tembalang.jpeg') }}"
                 class="brand-logo" alt="Logo Kopi Tembalang">

            <h1 class="brand-title">
                KOPI
                <span>TEMBALANG.</span>
            </h1>

            <p class="brand-desc">
                Sistem manajemen<br>
                operasional harian<br>
                yang terintegrasi.
            </p>
        </div>

        <div class="panel-stats">
            <div class="stat-item">
                <div class="stat-num">POS</div>
                <div class="stat-label">Point of Sale</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">QR</div>
                <div class="stat-label">Self-Order</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">24H</div>
                <div class="stat-label">Monitoring</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         RIGHT PANEL — White Form
    ══════════════════════════════════════════ --}}
    <div class="panel-right">
        <div class="form-card">

            {{-- Accent line (desktop only) --}}
            <div class="card-accent"></div>

            {{-- Mobile Logo --}}
            <div class="mobile-logo">
                <img src="{{ asset('images/assets/images/Logo_Kopi_Tembalang.jpeg') }}" alt="Logo">
                <div class="mobile-logo-text">KOPI <span>TEMBALANG.</span></div>
            </div>

            {{-- Heading --}}
            <div class="form-heading">
                <h2>Selamat <span>Datang.</span></h2>
                <p>Silahkan masuk ke akun anda</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email Akses</label>
                    <div class="input-wrap">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="form-input"
                            placeholder="nama@kopitembalang.com"
                        >
                    </div>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group" x-data="{ show: false }">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div class="input-wrap">
                        <input
                            id="password"
                            :type="show ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="form-input"
                            style="padding-right: 46px;"
                            placeholder="••••••••••••"
                        >
                        <button type="button" @click="show = !show" class="input-icon">
                            <template x-if="!show">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </template>
                            <template x-if="show">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </template>
                        </button>
                    </div>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember & Forgot --}}
                <div class="form-meta">
                    <label class="remember-label">
                        <input type="checkbox" name="remember">
                        <span>Ingat Saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa Sandi?</a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-submit">
                    Masuk Sekarang →
                </button>
            </form>

            {{-- Footer --}}
            <div class="form-footer">
                <p>&copy; {{ date('Y') }} &nbsp;•&nbsp; Kopi Tembalang</p>
            </div>

        </div>
    </div>

</x-layouts.guest>