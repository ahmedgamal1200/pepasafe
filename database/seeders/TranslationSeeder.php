<?php

namespace Database\Seeders;

use App\Models\TranslationKey;
use Database\Factories\TranslationKeyFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all translation keys and their Arabic/English values
        $translations = [
            // Subscription Section
            'subscription.summary_title' => ['ar' => 'ملخص الباقة', 'en' => 'Package Summary'],
            'subscription.package_name_label' => ['ar' => 'اسم الباقة', 'en' => 'Package Name'],
            'subscription.remaining_balance_label' => ['ar' => 'المتبقي من الرصيد في باقتك', 'en' => 'Remaining Balance'],
            'subscription.carry_over_credit_label' => ['ar' => 'ترحيل الرصيد', 'en' => 'Credit Carry Over'],
            'subscription.auto_renew_label' => ['ar' => 'التجديد التلقائي', 'en' => 'Auto-Renewal'],
            'subscription.renew_now_button' => ['ar' => 'تجديد الباقة الآن', 'en' => 'Renew Package Now'],

            // General Messages
            'general.yes_carry_over' => ['ar' => 'نعم، يتم ترحيل الرصيد', 'en' => 'Yes, credit is carried over'],
            'general.no_dont_carry_over' => ['ar' => 'لا، لا يتم ترحيل الرصيد', 'en' => 'No, credit is not carried over'],
            'general.user_label' => ['ar' => 'المستخدم', 'en' => 'User'],
            'general.expiry_date_label' => ['ar' => 'تاريخ الانتهاء', 'en' => 'Expiry Date'],

            // Dynamic Message with placeholder (The date will be inserted by the view)
            'subscription.auto_renew_message_prefix' => ['ar' => 'سيتم التجديد التلقائي بتاريخ', 'en' => 'Auto-renewal will take place on'],


            // ----------------------------------------------------------------
            // المفاتيح الجديدة لقسم المحفظة (Wallet)
            // ----------------------------------------------------------------
            'wallet.available_balance_title' => ['ar' => 'الرصيد المتاح', 'en' => 'Available Balance'],
            'wallet.currency_unit' => ['ar' => 'جنية', 'en' => 'EGP'], // يمكنك تغيير "EGP" إلى العملة المناسبة
            'wallet.can_issue_prefix' => ['ar' => 'يمكن إصدار', 'en' => 'You can issue'],
            'wallet.can_issue_suffix' => ['ar' => 'وثيقة أخرى', 'en' => 'more document(s)'],
            'wallet.recharge_wallet_title' => ['ar' => 'شحن المحفظة', 'en' => 'Wallet Recharge'],
            'wallet.recharge_wallet_button' => ['ar' => 'شحن المحفظة', 'en' => 'Recharge Wallet'],
            'wallet.payment_method_default_name' => ['ar' => 'طريقة الدفع', 'en' => 'Payment Method'],

            // مفاتيح النافذة المنبثقة (Popup)
            'wallet.confirm_recharge_title' => ['ar' => 'تأكيد شحن المحفظة', 'en' => 'Confirm Wallet Recharge'],
            'wallet.charge_amount_label' => ['ar' => 'قيمة الشحن', 'en' => 'Charge Amount'],
            'wallet.charge_amount_placeholder' => ['ar' => 'أدخل قيمة الشحن', 'en' => 'Enter the charge amount'],
            'wallet.receipt_upload_label' => ['ar' => 'وصل الدفع', 'en' => 'Payment Receipt'],
            'wallet.confirm_charge_button' => ['ar' => 'تأكيد الشحن', 'en' => 'Confirm Charge'],

            // مفاتيح عامة (General)
            'general.copy_button_title' => ['ar' => 'نسخ', 'en' => 'Copy'],
            'general.close_button_title' => ['ar' => 'إغلاق', 'en' => 'Close'],

            // ----------------------------------------------------------------
            // المفاتيح الجديدة لقسم الباقات (Plans/Upgrade)
            // ----------------------------------------------------------------
            'plan.current_plan_label' => ['ar' => 'باقتك الحالية', 'en' => 'Current Plan'],
            'plan.payment_details_title' => ['ar' => 'تفاصيل الدفع', 'en' => 'Payment Details'],
            'plan.attach_receipt_button' => ['ar' => 'إرفاق وصل الدفع', 'en' => 'Attach Payment Receipt'],
            'plan.upgrade_plan_button' => ['ar' => 'ترقية الباقة', 'en' => 'Upgrade Plan'],

            // مفاتيح الباقة المخصصة
            'plan.custom_plan_title' => ['ar' => 'باقة مخصصة', 'en' => 'Custom Plan'],
            'plan.custom_plan_desc_p1' => ['ar' => 'لأن احتياجاتك مختلفة، وفرنا لك إمكانية تصميم باقة تناسب نشاطك بالضبط.', 'en' => 'Because your needs are unique, we offer the option to design a package that perfectly suits your business.'],
            'plan.custom_plan_desc_p2' => ['ar' => 'تواصل معنا وسنساعدك في اختيار الحل الأمثل لك.', 'en' => 'Contact us, and we will help you choose the optimal solution.'],
            'plan.contact_us_label' => ['ar' => 'لطلب باقة مخصصة، اتصل بنا على:', 'en' => 'To request a custom plan, call us on:'],

            // مفتاح العملة (تعديل طفيف لاستخدام مختصر)
            'wallet.currency_unit_short' => ['ar' => 'ج.م', 'en' => 'EGP'],

            // ----------------------------------------------------------------
            // المفاتيح الجديدة لسجلات الاشتراك والشحن (History)
            // ----------------------------------------------------------------
            // العناوين والأزرار
            'history.transactions_log_title' => ['ar' => 'سجل المعاملات', 'en' => 'Subscription Transactions Log'],
            'history.recharge_log_title' => ['ar' => 'سجل عمليات الشحن', 'en' => 'Recharge History Log'],
            'history.sort_latest_first' => ['ar' => 'الأحدث أولاً', 'en' => 'Latest First'],

            // رؤوس الأعمدة
            'history.col_operation_type' => ['ar' => 'نوع العملية', 'en' => 'Operation Type'],
            'history.col_plan_type' => ['ar' => 'نوع الباقة', 'en' => 'Plan Type'],
            'history.col_price' => ['ar' => 'السعر', 'en' => 'Price'],
            'history.col_recharge_amount' => ['ar' => 'قيمة الشحن', 'en' => 'Recharge Amount'], // لجدول الشحن
            'history.col_date' => ['ar' => 'التاريخ', 'en' => 'Date'],
            'history.col_status' => ['ar' => 'الحالة', 'en' => 'Status'],

            // رسائل عدم وجود بيانات
            'history.no_transactions_message' => ['ar' => 'لا يوجد سجل معاملات اشتراك متاحة حتى الآن.', 'en' => 'No subscription transactions log available yet.'],
            'history.no_recharges_message' => ['ar' => 'لا يوجد سجل عمليات شحن متاحة حتى الآن.', 'en' => 'No recharge history log available yet.'],

            // ترجمة حالات العملية (الـ Statuses)
            'history.status_completed' => ['ar' => 'مكتملة', 'en' => 'Completed'],
            'history.status_pending_review' => ['ar' => 'قيد المراجعة', 'en' => 'Pending Review'],
            'history.status_rejected' => ['ar' => 'مرفوضة', 'en' => 'Rejected'],
            'history.status_expired' => ['ar' => 'منتهية', 'en' => 'Expired'],
            'history.status_paused' => ['ar' => 'متوقفة مؤقتاً', 'en' => 'Paused'],
            'history.status_failed' => ['ar' => 'فشل', 'en' => 'Failed'],

            // ترجمة أنواع العملية (الـ Types)
            'history.type_initial' => ['ar' => 'اشتراك جديد', 'en' => 'Initial Subscription'],
            'history.type_upgrade' => ['ar' => 'ترقية باقة', 'en' => 'Plan Upgrade'],
            'history.type_renewal' => ['ar' => 'تجديد اشتراك', 'en' => 'Subscription Renewal'],
            'history.type_expired' => ['ar' => 'اشتراك منتهي', 'en' => 'Subscription Expired'],
            // قد تحتاج لإضافة أنواع أخرى لعمليات الشحن، مثل:
            'history.type_recharge' => ['ar' => 'شحن رصيد', 'en' => 'Recharge'],

            // مفاتيح عامة
            'general.unknown' => ['ar' => 'غير معروف', 'en' => 'Unknown'],
            'the.wallet' => ['ar' => 'المحفظة', 'en' => 'Wallet'],
            'upgrade.plan' => ['ar' => ' ترقية الباقة', 'en' => 'Upgrade Plan'],
            'chose.your.plan' => ['ar' => 'اختر الباقة المناسبة لك', 'en' => 'Choose Your Plan'],

            'footer.about_pepasafe_title' => [
                'ar' => 'عن Pepasafe',
                'en' => 'About Pepasafe',
            ],
            'footer.about_pepasafe_desc' => [
                'ar' => 'منصّة تُصدر تصميماتك بهويتك البصرية مع ختم تحقق رقمي (QR + كود فريد) يثبت الملكية والأصالة عند الفحص، مع تنزيل جاهز للطباعة/المشاركة ومشاركة تشغيلية عبر القنوات المعتمدة عند التفعيل.',
                'en' => 'A platform that issues your designs with your visual identity, featuring a digital verification stamp (QR + unique code) to confirm ownership and authenticity upon inspection. It provides ready-to-print/share downloads and operational sharing across approved channels upon activation.',
            ],
            'footer.quick_links_title' => [
                'ar' => 'روابط سريعة',
                'en' => 'Quick Links',
            ],
            'footer.privacy_policy' => [
                'ar' => 'سياسة الخصوصية',
                'en' => 'Privacy Policy',
            ],
            'footer.terms_and_conditions' => [
                'ar' => 'الشروط والأحكام',
                'en' => 'Terms & Conditions',
            ],
            'footer.contact_us' => [
                'ar' => 'تواصل معنا',
                'en' => 'Contact Us',
            ],
            'footer.about_us' => [
                'ar' => 'من نحن',
                'en' => 'About Us',
            ],
            'footer.contact_title' => [
                'ar' => 'تواصل معنا', // تم تكرار المفتاح بعنوان عمود منفصل
                'en' => 'Contact Info',
            ],
            'footer.follow_us_title' => [
                'ar' => 'تابعنا على',
                'en' => 'Follow Us On',
            ],
            'footer.all_rights_reserved' => [
                'ar' => 'جميع الحقوق محفوظة.',
                'en' => 'All rights reserved.',
            ],
            'footer.developed_by' => [
                'ar' => 'تم تطويره بواسطة',
                'en' => 'Developed by',
            ],

            // ----------------------------------------------------------------
            // المفاتيح الجديدة لروابط التواصل الاجتماعي
            // ----------------------------------------------------------------
            'footer.facebook_link' => [
                'ar' => '#', // استبدل # بالرابط الفعلي
                'en' => '#', // استبدل # بالرابط الفعلي
            ],
            'footer.twitter_link' => [
                'ar' => '#', // استبدل # بالرابط الفعلي
                'en' => '#', // استبدل # بالرابط الفعلي
            ],
            'footer.instagram_link' => [
                'ar' => '#', // استبدل # بالرابط الفعلي
                'en' => '#', // استبدل # بالرابط الفعلي
            ],


            // ----------------------------------------------------------------
// المفاتيح الجديدة لـ "في انتظار الموافقة"
// ----------------------------------------------------------------
            'approval.pending_title' => [
                'ar' => 'في انتظار الموافقة على طلبك',
                'en' => 'Your Request is Pending Approval',
            ],
            'approval.pending_message' => [
                'ar' => 'طلبك قيد المراجعة حاليًا من قبل فريقنا. سنقوم بإبلاغك فور الموافقة عليه. نشكرك على صبرك وتفهمك.',
                'en' => 'Your request is currently under review by our team. We will notify you immediately upon approval. Thank you for your patience and understanding.',
            ],
            'approval.reviewing_request' => [
                'ar' => 'جارٍ مراجعة طلبك...',
                'en' => 'Reviewing your request...',
            ],

// ----------------------------------------------------------------
// مفاتيح التواصل (افتراضية لموديل PhoneNumber)
// ----------------------------------------------------------------
            'contact.call_us_prefix' => [
                'ar' => 'لتواصل معنا، يرجى الاتصال على الأرقام التالية:',
                'en' => 'To contact us, please call the following numbers:',
            ],
            'contact.or' => [
                'ar' => 'أو',
                'en' => 'or',
            ],

            // ----------------------------------------------------------------
// المفاتيح الجديدة لـ "تم رفض الطلب"
// ----------------------------------------------------------------
            'approval.rejected_title' => [
                'ar' => 'عفواً، تم رفض طلبك',
                'en' => 'Sorry, Your Request was Rejected',
            ],
            'approval.rejected_message' => [
                'ar' => 'نعتذر لإبلاغك بأنه تم رفض طلبك بعد مراجعته. يُرجى التواصل معنا للمزيد من التفاصيل حول أسباب الرفض وكيفية إعادة التقديم.',
                'en' => 'We regret to inform you that your request was rejected after review. Please contact us for more details on the reasons for rejection and how to re-apply.',
            ],
            'approval.rejection_status' => [
                'ar' => 'للأسف، لم يتم قبول طلبك.',
                'en' => 'Unfortunately, your request was not accepted.',
            ],
            'contact.inquiry_prefix' => [
                'ar' => 'إذا كان لديك أي استفسار، يرجى التواصل معنا على الأرقام التالية:',
                'en' => 'If you have any questions, please contact us at the following numbers:',
            ],

            // ----------------------------------------------------------------
// المفاتيح الجديدة لقسم "أنشئ حدثك الأول الآن" (Call to Action)
// ----------------------------------------------------------------
            'cta.create_event_title' => [
                'ar' => 'أنشئ حدثك الأول الآن',
                'en' => 'Create Your First Event Now',
            ],
            'cta.create_event_description' => [
                'ar' => 'استعد لمشاركة أفكارك وأهدافك مع جمهورك! في هذا القسم، يمكنك بسهولة تحديد عنوان الحدث وإضافة وصف تفصيلي يغطي كافة التفاصيل، مثل الزمان والمكان والموضوع. بمجرد استكمال البيانات، سيظهر الحدث جاهزًا للنشر والمشاركة مع الجميع في دقائق.',
                'en' => 'Get ready to share your ideas and goals with your audience! In this section, you can easily define the event title and add a detailed description covering all the specifics, such as time, place, and topic. Once the data is complete, the event will be ready to publish and share with everyone in minutes.',
            ],
            'cta.create_event_button' => [
                'ar' => 'إنشاء الحدث الأول الآن',
                'en' => 'Create First Event Now',
            ],

            'search.result_title' => [
                'ar' => 'نتيجة البحث:',
                'en' => 'Search Result:',
            ],
            'document.image_alt' => [
                'ar' => 'صورة الوثيقة',
                'en' => 'Document Image',
            ],
            'document.title_prefix' => [
                'ar' => 'وثيقة',
                'en' => 'Document',
            ],
            'document.issue_date' => [
                'ar' => 'تاريخ الإصدار',
                'en' => 'Issue Date',
            ],
            'document.show_profile' => [
                'ar' => 'إظهار على الملف الشخصي',
                'en' => 'Show on Profile',
            ],
            'document.hide_profile' => [
                'ar' => 'إخفاء من الملف الشخصي',
                'en' => 'Hide from Profile',
            ],

            // ----------------------------------------------------------------
// المفاتيح الجديدة لشريط التنقل (Navigation)
// ----------------------------------------------------------------
            'nav.home' => [
                'ar' => 'الرئيسية',
                'en' => 'Home',
            ],
            'nav.wallet' => [
                'ar' => 'المحفظة',
                'en' => 'Wallet',
            ],
            'nav.about' => [
                'ar' => 'من نحن',
                'en' => 'About Us',
            ],
            'nav.notifications_title' => [
                'ar' => 'الإشعارات',
                'en' => 'Notifications',
            ],
            'nav.mark_all_read' => [
                'ar' => 'وضع علامة "مقروء" على الكل',
                'en' => 'Mark all as read',
            ],
            'nav.no_notifications' => [
                'ar' => 'لا توجد إشعارات',
                'en' => 'No notifications',
            ],
            'nav.notification_from_prefix' => [
                'ar' => 'إشعار من',
                'en' => 'Notification From',
            ],
            'nav.profile' => [
                'ar' => 'الملف الشخصي',
                'en' => 'Profile',
            ],
            'nav.dashboard' => [
                'ar' => 'لوحة التحكم',
                'en' => 'Dashboard',
            ],
            'nav.logout' => [
                'ar' => 'تسجيل الخروج',
                'en' => 'Logout',
            ],
            'nav.login' => [
                'ar' => 'تسجيل الدخول',
                'en' => 'Login',
            ],
            'nav.avatar_alt' => [
                'ar' => 'صورة الملف الشخصي',
                'en' => 'Profile Picture',
            ],
            'nav.language_switch_alert_prefix' => [
                'ar' => 'جاري تبديل اللغة إلى',
                'en' => 'Switching language to',
            ],

            // ----------------------------------------------------------------
// مفاتيح صفحة إدارة/إنشاء الموظفين (ManageEvents/AddEmployee)
// ----------------------------------------------------------------
            'Events.navigation_label' => [
                'ar' => 'إضافة موظف',
                'en' => 'Add Employee',
            ],
            'Events.page_title' => [
                'ar' => 'إضافة موظف جديد',
                'en' => 'Add New Employee',
            ],
            'Events.name_label' => [
                'ar' => 'الاسم',
                'en' => 'Name',
            ],
            'Events.email_label' => [
                'ar' => 'البريد الإلكتروني',
                'en' => 'Email',
            ],
            'Events.password_label' => [
                'ar' => 'كلمة المرور',
                'en' => 'Password',
            ],
            'Events.phone_label' => [
                'ar' => 'رقم الهاتف',
                'en' => 'Phone',
            ],
            'Events.permissions_label' => [
                'ar' => 'الصلاحيات',
                'en' => 'Permissions',
            ],
            'Events.perm_full_access' => [
                'ar' => 'صلاحية كاملة لإدارة الأحداث',
                'en' => 'Full access to events',
            ],
            'Events.perm_search_doc' => [
                'ar' => 'البحث عن مستند',
                'en' => 'Search for a document',
            ],
            'Events.perm_search_qr' => [
                'ar' => 'البحث عن طريق رمز QR',
                'en' => 'Search by QR code',
            ],
            'Events.perm_create_event' => [
                'ar' => 'إنشاء حدث',
                'en' => 'Create an event',
            ],
            'Events.perm_edit_events' => [
                'ar' => 'تعديل الأحداث',
                'en' => 'Edit events',
            ],
            'Events.perm_delete_event' => [
                'ar' => 'حذف حدث',
                'en' => 'Delete event',
            ],
            'Events.max_users_exhausted' => [
                'ar' => 'لقد استنفدت عدد المستخدمين المسموح به لحسابك.',
                'en' => 'You have exhausted the number of allowed users for your account.',
            ],
            'Events.create_user_success' => [
                'ar' => 'تم إنشاء المستخدم بنجاح وخصم مستخدم من رصيد حسابك.',
                'en' => 'User created successfully and one user was deducted from your account balance.',
            ],
            'Events.error_prefix' => [
                'ar' => 'حدث خطأ: ',
                'en' => 'An error occurred: ',
            ],

            // ----------------------------------------------------------------
            // مفاتيح مورد الفئات (CategoryResource)
            // ----------------------------------------------------------------
            'Categories.navigation_label' => [
                'ar' => 'الفئات',
                'en' => 'Categories',
            ],
            'Categories.title' => [
                'ar' => 'إدارة الفئات',
                'en' => 'Manage Categories',
            ],
            'Categories.model_label' => [
                'ar' => 'فئة',
                'en' => 'Category',
            ],
            'Categories.plural_model_label' => [
                'ar' => 'فئات',
                'en' => 'Categories',
            ],
            'Categories.type_label' => [
                'ar' => 'النوع',
                'en' => 'Type',
            ],
            'Categories.icon_label' => [
                'ar' => 'الأيقونة',
                'en' => 'Icon',
            ],
            'Categories.edit_action' => [
                'ar' => 'تعديل',
                'en' => 'Edit',
            ],
            'Categories.delete_action' => [
                'ar' => 'حذف',
                'en' => 'Delete',
            ],
            'Categories.delete_bulk_action' => [
                'ar' => 'حذف المحدد',
                'en' => 'Delete selected',
            ],

            // ----------------------------------------------------------------
            // ترجمات الصلاحيات المستخدمة في CategoryResource::canAccess()
            // ----------------------------------------------------------------
            'permission.show_categories' => [
                'ar' => 'عرض الفئات',
                'en' => 'Show Categories',
            ],
            'permission.add_categories' => [
                'ar' => 'إضافة فئات',
                'en' => 'Add Categories',
            ],

            // ----------------------------------------------------------------
            // مفاتيح مورد طرق الدفع (PaymentMethodResource)
            // ----------------------------------------------------------------
            'PaymentMethods.navigation_label' => [
                'ar' => 'طرق الدفع',
                'en' => 'Payment Methods',
            ],
            'PaymentMethods.model_label' => [
                'ar' => 'طريقة دفع',
                'en' => 'Payment Method',
            ],
            'PaymentMethods.plural_model_label' => [
                'ar' => 'طرق الدفع',
                'en' => 'Payment Methods',
            ],
            'PaymentMethods.key_label' => [
                'ar' => 'اسم طريقة الدفع',
                'en' => 'Name Of Payment Method',
            ],
            'PaymentMethods.value_label' => [
                'ar' => 'قيمة طريقة الدفع',
                'en' => 'Value Of Payment Method',
            ],
            'PaymentMethods.key_placeholder' => [
                'ar' => 'مثل حساب بنكي أو فودافون كاش',
                'en' => 'Like Bank Account Or VodCash',
            ],
            'PaymentMethods.value_placeholder' => [
                'ar' => '123456789123456',
                'en' => '123456789123456',
            ],
            'PaymentMethods.edit_action' => [
                'ar' => 'تعديل',
                'en' => 'Edit',
            ],
            'PaymentMethods.delete_bulk_action' => [
                'ar' => 'حذف المحدد',
                'en' => 'Delete selected',
            ],

            // ----------------------------------------------------------------
            // ترجمات الصلاحيات المستخدمة في PaymentMethodResource::canAccess()
            // ----------------------------------------------------------------
            'permission.show_all_payment_methods' => [
                'ar' => 'عرض جميع طرق الدفع',
                'en' => 'Show all payment methods',
            ],
            'permission.create_new_payment_method' => [
                'ar' => 'إنشاء طريقة دفع جديدة',
                'en' => 'Create new payment method',
            ],
            'permission.edit_all_payment_methods' => [
                'ar' => 'تعديل جميع طرق الدفع',
                'en' => 'Edit all payment methods',
            ],
            'permission.delete_payment_method' => [
                'ar' => 'حذف طريقة الدفع',
                'en' => 'Delete payment method',
            ],

            // ----------------------------------------------------------------
            // ترجمات الصلاحيات المستخدمة في PaymentMethodResource::canAccess()
            // ----------------------------------------------------------------
            'permission.show_all_payment_methods' => [
                'ar' => 'عرض جميع طرق الدفع',
                'en' => 'Show all payment methods',
            ],
            'permission.create_new_payment_method' => [
                'ar' => 'إنشاء طريقة دفع جديدة',
                'en' => 'Create new payment method',
            ],
            'permission.edit_all_payment_methods' => [
                'ar' => 'تعديل جميع طرق الدفع',
                'en' => 'Edit all payment methods',
            ],
            'permission.delete_payment_method' => [
                'ar' => 'حذف طريقة الدفع',
                'en' => 'Delete payment method',
            ],

            // ----------------------------------------------------------------
            // مفاتيح صفحة إعدادات API (ApiConfig)
            // ----------------------------------------------------------------
            'ApiConfig.navigation_label' => [
                'ar' => 'إعدادات API',
                'en' => 'API Configuration',
            ],
            'ApiConfig.page_title' => [
                'ar' => 'إعدادات واجهات البرامج',
                'en' => 'API Configuration',
            ],
            'ApiConfig.navigation_group' => [
                'ar' => 'الإعدادات',
                'en' => 'Settings',
            ],
            'ApiConfig.tabs_title' => [
                'ar' => 'تكوينات واجهات البرامج',
                'en' => 'API Configurations',
            ],
            'ApiConfig.tab_whatsapp' => [
                'ar' => 'واتساب',
                'en' => 'WhatsApp',
            ],
            'ApiConfig.tab_sms' => [
                'ar' => 'رسائل SMS',
                'en' => 'SMS',
            ],
            'ApiConfig.tab_email' => [
                'ar' => 'خدمات البريد الإلكتروني',
                'en' => 'Email Services',
            ],

            // WhatsApp Settings
            'ApiConfig.whatsapp_service_label' => [
                'ar' => 'مزود خدمة واتساب',
                'en' => 'WhatsApp Service Provider',
            ],
            'ApiConfig.authkey_instance_id_label' => [
                'ar' => 'معرّف مثيل Authkey',
                'en' => 'Authkey Instance ID',
            ],
            'ApiConfig.authkey_token_label' => [
                'ar' => 'رمز Authkey (Token)',
                'en' => 'Authkey Token',
            ],
            'ApiConfig.twilio_sid_label' => [
                'ar' => 'Twilio SID',
                'en' => 'Twilio SID',
            ],
            'ApiConfig.twilio_token_label' => [
                'ar' => 'Twilio Auth Token',
                'en' => 'Twilio Auth Token',
            ],
            'ApiConfig.twilio_whatsapp_phone_number_label' => [
                'ar' => 'رقم هاتف Twilio للواتساب',
                'en' => 'Twilio WhatsApp Phone Number',
            ],
            'ApiConfig.beonchat_whatsapp_api_key_label' => [
                'ar' => 'Beon.chat API Key',
                'en' => 'Beon.chat API Key',
            ],
            'ApiConfig.beonchat_whatsapp_sender_id_label' => [
                'ar' => 'Beon.chat Sender ID',
                'en' => 'Beon.chat Sender ID',
            ],

            // SMS Settings
            'ApiConfig.sms_service_label' => [
                'ar' => 'مزود خدمة رسائل SMS',
                'en' => 'SMS Service Provider',
            ],
            'ApiConfig.beonchat_sms_api_key_label' => [
                'ar' => 'Beon.chat API Key للرسائل',
                'en' => 'Beon.chat SMS API Key',
            ],
            'ApiConfig.beonchat_sms_sender_id_label' => [
                'ar' => 'Beon.chat Sender ID للرسائل',
                'en' => 'Beon.chat SMS Sender ID',
            ],
            'ApiConfig.softex_api_key_label' => [
                'ar' => 'Softex API Key',
                'en' => 'Softex API Key',
            ],
            'ApiConfig.softex_api_secret_label' => [
                'ar' => 'Softex API Secret',
                'en' => 'Softex API Secret',
            ],
            'ApiConfig.softex_sender_id_label' => [
                'ar' => 'Softex Sender ID',
                'en' => 'Softex Sender ID',
            ],
            'ApiConfig.twilio_sms_phone_number_label' => [
                'ar' => 'رقم هاتف Twilio للرسائل',
                'en' => 'Twilio SMS Phone Number',
            ],

            // Email Settings
            'ApiConfig.mail_service_label' => [
                'ar' => 'مزود خدمة البريد الإلكتروني',
                'en' => 'Email Service Provider',
            ],
            'ApiConfig.zoho_api_key_label' => [
                'ar' => 'Zoho API Key',
                'en' => 'Zoho API Key',
            ],
            'ApiConfig.brevo_api_key_label' => [
                'ar' => 'Brevo API Key',
                'en' => 'Brevo API Key',
            ],
            'ApiConfig.sendpulse_api_id_label' => [
                'ar' => 'SendPulse API ID',
                'en' => 'SendPulse API ID',
            ],
            'ApiConfig.sendpulse_api_secret_label' => [
                'ar' => 'SendPulse API Secret',
                'en' => 'SendPulse API Secret',
            ],
            'ApiConfig.from_email_address_label' => [
                'ar' => 'عنوان البريد الإلكتروني للمرسل',
                'en' => 'From Email Address',
            ],
            'ApiConfig.from_name_label' => [
                'ar' => 'اسم المرسل',
                'en' => 'From Name',
            ],
            // SMTP Fields
            'ApiConfig.mail_driver_label' => [
                'ar' => 'برنامج تشغيل البريد',
                'en' => 'Mail Driver',
            ],
            'ApiConfig.mail_driver_placeholder' => [
                'ar' => 'مثال: smtp',
                'en' => 'e.g. smtp',
            ],
            'ApiConfig.smtp_host_label' => [
                'ar' => 'مُضيف SMTP',
                'en' => 'SMTP Host',
            ],
            'ApiConfig.smtp_host_placeholder' => [
                'ar' => 'مثال: smtp.gmail.com',
                'en' => 'e.g. smtp.gmail.com',
            ],
            'ApiConfig.smtp_port_label' => [
                'ar' => 'منفذ SMTP',
                'en' => 'SMTP Port',
            ],
            'ApiConfig.smtp_port_placeholder' => [
                'ar' => 'مثال: 587',
                'en' => 'e.g. 587',
            ],
            'ApiConfig.smtp_username_label' => [
                'ar' => 'اسم مستخدم SMTP',
                'en' => 'SMTP Username',
            ],
            'ApiConfig.smtp_username_placeholder' => [
                'ar' => 'مثال: your-email@gmail.com',
                'en' => 'e.g. your-email@gmail.com',
            ],
            'ApiConfig.smtp_password_label' => [
                'ar' => 'كلمة مرور SMTP',
                'en' => 'SMTP Password',
            ],
            'ApiConfig.smtp_password_placeholder' => [
                'ar' => 'كلمة مرور التطبيق',
                'en' => 'App password',
            ],
            'ApiConfig.encryption_label' => [
                'ar' => 'التشفير',
                'en' => 'Encryption',
            ],
            'ApiConfig.encryption_none' => [
                'ar' => 'لا شيء',
                'en' => 'None',
            ],

            // Notifications
            'ApiConfig.success_title' => [
                'ar' => 'تم الحفظ بنجاح!',
                'en' => 'Success!',
            ],
            'ApiConfig.success_body' => [
                'ar' => 'تم حفظ إعدادات واجهات البرامج (API) بنجاح.',
                'en' => 'API configurations have been saved successfully.',
            ],
            'ApiConfig.error_title' => [
                'ar' => 'خطأ في الحفظ',
                'en' => 'Error saving settings',
            ],
            'ApiConfig.error_body' => [
                'ar' => 'حدث خطأ أثناء محاولة حفظ الإعدادات: ',
                'en' => 'An error occurred while trying to save the settings: ',
            ],
            'app.available_users_count' => [
                'ar' => 'عدد المستخدمين المتاحين',
                'en' => 'Available Users Count',
            ],


                // ----------------------------------------------------------------
                // المفاتيح الجديدة لصفحة GeneralSettings
                // ----------------------------------------------------------------
                'settings.system_settings_title' => [
                    'ar' => 'إعدادات النظام',
                    'en' => 'System Settings',
                ],
                'settings.navigation_group' => [
                    'ar' => 'الإعدادات',
                    'en' => 'Settings',
                ],
                'settings.general_settings_section' => [
                    'ar' => 'الإعدادات العامة',
                    'en' => 'General Settings',
                ],
                'settings.profile_page_enabled' => [
                    'ar' => 'تفعيل صفحة الملف الشخصي العامة',
                    'en' => 'Enable Public Profile Page',
                ],
                'settings.show_bio_public' => [
                    'ar' => 'عرض السيرة الذاتية علناً',
                    'en' => 'Show Bio Publicly',
                ],
                'settings.show_documents_in_profile' => [
                    'ar' => 'عرض المستندات في الملف الشخصي',
                    'en' => 'Show Documents in Profile',
                ],
                'settings.show_events_in_profile' => [
                    'ar' => 'عرض الأحداث في الملف الشخصي',
                    'en' => 'Show Events in Profile',
                ],
                'settings.user_can_share_here_profile' => [
                    'ar' => 'السماح للمستخدم بمشاركة هذا الملف',
                    'en' => 'User Can Share Here Profile',
                ],
                'settings.email_otp_active' => [
                    'ar' => 'تفعيل التحقق عبر البريد الإلكتروني (OTP)',
                    'en' => 'Enable Email OTP Verification',
                ],
                'settings.sms_otp_active' => [
                    'ar' => 'تفعيل التحقق عبر الرسائل النصية (OTP)',
                    'en' => 'Enable SMS OTP Verification',
                ],
                'settings.update_success_title' => [
                    'ar' => 'تم تحديث الإعداد بنجاح',
                    'en' => 'Setting updated successfully',
                ],


            // ----------------------------------------------------------------
            // المفاتيح الجديدة لصفحة LogoSettings
            // ----------------------------------------------------------------
            'logo_settings.page_title' => [
                'ar' => 'إعدادات الشعار والعلامة التجارية',
                'en' => 'Logo & Branding Settings',
            ],
            'logo_settings.navigation_group' => [
                'ar' => 'محتوى الموقع',
                'en' => 'Site Content',
            ],
            'logo_settings.branding_section' => [
                'ar' => 'العلامة التجارية',
                'en' => 'Branding',
            ],
            'logo_settings.site_name_label' => [
                'ar' => 'اسم الموقع',
                'en' => 'Site Name',
            ],
            'logo_settings.site_logo_label' => [
                'ar' => 'شعار الموقع',
                'en' => 'Site Logo',
            ],
            'logo_settings.delete_logo_action' => [
                'ar' => 'حذف الشعار',
                'en' => 'Delete Logo',
            ],
            'logo_settings.logo_delete_success' => [
                'ar' => 'تم حذف الشعار بنجاح!',
                'en' => 'Logo deleted successfully!',
            ],
            'logo_settings.update_success' => [
                'ar' => 'تم تحديث إعدادات العلامة التجارية بنجاح!',
                'en' => 'Branding settings updated successfully!',
            ],

            // ----------------------------------------------------------------
            // المفاتيح الجديدة لصفحة AboutUsResource (من نحن)
            // ----------------------------------------------------------------
            'about_us.navigation_label' => [
                'ar' => 'من نحن',
                'en' => 'About Us',
            ],
            'about_us.model_label' => [
                'ar' => 'صفحة من نحن',
                'en' => 'About Us Page',
            ],
            'about_us.plural_model_label' => [
                'ar' => 'صفحات من نحن',
                'en' => 'About Us Pages',
            ],
            'about_us.description_label' => [
                'ar' => 'الوصف/المحتوى',
                'en' => 'Description/Content',
            ],

            // ----------------------------------------------------------------
            // المفاتيح العامة (Shared Keys)
            // ----------------------------------------------------------------
            'site_content.navigation_group' => [
                'ar' => 'محتوى الموقع',
                'en' => 'Site Content',
            ],

            // ----------------------------------------------------------------
            // المفاتيح الجديدة لـ FaqResource (الأسئلة الشائعة)
            // ----------------------------------------------------------------
            'faq.navigation_label' => [
                'ar' => 'الأسئلة الشائعة',
                'en' => 'FAQs',
            ],
            'faq.model_label' => [
                'ar' => 'سؤال شائع',
                'en' => 'FAQ Item',
            ],
            'faq.plural_model_label' => [
                'ar' => 'الأسئلة الشائعة',
                'en' => 'FAQs',
            ],
            'faq.question_label' => [
                'ar' => 'السؤال',
                'en' => 'Question',
            ],
            'faq.answer_label' => [
                'ar' => 'الإجابة',
                'en' => 'Answer',
            ],

            // ----------------------------------------------------------------
            // المفاتيح الجديدة لـ OfficialEmailResource (البريد الرسمي)
            // ----------------------------------------------------------------
            'official_email.navigation_label' => [
                'ar' => 'البريد الرسمي',
                'en' => 'Official Emails',
            ],
            'official_email.model_label' => [
                'ar' => 'بريد رسمي',
                'en' => 'Official Email',
            ],
            'official_email.plural_model_label' => [
                'ar' => 'قائمة البريد الرسمي',
                'en' => 'Official Emails List',
            ],
            'official_email.email_label' => [
                'ar' => 'البريد الإلكتروني الرسمي',
                'en' => 'Official Email Address',
            ],

            // ----------------------------------------------------------------
            // مفاتيح الصلاحيات الجديدة
            // ----------------------------------------------------------------
            'permission.show_about_us' => [
                'ar' => 'عرض صفحة من نحن',
                'en' => 'Show About Us Page',
            ],
            'permission.add_about_us' => [
                'ar' => 'إضافة/تعديل صفحة من نحن',
                'en' => 'Add/Edit About Us Page',
            ],
            'permission.show_faq' => [
                'ar' => 'عرض الأسئلة الشائعة',
                'en' => 'Show FAQs',
            ],
            'permission.add_faq' => [
                'ar' => 'إضافة/تعديل الأسئلة الشائعة',
                'en' => 'Add/Edit FAQs',
            ],
            'permission.show_official_emails' => [
                'ar' => 'عرض البريد الرسمي',
                'en' => 'Show Official Emails',
            ],
            'permission.add_official_emails' => [
                'ar' => 'إضافة/تعديل البريد الرسمي',
                'en' => 'Add/Edit Official Emails',
            ],
            'permission.show_phone_numbers' => [
                'ar' => 'عرض أرقام الهواتف',
                'en' => 'Show Phone Numbers',
            ],
            'permission.add_phone_numbers' => [
                'ar' => 'إضافة/تعديل أرقام الهواتف',
                'en' => 'Add/Edit Phone Numbers',
            ],

            // ----------------------------------------------------------------
            // المفاتيح الجديدة لـ PrivacyPolicyResource (سياسة الخصوصية)
            // ----------------------------------------------------------------
            'privacy_policy.navigation_label' => [
                'ar' => 'سياسة الخصوصية',
                'en' => 'Privacy Policy',
            ],
            'privacy_policy.model_label' => [
                'ar' => 'سياسة الخصوصية',
                'en' => 'Privacy Policy Item',
            ],
            'privacy_policy.plural_model_label' => [
                'ar' => 'سياسات الخصوصية',
                'en' => 'Privacy Policies',
            ],
            'privacy_policy.content_label' => [
                'ar' => 'محتوى سياسة الخصوصية',
                'en' => 'Privacy Policy Content',
            ],
            'privacy_policy.content_label_short' => [
                'ar' => 'المحتوى',
                'en' => 'Content',
            ],

            // ----------------------------------------------------------------
            // المفاتيح الجديدة لـ TermsAndConditionResource (الشروط والأحكام)
            // ----------------------------------------------------------------
            'terms_and_conditions.navigation_label' => [
                'ar' => 'الشروط والأحكام',
                'en' => 'Terms & Conditions',
            ],
            'terms_and_conditions.model_label' => [
                'ar' => 'الشروط والأحكام',
                'en' => 'Terms & Condition Item',
            ],
            'terms_and_conditions.plural_model_label' => [
                'ar' => 'قائمة الشروط والأحكام',
                'en' => 'Terms & Conditions List',
            ],
            'terms_and_conditions.content_label' => [
                'ar' => 'محتوى الشروط والأحكام',
                'en' => 'Terms & Conditions Content',
            ],
            'terms_and_conditions.content_label_short' => [
                'ar' => 'المحتوى',
                'en' => 'Content',
            ],

            // ----------------------------------------------------------------
            // المفاتيح الجديدة لـ PhoneNumberResource (أرقام الهواتف)
            // ----------------------------------------------------------------
            'phone_number.navigation_label' => [
                'ar' => 'أرقام الهواتف',
                'en' => 'Phone Numbers',
            ],
            'phone_number.model_label' => [
                'ar' => 'رقم هاتف',
                'en' => 'Phone Number',
            ],
            'phone_number.plural_model_label' => [
                'ar' => 'قائمة أرقام الهواتف',
                'en' => 'Phone Numbers List',
            ],
            'phone_number.phone_label' => [
                'ar' => 'رقم الهاتف الخاص بالموقع',
                'en' => 'Website Phone Number',
            ],


            // ----------------------------------------------------------------
            // المفاتيح الجديدة لصفحة SendMessage (الإشعارات)
            // ----------------------------------------------------------------
            'notifications.navigation_group' => [
                'ar' => 'الإشعارات والمراسلات',
                'en' => 'Notifications & Messaging',
            ],
            'send_message.navigation_label' => [
                'ar' => 'إرسال إشعار مُجدوَل',
                'en' => 'Schedule Notification',
            ],
            'send_message.page_title' => [
                'ar' => 'إرسال إشعار مُجدوَل',
                'en' => 'Schedule Notification',
            ],
            'send_message.send_to_all_label' => [
                'ar' => 'إرسال إلى جميع المستخدمين',
                'en' => 'Send to all users',
            ],
            'send_message.schedule_dates_label' => [
                'ar' => 'تحديد تواريخ الإرسال',
                'en' => 'Schedule Dates & Times',
            ],
            'send_message.date_time_label' => [
                'ar' => 'التاريخ والوقت',
                'en' => 'Date & Time',
            ],
            'send_message.date_time_placeholder' => [
                'ar' => 'اختر تاريخ ووقت الإرسال',
                'en' => 'Choose a date and time',
            ],
            'send_message.add_date_button' => [
                'ar' => 'أضف تاريخ آخر',
                'en' => 'Add another date',
            ],
            'send_message.select_users_label' => [
                'ar' => 'اختر المستخدمين',
                'en' => 'Select users',
            ],
            'send_message.select_channel_label' => [
                'ar' => 'اختر قناة الإرسال',
                'en' => 'Select channel',
            ],
            'send_message.channel_whatsapp' => [
                'ar' => 'واتساب',
                'en' => 'WhatsApp',
            ],
            'send_message.channel_email' => [
                'ar' => 'بريد إلكتروني',
                'en' => 'Email',
            ],
            'send_message.channel_sms' => [
                'ar' => 'رسالة نصية (SMS)',
                'en' => 'SMS',
            ],
            'send_message.channel_database' => [
                'ar' => 'إشعار داخل النظام',
                'en' => 'Notification in system',
            ],
            'send_message.translations_tab_title' => [
                'ar' => 'محتوى الرسالة',
                'en' => 'Message Content',
            ],
            'send_message.tab_arabic' => [
                'ar' => 'العربية',
                'en' => 'Arabic',
            ],
            'send_message.tab_english' => [
                'ar' => 'الإنجليزية',
                'en' => 'English',
            ],
            'send_message.subject_ar_label' => [
                'ar' => 'موضوع البريد (بالعربية)',
                'en' => 'Email Subject (Arabic)',
            ],
            'send_message.subject_ar_placeholder' => [
                'ar' => 'ادخل موضوع الرسالة بالعربية',
                'en' => 'Enter the subject of the message in Arabic',
            ],
            'send_message.message_ar_label' => [
                'ar' => 'محتوى الإشعار (بالعربية)',
                'en' => 'Notification Message (Arabic)',
            ],
            'send_message.message_ar_placeholder' => [
                'ar' => 'ادخل محتوى الرسالة بالعربية',
                'en' => 'Enter the message content in Arabic',
            ],
            'send_message.subject_en_label' => [
                'ar' => 'موضوع البريد (بالإنجليزية)',
                'en' => 'Email Subject (English)',
            ],
            'send_message.subject_en_placeholder' => [
                'ar' => 'Enter the subject of the message in English',
                'en' => 'Enter the subject of the message in English',
            ],
            'send_message.message_en_label' => [
                'ar' => 'محتوى الإشعار (بالإنجليزية)',
                'en' => 'Notification Message (English)',
            ],
            'send_message.message_en_placeholder' => [
                'ar' => 'ادخل محتوى الرسالة بالإنجليزية',
                'en' => 'Enter the message content in English',
            ],
            // Notifications
            'send_message.success_title' => [
                'ar' => 'تم الجدولة بنجاح!',
                'en' => 'Scheduled successfully!',
            ],
            'send_message.success_body' => [
                'ar' => 'تمت جدولة رسالتك للإرسال في المواعيد المحددة.',
                'en' => 'Your message has been scheduled for sending.',
            ],
            'send_message.validation_message_required_title' => [
                'ar' => 'الرسالة مطلوبة',
                'en' => 'Message is required',
            ],
            'send_message.validation_message_required_body' => [
                'ar' => 'الرجاء إدخال محتوى الرسالة في لغة واحدة على الأقل (العربية أو الإنجليزية).',
                'en' => 'Please enter the message in at least one language (Arabic or English).',
            ],
            'send_message.validation_subject_required_title' => [
                'ar' => 'الموضوع مطلوب',
                'en' => 'Subject is required',
            ],
            'send_message.validation_subject_required_body' => [
                'ar' => 'الرجاء إدخال موضوع للبريد الإلكتروني في لغة واحدة على الأقل.',
                'en' => 'Please enter a subject for the email in at least one language.',
            ],
            'send_message.validation_users_required_title' => [
                'ar' => 'لم يتم اختيار مستخدمين',
                'en' => 'No users selected',
            ],
            'send_message.validation_users_required_body' => [
                'ar' => 'الرجاء اختيار مستخدم واحد على الأقل أو تفعيل خيار "إرسال إلى جميع المستخدمين".',
                'en' => 'Please select at least one user or enable "Send to all".',
            ],


            // ----------------------------------------------------------------
            // مفاتيح مورد ScheduledNotificationResource
            // ----------------------------------------------------------------
            'scheduled_notification.navigation_label' => [
                'ar' => 'جدول الإشعارات',
                'en' => 'Scheduled Notifications',
            ],
            'scheduled_notification.model_label' => [
                'ar' => 'إشعار مُجدوَل',
                'en' => 'Scheduled Notification',
            ],
            'scheduled_notification.plural_model_label' => [
                'ar' => 'الإشعارات المُجدوَلة',
                'en' => 'Scheduled Notifications',
            ],
            'scheduled_notification.channel_label' => [
                'ar' => 'القناة',
                'en' => 'Channel',
            ],
            'scheduled_notification.subject_label' => [
                'ar' => 'الموضوع',
                'en' => 'Subject',
            ],
            'scheduled_notification.send_to_all_label' => [
                'ar' => 'للجميع',
                'en' => 'Send to All',
            ],
            'scheduled_notification.scheduled_at_label' => [
                'ar' => 'تاريخ الإرسال المُجدوَل',
                'en' => 'Scheduled At',
            ],
            'scheduled_notification.status_label' => [
                'ar' => 'الحالة',
                'en' => 'Status',
            ],
            'scheduled_notification.created_at_label' => [
                'ar' => 'تاريخ الإنشاء',
                'en' => 'Created At',
            ],
            'scheduled_notification.updated_at_label' => [
                'ar' => 'آخر تحديث',
                'en' => 'Updated At',
            ],
            'scheduled_notification.delete_success_title' => [
                'ar' => 'تم الحذف بنجاح',
                'en' => 'Deleted successfully',
            ],
            'scheduled_notification.delete_success_body' => [
                'ar' => 'تم حذف الإشعار المُجدوَل.',
                'en' => 'The scheduled notification has been deleted.',
            ],
            'scheduled_notification.delete_success_body_bulk' => [
                'ar' => 'تم حذف الإشعارات المُجدوَلة المختارة (المُعلّقة).',
                'en' => 'The selected pending scheduled notifications have been deleted.',
            ],
            'scheduled_notification.delete_failed_title' => [
                'ar' => 'فشل الحذف',
                'en' => 'Deletion Failed',
            ],
            'scheduled_notification.delete_failed_body' => [
                'ar' => 'لا يمكن حذف هذا الإشعار، قد يكون تم إرساله بالفعل.',
                'en' => 'Cannot delete this notification, it may have already been sent.',
            ],
            'scheduled_notification.delete_bulk_action_label' => [
                'ar' => 'حذف الإشعارات المُختارة',
                'en' => 'Delete Selected',
            ],
            'scheduled_notification.bulk_delete_partial_title' => [
                'ar' => 'حذف جزئي',
                'en' => 'Partial Deletion',
            ],
            'scheduled_notification.bulk_delete_partial_body' => [
                'ar' => 'تم حذف :deleted_count إشعار مُعلق فقط من أصل :total_count. لا يمكن حذف الإشعارات التي تم إرسالها.',
                'en' => 'Only :deleted_count pending notifications out of :total_count were deleted. Sent notifications cannot be deleted.',
            ],
            'scheduled_notification.bulk_delete_failed_title' => [
                'ar' => 'لا يوجد إشعارات للحذف',
                'en' => 'No Notifications to Delete',
            ],
            'scheduled_notification.bulk_delete_failed_body' => [
                'ar' => 'لا يمكن حذف أي إشعارات مُرسلة أو فاشلة. يمكنك فقط حذف الإشعارات المُعلّقة.',
                'en' => 'No sent or failed notifications can be deleted. You can only delete pending ones.',
            ],


            // Navigation & Model Labels
            'custom_plans.navigation_label' => [
                'ar' => 'الخطط المخصصة',
                'en' => 'Custom Plans',
            ],
            'custom_plans.model_label' => [
                'ar' => 'خطة مخصصة',
                'en' => 'Custom Plan',
            ],
            'custom_plans.plural_model_label' => [
                'ar' => 'الخطط المخصصة',
                'en' => 'Custom Plans',
            ],
            'custom_plans.user' => [
                'ar' => 'المستخدم',
                'en' => 'User',
            ],
            'custom_plans.is_public_label' => [
                'ar' => 'خطة خاصة مخصصة؟',
                'en' => 'Custom Private Plan?',
            ],

            // Form Fields
            'custom_plans.plan_name' => [
                'ar' => 'اسم الخطة',
                'en' => 'Plan Name',
            ],
            'custom_plans.price_after_discount' => [
                'ar' => 'السعر بعد الخصم',
                'en' => 'Price After Discount',
            ],
            'custom_plans.price_before_discount' => [
                'ar' => 'السعر قبل الخصم',
                'en' => 'Price Before Discount',
            ],
            'custom_plans.credit_amount' => [
                'ar' => 'قيمة الرصيد',
                'en' => 'Credit Amount',
            ],
            'custom_plans.duration_days' => [
                'ar' => 'مدة الخطة بالأيام',
                'en' => 'Duration Days',
            ],
            'custom_plans.duration_days_placeholder' => [
                'ar' => 'مثال: 30 يوماً',
                'en' => 'e.g., 30 days',
            ],
            'custom_plans.maximum_users' => [
                'ar' => 'الحد الأقصى للمستخدمين',
                'en' => 'Maximum Users',
            ],
            'custom_plans.features' => [
                'ar' => 'الميزات',
                'en' => 'Features',
            ],

            // Price Fields
            'custom_plans.document_price_in_plan' => [
                'ar' => 'سعر الوثيقة (ضمن الخطة)',
                'en' => 'In-Plan Document Price (Within Plan)',
            ],
            'custom_plans.document_price_outside_plan' => [
                'ar' => 'سعر الوثيقة (الدفع عند الاستخدام)',
                'en' => 'Pay-As-You-Go Document Price (Outside Plan)',
            ],
            'custom_plans.sms_price_outside_plan' => [
                'ar' => 'سعر رسالة SMS (الدفع عند الاستخدام)',
                'en' => 'Pay-As-You-Go SMS Message Price (Outside Plan)',
            ],
            'custom_plans.sms_price_in_plan' => [
                'ar' => 'سعر رسالة SMS (ضمن الخطة)',
                'en' => 'In-Plan SMS Message Price (Within Plan)',
            ],
            'custom_plans.whatsapp_price_outside_plan' => [
                'ar' => 'سعر رسالة واتساب (الدفع عند الاستخدام)',
                'en' => 'Pay-As-You-Go WhatsApp Message Price (Outside Plan)',
            ],
            'custom_plans.whatsapp_price_in_plan' => [
                'ar' => 'سعر رسالة واتساب (ضمن الخطة)',
                'en' => 'In-Plan WhatsApp Message Price (Within Plan)',
            ],
            'custom_plans.email_price_outside_plan' => [
                'ar' => 'سعر رسالة الإيميل (الدفع عند الاستخدام)',
                'en' => 'Pay-As-You-Go Email Message Price (Outside Plan)',
            ],
            'custom_plans.email_price_in_plan' => [
                'ar' => 'سعر رسالة الإيميل (ضمن الخطة)',
                'en' => 'In-Plan Email Message Price (Within Plan)',
            ],

            // Checkbox Options (Long Form)
            'custom_plans.carry_over_credit' => [
                'ar' => 'السماح بترحيل الرصيد المتبقي عند التجديد ؟',
                'en' => 'Allow remaining credit to be carried over upon renewal?',
            ],
            'custom_plans.enable_attendance' => [
                'ar' => 'تفعيل الحضور في هذه الباقة ؟',
                'en' => 'Enable attendance tracking in this package?',
            ],
            'custom_plans.enable_multiple_templates' => [
                'ar' => 'تفعيل استخدام أكثر من نموذج في هذه الباقة ؟',
                'en' => 'Enable usage of multiple templates in this package?',
            ],

            // Channel Selection
            'custom_plans.enabled_channels_documents' => [
                'ar' => 'قنوات إرسال الوثائق',
                'en' => 'Document Sending Channels',
            ],
            'custom_plans.enabled_channels_attendance' => [
                'ar' => 'قنوات إرسال الحضور',
                'en' => 'Attendance Sending Channels',
            ],
            'custom_plans.channel_email' => [
                'ar' => 'البريد الإلكتروني',
                'en' => 'Email',
            ],
            'custom_plans.channel_sms' => [
                'ar' => 'رسائل SMS',
                'en' => 'SMS',
            ],
            'custom_plans.channel_whatsapp' => [
                'ar' => 'واتساب',
                'en' => 'WhatsApp',
            ],

            // Table Headers (Short Form)
            'custom_plans.price' => [
                'ar' => 'السعر',
                'en' => 'Price',
            ],
            'custom_plans.compare_price' => [
                'ar' => 'سعر قبل الخصم',
                'en' => 'Compare Price',
            ],
            'custom_plans.carry_over_credit_short' => [
                'ar' => 'ترحيل رصيد',
                'en' => 'Credit Carryover',
            ],
            'custom_plans.enable_attendance_short' => [
                'ar' => 'حضور',
                'en' => 'Attendance',
            ],
            'custom_plans.multiple_templates_short' => [
                'ar' => 'قوالب متعددة',
                'en' => 'Multiple Templates',
            ],
            'custom_plans.is_public_short' => [
                'ar' => 'عام/خاص',
                'en' => 'Public/Private',
            ],
            'custom_plans.doc_price_in' => [
                'ar' => 'وثيقة (بالخطة)',
                'en' => 'Doc Price (In)',
            ],
            'custom_plans.doc_price_out' => [
                'ar' => 'وثيقة (خارج الخطة)',
                'en' => 'Doc Price (Out)',
            ],
            'custom_plans.sms_price_in' => [
                'ar' => 'SMS (بالخطة)',
                'en' => 'SMS Price (In)',
            ],
            'custom_plans.sms_price_out' => [
                'ar' => 'SMS (خارج الخطة)',
                'en' => 'SMS Price (Out)',
            ],
            'custom_plans.whatsapp_price_in' => [
                'ar' => 'واتساب (بالخطة)',
                'en' => 'WhatsApp Price (In)',
            ],
            'custom_plans.whatsapp_price_out' => [
                'ar' => 'واتساب (خارج الخطة)',
                'en' => 'WhatsApp Price (Out)',
            ],
            'custom_plans.email_price_in' => [
                'ar' => 'إيميل (بالخطة)',
                'en' => 'Email Price (In)',
            ],
            'custom_plans.email_price_out' => [
                'ar' => 'إيميل (خارج الخطة)',
                'en' => 'Email Price (Out)',
            ],


            // Navigation & Model Labels
            'plans.navigation_label' => [
                'ar' => 'الباقات والخطط',
                'en' => 'Packages and Plans',
            ],
            'plans.model_label' => [
                'ar' => 'باقة/خطة',
                'en' => 'Package/Plan',
            ],
            'plans.plural_model_label' => [
                'ar' => 'الباقات والخطط',
                'en' => 'Packages and Plans',
            ],
            'plans.plan_name' => [
                'ar' => 'اسم الباقة',
                'en' => 'Plan Name',
            ],
            'plans.price_after_discount' => [
                'ar' => 'السعر بعد الخصم',
                'en' => 'Price After Discount',
            ],
            'plans.price_before_discount' => [
                'ar' => 'السعر قبل الخصم',
                'en' => 'Price Before Discount',
            ],
            'plans.credit_amount' => [
                'ar' => 'قيمة الرصيد',
                'en' => 'Credit Amount',
            ],
            'plans.duration_days' => [
                'ar' => 'مدة الخطة بالأيام',
                'en' => 'Duration Days',
            ],
            'plans.duration_days_placeholder' => [
                'ar' => 'مثال: 30 يوماً',
                'en' => 'e.g., 30 days',
            ],
            'plans.maximum_users' => [
                'ar' => 'الحد الأقصى للمستخدمين',
                'en' => 'Maximum Users',
            ],
            'plans.features' => [
                'ar' => 'الميزات',
                'en' => 'Features',
            ],

            // Price Fields
            'plans.document_price_in_plan' => [
                'ar' => 'سعر الوثيقة (ضمن الخطة)',
                'en' => 'In-Plan Document Price (Within Plan)',
            ],
            'plans.document_price_outside_plan' => [
                'ar' => 'سعر الوثيقة (الدفع عند الاستخدام)',
                'en' => 'Pay-As-You-Go Document Price (Outside Plan)',
            ],
            'plans.sms_price_outside_plan' => [
                'ar' => 'سعر رسالة SMS (الدفع عند الاستخدام)',
                'en' => 'Pay-As-You-Go SMS Message Price (Outside Plan)',
            ],
            'plans.sms_price_in_plan' => [
                'ar' => 'سعر رسالة SMS (ضمن الخطة)',
                'en' => 'In-Plan SMS Message Price (Within Plan)',
            ],
            'plans.whatsapp_price_outside_plan' => [
                'ar' => 'سعر رسالة واتساب (الدفع عند الاستخدام)',
                'en' => 'Pay-As-You-Go WhatsApp Message Price (Outside Plan)',
            ],
            'plans.whatsapp_price_in_plan' => [
                'ar' => 'سعر رسالة واتساب (ضمن الخطة)',
                'en' => 'In-Plan WhatsApp Message Price (Within Plan)',
            ],
            'plans.email_price_outside_plan' => [
                'ar' => 'سعر رسالة الإيميل (الدفع عند الاستخدام)',
                'en' => 'Pay-As-You-Go Email Message Price (Outside Plan)',
            ],
            'plans.email_price_in_plan' => [
                'ar' => 'سعر رسالة الإيميل (ضمن الخطة)',
                'en' => 'In-Plan Email Message Price (Within Plan)',
            ],

            // Checkbox Options (Long Form)
            'plans.carry_over_credit' => [
                'ar' => 'ترحيل الرصيد المتبقي',
                'en' => 'Carry Over Remaining Credit',
            ],
            'plans.enable_attendance' => [
                'ar' => 'تفعيل الحضور',
                'en' => 'Enable Attendance Tracking',
            ],
            'plans.enable_multiple_templates' => [
                'ar' => 'تفعيل القوالب المتعددة',
                'en' => 'Enable Multiple Templates',
            ],

            // Channel Selection
            'plans.enabled_channels_documents' => [
                'ar' => 'قنوات إرسال الوثائق',
                'en' => 'Document Sending Channels',
            ],
            'plans.enabled_channels_attendance' => [
                'ar' => 'قنوات إرسال الحضور',
                'en' => 'Attendance Sending Channels',
            ],
            'plans.channel_email' => [
                'ar' => 'البريد الإلكتروني',
                'en' => 'Email',
            ],
            'plans.channel_sms' => [
                'ar' => 'رسائل SMS',
                'en' => 'SMS',
            ],
            'plans.channel_whatsapp' => [
                'ar' => 'واتساب',
                'en' => 'WhatsApp',
            ],

            // Table Headers (Short Form)
            'plans.price' => [
                'ar' => 'السعر',
                'en' => 'Price',
            ],
            'plans.compare_price' => [
                'ar' => 'سعر قبل الخصم',
                'en' => 'Compare Price',
            ],
            'plans.carry_over_credit_short' => [
                'ar' => 'ترحيل رصيد',
                'en' => 'Credit Carryover',
            ],
            'plans.enable_attendance_short' => [
                'ar' => 'حضور',
                'en' => 'Attendance',
            ],
            'plans.multiple_templates_short' => [
                'ar' => 'قوالب متعددة',
                'en' => 'Multiple Templates',
            ],
            'plans.doc_price_in' => [
                'ar' => 'وثيقة (بالخطة)',
                'en' => 'Doc Price (In)',
            ],
            'plans.doc_price_out' => [
                'ar' => 'وثيقة (خارج الخطة)',
                'en' => 'Doc Price (Out)',
            ],
            'plans.sms_price_in' => [
                'ar' => 'SMS (بالخطة)',
                'en' => 'SMS Price (In)',
            ],
            'plans.sms_price_out' => [
                'ar' => 'SMS (خارج الخطة)',
                'en' => 'SMS Price (Out)',
            ],
            'plans.whatsapp_price_in' => [
                'ar' => 'واتساب (بالخطة)',
                'en' => 'WhatsApp Price (In)',
            ],
            'plans.whatsapp_price_out' => [
                'ar' => 'واتساب (خارج الخطة)',
                'en' => 'WhatsApp Price (Out)',
            ],
            'plans.email_price_in' => [
                'ar' => 'إيميل (بالخطة)',
                'en' => 'Email Price (In)',
            ],
            'plans.email_price_out' => [
                'ar' => 'إيميل (خارج الخطة)',
                'en' => 'Email Price (Out)',
            ],

            // --- مفاتيح خاصة بـ SubscriptionResource (الاشتراكات) ---

            // Navigation & Model Labels
            'subscriptions.navigation_label' => [
                'ar' => 'الاشتراكات',
                'en' => 'Subscriptions',
            ],
            'subscriptions.model_label' => [
                'ar' => 'اشتراك',
                'en' => 'Subscription',
            ],
            'subscriptions.plural_model_label' => [
                'ar' => 'الاشتراكات',
                'en' => 'Subscriptions',
            ],
            'subscriptions.user' => [
                'ar' => 'المستخدم',
                'en' => 'User',
            ],
            'subscriptions.plan' => [
                'ar' => 'الباقة/الخطة',
                'en' => 'Plan',
            ],
            'subscriptions.balance' => [
                'ar' => 'الرصيد الكلي',
                'en' => 'Total Balance',
            ],
            'subscriptions.remaining' => [
                'ar' => 'الرصيد المتبقي',
                'en' => 'Remaining Balance',
            ],
            'subscriptions.start_date' => [
                'ar' => 'تاريخ البدء',
                'en' => 'Start Date',
            ],
            'subscriptions.end_date' => [
                'ar' => 'تاريخ الانتهاء',
                'en' => 'End Date',
            ],
            'subscriptions.status' => [
                'ar' => 'الحالة',
                'en' => 'Status',
            ],
            'subscriptions.auto_renew' => [
                'ar' => 'التجديد التلقائي',
                'en' => 'Auto Renew',
            ],
            'subscriptions.filter_status' => [
                'ar' => 'تصفية حسب الحالة',
                'en' => 'Filter by Status',
            ],

            // Status Options
            'subscriptions.status_active' => [
                'ar' => 'نشط',
                'en' => 'Active',
            ],
            'subscriptions.status_pending' => [
                'ar' => 'معلق',
                'en' => 'Pending',
            ],
            'subscriptions.status_expired' => [
                'ar' => 'منتهي الصلاحية',
                'en' => 'Expired',
            ],

            // --- مفاتيح خاصة بـ PaymentReceiptResource (طلبات إيصالات الدفع) ---

            // Navigation & Model Labels
            'payment_receipts.navigation_group' => [
                'ar' => 'الطلبات',
                'en' => 'Requests',
            ],
            'payment_receipts.navigation_label' => [
                'ar' => 'طلبات إيصالات الدفع',
                'en' => 'Payment Receipt Requests',
            ],
            'payment_receipts.model_label' => [
                'ar' => 'طلب إيصال دفع',
                'en' => 'Payment Receipt Request',
            ],
            'payment_receipts.plural_model_label' => [
                'ar' => 'طلبات إيصالات الدفع',
                'en' => 'Payment Receipt Requests',
            ],
            // Fields & Columns
            'payment_receipts.user_name' => [
                'ar' => 'اسم المستخدم',
                'en' => 'User Name',
            ],
            'payment_receipts.plan_name' => [
                'ar' => 'اسم الباقة',
                'en' => 'Plan Name',
            ],
            'payment_receipts.image_path' => [
                'ar' => 'الإيصال',
                'en' => 'Receipt Image',
            ],
            'payment_receipts.status' => [
                'ar' => 'الحالة',
                'en' => 'Status',
            ],
            // Permissions & Actions
            'payment_receipts.approve' => [
                'ar' => 'اعتماد',
                'en' => 'Approve',
            ],
            'payment_receipts.reject' => [
                'ar' => 'رفض',
                'en' => 'Reject',
            ],
            // Status Options
            'payment_receipts.status_pending' => [
                'ar' => 'معلق',
                'en' => 'Pending',
            ],
            'payment_receipts.status_approved' => [
                'ar' => 'معتمد',
                'en' => 'Approved',
            ],
            'payment_receipts.status_rejected' => [
                'ar' => 'مرفوض',
                'en' => 'Rejected',
            ],

            // --- مفاتيح خاصة بـ PlanUpgradeRequestResource (طلبات ترقية الباقة) ---

            // Navigation & Model Labels
            'plan_upgrade_requests.navigation_group' => [
                'ar' => 'الطلبات',
                'en' => 'Requests',
            ],
            'plan_upgrade_requests.navigation_label' => [
                'ar' => 'طلبات ترقية الباقة',
                'en' => 'Plan Upgrade Requests',
            ],
            'plan_upgrade_requests.model_label' => [
                'ar' => 'طلب ترقية باقة',
                'en' => 'Plan Upgrade Request',
            ],
            'plan_upgrade_requests.plural_model_label' => [
                'ar' => 'طلبات ترقية الباقة',
                'en' => 'Plan Upgrade Requests',
            ],
            // Fields & Columns
            'plan_upgrade_requests.user_name' => [
                'ar' => 'اسم المستخدم',
                'en' => 'User Name',
            ],
            'plan_upgrade_requests.plan_name' => [
                'ar' => 'الباقة الجديدة',
                'en' => 'New Plan',
            ],
            'plan_upgrade_requests.subscription' => [
                'ar' => 'الاشتراك الحالي (#)',
                'en' => 'Current Subscription (#)',
            ],
            'plan_upgrade_requests.receipt_path' => [
                'ar' => 'وصل الدفع',
                'en' => 'Payment Receipt',
            ],
            'plan_upgrade_requests.status' => [
                'ar' => 'الحالة',
                'en' => 'Status',
            ],
            'plan_upgrade_requests.rejected_reason' => [
                'ar' => 'سبب الرفض',
                'en' => 'Rejection Reason',
            ],
            // Actions & Confirmation
            'plan_upgrade_requests.approve' => [
                'ar' => 'اعتماد',
                'en' => 'Approve',
            ],
            'plan_upgrade_requests.reject' => [
                'ar' => 'رفض',
                'en' => 'Reject',
            ],
            'plan_upgrade_requests.reject_reason_prompt' => [
                'ar' => 'سبب الرفض',
                'en' => 'Rejection reason',
            ],
            'plan_upgrade_requests.send_email_toggle' => [
                'ar' => 'هل تريد إرسال سبب الرفض على البريد الإلكتروني؟',
                'en' => 'Send rejection reason via email?',
            ],

            // --- مفاتيح خاصة بـ WalletRechargeRequestResource (طلبات شحن المحفظة) ---

            // Navigation & Model Labels
            'wallet_recharge_requests.navigation_group' => [
                'ar' => 'الطلبات',
                'en' => 'Requests',
            ],
            'wallet_recharge_requests.navigation_label' => [
                'ar' => 'طلبات شحن المحفظة',
                'en' => 'Wallet Recharge Requests',
            ],
            'wallet_recharge_requests.model_label' => [
                'ar' => 'طلب شحن محفظة',
                'en' => 'Wallet Recharge Request',
            ],
            'wallet_recharge_requests.plural_model_label' => [
                'ar' => 'طلبات شحن المحفظة',
                'en' => 'Wallet Recharge Requests',
            ],
            // Fields & Columns
            'wallet_recharge_requests.user_name' => [
                'ar' => 'اسم المستخدم',
                'en' => 'User Name',
            ],
            'wallet_recharge_requests.plan_name' => [
                'ar' => 'الباقة',
                'en' => 'Plan',
            ],
            'wallet_recharge_requests.subscription' => [
                'ar' => 'الاشتراك (#)',
                'en' => 'Subscription (#)',
            ],
            'wallet_recharge_requests.amount' => [
                'ar' => 'قيمة الشحن',
                'en' => 'Recharge Amount',
            ],
            'wallet_recharge_requests.receipt_path' => [
                'ar' => 'وصل الدفع',
                'en' => 'Payment Receipt',
            ],
            'wallet_recharge_requests.status' => [
                'ar' => 'الحالة',
                'en' => 'Status',
            ],
            'wallet_recharge_requests.admin_note' => [
                'ar' => 'ملاحظات الإدارة/سبب الرفض',
                'en' => 'Admin Note/Rejection Reason',
            ],
            'wallet_recharge_requests.approved_at' => [
                'ar' => 'تاريخ الاعتماد',
                'en' => 'Approved At',
            ],
            'wallet_recharge_requests.reviewed_by' => [
                'ar' => 'تمت المراجعة بواسطة',
                'en' => 'Reviewed By',
            ],
            // Status Options
            'wallet_recharge_requests.status_pending' => [
                'ar' => 'معلق',
                'en' => 'Pending',
            ],
            'wallet_recharge_requests.status_approved' => [
                'ar' => 'معتمد',
                'en' => 'Approved',
            ],
            'wallet_recharge_requests.status_rejected' => [
                'ar' => 'مرفوض',
                'en' => 'Rejected',
            ],
            // Actions & Confirmation
            'wallet_recharge_requests.approve' => [
                'ar' => 'اعتماد',
                'en' => 'Approve',
            ],
            'wallet_recharge_requests.reject' => [
                'ar' => 'رفض',
                'en' => 'Reject',
            ],
            'wallet_recharge_requests.reject_reason_prompt' => [
                'ar' => 'سبب الرفض',
                'en' => 'Rejection reason',
            ],
            'wallet_recharge_requests.send_email_toggle' => [
                'ar' => 'هل تريد إرسال سبب الرفض على البريد الإلكتروني؟',
                'en' => 'Send rejection reason via email?',
            ],

            'plan_upgrade_requests.status_pending' => [
                'ar' => 'معلق',
                'en' => 'Pending',
            ],
            'plan_upgrade_requests.status_approved' => [
                'ar' => 'معتمد',
                'en' => 'Approved',
            ],
            'plan_upgrade_requests.status_rejected' => [
                'ar' => 'مرفوض',
                'en' => 'Rejected',
            ],
            'plans.navigation_group' => [
                'ar' => 'الخطط & الاشتراكات',
                'en' => 'Plans & Subscriptions',
            ],

            // --- مفاتيح خاصة بـ RoleResource (الأدوار) ---

            // Navigation & Model Labels
            'roles.navigation_group' => [
                'ar' => 'التحكم بالصلاحيات',
                'en' => 'Access Control',
            ],
            'roles.navigation_label' => [
                'ar' => 'الأدوار',
                'en' => 'Roles',
            ],
            'roles.model_label' => [
                'ar' => 'دور',
                'en' => 'Role',
            ],
            'roles.plural_model_label' => [
                'ar' => 'الأدوار',
                'en' => 'Roles',
            ],
            // Fields & Columns
            'roles.role_name' => [
                'ar' => 'اسم الدور',
                'en' => 'Role Name',
            ],
            'roles.permissions' => [
                'ar' => 'تعيين الصلاحيات',
                'en' => 'Assign Permissions',
            ],
            'roles.created_at' => [
                'ar' => 'تاريخ الإنشاء',
                'en' => 'Created At',
            ],

            /* مفاتيح مورد الصلاحيات (PermissionResource) */

            'permissions.model_label' => [
                'ar' => 'صلاحية',
                'en' => 'Permission',
            ],
            'permissions.plural_model_label' => [
                'ar' => 'الصلاحيات',
                'en' => 'Permissions',
            ],
            'permissions.permission_name' => [
                'ar' => 'اسم الصلاحية',
                'en' => 'Permission Name',
            ],
            'permissions.created_at' => [
                'ar' => 'تاريخ الإنشاء',
                'en' => 'Created At',
            ],

            /* مفاتيح مورد المستخدمين (UserResource) */

            'users.model_label' => [
                'ar' => 'مستخدم',
                'en' => 'User',
            ],
            'users.plural_model_label' => [
                'ar' => 'المستخدمون',
                'en' => 'Users',
            ],
            'users.name' => [
                'ar' => 'الاسم',
                'en' => 'Name',
            ],
            'users.email' => [
                'ar' => 'البريد الإلكتروني',
                'en' => 'Email',
            ],
            'users.password' => [
                'ar' => 'كلمة المرور',
                'en' => 'Password',
            ],
            'users.password_confirmation' => [
                'ar' => 'تأكيد كلمة المرور',
                'en' => 'Password Confirmation',
            ],
            'users.phone' => [
                'ar' => 'الهاتف',
                'en' => 'Phone',
            ],
            'users.category' => [
                'ar' => 'الفئة',
                'en' => 'Category',
            ],
            'users.assign_role' => [
                'ar' => 'إسناد الدور',
                'en' => 'Assign Role',
            ],
            'users.direct_permissions' => [
                'ar' => 'الأذونات المباشرة',
                'en' => 'Direct Permissions',
            ],
            'users.roles' => [
                'ar' => 'الأدوار',
                'en' => 'Roles',
            ],
            'users.permissions' => [
                'ar' => 'الصلاحيات',
                'en' => 'Permissions',
            ],
            'users.created_at' => [
                'ar' => 'تاريخ الإنشاء',
                'en' => 'Created At',
            ],

            'translation_keys.navigation_label' => [
                'ar' => 'مفاتيح الترجمة',
                'en' => 'Translation Keys',
            ],
            'translation_keys.navigation_group' => [
                'ar' => 'إعدادات الترجمة',
                'en' => 'Translation Settings',
            ],
            'translation_keys.model_label' => [
                'ar' => 'مفتاح الترجمة',
                'en' => 'Translation Key',
            ],
            'translation_keys.plural_model_label' => [
                'ar' => 'مفاتيح الترجمة',
                'en' => 'Translation Keys',
            ],
            'translation_keys.key_label' => [
                'ar' => 'المفتاح (مثلاً: document.title.default)',
                'en' => 'Key (e.g., document.title.default)',
            ],
            'translation_keys.key_column' => [
                'ar' => 'المفتاح',
                'en' => 'Key',
            ],

            'translation.navigation_label' => [
                'ar' => 'الترجمة',
                'en' => 'Translation',
            ],
            'translation.model_label' => [
                'ar' => 'قيم الترجمة',
                'en' => 'Translation Values',
            ],
            'translation.local' => [
                'ar' => 'اللغة',
                'en' => 'Lang',
            ],
            'translation.content' => [
                'ar' => 'النص',
                'en' => 'Content',
            ],
            'translation.value' => [
                'ar' => 'القيمة',
                'en' => 'Value',
            ],
            'event.sort.newest' => [
                'ar' => 'الأحدث أولاً',
                'en' => 'Newest First',
            ],
            'event.sort.older' => [
                'ar' => 'الأقدم أولاً',
                'en' => 'Oldest First',
            ],
            'form.important_note' => [
                'ar' => 'اقرأ التعليمات التالية جيدًا قبل البدء في الاستخدام، فهي ضرورية للغاية:',
                'en' => 'Please read the following instructions carefully before you start using, as they are extremely important:',
            ],
            'form.important_note.1' => [
                'ar' => 'اختر المقاس الذي تريده من القائمة المنسدلة المسماة "اختار مقاس القالب". ومن الضروري جدًا أن يكون القالب الذي ترفعه مطابقًا للمقاس الذي اخترته حتى لا تتشوه الصورة.',
                'en' => "Select the desired size from the dropdown menu named 'Choose the template size'. It is very important that the template you upload matches the selected size to avoid image distortion.",
            ],

            'form.important_note.2' => [
                'ar' => 'يجب إرفاق القالب أولاً، ثم بعد ذلك رفع ملفات Excel.',
                'en' => 'The template must be uploaded first, then the Excel files can be uploaded.',
            ],
            'form.important_note.3' => [
                'ar' => 'يجب أن يكون ملف بيانات القالب مطابقًا لملف بيانات التواصل.',
                'en' => 'The template data file must match the contact data file.',
            ],
            'form.certificate_section' => [
                'ar' => 'الجزء الخاص بالشهادة',
                'en' => 'Certificate Section',
            ],
            'chose_the_template_size' => [
                'ar' => 'اختار مقاس القالب',
                'en' => 'Choose the template size',
            ],
            'cm' => [
                'ar' => 'سم' ,
                'en' => 'cm',
            ],
            'template_default' => [
                'ar' => 'الوضع الافتراضي' ,
                'en' => 'Default',
            ],
            'search_for_template_size' => [
                'ar' => 'البحث عن مقاس القالب' ,
                'en' => 'Search for template size',
            ],
            'page_orientation' => [
                'ar' => 'اتجاه الصفحة' ,
                'en' => 'Page Orientation',
            ],
            'landscape' => [
                'ar' => 'أفقي' ,
                'en' => 'Landscape',
            ],
            'portrait' => [
                'ar' => 'عمودي' ,
                'en' => 'Portrait',
            ],
            'form.choose_validity' => [
                  'ar' => 'اختر الصلاحية' ,
                   'en' => 'Choose Validity',
            ],
            'channels.sms' => [
                    'ar' => 'رسائل SMS' ,
                   'en' => 'SMS',
            ],
            'channels.email' => [
                    'ar' => 'البريد الإلكتروني' ,
                   'en' => 'Email',
            ],
            'event.your.docs' => [
                    'ar' => 'الشهادات الخاصة بك' ,
                   'en' => 'Your Certificates',
            ],
            'buttons.data_for_users' => [
                    'ar' => 'ملف بيانات المشاركين' ,
                   'en' => 'Participants Data File',
            ],
            'register.email.placeholder' => [
            'en' => 'e.g: example@example.com',
            'ar' => 'مثال: name@example.com', // <--- هذه هي القيمة العربية الجديدة
        ],
        'register.phone.placeholder' => [
            'en' => 'e.g: 01012345678',
            'ar' => 'مثال: 01012345678', // <--- القيمة العربية الجديدة
        ],
        'terms' => [
            'ar' => 'الشروط والاحكام وسياسه الخصوصيه',
            'en' => 'Terms and Conditions and Privacy Policy ', // <--- القيمة العربية الجديدة
        ],
        'scan.qr' => [
            'ar' => "امسح رمز QR",
            'en' => 'Scan Qr ', // <--- القيمة العربية الجديدة
        ],
        'viewing.shared.profile' => [
            'ar' => "أنت تشاهد حاليًا ملفًا شخصيًا تمت مشاركته معك من قبل مستخدم آخر.",
            'en' => 'You are currently viewing a profile shared with you by another user.',
        ],
        'Profile.Information..' => [
            'ar' => "معلومات الملف الشخصي",
            'en' => 'Profile Information',
        ],
        'documents.Information.' => [
            'ar' => "  هذه هي الوثائق",
            'en' => 'These are the documents of',
        ],
        'documents.Information.user' => [
            'ar' => "  هذه هي المستندات التي سمح المستخدم للآخرين بالاطلاع عليها.",
            'en' => 'These are the documents that the user has allowed others to view.',
        ],
         'available.documents.' => [
            'ar' => "لا توجد مستندات متاحة هنا!.",
            'en' => 'No documents available here!',
        ],
         'verify.owner.allowed' => [
            'ar' => "يرجى التحقق من أن صاحب الحساب قد سمح بإظهار مستنداته..",
            'en' => 'Please verify that the account owner has allowed their documents to be visible.',
        ],
         'events.of' => [
            'ar' => "هذه هي أحداث",
            'en' => 'These are the Events of',
        ],
        'organizer.allowed.events' => [
            'ar' => "هذه هي الفعاليات التي سمح المنظم بإظهارها على ملفه الشخصي.",
            'en' => 'These are the events that the organizer has allowed to be visible on their personal profile.',
        ],
        'subscription.success' => [
            'ar' => "تم تحديث إعداد التجديد التلقائي بنجاح.",
            'en' => 'The automatic renewal setting has been updated successfully.',
        ],
        'subscription.error' => [
            'ar' => "لا يوجد اشتراك مرتبط بهذا الحساب.",
            'en' => 'There is no subscription associated with this account.',
        ],
        'subscription.renewed' => [
            'ar' => "تم تجديد الباقة بنجاح ",
            'en' => 'The package has been successfully renewed.',
        ],
            'payment_receipts.created_at' => [
            'ar' => "تاريخ ألإنشاء",
            'en' => 'Created at',
        ],

            'payment_receipts.sort_order' => [
                'ar' => 'الترتيب',
                'en' => 'Sort order',
            ],
            'payment_receipts.sort_order_desc' => [
                'ar' => 'من الأحدث للأقدم',
                'en' => 'From newest to oldest',
            ],
            'payment_receipts.sort_order_ASC' => [
                'ar' => 'من ألأقدم للأحدث',
                'en' => 'From oldest to newest',
            ],
            'profile.link.copy' => [
                'ar' => 'تم نسخ الرابط الي الحافظة',
                'en' => 'Link copied to clipboard!',
            ]


        ];

        foreach ($translations as $key => $values) {
            /** @var TranslationKeyFactory $factory */
            $factory = TranslationKey::factory();

            $factory->createOrUpdateValues(
                $key,
                $values['ar'],
                $values['en'] ?? null
            );
        }
    }
}
