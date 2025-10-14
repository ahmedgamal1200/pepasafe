<!DOCTYPE html>
{{--<html lang="ar" dir="rtl">--}}
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.jpg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Create Documents</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" />
    <!-- Canva Embed SDK -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

</head>


<style>
    /* هذا الكود يمكن وضعه في ملف CSS خاص بك أو في <style> في ملف الـ HTML */
    /* سيجعل أي عنصر يحمل الكلاس dragging-proxy يظهر فوق كل شيء آخر */
    .dragging-proxy {
        z-index: 999999999 !important; /* رقم كبير جداً لضمان الظهور فوق كل شيء */
        /* يمكنك أيضاً إضافة هذه الخاصية لمنع القص بواسطة عناصر أب */
        position: fixed !important; /* هام جداً: يجعله مرتبطاً بنافذة العرض وليس بالعناصر الأبوية */
        pointer-events: none; /* لا يتفاعل مع الماوس، فقط للعرض */
    }
</style>
<body class="space-y-12 bg-white">

{{--    <div dir="ltr" class="text-right">--}}
        @include('partials.auth-navbar')
{{--    </div>--}}


<!-- Plan Card -->
<div class="max-w-5xl mx-auto bg-gradient-to-l from-blue-600 to-purple-500 text-white rounded-lg p-6 flex flex-col md:flex-row justify-between items-center gap-4 mb-8 hover:shadow-lg transition-shadow duration-300">

    @php
        $isRTL = app()->getLocale() === 'ar';
        $direction = $isRTL ? 'rtl' : 'ltr';
        $textAlignment = $isRTL ? 'text-right' : 'text-left';
        $flexDirection = $isRTL ? 'flex-row-reverse' : 'flex-row';
    @endphp

    <div class="flex flex-col gap-2 w-full md:w-2/3" dir="{{ $direction }}">

        <div class="text-xl font-semibold {{ $textAlignment }}">
            {{ trans_db('plan.title') }}:
            <strong>{{ $plan->name ?? 'Super Admin' }}</strong>
            ({{ $docsAvailableInPlan }} {{ trans_db('plan.docs_available') }})
        </div>

        <div class="text-base {{ $textAlignment }}">
            {{ trans_db('wallet.balance') }}:
            <strong>
                {{ intval($walletBalance) }} {{ trans_db('wallet.currency') }}
            </strong>
            ({{ trans_db('wallet.can_issue') }} {{ intval($docsAvailableFromWallet) }} {{ trans_db('wallet.extra_certificates') }})
        </div>
    </div>
    @php
        $isRTL = app()->getLocale() === 'ar';
        $direction = $isRTL ? 'rtl' : 'ltr';
        $flexDirection = $isRTL ? 'flex-row-reverse' : 'flex-row';
        $justifyContent = $isRTL ? 'md:justify-start' : 'md:justify-end';
    @endphp

    <div class="flex gap-4 w-full md:w-1/3 justify-center {{ $justifyContent }}" dir="{{ $direction }}">
        <a href="{{ route('wallet') }}#recharge" class="inline-block">
            <button class="flex items-center gap-2 px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded-md hover:bg-blue-600 hover:text-white transition {{ $flexDirection }}">
                <i class="fab fa-cc-visa fa-lg"></i><span>{{ trans_db('event.charge') }}</span>
            </button>
        </a>
        <a href="{{ route('wallet') }}#upgrade" class="inline-block">
            <button class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-700 to-indigo-600 text-white rounded-md hover:opacity-90 transition {{ $flexDirection }}">
                <i class="fas fa-arrow-up fa-lg"></i><span>{{ trans_db('event.upgrade') }}</span>
            </button>
        </a>
    </div>
</div>

