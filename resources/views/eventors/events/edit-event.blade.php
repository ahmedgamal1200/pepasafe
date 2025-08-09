<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>تصميم واجهة المستخدم</title>
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

@include('partials.auth-navbar')



<!-- Event Section -->
<form action="{{ route('document-generation.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

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
            <h2 class="text-xl font-semibold">تفاصيل الحدث</h2>
        </div>
        <div class="flex flex-col gap-2 mb-4">
            <label for="event-name" class="text-base">{{ trans_db('event.name.label', 'ar') }}:</label>
            <input type="text" id="event-name" placeholder="أدخل اسم الحدث" name="event_title"
                   value="{{ $event->title }}" class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full" />
        </div>
        <div class="flex flex-col gap-2 mb-4">
            <label for="Issuing-authority-name" class="text-base">{{ trans_db('event.issuer.label', 'ar') }}:</label>
            <input type="text" id="Issuing-authority-name" placeholder="أدخل جهة الاصدار" name="issuer"
                   value="{{ $event->issuer }}"  class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full" />
        </div>
        <div class="flex flex-col md:flex-row items-center gap-2">
            <input type="date" id="from-date" name="event_start_date"
                   value="{{ $event->start_date }}" class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full md:w-1/2" />
            <span class="text-base">إلى</span>
            <input type="date" id="to-date" name="event_end_date"
                   value="{{ $event->end_date }}" class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full md:w-1/2" />
        </div>
        <input type="hidden" name="user_id" value="{{ $user->id }}">
    </section>

    <!-- Forms Container -->
    <div id="forms-container">
        <!-- قالب النموذج -->
        <div class="form-card max-w-4xl mx-auto bg-white rounded-lg p-6 shadow-md mb-8 hover:shadow-lg transition-shadow duration-300">
            <div class="flex justify-between items-center mb-4" style="margin-top: 20px;">
                <div class="flex items-center gap-3">
                    <i class="fas fa-graduation-cap text-xl text-blue-600"></i>
                    <h3 class="text-xl font-semibold">نموذج 1</h3>
                </div>

                @if ($plan && $plan->enable_attendance)
                    <div class="inline-flex items-center justify-center gap-3 p-2 bg-blue-100 border border-blue-600 rounded-lg presence-wrapper">
                        <span class="presence-label font-medium text-blue-600">تفعيل الحضور</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer toggle-presence" name="is_attendance_enabled" />
                            <!-- المسار -->
                            <div class="toggle-track w-12 h-6 bg-gray-200 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                            <!-- الكورة -->
                            <div class="toggle-thumb absolute left-1 top-1 bg-white w-4 h-4 rounded-full peer-checked:translate-x-6 transition-transform duration-300"></div>
                        </label>
                    </div>
                @endif




            </div>

            <!-- محتوى النموذج الرئيسي -->
            <div class="flex flex-col gap-6 mb-4" style="margin-top: 20px;">
                <label class="text-base">اسم النموذج:</label>
                <input type="text" placeholder="اسم النموذج"  value="{{ $documentTemplate->title }}" class="border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full" name="document_title"/>
            </div>

            <!-- بطاقة الإعدادات المخفية -->
            <div class="presence-card hidden mt-6 max-w-4xl mx-auto border-2 border-dashed border-blue-600 rounded-lg p-4 hover:shadow-lg transition-shadow duration-300">
                <h4 class="text-lg font-semibold mb-2">
                    إعدادات <span id="model-title" class="text-blue-600"></span>
                </h4>

                <div class="flex flex-col gap-6">


                    <div class="flex flex-col gap-4 mb-6">
                        <!-- من -->
                        <div class="flex flex-col gap-2 w-full">
                            <label for="project-from" class="font-medium text-base">تاريخ الإرسال (من)</label>
                            <input
                                type="date"
                                id="project-from"
                                name="attendance_send_at"
                                value="{{ $documentTemplate->send_at }}"
                                class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full"
                            />
                        </div>

                        <!-- تفاصيل المشروع -->
                        <div class="flex flex-col gap-2 w-full">
                            <label for="project-details" class="font-medium text-base">نص الرسالة</label>
                            <textarea
                                id="project-details"
                                name="attendance_message"
                                rows="4"
                                placeholder="اكتب هنا ملاحظات أو تفاصيل إضافية عن المشروع…"
                                class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full"
                            >{</textarea>
                        </div>
                    </div>



                    <!-- طرق إرسال المشروع -->
                    @if ($plan && $plan->enabled_channels && is_array($plan->enabled_channels['attendance'] ?? []))
                        <div class="flex flex-col gap-2 mb-6">
                            <label class="font-medium text-base">طرق إرسال البادج</label>
                            <div class="flex flex-wrap gap-4">

                                @if (in_array('whatsapp', $plan->enabled_channels['attendance']))
                                    <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition">
                                        <input type="checkbox" name="attendance_send_via[]" value="whatsapp" class="form-checkbox text-green-500"/>
                                        <i class="fab fa-whatsapp text-2xl text-green-600"></i>
                                        <span>واتساب</span>
                                    </label>
                                @endif

                                @if (in_array('email', $plan->enabled_channels['attendance']))
                                    <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition">
                                        <input type="checkbox" name="attendance_send_via[]" value="email" class="form-checkbox text-blue-500"/>
                                        <i class="fas fa-envelope text-2xl text-blue-600"></i>
                                        <span>إيميل</span>
                                    </label>
                                @endif

                                @if (in_array('sms', $plan->enabled_channels['attendance']))
                                    <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition">
                                        <input type="checkbox" name="attendance_send_via[]" value="sms" class="form-checkbox text-purple-500"/>
                                        <i class="fas fa-sms text-2xl text-purple-600"></i>
                                        <span>SMS</span>
                                    </label>
                                @endif

                            </div>
                        </div>
                    @endif




                    {{-------------------------------------------------------------------------------------------------}}

                    <div class="flex flex-col md:flex-row gap-6">

                        <div class="flex-1 bg-gray-100 border-2 border-dashed border-gray-400 rounded-lg p-5 flex flex-col items-center gap-4 hover:border-blue-600 transition-colors duration-300">
                            <i class="fas fa-file-excel text-4xl text-green-600"></i>
                            <span class="font-semibold">ملف بيانات القالب (البادج)(Excel)</span>
                            <p class="text-sm text-gray-600 text-center">وصف قصير عن الملف</p>
                            <label class="mt-auto inline-flex items-center gap-3 px-5 py-2 bg-green-600 text-white rounded-md cursor-pointer hover:bg-green-700 transition">
                                <i class="fas fa-upload"></i>
                                ارفاق ملفات
                                <input name="attendance_template_data_file_path" id="badge-excel-input-2" type="file" class="sr-only" accept=".xlsx,.xls" multiple />
                            </label>
                        </div>




                    </div>
                    <div class="flex items-center gap-3 bg-yellow-100 border-2	border-dashed	border-gray-400 rounded-lg p-5 hover:bg-yellow-200 transition-colors duration-300">
                        <i class="fas fa-thumbtack text-2xl text-gray-600"></i>
                        <span class="font-semibold text-gray-800">ملاحظة هامة: الرجاء التأكد من إرفاق جميع ملفات البادج المطلوبة قبل المتابعة.</span>
                    </div>
                </div>
                <!-- محرر النموذج + Options -->
                <div class="flex	flex-col	gap-6 mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-edit text-2xl text-blue-600 cursor-pointer"></i>
                        <h4 class="text-2xl font-semibold">محرر النموذج</h4>
                    </div>
                    {{--                    البادجججججججججججججججججججججججججججججججججج--}}
                    <!-- داخل كل .form-card، وابحث عن هذا الـ div -->
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6 mb-4">

                        <!-- صلاحية الشهادة -->
                        <div class="flex flex-col w-full lg:w-1/3">
                            <label for="certificate-validity" class="mb-1 font-medium">صلاحية البادج</label>
                            <select id="certificate-validity" class="certificate-validity border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full">
                                <option value="permanent">دائمة</option>
                                <option value="temporary">مؤقتة</option>
                            </select>
                        </div>

                    </div>

                    <!-- تواريخ الصلاحية (مخفية افتراضياً) -->
                    <div class="flex flex-col md:flex-row items-center gap-2 mb-4 hidden certificate-dates">
                        <input type="date" name="attendance_valid_from" class="valid-from border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full md:w-1/2" />
                        <span class="text-base">إلى</span>
                        <input type="date" name="attendance_valid_until" class="valid-to border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full md:w-1/2" />
                    </div>
                </div>



                <div class="form-block mb-8">
                    <div class="flex items-center gap-6 mb-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="attendance-face" value="front" class="js-face" data-face="front" checked/>
                            <span>وجه واحد </span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="attendance-face" value="back" class="js-face" data-face="back"/>
                            <span>وجهين</span>
                        </label>
                    </div>

                    <div class="js-filehub attendance-filehub">
                    </div>

                    <div class="fabric-canvas-container mt-4">
                        <canvas id="attendance-preview-canvas"></canvas>
                    </div>
                </div>





                <!-- Attachment Card -->
                {{--                <div class="attachment-card filebox-card border-2 border-dashed border-gray-400 rounded-lg p-6 flex flex-col items-center gap-4 mb-4 hover:border-blue-600 transition-colors duration-300">--}}

                {{--                    <div class="initial-upload-state flex flex-col items-center gap-4">--}}
                {{--                        <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 file-icon"></i>--}}
                {{--                        <h4 class="text-lg font-semibold">الوجه</h4>--}}
                {{--                        <p class="text-center text-gray-600">قم برفع ملفات PDF أو صور فقط.</p>--}}
                {{--                        <label class="inline-flex items-center gap-3 px-6 py-3 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-700 transition">--}}
                {{--                            <i class="fas fa-upload"></i>--}}
                {{--                            أرفاق PDF وصور--}}
                {{--                            <input name="drag-pdf" type="file" class="sr-only file-input" accept="application/pdf,image/*">--}}
                {{--                        </label>--}}
                {{--                    </div>--}}

                {{--                    <div class="file-preview hidden w-full h-48 flex justify-center items-center overflow-hidden absolute inset-0">--}}
                {{--                        <button type="button" class="remove-preview-btn absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg hover:bg-red-600 transition z-10" title="إزالة الملف">--}}
                {{--                            &times;--}}
                {{--                        </button>--}}
                {{--                    </div>--}}
                {{--                </div>--}}


                <!-- Finalize Button البادج -->
                <button
                    type="button"
                    id="attendance-fabric-popup"
                    class="finalize-btn w-full max-w-md mx-auto block px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition disabled:opacity-50"
                    data-design-url="https://www.canva.com/design/YYYYYYYY/view?embed"
                >
                    المعاينة النهائيّة
                </button>

                <!-- 2. مودال المحرّر -->
                <div
                    id="canva-overlay"
                    class="fixed inset-0 bg-black bg-opacity-50 hidden z-50"
                    style="
    padding-right: 20px;
    padding-top: 20px;
"

                >
                    <div
                        class="canva-modal-content
           absolute top-[50%] left-[50%]
           transform -translate-x-1/2 -translate-y-1/2
           bg-white rounded-lg overflow-hidden
           w-full max-w-3xl"

                    >
                        <!-- هيدر المودال -->
                        <div class="flex justify-between items-center bg-gray-100 p-4">
                            <h3 class="text-lg font-semibold">محرر Canva</h3>
                            <div class="flex gap-2">
                                <button
                                    id="canva-save-btn"
                                    type="button"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                                >
                                    حفظ
                                </button>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- تاريخ إرسال المشروع و textarea بشكل عمودي -->
            <div class="flex flex-col gap-4 mb-6" style="margin-top: 20px;">
                <!-- من -->
                <div class="flex flex-col gap-2 w-full">
                    <label for="project-from" class="font-medium text-base">تاريخ ارسال النموذج : </label>
                    <input
                        type="date"
                        id="project-from"
                        name="document_send_at"

{{--                        @dd($documentTemplate)--}}
                        value="{{ \Carbon\Carbon::parse($documentTemplate->send_at)->format('m/d/Y') }}"

                        class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full"
                    />
                </div>

                <!-- تفاصيل المشروع -->
                <div class="flex flex-col gap-2 w-full">
                    <label for="project-details" class="font-medium text-base"> نص الرسالة:</label>
                    <textarea
                        id="project-details"
                        name="document_message"
                        rows="4"
                        placeholder="اكتب هنا ملاحظات أو تفاصيل إضافية عن المشروع…"
                        class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-600 w-full"
                    ></textarea>
                </div>
            </div>

            <!-- طرق إرسال المشروع -->
            @if ($plan && is_array($plan->enabled_channels['documents'] ?? []))
                <div class="flex flex-col gap-2 mb-6">
                    <label class="font-medium text-base">طرق إرسال النموذج</label>
                    <div class="flex flex-wrap gap-4">
                        @if (in_array('whatsapp', $plan->enabled_channels['documents']))
                            <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" name="document_send_via[]" value="whatsapp" class="form-checkbox text-green-500"/>
                                <i class="fab fa-whatsapp text-2xl text-green-600"></i>
                                <span>واتساب</span>
                            </label>
                        @endif

                        @if (in_array('email', $plan->enabled_channels['documents']))
                            <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" name="document_send_via[]" value="email" class="form-checkbox text-blue-500"/>
                                <i class="fas fa-envelope text-2xl text-blue-600"></i>
                                <span>إيميل</span>
                            </label>
                        @endif

                        @if (in_array('sms', $plan->enabled_channels['documents']))
                            <label class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" name="document_send_via[]" value="sms" class="form-checkbox text-purple-500"/>
                                <i class="fas fa-sms text-2xl text-purple-600"></i>
                                <span>SMS</span>
                            </label>
                        @endif
                    </div>
                </div>
            @endif


            <div class="flex flex-col md:flex-row gap-6 mb-4">
                <!-- Excel Card -->
                <div class="flex-1 bg-gray-100 border-2 border-dashed border-gray-400 rounded-lg p-5 flex flex-col items-center gap-4 hover:border-blue-600 transition-colors duration-300">
                    <i class="fas fa-file-excel text-4xl text-green-600"></i>
                    <span class="font-semibold">ملف التواصل (Excel)</span>
                    <p class="text-sm text-gray-600 text-center">وصف قصير عن الملف</p>
                    <label class="mt-auto inline-flex items-center gap-3 px-5 py-2 bg-green-600 text-white rounded-md cursor-pointer hover:bg-green-700 transition">
                        <i class="fas fa-upload"></i>
                        ارفاق ملفات
                        <input name="recipient_file_path" id="excel-input-model-1" type="file" class="sr-only" accept=".xlsx,.xls" multiple />
                    </label>
                </div>

                <!-- Word Card -->
                <div class="flex-1 bg-gray-100 border-2 border-dashed border-gray-400 rounded-lg p-5 flex flex-col items-center gap-4 hover:border-blue-600 transition-colors duration-300">
                    <i class="fas fa-file-excel text-4xl text-green-600"></i>
                    <span class="font-semibold">ملف بيانات القالب (الوثيقة) (Excel)</span>
                    <p class="text-sm text-gray-600 text-center">وصف قصير عن الملف</p>
                    <label class="mt-auto inline-flex items-center gap-3 px-5 py-2 bg-green-600 text-white rounded-md cursor-pointer hover:bg-green-700 transition">
                        <i class="fas fa-upload"></i>
                        ارفاق ملفات
                        <input name="template_data_file_path" id="excel-input-model-2" type="file" class="sr-only" accept=".xlsx,.xls" multiple />
                    </label>
                </div>


            </div>

            <!-- محرر النموذج + Options -->
            <div class="flex flex-col gap-6 mb-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-edit text-2xl text-blue-600 cursor-pointer"></i>
                    <h4 class="text-2xl font-semibold">محرر النموذج</h4>
                </div>






                <!-- ضمن نفس العنصر اللي فيه نوع الورق والاتجاه -->
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6">


                    <!-- صلاحية الشهادة الجديدة -->
                    <div class="flex flex-col w-full lg:w-1/3">
                        <label for="select-cert-validity-new" class="mb-1 font-medium">صلاحية الشهادة</label>
                        <select name="document_validity" class="select-cert-validity-new border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full cert-validity-new">
                            <option value="permanent" selected>دائم</option>
                            <option value="temporary">مؤقت</option>
                        </select>
                    </div>
                </div>

                <!-- الحقول المخفية لتواريخ الصلاحية -->
                <div class="cert-dates-new mt-4 hidden">
                    <div class="flex flex-col lg:flex-row gap-6">
                        <div class="flex flex-col w-full lg:w-1/3">
                            <label for="date-valid-from-new" class="mb-1 font-medium">من</label>
                            <input
                                type="date"
                                id="date-valid-from-new"
                                name="valid_from"
                                class="border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full"
                            />
                        </div>
                        <div class="flex flex-col w-full lg:w-1/3">
                            <label for="date-valid-to-new" class="mb-1 font-medium">إلى</label>
                            <input
                                type="date"
                                id="date-valid-to-new"
                                name="valid_until"
                                class="border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-600 w-full"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{--                                الشهادة--}}
            <template id="file-template" class="js-document-upload-template">
                <div class="filebox-card border-2 border-dashed border-gray-400 rounded-lg p-6 flex flex-col items-center gap-4 mb-4 hover:border-blue-600 transition-colors duration-300 relative min-h-[200px]">
                    <h3 class="card-title text-xl font-bold mb-4">عنوان الكارد هنا</h3>

                    <div class="initial-upload-state flex flex-col items-center gap-4">
                        <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 file-icon"></i>
                        <h4 class="text-lg font-semibold">تحميل ملف التيمبلت (صورة أو PDF)</h4>
                        <p class="text-center text-gray-600">قم برفع ملفات PDF أو صور فقط للشهادة.</p>
                        <label class="inline-flex items-center gap-3 px-6 py-3 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-700 transition">
                            <i class="fas fa-upload"></i>
                            أرفاق ملف التيمبلت
                            <input name="document_template_file_path[]" type="file" class="sr-only file-input" accept="application/pdf,image/*">
                            <input type="hidden" name="template_sides[]" class="side-input" value="">
                        </label>
                    </div>

                    <div class="fabric-canvas-container hidden w-full h-96 flex justify-center items-center absolute inset-0 relative">
                        {{-- 💡 تم حذف عنصر <canvas> من هنا. سيتم إنشاؤه بالكامل بواسطة JavaScript. --}}
                        <button type="button" class="remove-preview-btn absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg hover:bg-red-600 transition z-10" title="إزالة الملف">
                            &times;
                        </button>
                    </div>
                </div>
            </template>

            {{--                                    الشهادة--}}
            <div class="form-block mb-8">
                <div class="flex items-center gap-6 mb-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="front" value="front" class="js-face" data-face="front"/>
                        <span>وجه واحد</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="back" value="back" class="js-face" data-face="back"/>
                        <span>وجهين</span>
                    </label>
                </div>
                <div class="js-filehub"></div>
            </div>

            <!-- محرر النصوص -->
            <div id="text-editor-panel" class="hidden p-3 border rounded-md shadow bg-white space-y-2 mt-3 w-72 text-sm">
                <label class="block">
                    <span class="text-gray-700 text-xs">محتوى النص:</span>
                    <input id="text-content" type="text" class="border px-2 py-1 w-full text-sm" />
                </label>
                <label class="block">
                    <span class="text-gray-700 text-xs">حجم الخط:</span>
                    <input id="font-size" type="number" min="10" max="200" value="20" class="border px-2 py-1 w-full text-sm" />
                </label>
                <label class="block">
                    <span class="text-gray-700 text-xs">لون الخط:</span>
                    <input id="font-color" type="color" value="#000000" class="w-full h-8" />
                </label>
                <label class="block">
                    <span class="text-gray-700 text-xs">نوع الخط:</span>
                    <select id="font-family" class="border px-2 py-1 w-full text-sm">
                        <option value="Arial">Arial</option>
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Courier New">Courier New</option>
                        <option value="Tahoma">Tahoma</option>
                    </select>
                </label>
            </div><br>




            <button type="button" id="fabric-popup"
                    class="finalize-btn w-full max-w-md mx-auto block px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition disabled:opacity-50"
                    data-design-url="https://www.canva.com/design/YYYYYYYY/view?embed">
                المعاينة النهائيّة
            </button>

            {{--            <div id="fabric-container" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); justify-content: center; align-items: center; z-index: 1000;">--}}
            {{--                <div style="background-color: white; padding: 20px; border-radius: 8px; position: relative;">--}}
            {{--                    <canvas id="fabricCanvas" width="800" height="600" style="border:1px solid #ccc;"></canvas>--}}
            {{--                    <button id="close-fabric-popup" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>--}}
            {{--                </div>--}}
            {{--            </div>--}}






        </div>
    </div>

    <!-- زر إضافة نموذج جديد -->
    <button id="add-card-btn" type="button"
            class="w-full max-w-4xl mx-auto bg-gray-100 border-2 border-dashed border-gray-400 rounded-lg p-6 shadow-sm flex flex-col items-center gap-4 cursor-pointer hover:bg-gray-200 transition mb-8">
        <i class="fas fa-plus text-4xl text-blue-600"></i>
        <h3 class="text-xl font-semibold">إضافة نموذج جديد</h3>
        <p class="text-blue-600 text-center">اضغط هنا لإنشاء نموذج حضور جديد</p>
    </button>


    <!-- Adjusted Warning Section: unified design -->
    <!-- Warning Card (yellow) -->
    <div class="max-w-5xl mx-auto bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg p-6 flex items-start gap-4 mb-8">
        <i class="fas fa-exclamation-circle text-3xl mt-1"></i>
        <ul class="list-disc list-inside space-y-2">
            <li>تأكد من مراجعة جميع البيانات قبل الإرسال.</li>
            <li>لا تشارك معلوماتك الشخصية مع أي جهة غير موثوقة.</li>
            <li>احتفظ بنسخة احتياطية من المستندات المرفوعة.</li>
            <li>في حال واجهتك أي مشكلة تقنية، تواصل معنا فوراً.</li>
        </ul>
    </div>

    <!-- Warning Card (red) -->
    <div class="max-w-5xl mx-auto bg-red-100 border border-red-400 text-red-700 rounded-lg p-6 flex items-center gap-4 mb-8">
        <i class="fas fa-exclamation-triangle text-3xl"></i>
        <p class="text-lg font-medium">تأكد من حفظ جميع التغييرات قبل الخروج من الصفحة.</p>
    </div>


    <!-- حاوية مركزية -->
    <div class="flex justify-center mb-8">
        <button class="flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">
            <i class="fas fa-check fa-lg"></i>
            <span>تأكيد وإنشاء الحدث</span>
        </button>
    </div>









</form>

@include('partials.footer')


<!-- Script -->
<script src="{{ asset('js/create-event.js') }}"></script>
{{--<script src="{{ asset('js/attendance.js') }}"></script>--}}

</body>
</html>
