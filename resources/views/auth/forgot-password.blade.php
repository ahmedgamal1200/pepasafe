<x-guest-layout>
    @php
        $isRTL = app()->getLocale() === 'ar';
        $direction = $isRTL ? 'rtl' : 'ltr';
        $textAlignment = $isRTL ? 'text-right' : 'text-left';
        $justifyAlignment = $isRTL ? 'justify-start' : 'justify-end';
    @endphp

    <div dir="{{ $direction }}">
        <div class="mb-4 text-sm text-gray-600 {{ $textAlignment }}">
            {{ __(trans_db('description.forget.pass')) }}
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div>
                <x-input-label for="email" :value="__(trans_db('register.email'))" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center mt-4 {{ $justifyAlignment }}">
                <x-primary-button>
                    {{ __(trans_db('bottom.forget.password')) }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