<!-- Event Section -->
<form id="documentGenerationForm" action="{{ route('document-generation.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- حقل مخفي جديد للحضور -->
    <input type="hidden" name="attendance_text_data" id="attendance_text_data">

    <!-- حقل مخفي جديد للشهادات -->
    <input type="hidden" name="certificate_text_data" id="certificate_text_data">

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 border border-green-400 text-green-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 border border-red-400 text-red-800 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 border border-red-400 text-red-800 px-4 py-3">
            <ul class="list-disc ps-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif









    <section class="max-w-5xl mx-auto bg-white rounded-lg p-6 shadow-md mb-8 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-house-chimney text-2xl text-blue-600"></i>
            <h2 class="text-xl font-semibold">{{ trans_db('event.details') }}</h2>
        </div>
        <div class="flex flex-col gap-2 mb-4">
            <label for="event-name" class="text-base">{{ trans_db('event.name.label') }}:</label>
            <input type="text" id="event-name" placeholder="{{ trans_db('event.name_placeholder') }}" name="event_title"
                   value="{{ old('event_title') }}"
                   class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full" />
        </div>
        <div class="flex flex-col gap-2 mb-4">
            <label for="Issuing-authority-name" class="text-base">{{ trans_db('event.issuer.label') }}:</label>
            <input type="text" id="Issuing-authority-name" placeholder="{{ trans_db('event.issuer_placeholder') }}" name="issuer"
                   value="{{ old('issuer') }}" class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full" />
        </div>
        @php
            $isRTL = app()->getLocale() === 'ar';
            $direction = $isRTL ? 'rtl' : 'ltr';
            $textAlignment = $isRTL ? 'text-right' : 'text-left';
        @endphp

        <div class="flex flex-col md:flex-row items-center gap-2" dir="{{ $direction }}">
            <!-- حاوية حقل 'من' (From) -->
            <div class="flex flex-col w-full md:w-1/2">
                <label for="from-date" class="{{ $textAlignment }} mb-1">{{ trans_db('doc.date.from') }}:</label>
                <input type="date" id="from-date" name="event_start_date"
                       value="{{ old('event_start_date') }}"
                       class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full" />
            </div>

            <!-- حاوية حقل 'إلى' (To) -->
            <div class="flex flex-col w-full md:w-1/2">
                <label for="to-date" class="{{ $textAlignment }} mb-1">{{ trans_db('doc.date.to') }}:</label>
                <input type="date" id="to-date" name="event_end_date"
                       value="{{ old('event_end_date') }}"
                       class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full" />
            </div>
        </div>
        <input type="hidden" name="user_id" value="{{ $user->id }}">
    </section>

    <!-- Forms Container -->
    @php
        $isRTL = app()->getLocale() === 'ar';
        $direction = $isRTL ? 'rtl' : 'ltr';
        $textAlignment = $isRTL ? 'text-right' : 'text-left';
        $flexDirection = $isRTL ? 'flex-row-reverse' : 'flex-row';
        $reverseFlexDirection = $isRTL ? 'flex-row' : 'flex-row-reverse';
        $iconMargin = $isRTL ? 'mr-2' : 'ml-2';
        $paddingStart = $isRTL ? 'pr-4' : 'pl-4';
//         $textAlignment = $isRTL ? 'text-right' : 'text-left';
        $paddingEnd = $isRTL ? 'pl-4' : 'pr-4';
    @endphp
    <div id="forms-container" dir="{{ $direction }}">
        <div class="form-card max-w-4xl mx-auto bg-white rounded-lg p-6 shadow-md mb-8 hover:shadow-lg transition-shadow duration-300">
            <div class="flex justify-between items-center mb-4" style="margin-top: 20px;">
                <div class="flex items-center gap-3 {{ $flexDirection }}">
                    <i class="fas fa-graduation-cap text-xl text-blue-600 {{ $iconMargin }}"></i>
                    <h3 class="text-xl font-semibold">{{ trans_db('form.title') }} 1</h3>
                </div>

                @if ($plan && $plan->enable_attendance)
                    <div class="inline-flex items-center justify-center gap-3 p-2 bg-blue-100 border border-blue-600 rounded-lg presence-wrapper {{ $reverseFlexDirection }}">
                        <span class="presence-label font-medium text-blue-600">{{ trans_db('form.enable_attendance') }}</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer toggle-presence" name="is_attendance_enabled"/>
                            <div class="toggle-track w-12 h-6 bg-gray-200 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                            <div class="toggle-thumb absolute left-1 top-1 bg-white w-4 h-4 rounded-full peer-checked:translate-x-6 transition-transform duration-300"></div>
                        </label>
                    </div>
                @endif
            </div>

            <div class="flex flex-col gap-6 mb-4" style="margin-top: 20px;">
                <label class="text-base {{ $textAlignment }}">{{ trans_db('form.name') }}:</label>
                <input type="text" placeholder="{{ trans_db('form.name.placeholder') }}" class="border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full {{ $textAlignment }}"
                       name="document_title" value="{{ old('document_title') }}"
                />
            </div>

            <div class="presence-card hidden mt-6 max-w-4xl mx-auto border-2 border-dashed border-blue-600 rounded-lg p-4 hover:shadow-lg transition-shadow duration-300">
                <h4 class="text-lg font-semibold mb-2 {{ $textAlignment }}">
                    {{ trans_db('settings.title') }} <span id="model-title" class="text-blue-600"></span>
                </h4>

                <div class="flex flex-col gap-6">
                    <div class="flex flex-col gap-4 mb-6">
                        <div class="flex flex-col gap-2 w-full">
                            <label for="project-from" class="font-medium text-base {{ $textAlignment }}">{{ trans_db('form.send_date') }}</label>
                            <input
                                type="datetime-local"
                                id="project-from"
                                name="attendance_send_at"
                                class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full {{ $textAlignment }}"
                                value="{{ old('attendance_send_at') }}"
                            />
                        </div>

                        <div class="flex flex-col gap-2 w-full">
                            <label for="attendance-message-input" class="font-medium text-base {{ $textAlignment }}">{{ trans_db('form.message_text') }}</label>
                            <textarea
                                id="attendance-message-input"
{{--                                id="document-message-input"--}}
                                name="attendance_message"
                                rows="4"
                                placeholder="{{ trans_db('form.message_placeholder') }}"
                                class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full {{ $textAlignment }}"
                            >{{ old('attendance_message') }}</textarea>
                            <div id="attendance-char-counter" class="text-sm text-gray-500 {{ $textAlignment }}"></div>
                            <input type="hidden" name="attendance_message_char_count" id="attendance-char-count-hidden">
                        </div>
                    </div>

                    @if ($plan && $plan->enabled_channels && is_array($plan->enabled_channels['attendance'] ?? []))
                        <div class="flex flex-col gap-2 mb-6">
                            <label class="font-medium text-base {{ $textAlignment }}">{{ trans_db('form.channels') }}</label>
                            <div class="flex flex-wrap gap-4 {{ $direction === 'rtl' ? 'justify-end' : 'justify-start' }}">
                                @if (in_array('whatsapp', $plan->enabled_channels['attendance']))
                                    <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition {{ $reverseFlexDirection }}">
                                        <input type="checkbox" name="attendance_send_via[]" value="whatsapp" class="form-checkbox text-green-500"
                                               data-placeholder="{{ trans_db('placeholders.whatsapp_message') }}"
                                            {{ in_array('whatsapp', old('attendance_send_via', [])) ? 'checked' : '' }} />
                                        <i class="fab fa-whatsapp text-2xl text-green-600"></i>
                                        <span>{{ trans_db('channels.whatsapp', 'ar') }}</span>
                                    </label>
                                @endif

                                @if (in_array('email', $plan->enabled_channels['attendance']))
                                    <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition {{ $reverseFlexDirection }}">
                                        <input type="checkbox" name="attendance_send_via[]" value="email" class="form-checkbox text-blue-500"
                                               data-placeholder="{{ trans_db('placeholders.email_message') }}" {{-- **مهم: هذا هو الـ Placeholder الخاص بالإيميل** --}}
                                            {{ in_array('email', old('attendance_send_via', [])) ? 'checked' : '' }} />
                                        <i class="fas fa-envelope text-2xl text-blue-600"></i>
                                        <span>{{ trans_db('channels.email', 'ar') }}</span>
                                    </label>
                                @endif

                                @if (in_array('sms', $plan->enabled_channels['attendance']))
                                    <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition {{ $reverseFlexDirection }}">
                                        <input type="checkbox" name="attendance_send_via[]" value="sms" class="form-checkbox text-purple-500"
                                               data-placeholder="{{ trans_db('placeholders.sms_message') }}" {{-- **مهم: هذا هو الـ Placeholder الخاص بالرسائل النصية** --}}
                                            {{ in_array('sms', old('attendance_send_via', [])) ? 'checked' : '' }} />
                                        <i class="fas fa-sms text-2xl text-purple-600"></i>
                                        <span>{{ trans_db('channels.sms', 'ar') }}</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    @endif

