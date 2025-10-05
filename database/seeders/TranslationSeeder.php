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
