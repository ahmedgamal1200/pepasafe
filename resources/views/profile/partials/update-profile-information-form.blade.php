@php
    use App\Models\Setting;use Illuminate\Contracts\Auth\MustVerifyEmail;
    $currentLocale = app()->getLocale();
    $direction = ($currentLocale === 'ar') ? 'rtl' : 'ltr';
    $dirClass = $direction === 'rtl' ? 'text-right' : 'text-left';
    $flexDir = $direction === 'rtl' ? 'flex-row-reverse' : 'flex-row';
@endphp

<section dir="{{ $direction }}">

    <header class="{{ $dirClass }}">
        {{-- تم عكس ترتيب العناصر في الـ Header ليتناسب مع RTL --}}
        <div class="flex items-center gap-2 {{ $direction === 'rtl' ? 'flex-row-reverse' : 'flex-row' }}">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" style="color: #1a202c;">
                {{ trans_db('verify.profile_information') }}
            </h2>

            <div class="w-px h-6 bg-gray-400"></div>


            @if($user->category)
                <div class="flex items-center gap-2 text-gray-900 dark:text-gray-100 text-lg font-semibold"
                     style="color: #1a202c;">
                    <span style="color: #1a202c;">{{ $user->name }}</span>
                    <span class="flex-shrink-0" title="{{ $category->icon }}">
                {!! App\Helpers\IconHelper::get($category->icon) !!}
            </span>
                </div>
            @else
                <div class="text-gray-900 dark:text-gray-100 text-lg font-semibold" style="color: #1a202c;">
                    <span style="color: #1a202c;">{{ $user->name }}</span>
                </div>
            @endif
        </div>


        <p class="mt-1 text-sm text-gray-600">
            {{ trans_db('verify.update_profile_email') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')


        {{-- تم استخدام $flexDir لعكس الصورة والنص في RTL --}}
        <div class="flex items-center gap-4 {{ $flexDir }}">
            <div class="shrink-0">
                <img id="current_avatar_preview"
                     src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAMAAACahl6sAAAAMFBMVEXU1NT////Y2Nj7+/va2trm5ubz8/Pf39/29vbe3t7j4+P8/Pzt7e3Z2dn09PTp6enlgXfuAAAEj0lEQVR4nO2dCZarOgxEMVPCkGT/u31N8+mEEIIHVUmf47sC6ghNRhZFkclkMplMJpPJZDKZTCaTyWQymUwmk8lsKLuu75sf+r7rSu2niaNrxrZyK6p2bDrt5wqibtrB7TC0Ta39fH6Uj+ueiIXrw/5r1rdHKmbaXvtJv9JUxxL+PKbRfto9yhAZsxSTb1gfKONXir3XrPb0jXdaYyHssRtujxge2s/+wu0w4H7jetN+/oU+2hz/GcWIp4xpMiZGbQ0TkV6+ptVWUZR3CR3O3ZVTSpnk5q9cVZWUEUlwj0pRiZw9JhRtIuQfC3ctHSLx6hWl2PWQ1uGcSrlykdfh3IWvQzJgPVEIXeIOMkN3kwajwzlyA1wmFrz7DNyXS6Di3YNaCXc4Hc4xDyNFS5N3rjwdPVKHc7yGEWoQokkgOf0VVn4HG4RmEmjImuEELmAOWeDkEki1uKZi6ADH3hlGBAaVvWsYRTCsXHxlwOuAJ5EZfCoBdOqfwHfv8Gw4A8+JJUeHc+j+iuQieCeB9ervoHt3Qn0yg65SKOlwAp0SCYXWDLrcgulwDquDFn3R8bfmCcGORBC6wwVsl3gaIbTEjk7tlPZwBtsknsYip/GR0wg5TR45TYlynqKR1LLjm3bT9COk0yD8edBpDh9OcxzEClv4DwukYxT8px5S/Yv/QEJyEsJECiUlMr7rUg5NGcNOlHeLMutEqFI4c3SEuEUaq4HnRMpn9oLg7qy5RtxA4wxvrBFcy/PmsTHDywvMIWaol1Anf4F1CnE2s4Ae1JGv7sPaEvZNPpS/868r1JBkMijcQYaUXCqXXQFuonTVVTwGcyPvE2mH17tS2Yk6/KC4/KWTvOKqusSmFlNSKS9/kFKiraMobiJKKgN7HySuUOteZv8jOTOaWPkwcUl6vSqFC7p7lAmHdq2N12ohdjeKlZ0oT25RnjIaiFYbuuDwdbW6ke4S5CqtISff0Hi7ymB24VlR9mNQGK7G3lbA+qVsonaL3I1tb/PdBfgJO/sB67A3aks1qpe+P1xE1tXctSPYRW6bk6aUXnYJkpazyFnjT4qGVW6Qr9QtvfaKX8z4HfLaxph1n74Q14KmtFE+sFqttMbWB07zSxmhwx9H1KxLx+CqJXVtqT/YZp42vjwBDMS0i7ozKEeRXS/pA+YkVe4Lgj+IM3oNHQglOjrklWjpkFYi+a0wWIngcaSePX6ViNkEOzDnoUQoCvPzxztC+YR2P2wfkclscl3yGYFqhbbR5TvJZ/fEW8bfSQzC2gHrSWLoMuDoC0kOb8RBZhLcBDOAGUvC4KZ6JlwTPSlI7dB9iOzibb1YE5Evl6GItRAVuYi7XPyJOOyykwpfiUiLJmrFLcHVI/pCWCzBF8mMGiTYJFYNEmwSswYJNMnNrEF+TBLy4dewQYJMYtdDJgK8xFy1uMa/djSZ1J943xInLpqLw/frtcGyd41nEUzcVxqLn7sbd/UJP3c31ql/wqt7Jy7+i8en5zV1lrWHzxmX8E8OMXj8OvF/ELMmjuOWyTOHLcenEOaz4cxxTjRd+D7Z/KDkH+MbT03dnEr6AAAAAElFTkSuQmCC"}}"
                     alt="Current Profile Avatar" class="h-16 w-16 rounded-full object-cover">
            </div>
            <div class="flex-1"> {{-- إضافة flex-1 لضمان أن العناصر التالية تملأ المساحة المتبقية --}}
                <x-input-label for="profile_picture" :value="trans_db('verify.profile_picture')"
                               class="{{ $dirClass }}"/>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden"
                       onchange="updateFileName(this)"/>
                <label for="profile_picture"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer">
                    {{ trans_db('verify.choose_file') }}
                </label>
                {{-- استخدام الهامش الشرطي: ml-2 في LTR و mr-2 في RTL --}}
                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')"/>
                <p class="mt-1 text-sm text-gray-600 {{ $dirClass }}">
                    {{ trans_db('verify.upload_new_picture') }}
                </p>
            </div>
        </div>


        <script>
            function updateFileName(input) {
                const fileNameSpan = document.getElementById('file_name');
                const noFileText = '{{ trans_db('verify.no_file_chosen') }}';
                if (input.files && input.files.length > 0) {
                    fileNameSpan.textContent = input.files [0].name;
                } else {
                    fileNameSpan.textContent = noFileText;
                }
            }
        </script>


        {{-- تم تغليف كل مجموعة إدخال بـ div مع $dirClass لضمان المحاذاة --}}
        <div class="{{ $dirClass }}">
            <x-input-label for="name" :value="trans_db('verify.name')"/>
            {{-- إضافة $dirClass لضمان محاذاة النص داخل الإدخال --}}
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full {{ $dirClass }}"
                          :value="old('name', $user->name)" required autofocus autocomplete="name"/>
            <x-input-error class="mt-2" :messages="$errors->get('name')"/>
        </div>

        <div class="{{ $dirClass }}">
            <x-input-label for="email" :value="trans_db('verify.email')"/>
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full {{ $dirClass }}"
                          :value="old('email', $user->email)" required autocomplete="username"/>
            <x-input-error class="mt-2" :messages="$errors->get('email')"/>

            @if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ trans_db('verify.email_unverified') }}

                        <button form="send-verification"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ trans_db('verify.click_to_resend') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ trans_db('verify.verification_link_sent') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- إضافة خانة رقم الهاتف هنا --}}
        <div class="{{ $dirClass }}">
            <x-input-label for="phone" :value="trans_db('verify.phone_number')"/>
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full {{ $dirClass }}"
                          :value="old('phone', $user->phone)"
                          autocomplete="tel"/> {{-- type="tel" for semantic HTML --}}
            <x-input-error class="mt-2" :messages="$errors->get('phone')"/>
        </div>

        @if (Setting::where('key', 'show_bio_public')->value('value') === '1')
            <div class="{{ $dirClass }}">
                <x-input-label for="bio" :value="trans_db('verify.bio')"/>

                <textarea id="bio"
                          name="bio"
                          rows="4"
                          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $dirClass }}"
{{--                          placeholder="{{ trans_db('verify.bio_placeholder') }}"--}}
                >{{ old('bio', $user->bio) }}</textarea>

                <x-input-error class="mt-2" :messages="$errors->get('bio')"/>
            </div>
        @endif


        {{-- الأزرار: استخدام justify-end في وضع RTL لترحيلها إلى اليمين --}}
        <div class="flex items-center gap-4">
            <x-primary-button>{{ trans_db('verify.saved') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ trans_db('verify.saved') }}</p>
            @endif
        </div>
    </form>
</section>
