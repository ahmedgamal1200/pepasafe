<section>


    @if($events->isNotEmpty() && $events->where('visible_on_profile', 1)->isNotEmpty())
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ trans_db('profile.event.title') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ trans_db('profile.event.desc') }}
            </p>
        </header>



        <div class="container mx-auto px-4"><br>


            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach($events as $event)
                    @if($event->visible_on_profile)
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
                    @endif
                @endforeach
            </div>
        </div>

    @else
        <div class="text-center text-gray-500 text-sm py-12">
            <div class="relative inline-block">
                <i class="fas fa-calendar-alt text-4xl text-blue-400 mb-4"></i>
                <i class="fas fa-times-circle text-red-500 absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 text-xl"></i>
            </div>
            <h2 class="text-lg font-semibold mb-2">{{ trans_db('event.not.found.title') }}</h2>
            <p class="text-sm text-red-600">
                {{ trans_db('event.not.found.desc') }}
            </p>
        </div>


    @endif

</section>