{{--                    //////هتحط الكود هنا--}}

                    <div class="form-block mb-8" dir="{{ $direction }}">
                        <div class="flex items-center gap-6 mb-4">
                            <label class="inline-flex items-center gap-2 {{ $reverseFlexDirection }}">
                                <input type="radio" name="attendance_template_sides[]" value="front" class="js-face" data-face="front">
                                <span>{{ trans_db('form.one_side') }}</span>
                            </label>
                            <label class="inline-flex items-center gap-2 {{ $reverseFlexDirection }}">
                                <input type="radio" name="attendance_template_sides[]" value="back" class="js-face" data-face="back">
                                <span>{{ trans_db('form.two_sides') }}</span>
                            </label>
                        </div>

                        <div class="js-filehub attendance-filehub"></div>

                        <div class="flex flex-col gap-6 mb-4">
                            <div class="flex items-center gap-3 {{ $flexDirection }}">
                                <i class="fas fa-edit text-2xl text-blue-600 cursor-pointer {{ $isRTL ? 'ml-2' : 'mr-2' }}"></i>
                                <h4 class="text-2xl font-semibold">{{ trans_db('editor.title') }}</h4>
                            </div>

                            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6 mb-4" dir="{{ $direction }}">
                                <div class="flex flex-col w-full lg:w-1/3">
                                    <label for="certificate-validity" class="mb-1 font-medium {{ $textAlignment }}">{{ trans_db('certificate.validity') }}</label>
                                    <select id="certificate-validity" name="attendance_validity" class="certificate-validity border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full {{ $textAlignment }}">
                                        <option value="permanent" {{ old('attendance_validity') == 'permanent' ? 'selected' : '' }}>{{ trans_db('certificate.permanent') }}</option>
                                        <option value="temporary" {{ old('attendance_validity') == 'temporary' ? 'selected' : '' }}>{{ trans_db('certificate.temporary') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row items-center gap-2 mb-4 hidden certificate-dates" dir="{{ $direction }}">
                                <input type="date" name="attendance_valid_from" value="{{ old('attendance_valid_from') }}"
                                       class="valid-from border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full md:w-1/2 {{ $textAlignment }}" />
                                <span class="text-base">{{ trans_db('doc.date.to') }}</span>
                                <input type="date" name="attendance_valid_until" value="{{ old('attendance_valid_until') }}"
                                       class="valid-to border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full md:w-1/2 {{ $textAlignment }}" />
                            </div>
                        </div>

                        <div id="attendance-text-editor-panel" class="hidden p-3 border rounded-md shadow bg-white space-y-2 mt-3 w-72 text-sm {{ $textAlignment }}" dir="{{ $direction }}">
                            <label class="block">
                                <span class="text-gray-700 text-xs">{{ trans_db('editor.text_content') }}:</span>
                                <input id="attendance-text-content" type="text" class="border px-2 py-1 w-full text-sm {{ $textAlignment }}" value="{{ old('text') }}"/>
                            </label>
                            <label class="block">
                                <span class="text-gray-700 text-xs">{{ trans_db('editor.font_size') }}:</span>
                                <input id="attendance-font-size" name="attendance_font_size" type="number" min="10" max="200"  value="{{ old('attendance_font_size', 20) }}" class="border px-2 py-1 w-full text-sm {{ $textAlignment }}"/>
                            </label>
                            <label class="block">
                                <span class="text-gray-700 text-xs">{{ trans_db('editor.font_color') }}:</span>
                                <input id="attendance-font-color" type="color" name="attendance_font_color" value="{{ old('attendance_font_color', '#000000') }}" class="w-full h-8" />
                            </label>
                            <label class="block">
                                <span class="text-gray-700 text-xs">{{ trans_db('editor.font_family') }}:</span>
                                <select id="attendance-font-family" name="attendance_font_family" class="border px-2 py-1 w-full text-sm {{ $textAlignment }}">
                                    <option value="Arial" {{ old('attendance_font_family', 'Arial') == 'Arial' ? 'selected' : '' }}>Arial</option>
                                    <option value="Times New Roman" {{ old('attendance_font_family') == 'Times New Roman' ? 'selected' : '' }}>Times New Roman</option>
                                    <option value="Courier New" {{ old('attendance_font_family') == 'Courier New' ? 'selected' : '' }}>Courier New</option>
                                    <option value="Tahoma" {{ old('attendance_font_family') == 'Tahoma' ? 'selected' : '' }}>Tahoma</option>
                                </select>
                            </label>
                        </div>




                        <div class="flex flex-col md:flex-row gap-6 {{ $direction === 'rtl' ? 'md:flex-row-reverse' : '' }}">
                        <div class="flex-1 bg-gray-100 border-2 border-dashed border-gray-400 rounded-lg p-5 flex flex-col items-center gap-4 hover:border-blue-600 transition-colors duration-300">
                            <i class="fas fa-file-excel text-4xl text-green-600"></i>
                            <span id="badge-file-name-display" class="font-medium text-sm text-gray-500 hidden {{ $textAlignment }}"></span>
                            <span class="font-semibold {{ $textAlignment }}">{{ trans_db('form.template_excel_file') }}</span>
                            <p class="text-sm text-gray-600 text-center {{ $textAlignment }}">{{ trans_db('form.tem.file_description') }}</p>
                            <label class="mt-auto inline-flex items-center gap-3 px-5 py-2 bg-green-600 text-white rounded-md cursor-pointer hover:bg-green-700 transition {{ $reverseFlexDirection }}">
                                <i class="fas fa-upload"></i>
                                <span>{{ trans_db('buttons.attach_files') }}</span>
                                <input name="attendance_template_data_file_path" id="badge-excel-input-2" type="file" class="sr-only" accept=".xlsx,.xls" multiple />
                            </label>
                        </div>
                    </div>

{{--                    <div class="flex items-center gap-3 bg-yellow-100 border-2  border-dashed  border-gray-400 rounded-lg p-5 hover:bg-yellow-200 transition-colors duration-300 {{ $reverseFlexDirection }}">--}}
{{--                        <i class="fas fa-thumbtack text-2xl text-gray-600 {{ $iconMargin }}"></i>--}}
{{--                        <span class="font-semibold text-gray-800 {{ $textAlignment }}">{{ trans_db('form.important_note') }}</span>--}}
{{--                    </div>--}}
                </div>


                    <div class="fabric-canvas-container mt-4" dir="{{ $direction }}">
                        <canvas id="attendance-preview-canvas"></canvas>
                    </div>

{{--            شلت الكود من هنا--}}

                </div>

                <button
                    type="button"
                    id="attendance-fabric-popup"
                    class="finalize-btn w-full max-w-md mx-auto block px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition disabled:opacity-50"
                    data-design-url="https://www.canva.com/design/YYYYYYYY/view?embed"
                    dir="{{ $direction }}"
                >
                    {{ trans_db('buttons.preview_final') }}
                </button>

                <div
                    id="canva-overlay"
                    class="fixed inset-0 bg-black bg-opacity-50 hidden z-50"
                    style="padding-right: 20px; padding-top: 20px;" dir="{{ $direction }}"
                >
                    <div
                        class="canva-modal-content absolute top-[50%] left-[50%] transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg overflow-hidden w-full max-w-3xl"
                    >
                        <div class="flex justify-between items-center bg-gray-100 p-4 {{ $flexDirection }}">
                            <h3 class="text-lg font-semibold">{{ trans_db('editor.canva_editor') }}</h3>
                            <div class="flex gap-2">
                                <button
                                    id="canva-save-btn"
                                    type="button"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                                >
                                    {{ trans_db('buttons.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4 mb-6" style="margin-top: 20px;" dir="{{ $direction }}">
                <div class="flex flex-col gap-2 w-full">
                    <label for="project-from" class="font-medium text-base {{ $textAlignment }}">{{ trans_db('form.send_date') }}:</label>
                    <input
                        type="datetime-local"
                        id="project-from"
                        name="document_send_at"
                        value="{{ old('document_send_at') }}"
                        class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full {{ $textAlignment }}"
                    />
                </div>

                <div class="flex flex-col gap-2 w-full">
                    <label for="document-message-input" class="font-medium text-base {{ $textAlignment }}">{{ trans_db('form.message_text') }}:</label>
                    <textarea
                        id="document-message-input"
                        name="document_message"
                        rows="4"
                        placeholder="{{ trans_db('form.message_placeholder') }}"
                        class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full {{ $textAlignment }}"
                    >{{ old('document_message') }}</textarea>
                    <div id="document-char-counter" class="text-sm text-gray-500 {{ $textAlignment }}"></div>
                    <input type="hidden" name="document_message_char_count" id="document-char-count-hidden">
                </div>
            </div>

            @php
                $isRTL = app()->getLocale() === 'ar';
                $direction = $isRTL ? 'rtl' : 'ltr';
                $textAlignment = $isRTL ? 'text-right' : 'text-left';
                $justifyContent = $isRTL ? 'justify-end' : 'justify-start';
                $flexDirection = $isRTL ? 'flex-row-reverse' : 'flex-row'; // 👈 ده الجديد
            @endphp


        @if ($plan && is_array($plan->enabled_channels['documents'] ?? []))
                <div class="flex flex-col gap-2 mb-6" dir="{{ $direction }}">
                    <label class="font-medium text-base {{ $textAlignment }}">{{ trans_db('form.channels') }}</label>
                    <div class="flex flex-wrap gap-4 {{ $justifyContent }}">
                        @if (in_array('whatsapp', $plan->enabled_channels['documents']))
                            <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition {{ $flexDirection }}">
                                <input type="checkbox" name="document_send_via[]" value="whatsapp"
                                       class="form-checkbox text-green-500"
                                       data-placeholder="{{ trans_db('placeholders.whatsapp_message') }}" {{-- **مهم: هذا هو الـ Placeholder الخاص بالواتساب** --}}
                                    {{ in_array('whatsapp', old('document_send_via', [])) ? 'checked' : '' }} />
                                <i class="fab fa-whatsapp text-2xl text-green-600"></i>
                                <span>{{ trans_db('channels.whatsapp', 'ar') }}</span>
                            </label>
                        @endif

                        @if (in_array('email', $plan->enabled_channels['documents']))
                            <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition">
                                @if ($isRTL)
                                    <span>{{ trans_db('channels.email') }}</span>
                                    <i class="fas fa-envelope text-2xl text-blue-600"></i>
                                    <input type="checkbox" name="document_send_via[]" value="email" class="form-checkbox text-blue-500"
                                           data-placeholder="{{ trans_db('placeholders.email_message') }}" {{-- **مهم: هذا هو الـ Placeholder الخاص بالإيميل** --}}
                                        {{ in_array('email', old('document_send_via', [])) ? 'checked' : '' }} />
                                @else
                                    <input type="checkbox" name="document_send_via[]" value="email" class="form-checkbox text-blue-500"
                                           data-placeholder="{{ trans_db('placeholders.email_message') }}"
                                        {{ in_array('email', old('document_send_via', [])) ? 'checked' : '' }} />
                                    <i class="fas fa-envelope text-2xl text-blue-600"></i>
                                    <span>{{ trans_db('channels.email', 'ar') }}</span>
                                @endif
                            </label>
                        @endif

                        @if (in_array('sms', $plan->enabled_channels['documents']))
                            <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition">
                                @if ($isRTL)
                                    <span>{{ trans_db('channels.sms') }}</span>
                                    <i class="fas fa-sms text-2xl text-purple-600"></i>
                                    <input type="checkbox" name="document_send_via[]" value="sms" class="form-checkbox text-purple-500"
                                           data-placeholder="{{ trans_db('placeholders.sms_message') }}" {{-- **مهم: هذا هو الـ Placeholder الخاص بالرسائل النصية** --}}
                                        {{ in_array('sms', old('document_send_via', [])) ? 'checked' : '' }} />
                                @else
                                    <input type="checkbox" name="document_send_via[]" value="sms" class="form-checkbox text-purple-500"
                                           data-placeholder="{{ trans_db('placeholders.sms_message') }}" {{-- **مهم: هذا هو الـ Placeholder الخاص بالرسائل النصية** --}}
                                        {{ in_array('sms', old('document_send_via', [])) ? 'checked' : '' }} />
                                    <i class="fas fa-sms text-2xl text-purple-600"></i>
                                    <span>{{ trans_db('channels.sms', 'ar') }}</span>
                                @endif
                            </label>
                        @endif
                    </div>
                </div>
            @endif


            <template id="file-template" class="js-document-upload-template">
                <div class="filebox-card border-2 border-dashed border-gray-400 rounded-lg p-6 flex flex-col items-center gap-4 mb-4 hover:border-blue-600 transition-colors duration-300 relative min-h-[200px]" dir="{{ $direction }}">
                    <h3 class="card-title text-xl font-bold mb-4 {{ $textAlignment }}">{{ trans_db('form.card_title') }}</h3>
                    <div class="initial-upload-state flex flex-col items-center gap-4">
                        <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 file-icon"></i>
                        <h4 class="text-lg font-semibold {{ $textAlignment }}">{{ trans_db('form.upload_template_file') }}</h4>
                        {{--                        <p class="text-center text-gray-600 {{ $textAlignment }}">{{ trans_db('form.upload_file_types') }}</p>--}}
                        <label class="inline-flex items-center gap-3 px-6 py-3 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-700 transition {{ $reverseFlexDirection }}">
                            <i class="fas fa-upload"></i>
                            <span>{{ trans_db('buttons.attach_template') }}</span>
                            <input name="document_template_file_path[]" type="file" class="sr-only file-input" accept="application/pdf,image/*">
                            <input type="hidden" name="document_template_sides[]" class="side-input" value="">
                        </label>
                    </div>

                    <div class="fabric-canvas-container hidden w-full h-96 flex justify-center items-center absolute inset-0 relative" dir="{{ $direction }}">
                        <button type="button" class="remove-preview-btn absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg hover:bg-red-600 transition z-10" title="{{ trans_db('buttons.remove_file') }}">
                            &times;
                        </button>
                    </div>
                </div>
            </template>

            <div class="form-block mb-8" dir="{{ $direction }}">
                <div class="flex items-center gap-6 mb-4">
                    <label class="inline-flex items-center gap-2 {{ $reverseFlexDirection }}">
                        <input type="radio" name="front" value="front" class="js-face" data-face="front"/>
                        <span>{{ trans_db('form.one_side') }}</span>
                    </label>
                    <label class="inline-flex items-center gap-2 {{ $reverseFlexDirection }}">
                        <input type="radio" name="back" value="back" class="js-face" data-face="back"/>
                        <span>{{ trans_db('form.two_sides') }}</span>
                    </label>
                </div>
                <div class="js-filehub"></div>
            </div>

            <div class="flex flex-col gap-6 mb-4" dir="{{ $direction }}">
                <div class="flex items-center gap-3 {{ $flexDirection }}">
                    <i class="fas fa-edit text-2xl text-blue-600 cursor-pointer {{ $iconMargin }}"></i>
                    <h4 class="text-2xl font-semibold">{{ trans_db('editor.title') }}</h4>
                </div>

                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6">
                    <div class="flex flex-col w-full lg:w-1/3">
                        <label for="select-cert-validity-new" class="mb-1 font-medium {{ $textAlignment }}">{{ trans_db('certificate.validity') }}</label>
                        <select name="document_validity" class="select-cert-validity-new border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full cert-validity-new {{ $textAlignment }}">
                            <option value="permanent" {{ old('document_validity') == 'permanent' ? 'selected' : '' }}>{{ trans_db('certificate.permanent') }}</option>
                            <option value="temporary" {{ old('document_validity') == 'temporary' ? 'selected' : '' }}>{{ trans_db('certificate.temporary') }}</option>
                        </select>
                    </div>
                </div>

                <div class="cert-dates-new mt-4 hidden">
                    <div class="flex flex-col lg:flex-row gap-6">
                        <div class="flex flex-col w-full lg:w-1/3">
                            <label for="date-valid-from-new" class="mb-1 font-medium {{ $textAlignment }}">{{ trans_db('doc.date.from') }}</label>
                            <input
                                type="date"
                                id="date-valid-from-new"
                                name="valid_from"
                                value="{{ old('valid_from') }}"
                                class="border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full {{ $textAlignment }}"
                            />
                        </div>
                        <div class="flex flex-col w-full lg:w-1/3">
                            <label for="date-valid-to-new" class="mb-1 font-medium {{ $textAlignment }}">{{ trans_db('doc.date.to') }}</label>
                            <input
                                type="date"
                                id="date-valid-to-new"
                                name="valid_until"
                                value="{{ old('valid_until') }}"
                                class="border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full {{ $textAlignment }}"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div id="text-editor-panel" class="hidden p-3 border rounded-md shadow bg-white space-y-2 mt-3 w-72 text-sm {{ $textAlignment }}" dir="{{ $direction }}">
                <label class="block">
                    <span class="text-gray-700 text-xs">{{ trans_db('editor.text_content') }}:</span>
                    <input id="text-content" type="text" class="border px-2 py-1 w-full text-sm {{ $textAlignment }}" />
                </label>
                <label class="block">
                    <span class="text-gray-700 text-xs">{{ trans_db('editor.font_size') }}:</span>
                    <input id="font-size" type="number" min="10" max="200" value="20" class="border px-2 py-1 w-full text-sm {{ $textAlignment }}" />
                </label>
                <label class="block">
                    <span class="text-gray-700 text-xs">{{ trans_db('editor.font_color') }}:</span>
                    <input id="font-color" type="color" value="#000000" class="w-full h-8" />
                </label>
                <label class="block">
                    <span class="text-gray-700 text-xs">{{ trans_db('editor.font_family') }}:</span>
                    <select id="font-family" class="border px-2 py-1 w-full text-sm {{ $textAlignment }}">
                        <option value="Arial">Arial</option>
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Courier New">Courier New</option>
                        <option value="Tahoma">Tahoma</option>
                    </select>
                </label>
            </div><br>



            <div class="flex flex-col md:flex-row gap-6 mb-4 {{ $direction === 'rtl' ? 'md:flex-row-reverse' : '' }}" dir="{{ $direction }}">
                <div class="flex-1 bg-gray-100 border-2 border-dashed border-gray-400 rounded-lg p-5 flex flex-col items-center gap-4 hover:border-blue-600 transition-colors duration-300">
                    <i class="fas fa-file-excel text-4xl text-green-600"></i>
                    <span id="file-name-display" class="font-medium text-sm text-gray-500 hidden {{ $textAlignment }}"></span>
                    <span class="font-semibold {{ $textAlignment }}">{{ trans_db('form.contact_excel_file') }}</span>
                    <p class="text-sm text-gray-600 text-center {{ $textAlignment }}">{{ trans_db('form.con.file_description') }}</p>
                    <label class="mt-auto inline-flex items-center gap-3 px-5 py-2 bg-green-600 text-white rounded-md cursor-pointer hover:bg-green-700 transition {{ $reverseFlexDirection }}">
                        <i class="fas fa-upload"></i>
                        <span>{{ trans_db('buttons.attach_files') }}</span>
                        <input name="recipient_file_path" id="excel-input-model-1" type="file"
                               class="sr-only" accept=".xlsx,.xls" multiple />
                    </label>
                </div>

                <div class="flex-1 bg-gray-100 border-2 border-dashed border-gray-400 rounded-lg p-5 flex flex-col items-center gap-4 hover:border-blue-600 transition-colors duration-300">
                    <i class="fas fa-file-excel text-4xl text-green-600"></i>
                    <span id="file-name-display-2" class="font-medium text-sm text-gray-500 hidden {{ $textAlignment }}"></span>
                    <span class="font-semibold {{ $textAlignment }}">{{ trans_db('form.template_excel_file') }}</span>
                    <p class="text-sm text-gray-600 text-center {{ $textAlignment }}">{{ trans_db('form.tem.file_description') }}</p>
                    <label class="mt-auto inline-flex items-center gap-3 px-5 py-2 bg-green-600 text-white rounded-md cursor-pointer hover:bg-green-700 transition {{ $reverseFlexDirection }}">
                        <i class="fas fa-upload"></i>
                        <span>{{ trans_db('buttons.attach_files') }}</span>
                        <input name="template_data_file_path" id="excel-input-model-2" type="file" value="{{ old('template_data_file_path') }}" class="sr-only" accept=".xlsx,.xls" multiple />
                    </label>
                </div>
            </div>



{{--        -----------------------------------------------ده المكان اللي فيه ارفاق الفايلز --}}

            <button type="button" id="fabric-popup"
                    class="finalize-btn w-full max-w-md mx-auto block px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition disabled:opacity-50"
                    data-design-url="https://www.canva.com/design/YYYYYYYY/view?embed" dir="{{ $direction }}">
                {{ trans_db('buttons.preview_final') }}
            </button>
        </div>
    </div>

    <!-- زر إضافة نموذج جديد -->
    <button id="add-card-btn" type="button"
            class="w-full max-w-4xl hidden mx-auto bg-gray-100 border-2 border-dashed border-gray-400 rounded-lg p-6 shadow-sm flex flex-col items-center gap-4 cursor-pointer hover:bg-gray-200 transition mb-8">
        <i class="fas fa-plus text-4xl text-blue-600"></i>
        <h3 class="text-xl font-semibold">إضافة نموذج جديد</h3>
        <p class="text-blue-600 text-center">اضغط هنا لإنشاء نموذج حضور جديد</p>
    </button>


    <!-- Warning Card (red) -->
    <!-- بطاقة الرسالة النهائية (تم إضافة هذا الكود) -->
    @php
        $isRTL = app()->getLocale() === 'ar';
        $direction = $isRTL ? 'rtl' : 'ltr';
    @endphp

    <div dir="{{ $direction }}" id="warning-card" class="mt-6 mb-6 max-w-5xl mx-auto bg-gray-100 border border-gray-400 text-gray-700 rounded-lg p-6 flex items-center gap-4 hidden {{ $isRTL ? 'flex-row-reverse' : '' }}">
        <i id="warning-card-icon" class="fas fa-info-circle text-3xl"></i>
        <p id="warning-card-message" class="text-lg font-medium"></p>
    </div>



    <!-- حاوية مركزية -->
    <div class="flex justify-center mb-8">
        <button type="submit" class="flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">
            <i class="fas fa-check fa-lg"></i>
            <br><span>{{ trans_db('event.con.create.event') }}</span>
        </button>
    </div>

    <input type="hidden" name="text_data" id="text_data">


</form>

@include('partials.footer')

<script>
    window.documentGenerationStoreUrl = '{{ route('document-generation.store') }}';
    window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    window.cardData = window.cardData || {}; // تأكد إن ده معرف أو تم تهيئته في مكان آخر قبل استخدامه
</script>



<script>
    window.i18n = {
        documents_front_side: "{{ trans_db('documents_front_side') }}",
        documents_back_side: "{{ trans_db('documents_back_side') }}",
        on_attendance: "{{ trans_db('on_attendance') }}",
        off_attendance: "{{ trans_db('off_attendance') }}",
       form_count: "{{ trans_db('form_count') }}",
       char_count: "{{ trans_db('char_count') }}",
        issue_docs_message: "{{ trans_db('issue_docs_message') }}",
        issue_docs_wallet_message: {!! json_encode(trans_db('issue_docs_wallet_message')) !!}
    };
    document.getElementById('warning-card-message').innerHTML = messageHtml;
</script>

{{-- كود ال placeholder بتاع الوثيقة --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // نحدد جميع الـ Checkboxes التي لها الاسم document_send_via[]
        const checkboxes = document.querySelectorAll('input[name="document_send_via[]"]');

        // **التعديل هنا:** البحث عن الـ Textarea باستخدام السمة name="document_message"
        const textarea = document.querySelector('textarea[name="document_message"]');

        // نتأكد من أن الـ textarea موجود قبل المتابعة
        if (!textarea) {
            console.error('Textarea with name="document_message" not found.');
            return;
        }

        const defaultPlaceholder = textarea.placeholder; // حفظ الـ Placeholder الافتراضي

        function updatePlaceholder() {
            let selectedPlaceholder = defaultPlaceholder;

            // نمر على كل Checkbox ونبحث عن أول Checkbox مُعلم (Checked)
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    // نستخدم getAttribute للحصول على قيمة data-placeholder (يجب إضافتها إلى HTML Checkboxes)
                    selectedPlaceholder = checkbox.getAttribute('data-placeholder');

                    // نوقف البحث عند إيجاد أول Checkbox مُعلم
                    return;
                }
            });

            // تحديث سمة الـ placeholder لمنطقة النص
            textarea.placeholder = selectedPlaceholder;
        }

        // 1. إضافة مستمعي الحدث (Event Listeners) لكل Checkbox
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updatePlaceholder);
        });

        // 2. تشغيل الدالة مرة واحدة عند تحميل الصفحة
        updatePlaceholder();
    });
</script>

{{-- كود ال placeHolder بتاع الحضور --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // نحدد جميع الـ Checkboxes التي لها الاسم document_send_via[]
        const checkboxes = document.querySelectorAll('input[name="attendance_send_via[]"]');

        // **التعديل هنا:** البحث عن الـ Textarea باستخدام السمة name="document_message"
        const textarea = document.querySelector('textarea[name="attendance_message"]');

        // نتأكد من أن الـ textarea موجود قبل المتابعة
        if (!textarea) {
            console.error('Textarea with name="document_message" not found.');
            return;
        }

        const defaultPlaceholder = textarea.placeholder; // حفظ الـ Placeholder الافتراضي

        function updatePlaceholder() {
            let selectedPlaceholder = defaultPlaceholder;

            // نمر على كل Checkbox ونبحث عن أول Checkbox مُعلم (Checked)
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    // نستخدم getAttribute للحصول على قيمة data-placeholder (يجب إضافتها إلى HTML Checkboxes)
                    selectedPlaceholder = checkbox.getAttribute('data-placeholder');

                    // نوقف البحث عند إيجاد أول Checkbox مُعلم
                    return;
                }
            });

            // تحديث سمة الـ placeholder لمنطقة النص
            textarea.placeholder = selectedPlaceholder;
        }

        // 1. إضافة مستمعي الحدث (Event Listeners) لكل Checkbox
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updatePlaceholder);
        });

        // 2. تشغيل الدالة مرة واحدة عند تحميل الصفحة
        updatePlaceholder();
    });
</script>
<!-- Script -->
<script src="{{ asset('js/create-event.js') }}"></script>
<script src="{{ asset('js/calculate-doc-price.js') }}"></script>
<script src="{{ asset('js/sms-counter.js') }}"></script>

</body>
</html>
