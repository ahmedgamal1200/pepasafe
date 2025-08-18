<section>

    @if(session('status') === 'document-visibility-toggled')
        <div id="flash-message" class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300">
            <strong class="font-bold">تم بنجاح!</strong>
            <span class="ml-2">تم تحديث حالة ظهور الوثيقة في البروفايل.</span>
        </div>

        <script>
            setTimeout(() => {
                const msg = document.getElementById('flash-message');
                if (msg) {
                    msg.style.opacity = '0';
                    setTimeout(() => msg.remove(), 300); // يُزال من الـ DOM بعد الإخفاء
                }
            }, 3000); // تختفي بعد 3 ثوانٍ
        </script>
    @endif

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
                    <iframe src="{{ asset('storage/' . $document->file_path) }}" class="w-full h-32 object-cover"></iframe>

                    <div class="p-3 space-y-2 text-right">
                        <h2 class="text-lg font-semibold">وثيقة: {{ $document->template->title ?? '' }}</h2>
                        <h3 class="text-gray-500 text-sm">تاريخ الإصدار: {{ $document->template->send_at->format('Y-m-d') }}</h3>
                        <p class="text-gray-600 text-sm">{{ $document->template->event->title ?? '' }}</p>
                        <div class="inline-block border border-gray-300 p-1 rounded hover:border-blue-600 transition relative">
                            <form method="POST" action="{{ route('documents.toggleVisibility', $document->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-gray-500 hover:text-blue-600">
                                    <i class="bi {{ $document->visible_on_profile ? 'bi-eye-fill' : 'bi-eye-slash-fill' }}"></i>
                                </button>
                            </form>
                        </div>

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
