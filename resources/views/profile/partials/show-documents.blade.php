<section>


    @if($documents->isNotEmpty())
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Your Documents') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Click the eye icon to view your document, or to hide it from others on your profile.') }}
        </p>
    </header>



    <div class="container mx-auto px-4"><br>


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($documents as $document)
                @if($document->visible_on_profile || auth()->id() === $document->recipient->user_id)
                <a href="{{ auth()->check() ? route('documents.show', $document->uuid) : '#' }}">
                <section class="bg-white shadow-lg rounded-lg overflow-hidden transition-transform duration-200 hover:scale-105">
                    <img src="{{ asset('storage/' . $document->file_path) }}"
                         alt="صورة الوثيقة"
                         class="w-full h-auto object-contain rounded-lg shadow-md" />

                    <div class="p-3 space-y-2 text-right">
                        <h2 class="text-lg font-semibold">وثيقة: {{ $document->template->title ?? '' }}</h2>
                        <h3 class="text-gray-500 text-sm">تاريخ الإصدار: {{ $document->template->send_at->format('Y-m-d') }}</h3>
                        <p class="text-gray-600 text-sm">{{ $document->template->event->title ?? '' }}</p>
                    </div>
                </section>
                </a>
                @endif
            @endforeach
        </div>
    </div>

        @else
            <div class="text-center text-gray-500 text-sm py-12">
                <div class="relative inline-block">
                    <i class="fas fa-file-alt text-4xl text-blue-400 mb-4"></i>
                    <i class="fas fa-times-circle text-red-500 absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 text-xl"></i>
                </div>
                <h2 class="text-lg font-semibold mb-2">No documents available here!</h2>
                <p class="text-sm text-red-600">
                    Please ensure that you have enabled your documents to be visible on your profile.
                </p>
            </div>
        @endif

</section>
