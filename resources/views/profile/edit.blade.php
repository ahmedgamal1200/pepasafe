@php
    use App\Models\Setting;
@endphp

<x-app-layout>

    @include('partials.auth-navbar')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>

            </div>
            @if (Setting::where('key', 'show_documents_in_profile')->value('value') === '1')
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-6xl mx-auto">

                @include('profile.partials.show-documents')
                </div>
            </div>
            @endif
                @if (Setting::where('key', 'show_events_in_profile')->value('value') === '1')
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <div class="max-w-6xl mx-auto">

                            @include('profile.partials.show-events')
                        </div>
                    </div>
                @endif

            @if (Setting::where('key', 'user_can_share_here_profile')->value('value') === '1')
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.share-profile')
                </div>
            </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
