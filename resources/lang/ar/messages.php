<?php

return [

    /*
    |--------------------------------------------------------------------------
    | General Messages (Arabic)
    |--------------------------------------------------------------------------
    */

    // App
    'app_name' => 'نظام مساعدة الأسر',
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
        'audit_logs' => 'سجل العمليات',
        'members' => 'أفراد الأسرة',
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
        'download' => 'تحميل',
        'upload' => 'رفع',
        'verify' => 'تحقق',
        'approve' => 'موافقة',
        'reject' => 'رفض',
        'back' => 'رجوع',
        'next' => 'التالي',
        'previous' => 'السابق',
        'submit' => 'إرسال',
        'confirm' => 'تأكيد',
        'close' => 'إغلاق',
    ],
    
    // Status
    'status' => [
        'pending' => 'قيد الانتظار',
        'verified' => 'تم التحقق',
        'suspended' => 'معلق',
        'rejected' => 'مرفوض',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'completed' => 'مكتمل',
        'in_progress' => 'قيد التنفيذ',
        'not_eligible' => 'غير مؤهل',
        'not_received' => 'لم يستلم',
    ],
    
    // Success messages
    'success' => [
        'created' => 'تم الإنشاء بنجاح!',
        'updated' => 'تم التحديث بنجاح!',
        'deleted' => 'تم الحذف بنجاح!',
        'saved' => 'تم الحفظ بنجاح!',
        'imported' => 'تم الاستيراد بنجاح!',
        'exported' => 'تم التصدير بنجاح!',
        'verified' => 'تم التحقق بنجاح!',
    ],
    
    // Error messages
    'error' => [
        'general' => 'حدث خطأ. حاول مرة أخرى.',
        'not_found' => 'العنصر غير موجود.',
        'unauthorized' => 'غير مصرح لك بهذا الإجراء.',
        'validation' => 'يرجى تصحيح الأخطاء أدناه.',
    ],
    
    // Confirmation
    'confirm' => [
        'delete' => 'هل أنت متأكد من الحذف؟',
        'action' => 'هل أنت متأكد من هذا الإجراء؟',
    ],
    
    // Citizen
    'citizen' => [
        'onboarding' => 'تسجيل الأسرة',
        'my_household' => 'بيانات أسرتي',
        'benefit_history' => 'سجل الاستفادة',
        'last_benefit' => 'آخر استفادة',
        'no_benefits' => 'لا توجد سجلات استفادة',
        'registration_status' => 'حالة التسجيل',
        'household_code' => 'رمز الأسرة',
        'last_update' => 'آخر تحديث',
    ],
    
    // Household
    'household' => [
        'head_name' => 'اسم رب الأسرة',
        'head_national_id' => 'هوية رب الأسرة',
        'region' => 'المنطقة',
        'address' => 'العنوان',
        'housing_type' => 'نوع السكن',
        'phone' => 'رقم الجوال',
        'members' => 'أفراد الأسرة',
        'members_count' => 'عدد الأفراد',
    ],
    
    // Housing types
    'housing_types' => [
        'owned' => 'ملك',
        'rented' => 'إيجار',
        'family_hosted' => 'مستضاف عند أسرة',
        'other' => 'أخرى',
    ],
    
    // Relations
    'relations' => [
        'spouse' => 'زوج/زوجة',
        'son' => 'ابن',
        'daughter' => 'ابنة',
        'parent' => 'والد/والدة',
        'sibling' => 'أخ/أخت',
        'grandparent' => 'جد/جدة',
        'grandchild' => 'حفيد/حفيدة',
        'other' => 'أخرى',
    ],
    
    // Programs
    'program' => [
        'name' => 'اسم البرنامج',
        'description' => 'الوصف',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'benefit_type' => 'نوع المساعدة',
        'benefit_date' => 'تاريخ الاستفادة',
        'notes' => 'ملاحظات',
    ],
    
    // Tracking table
    'tracking' => [
        'program' => 'البرنامج',
        'benefit_type' => 'نوع المساعدة',
        'status' => 'الحالة',
        'last_updated' => 'آخر تحديث',
        'benefit_date' => 'تاريخ الاستفادة',
        'notes' => 'ملاحظات',
    ],
    
    // Language
    'language' => [
        'ar' => 'العربية',
        'en' => 'English',
        'switch' => 'تغيير اللغة',
    ],
    
    // Dates
    'today' => 'اليوم',
    'yesterday' => 'أمس',
    'this_month' => 'هذا الشهر',
    
    // Loading
    'loading' => 'جاري التحميل...',
    'please_wait' => 'يرجى الانتظار...',

];
