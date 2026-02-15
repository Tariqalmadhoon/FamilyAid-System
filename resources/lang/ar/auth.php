<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    */

    'failed' => 'بيانات الاعتماد غير صحيحة.',
    'password' => 'كلمة المرور المقدمة غير صحيحة.',
    'throttle' => 'عدد كبير من محاولات الدخول. يرجى المحاولة بعد :seconds ثانية.',

    // Auth page labels
    'login' => 'تسجيل الدخول',
    'register' => 'إنشاء حساب',
    'logout' => 'تسجيل الخروج',
    'forgot_password' => 'نسيت كلمة المرور؟',
    'reset_password' => 'إعادة تعيين كلمة المرور',
    'confirm_password' => 'تأكيد كلمة المرور',
    'remember_me' => 'تذكرني',
    
    // Form labels
    'national_id' => 'رقم الهوية ',
    'name' => 'الاسم الكامل',
    'first_name' => 'الاسم الأول',
    'father_name' => 'اسم الأب',
    'grandfather_name' => 'اسم الجد',
    'last_name' => 'اسم العائلة',
    'phone' => 'رقم الهاتف',
    'national_id_hint' => 'يجب أن يكون الرقم الوطني 9 أرقام بالضبط.',
    'phone_hint' => 'اختر المقدمة الدولية (+970 أو +972) ثم أدخل رقم الهاتف 9 أرقام بدون الصفر الأول.',
    'password_label' => 'كلمة المرور',
    'password_confirmation' => 'تأكيد كلمة المرور',

    // Password strength (واجهة فقط)
    'strength_weak' => 'ضعيفة',
    'strength_fair' => 'مقبولة',
    'strength_ok' => 'جيدة',
    'strength_good' => 'قوية',
    'strength_strong' => 'قوية جداً',
    'strength_hint_basic' => 'أضف مزيداً من الحروف لتقوية الكلمة.',
    'strength_hint_numbers' => 'إضافة أرقام تحسن القوة.',
    'strength_hint_upper' => 'امزج بين الحروف الكبيرة والصغيرة.',
    'strength_hint_symbols' => 'إضافة رمز تجعلها أقوى.',
    'strength_hint_strong' => 'كلمة المرور قوية!',
    
    // Buttons
    'login_btn' => 'دخول',
    'register_btn' => 'تسجيل',
    'reset_btn' => 'إعادة تعيين',
    'continue_btn' => 'متابعة',
    'verify_btn' => 'تحقق',
    
    // Messages
    'welcome_back' => 'مرحباً بعودتك',
    'login_subtitle' => 'أدخل بياناتك للدخول إلى حسابك',
    'register_subtitle' => 'أنشئ حسابك للوصول إلى خدماتنا',
    'no_account' => 'ليس لديك حساب؟',
    'have_account' => 'لديك حساب بالفعل؟',
    'enter_national_id' => 'أدخل رقمك الوطني',
    'enter_phone_or_id' => 'أدخل الرقم الوطني أو رقم الهاتف',
    'enter_otp' => 'أدخل الكود المرسل إلى هاتفك',
    'set_new_password' => 'أدخل كلمة مرور جديدة',
    'send_code' => 'إرسال الكود',
    'resend_code' => 'إعادة إرسال الكود',
    'code_sent' => 'تم إرسال رمز التحقق.',
    'code_invalid' => 'الرمز غير صحيح أو منتهي.',
    'sms_send_failed' => 'تعذر إرسال رمز التحقق للهاتف. تحقق من إعدادات مزود الرسائل وحاول مرة أخرى.',
    'sms_balance_low' => 'نعتذر، يتعذر الإرسال في الوقت الحالي. تواصل مع الدعم الفني.',
    'sms_verified_only' => 'حساب sms.to الحالي يسمح بالإرسال فقط إلى الرقم الموثّق. اشحن الرصيد أو فعّل الحساب للإرسال العام.',
    'sms_log_mode_notice' => 'وضع الرسائل الحالي تجريبي (LOG) ولا يرسل SMS فعليًا. فعّل مزود SMS (sms.to) من ملف .env.',
    'support_contact_notice' => 'نعتذر، يتعذر الإرسال في الوقت الحالي. تواصل مع الدعم الفني.',
    'contact_support_whatsapp' => 'الدعم الفني عبر واتساب',
    'otp_placeholder' => 'رمز من 6 أرقام',
    
    // Success/Error
    'login_success' => 'تم تسجيل الدخول بنجاح!',
    'register_success' => 'تم إنشاء الحساب بنجاح!',
    'password_reset_success' => 'تم تغيير كلمة المرور بنجاح!',
    'invalid_credentials' => 'بيانات الدخول غير صحيحة',
    'account_suspended' => 'تم إيقاف حسابك. يرجى التواصل مع الدعم.',

    // Login guidance
    'guidance_1' => 'هذا النظام مخصص لتسجيل بيانات الأسر في مخيمات القرارة.',
    'guidance_2' => 'عملية التسجيل تستغرق أقل من 3 دقائق.',
    'guidance_3' => 'يمكنك تعديل بياناتك لاحقًا من لوحة التحكم.',
    'guidance_4' => 'بياناتك محفوظة بسرية تامة.',
    'step_1' => 'إنشاء حساب',
    'step_2' => 'إدخال بيانات الأسرة',
    'step_3' => 'مراجعة واعتماد',
    'step_4' => 'متابعة حالة الاستفادة',
    'disclaimer' => 'التسجيل لا يعني قبول تلقائي، ويتم مراجعة البيانات من قبل الإدارة.',
    'show_guidance' => 'كيف يعمل النظام؟',
    'hide_guidance' => 'إخفاء الإرشادات',

];

