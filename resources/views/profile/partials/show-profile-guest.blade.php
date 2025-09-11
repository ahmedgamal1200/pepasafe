<x-app-layout>

    @if(auth()->check())
        @include('partials.auth-navbar')
    @else
        @include('partials.navbar')
    @endif

        <div class="mb-6 p-4 bg-blue-100 rounded-lg shadow-md border-l-4 border-blue-500">
            <p class="text-sm font-medium text-blue-700">
                {{ __('You are currently viewing a profile shared with you by another user.') }}
            </p>
        </div>


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


                        <br>
                        <div class="flex items-center gap-2">
                            <x-input-label for="name" :value="__('Name:')" />

                            <div class="flex items-center gap-2 text-gray-900 dark:text-gray-100 text-lg font-semibold" style="color: #1a202c;">
                                <span>{{ $user->name }}</span>

                                @if($user->category)
                                    <span class="flex-shrink-0" title="{{ $category->icon }}">
                                        {!! App\Helpers\IconHelper::get($category->icon) !!}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <br>



                        <div class="flex items-start gap-2">
                            <x-input-label for="bio" :value="__('Bio:')" />

                            <div class="text-gray-900 dark:text-gray-100">
                                @if($user->bio)
                                    <p>{{ $user->bio }}</p>
                                @else
                                    <p class="text-gray-500 italic">{{ __('No bio available.') }}</p>
                                @endif
                            </div>
                        </div>

                    </section>

                </div>
            </div>

{{--                                                الجزء الخاص بالوثيقة     --}}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-6xl mx-auto">

                        <section>
                            @if($documents->isNotEmpty())
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
                                        <section class="max-w-xs bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105 mx-auto lg:ml-auto lg:mr-5 relative">

                                            <div class="relative w-full h-auto">
                                                <img src="{{ asset('storage/' . $document->file_path) }}"
                                                     alt="صورة الشهادة"
                                                     class="w-full h-auto object-cover {{ auth()->check() ? '' : 'blur-sm' }}" />

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

                            @else
                                <div class="text-center text-gray-500 text-sm py-12">
                                    <div class="relative inline-block">
                                        <i class="fas fa-file-alt text-4xl text-blue-400 mb-4"></i>
                                        <i class="fas fa-times-circle text-red-500 absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 text-xl"></i>
                                    </div>
                                    <h2 class="text-lg font-semibold mb-2">No documents available here!</h2>
                                    <p class="text-sm text-red-600">
                                        Please verify that the account owner has allowed their documents to be visible.
                                    </p>
                                </div>



                            @endif

                        </section>

                    </div>
                </div>


            {{--                                                الجزء الخاص بالحدث     --}}

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-6xl mx-auto">

            <section>


                @if($events->isNotEmpty() && $events->where('visible_on_profile', 1)->isNotEmpty())
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('These are the Events of ') }} <strong>{{ $user->name }}</strong>
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('These are the events that the organizer has allowed to be visible on their personal profile.') }}
                        </p>
                    </header>



                    <div class="container mx-auto px-4"><br>


                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                            @foreach($events->where('visible_on_profile', 1) as $event)
                                    <section class="h-full flex flex-col justify-between bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105">


                                        <div class="p-3 space-y-2 rtl:text-right ltr:text-left">
                                            <h2 class="text-xl font-bold mb-3 min-h-[56px]" dir="auto">
                                                {{ trans_db('search.event.title') }}: {{ $event->title }}
                                            </h2>

                                            <div class="flex items-center text-gray-600 text-sm justify-start">
                                                <i class="bi bi-calendar-check-fill text-lg text-blue-600"></i>
                                                <span class="font-medium ltr:ml-2 rtl:mr-2">
                        {{ trans_db('search.event.start.date') }}: {{ $event->start_date }}
                    </span>
                                            </div>

                                            <div class="flex items-center text-gray-600 text-sm justify-start">
                                                <i class="bi bi-send-fill text-lg text-gray-500"></i>
                                                <span class="font-medium ltr:ml-2 rtl:mr-2">
                        {{ trans_db('search.event.end.date') }}: {{ $event->end_date }}
                    </span>
                                            </div>

                                            <div class="flex items-center text-gray-600 text-sm justify-start">
                                                <i class="bi bi-file-earmark-fill text-lg text-gray-500"></i>
                                                <span class="font-medium ltr:ml-2 rtl:mr-2">
                        {{ trans_db('search.event.count.template') }}: {{ $templateCount }}
                    </span>
                                            </div>

                                            <div class="flex items-center text-gray-600 text-sm mb-4 justify-start">
                                                <i class="bi bi-people-fill text-lg text-gray-500"></i>
                                                <span class="font-medium ltr:ml-2 rtl:mr-2">
                        {{ trans_db('search.event.count.participants') }}: {{ $recipientCount }}
                    </span>
                                            </div>

                                            @if(auth()->check() && auth()->user()->hasAnyPermission(['manage events', 'full access', 'full access to events']))
                                                <div class="flex justify-center items-center mt-4">
                                                    <a href="{{ route('showEvent', $event->slug) }}"
                                                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-semibold">
                                                        {{ trans_db('search.event.manage.event') }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </section>
                            @endforeach
                        </div>
                    </div>
{{--                                @endif--}}

                @else
                    <div class="text-center text-gray-500 text-sm py-12">
                        <div class="relative inline-block">
                            <i class="fas fa-calendar-alt text-4xl text-blue-400 mb-4"></i>
                            <i class="fas fa-times-circle text-red-500 absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 text-xl"></i>
                        </div>
                        <h2 class="text-lg font-semibold mb-2">No events available here!</h2>
                        <p class="text-sm text-red-600">
                            Please verify that the account owner has allowed their events to be visible.
                        </p>
                    </div>


                @endif

            </section>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>
