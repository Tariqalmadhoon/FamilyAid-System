<?php

return [

    // App
    'app_name' => 'نظام FamilyAid',
    'welcome' => 'مرحباً',
    'dashboard' => 'لوحة التحكم',

    // Navigation
    'nav' => [
        'home' => 'الرئيسية',
        'dashboard' => 'لوحة التحكم',
        'households' => 'الأسر',
        'programs' => 'البرامج',
        'distributions' => 'التوزيعات',
        'import_export' => 'استيراد/تصدير',
        'audit_logs' => 'سجلات التدقيق',
        'members' => 'الأعضاء',
        'settings' => 'الإعدادات',
        'profile' => 'الملف الشخصي',
        'logout' => 'تسجيل الخروج',
    ],

    // Actions
    'actions' => [
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'delete' => 'حذف',
        'edit' => 'تعديل',
        'view' => 'عرض',
        'create' => 'إنشاء',
        'add' => 'إضافة',
        'update' => 'تحديث',
        'search' => 'بحث',
        'filter' => 'تصفية',
        'clear' => 'مسح',
        'export' => 'تصدير',
        'import' => 'استيراد',
        'download' => 'تنزيل',
        'upload' => 'رفع',
        'verify' => 'توثيق',
        'approve' => 'اعتماد',
        'reject' => 'رفض',
        'back' => 'رجوع',
        'next' => 'التالي',
        'previous' => 'السابق',
        'submit' => 'إرسال',
        'confirm' => 'تأكيد',
        'close' => 'إغلاق',
        'select' => 'اختر',
        'view_all' => 'عرض الكل',
    ],

    // Status
    'status' => [
        'pending' => 'قيد المراجعة',
        'verified' => 'موثق',
        'suspended' => 'موقوف',
        'rejected' => 'مرفوض',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'completed' => 'مكتمل',
        'in_progress' => 'قيد التنفيذ',
        'not_eligible' => 'غير مؤهل',
        'not_received' => 'لم يتم الاستلام',
        'failed' => 'فشل',
        'processing' => 'قيد المعالجة',
    ],

    // Success messages
    'success' => [
        'created' => 'تم الإنشاء بنجاح!',
        'updated' => 'تم التحديث بنجاح!',
        'deleted' => 'تم الحذف بنجاح!',
        'saved' => 'تم الحفظ بنجاح!',
        'imported' => 'تم الاستيراد بنجاح!',
        'exported' => 'تم التصدير بنجاح!',
        'verified' => 'تم التوثيق بنجاح!',
    ],

    // Import/Export
    'import_export' => [
        'title' => 'استيراد/تصدير',
        'import_households' => 'استيراد الأسر',
        'import_description' => 'ارفع ملف Excel أو CSV لاستيراد الأسر دفعة واحدة.',
        'download_template' => 'تحميل النموذج',
        'select_file' => 'اختر الملف',
        'supported_formats' => 'الأنواع المدعومة: .xlsx، .xls، .csv (بحد أقصى 10MB)',
        'import_btn' => 'استيراد',
        'export_data' => 'تصدير البيانات',
        'export_households' => 'تصدير الأسر',
        'export_distributions' => 'تصدير التوزيعات',
        'all_status' => 'كل الحالات',
        'all_regions' => 'كل المناطق',
        'from_date' => 'من',
        'to_date' => 'إلى',
        'recent_imports' => 'أحدث عمليات الاستيراد',
        'no_imports' => 'لا يوجد استيراد بعد',
        'result_ok_failed' => ':ok ناجحة، :failed فاشلة',
        'file' => 'الملف',
        'date' => 'التاريخ',
        'user' => 'المستخدم',
        'status' => 'الحالة',
        'result' => 'النتيجة',
    ],

    // Error messages
    'error' => [
        'general' => 'حدث خطأ، يرجى المحاولة مرة أخرى.',
        'not_found' => 'العنصر غير موجود.',
        'unauthorized' => 'ليست لديك صلاحية لتنفيذ هذا الإجراء.',
        'validation' => 'يرجى تصحيح الأخطاء أدناه.',
    ],

    // Confirmation
    'confirm' => [
        'delete' => 'هل أنت متأكد من الحذف؟',
        'action' => 'هل أنت متأكد من تنفيذ هذا الإجراء؟',
    ],

    // Citizen
    'citizen' => [
        'onboarding' => 'تسجيل الأسرة',
        'my_household' => 'أسرتي',
        'benefit_history' => 'سجل المساعدات',
        'last_benefit' => 'آخر مساعدة',
        'no_benefits' => 'لا توجد مساعدات مسجلة',
        'registration_status' => 'حالة التسجيل',
        'household_code' => 'رقم الأسرة',
        'last_update' => 'آخر تحديث',
        'dashboard_title' => 'لوحة أسرتي',
        'last_benefit_title' => 'آخر مساعدة مستلمة',
        'no_benefits_helper' => 'ستظهر المساعدات هنا عند توزيعها.',
        'update_household' => 'تحديث بيانات الأسرة',
        'update_household_sub' => 'تعديل العنوان وبيانات الاتصال',
        'manage_members' => 'إدارة الأعضاء',
        'member_count' => ':count عضو',
        'your_region' => 'منطقتك',
        'benefit_history_empty' => 'لا يوجد سجل مساعدات متاح.',
        'household_info' => 'بيانات الأسرة',
    ],

    // Members
    'members' => [
        'manage_title' => 'إدارة أفراد الأسرة',
        'count' => ':count عضو',
        'add_btn' => 'إضافة عضو',
        'add_first' => 'أضف أول عضو',
        'none_title' => 'لا يوجد أعضاء مضافون',
        'none_helper' => 'أضف أفراد أسرتك لاستكمال ملف الأسرة.',
        'edit_title' => 'تعديل عضو',
        'full_name' => 'الاسم الكامل',
        'relation' => 'صلة القرابة',
        'national_id_optional' => 'الرقم الوطني (اختياري)',
        'gender' => 'النوع',
        'male' => 'ذكر',
        'female' => 'أنثى',
        'birth_date' => 'تاريخ الميلاد',
        'id_label' => 'الهوية',
        'age_years' => ':years سنة',
        'remove_title' => 'حذف عضو',
        'remove_confirm' => 'هل أنت متأكد من حذف :name؟',
        'saving' => 'جارٍ الحفظ...',
    ],

    // Household
    'household' => [
        'head_name' => 'اسم رب الأسرة',
        'head_national_id' => 'الرقم الوطني لرب الأسرة',
        'region' => 'المنطقة',
        'address' => 'العنوان',
        'housing_type' => 'نوع السكن',
        'phone' => 'رقم الهاتف',
        'members' => 'أفراد الأسرة',
        'members_count' => 'عدد الأفراد',
        'registered_at' => 'تاريخ التسجيل',
    ],

    // Housing types
    'housing_types' => [
        'owned' => 'ملك',
        'rented' => 'مستأجر',
        'family_hosted' => 'مستضاف لدى العائلة',
        'other' => 'أخرى',
    ],

    // Relations
    'relations' => [
        'spouse' => 'زوج/زوجة',
        'son' => 'ابن',
        'daughter' => 'ابنة',
        'parent' => 'أب/أم',
        'sibling' => 'أخ/أخت',
        'grandparent' => 'جد/جدة',
        'grandchild' => 'حفيد/حفيدة',
        'other' => 'أخرى',
    ],

    // Programs
    'program' => [
        'name' => 'اسم برنامج المساعدة',
        'description' => 'الوصف',
        'start_date' => 'تاريخ البدء',
        'end_date' => 'تاريخ الانتهاء',
        'benefit_type' => 'نوع المساعدة',
        'benefit_date' => 'تاريخ الصرف',
        'notes' => 'ملاحظات',
    ],

    // Programs (admin)
    'programs' => [
        'title' => 'البرامج',
        'new' => 'برنامج جديد',
        'table' => [
            'name' => 'اسم البرنامج',
            'period' => 'الفترة',
            'distributions' => 'التوزيعات',
            'status' => 'الحالة',
            'actions' => 'إجراءات',
        ],
        'period_from' => 'من :date',
        'period_range' => 'من :from إلى :to',
        'period_ongoing' => 'مستمر',
        'multi' => '(متعدد)',
        'no_programs' => 'لا توجد برامج بعد',
    ],

    // Households (admin)
    'households_admin' => [
        'title' => 'الأسر',
        'add' => 'إضافة أسرة',
        'search_placeholder' => 'ابحث بالاسم أو الرقم...',
        'all_status' => 'كل الحالات',
        'all_regions' => 'كل المناطق',
        'all_housing' => 'كل أنواع السكن',
        'table' => [
            'head' => 'اسم رب الأسرة',
            'national_id' => 'الرقم الوطني',
            'region' => 'المنطقة',
            'members' => 'الأفراد',
            'status' => 'الحالة',
            'actions' => 'إجراءات',
        ],
        'no_results' => 'لا توجد أسر',
        'record_distribution' => 'تسجيل توزيع',
    ],

    // Distributions
    'distributions' => [
        'title' => 'التوزيعات',
        'record' => 'تسجيل توزيع',
        'search_placeholder' => 'ابحث عن أسرة...',
        'all_programs' => 'كل البرامج',
        'from_date' => 'من',
        'to_date' => 'إلى',
        'table' => [
            'household' => 'الأسرة',
            'program' => 'البرنامج',
            'date' => 'التاريخ',
            'recorded_by' => 'مسجل بواسطة',
            'actions' => 'إجراءات',
        ],
        'no_results' => 'لا توجد توزيعات',
        'delete_confirm' => 'حذف هذا التوزيع؟',
    ],

    // Tracking table
    'tracking' => [
        'program' => 'البرنامج',
        'benefit_type' => 'نوع المساعدة',
        'status' => 'الحالة',
        'last_updated' => 'آخر تحديث',
        'benefit_date' => 'تاريخ الصرف',
        'date' => 'التاريخ',
        'notes' => 'ملاحظات',
    ],

    // Language
    'language' => [
        'ar' => 'العربية',
        'en' => 'الإنجليزية',
        'switch' => 'تغيير اللغة',
        'short_ar' => 'ع',
        'short_en' => 'EN',
    ],

    // Dates
    'today' => 'اليوم',
    'yesterday' => 'أمس',
    'this_month' => 'هذا الشهر',

    // Loading
    'loading' => 'جارٍ التحميل...',
    'please_wait' => 'يرجى الانتظار...',

    // General helpers
    'general' => [
        'unknown' => 'غير معروف',
        'unknown_region' => 'منطقة غير معروفة',
        'optional' => 'اختياري',
        'system' => 'النظام',
    ],

    // Exports
    'exports' => [
        'households' => [
            'national_id' => 'الرقم الوطني',
            'head_name' => 'اسم رب الأسرة',
            'region' => 'المنطقة',
            'address' => 'العنوان',
            'housing_type' => 'نوع السكن',
            'primary_phone' => 'الهاتف الأساسي',
            'secondary_phone' => 'هاتف إضافي',
            'status' => 'الحالة',
            'members_count' => 'عدد الأفراد',
            'member_names' => 'أسماء الأعضاء',
            'registered_date' => 'تاريخ التسجيل',
        ],
        'distributions' => [
            'date' => 'التاريخ',
            'program' => 'البرنامج',
            'national_id' => 'الرقم الوطني',
            'head_name' => 'اسم رب الأسرة',
            'region' => 'المنطقة',
            'phone' => 'الهاتف',
            'recorded_by' => 'مسجل بواسطة',
            'notes' => 'ملاحظات',
        ],
    ],

];
