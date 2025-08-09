<x-app-layout>

    @if(auth()->check())
        @include('partials.auth-navbar')
    @else
        @include('partials.navbar')
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Profile Information') }}
                            </h2><br>
                        </header>

                        <div class="flex items-center space-x-4">
                            <div class="shrink-0">
                                <img id="current_avatar_preview" src="{{$user->profile_picture ? asset('storage/' . $user->profile_picture) : "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAMAAACahl6sAAAAMFBMVEXU1NT////Y2Nj7+/va2trm5ubz8/Pf39/29vbe3t7j4+P8/Pzt7e3Z2dn09PTp6enlgXfuAAAEj0lEQVR4nO2dCZarOgxEMVPCkGT/u31N8+mEEIIHVUmf47sC6ghNRhZFkclkMplMJpPJZDKZTCaTyWQymUwmk8lsKLuu75sf+r7rSu2niaNrxrZyK6p2bDrt5wqibtrB7TC0Ta39fH6Uj+ueiIXrw/5r1rdHKmbaXvtJv9JUxxL+PKbRfto9yhAZsxSTb1gfKONXir0XrPb0jXdaYyHssRtujxge2s/+wu0w4H7jetN+/oU+2hz/GcWIp4xpMiZGbQ0TkV6+ptVWUZR3CR3O3ZVTSpnk5q9cVZWUEUlwj0pRiZw9JhRtIuQfC3ctHSLx6hWl2PWQ1uGcSrlykdfh3IWvQzJgPVEIXeIOMkN3kwajwzlyA1wmFrz7DNyXS6Di3YNaCXc4Hc4xDyNFS5N3rjwdPVKHc7yGEWoQokkgOf0VVn4HG4RmEmjImuEELmAOWeDkEki1uKZi6ADH3hlGBAaVvWsYRTCsXHxlwOuAJ5EZfCoBdOqfwHfv8Gw4A8+JJUeHc+j+iuQieCeB9ervoHt3Qn0yg65SKOlwAp0SCYXWDLrcYulwDquDFn3R8bfmCcGORBC6wwVsl3gaIbTEjk7tlPZwBtsknsYip/GR0wg5TR45TYlynqKR1LLjm/bT9COk0yD8edBpDh9OcxzEClv4DwukYxT8px5S/Yv/QEJyEsJECiUlMr7rUg5NGcNOlHeLMutEqFI4c3SEuEUaq4HnRMpn9oLg7qy5RtxA4wxvrBFcy/PmsTHDywvMIWaol1Anf4F1CnE2s4Ae1JGv7sPaEvZNPpS/868r1JBkMijcQYaUXCqXXQFuonTVVTwGcyPvE2mH17tS2Yk6/KC4/KWTvOKqusSmFlNSKS9/kFKiraMobiJKKgN7HySuUOteZv8jOTOaWPkwcUl6vSqFC7p7lAmHdq2N12ohdjeKlZ0oT25RnjIaiFYbuuDwdbW6ke4S5CqtISff0Hi7ymB24VlR9mNQGK7G3lbA+qVsonaL3I1tb/PdBfgJO/sB67A3aks1qpe+P1xE1tXctSPYRW6bk6aUXnYJkpazyFnjT4qGVW6Qr9QtvfaKX8z4HfLaxph1n74Q14KmtFE+sFqttMbWB07zSxmhwx9H1KxLx+CqJXVtqT/YZp42vjwBDMS0i7ozKEeRXS/pA+YkVe4Lgj+IM3oNHQglOjrklWjpkFYi+a0wWIngcaSePX6ViNkEOzDnoUQoCvPzxztC+YR2P2wfkclscl3yGYFqhbbR5TvJZ/fEW8bfSQzC2gHrSWLoMuDoC0kOb8RBZhLcBDOAGUvC4KZ6JlwTPSlI7dB9iOzibb1YE5Evl6GItRAVuYi7XPyJOOyykwpfiUiLJmrFLcHVI/pCWCzBF8mMGiTYJFYNEmwSswYJNMnNrEF+TBLy4dewQYJMYtdDJgK8xFy1uMa/djSZ1J943xInLpqLw/frtcGyd41nEUzcVxqLn7sbd/UJP3c31ql/wqt7Jy7+i8en5zV1lrWHzxmX8E8OMXj8OvF/ELMmjuOWyTOHLcenEOaz4cxxTjRd+D7Z/KDkH+MbT03dnEr6AAAAAElFTkSuQmCC"}}"
                                     alt="Current Profile Avatar" class="h-16 w-16 rounded-full object-cover">
                            </div>
                            <div>
                                <x-input-label for="profile_picture" :value="__('Profile Picture')" />
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden" onchange="updateFileName(this)" />
{{--                                --}}
                                <span id="file_name" class="ml-2 text-sm text-gray-500">{{ $user->profile_picture ? ($user->profile_picture) : __('No file chosen') }}</span>
                                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                            </div>
                        </div>



                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" readonly class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div><br>


                            <div>
                                <x-input-label for="bio" :value="__('Bio')" />

                                <textarea id="bio"
                                          name="bio"
                                          rows="4"
                                          readonly
                                          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                >{{ old('bio', $user->bio) }}</textarea>

                                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                            </div>

                    </section>

                </div>
            </div>

{{--                                                الجزء الخاص بالوثيقة     --}}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-6xl mx-auto">

                        <section>
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('These are the documents of') }} {{ $user->name }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __('These are the documents that the user has allowed others to view.') }}
                                </p>
                            </header>



                            <div class="container mx-auto px-4"><br>

                                @foreach($documents as $document)
                                <a href="{{ auth()->check() ? route('documents.show', $document->uuid) : '#' }}">

                                    <section class="max-w-xs bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105
                        mx-auto lg:ml-auto lg:mr-5 relative">

                                        {{-- صورة الشهادة --}}
                                        <div class="relative w-full h-32">
                                            <iframe src="{{ asset('storage/' . $document->file_path) }}"
                                                    class="w-full h-full object-cover {{ auth()->check() ? '' : 'blur-sm' }}">
                                            </iframe>

                                            {{-- Overlay بلور وقفل لو مش مسجل دخول --}}
                                            @guest
                                                <div class="absolute inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex flex-col justify-center items-center space-y-2 text-white text-center">
                                                    <i class="fas fa-lock text-2xl"></i>
                                                    <a href="{{ route('login') }}"
                                                       class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-sm font-medium">
                                                        سجل الدخول لعرض الشهادة
                                                    </a>
                                                </div>
                                            @endguest
                                        </div>

                                        {{-- تفاصيل الوثيقة --}}
                                        <div class="p-3 space-y-2 text-right">
                                            <h2 class="text-lg font-semibold">وثيقة: {{ $document->template->title ?? ''}}</h2>
                                            <h3 class="text-gray-500 text-sm">تاريخ الإصدار: {{ $document->template->send_at->format('Y-m-d')}}</h3>
                                            <p class="text-gray-600 text-sm">{{ $document->template->event->title ?? ''}}</p>
                                        </div>

                                    </section>
                                </a>
                                @endforeach
                            </div>

                        </section>

                    </div>
                </div>


        </div>
    </div>
</x-app-layout>
