<?php

return [

    // App
    'app_name' => 'FamilyAid System',
    'welcome' => 'Welcome',
    'dashboard' => 'Dashboard',

    // Navigation
    'nav' => [
        'home' => 'Home',
        'dashboard' => 'Dashboard',
        'households' => 'Households',
        'programs' => 'Programs',
        'distributions' => 'Distributions',
        'import_export' => 'Import/Export',
        'audit_logs' => 'Audit Logs',
        'members' => 'Members',
        'settings' => 'Settings',
        'profile' => 'Profile',
        'logout' => 'Logout',
    ],

    // Actions
    'actions' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'view' => 'View',
        'create' => 'Create',
        'add' => 'Add',
        'update' => 'Update',
        'search' => 'Search',
        'filter' => 'Filter',
        'clear' => 'Clear',
        'export' => 'Export',
        'import' => 'Import',
        'download' => 'Download',
        'upload' => 'Upload',
        'verify' => 'Verify',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'back' => 'Back',
        'next' => 'Next',
        'previous' => 'Previous',
        'submit' => 'Submit',
        'confirm' => 'Confirm',
        'close' => 'Close',
        'select' => 'Select',
        'view_all' => 'View All',
        'edit' => 'Edit',
    ],

    // Status
    'status' => [
        'pending' => 'Pending',
        'verified' => 'Verified',
        'suspended' => 'Suspended',
        'rejected' => 'Rejected',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'completed' => 'Completed',
        'in_progress' => 'In Progress',
        'not_eligible' => 'Not Eligible',
        'not_received' => 'Not Received',
        'failed' => 'Failed',
        'processing' => 'Processing',
    ],

    // Success messages
    'success' => [
        'created' => 'Created successfully!',
        'updated' => 'Updated successfully!',
        'deleted' => 'Deleted successfully!',
        'saved' => 'Saved successfully!',
        'imported' => 'Imported successfully!',
        'exported' => 'Exported successfully!',
        'verified' => 'Verified successfully!',
    ],

    // Import/Export
    'import_export' => [
        'title' => 'Import & Export',
        'import_households' => 'Import Households',
        'import_description' => 'Upload an Excel or CSV file to import households in bulk.',
        'download_template' => 'Download Template',
        'select_file' => 'Select File',
        'supported_formats' => 'Supported: .xlsx, .xls, .csv (max 10MB)',
        'import_btn' => 'Import',
        'export_data' => 'Export Data',
        'export_households' => 'Export Households',
        'export_distributions' => 'Export Distributions',
        'all_status' => 'All Status',
        'all_regions' => 'All Regions',
        'from_date' => 'From',
        'to_date' => 'To',
        'recent_imports' => 'Recent Imports',
        'no_imports' => 'No imports yet',
        'result_ok_failed' => ':ok ok, :failed failed',
        'file' => 'File',
        'date' => 'Date',
        'user' => 'User',
        'status' => 'Status',
        'result' => 'Result',
    ],

    // Error messages
    'error' => [
        'general' => 'An error occurred. Please try again.',
        'not_found' => 'Item not found.',
        'unauthorized' => 'You are not authorized for this action.',
        'validation' => 'Please correct the errors below.',
    ],

    // Confirmation
    'confirm' => [
        'delete' => 'Are you sure you want to delete?',
        'action' => 'Are you sure about this action?',
    ],

    // Citizen
    'citizen' => [
        'onboarding' => 'Register Household',
        'my_household' => 'My Household',
        'benefit_history' => 'Benefit History',
        'last_benefit' => 'Last Benefit',
        'no_benefits' => 'No benefit records',
        'registration_status' => 'Registration Status',
        'household_code' => 'Household Code',
        'last_update' => 'Last Update',
        'dashboard_title' => 'My Household Dashboard',
        'last_benefit_title' => 'Last Benefit Received',
        'no_benefits_helper' => 'Benefits will appear here once distributed.',
        'update_household' => 'Update Household',
        'update_household_sub' => 'Edit address & contact info',
        'manage_members' => 'Manage Members',
        'member_count' => ':count member(s)',
        'your_region' => 'Your region',
        'benefit_history_empty' => 'No benefit history available.',
        'household_info' => 'Household Information',
    ],

    // Onboarding form
    'onboarding_form' => [
        'title' => 'Complete Your Household Registration',
        'section_region_address' => 'Region & Address',
        'select_region' => 'Select Region',
        'select_region_placeholder' => '-- Select Region --',
        'full_address' => 'Full Address',
        'full_address_placeholder' => 'Enter your full address including street, building, floor, etc.',
        'housing_contact' => 'Housing & Contact Information',
        'housing_type' => 'Housing Type',
        'primary_phone' => 'Primary Phone',
        'primary_phone_placeholder' => 'e.g., 0501234567',
        'secondary_phone' => 'Secondary Phone (Optional)',
        'secondary_phone_placeholder' => 'e.g., 0509876543',
        'family_members_title' => 'Family Members',
        'add_member' => 'Add Member',
        'members_helper' => 'Add your household members (spouse, children, parents, etc.). You can skip this step and add members later.',
        'member_label' => 'Member',
        'member_full_name' => 'Full Name',
        'member_relation' => 'Relation',
        'member_national_id_optional' => 'National ID (Optional)',
        'member_gender' => 'Gender',
        'member_gender_male' => 'Male',
        'member_gender_female' => 'Female',
        'member_birth_date_optional' => 'Birth Date (Optional)',
        'members_empty_title' => 'No members added yet',
        'members_empty_helper' => 'Click "Add Member" to add family members',
        'review_title' => 'Review Your Information',
        'address_info' => 'Address Information',
        'edit' => 'Edit',
        'not_provided' => 'Not provided',
        'housing_info' => 'Housing & Contact',
        'housing_label' => 'Housing:',
        'phone_label' => 'Phone:',
        'not_selected' => 'Not selected',
        'members_summary_title' => 'Family Members',
        'members_none' => 'No members added',
        'pending_verification_title' => 'Pending Verification',
        'pending_verification_text' => 'Your household will be submitted for verification. You can update your information anytime from your dashboard.',
        'btn_previous' => 'Previous',
        'btn_next' => 'Next',
        'btn_submit' => 'Submit Registration',
        'submitting' => 'Submitting...',
        'step_address' => 'Address',
        'step_housing' => 'Housing',
        'step_members' => 'Members',
        'step_review' => 'Review',
    ],

    // Members
    'members' => [
        'manage_title' => 'Manage Family Members',
        'count' => ':count member(s)',
        'add_btn' => 'Add Member',
        'add_first' => 'Add First Member',
        'none_title' => 'No Members Added',
        'none_helper' => 'Add your family members to complete your household profile.',
        'edit_title' => 'Edit Member',
        'full_name' => 'Full Name',
        'relation' => 'Relation',
        'national_id_optional' => 'National ID (Optional)',
        'gender' => 'Gender',
        'male' => 'Male',
        'female' => 'Female',
        'birth_date' => 'Birth Date',
        'id_label' => 'ID',
        'age_years' => ':years years old',
        'remove_title' => 'Remove Member',
        'remove_confirm' => 'Are you sure you want to remove :name?',
        'saving' => 'Saving...',
    ],

    // Household
    'household' => [
        'head_name' => 'Head of Household',
        'head_national_id' => 'National ID of Head',
        'region' => 'Region',
        'address' => 'Address',
        'housing_type' => 'Housing Type',
        'phone' => 'Phone Number',
        'members' => 'Household Members',
        'members_count' => 'Number of Members',
        'registered_at' => 'Registered',
    ],

    // Housing types
    'housing_types' => [
        'owned' => 'Owned',
        'rented' => 'Rented',
        'family_hosted' => 'Family Hosted',
        'other' => 'Other',
    ],

    // Relations
    'relations' => [
        'spouse' => 'Spouse',
        'son' => 'Son',
        'daughter' => 'Daughter',
        'parent' => 'Father/Mother',
        'sibling' => 'Brother/Sister',
        'grandparent' => 'Grandfather/Grandmother',
        'grandchild' => 'Grandson/Granddaughter',
        'other' => 'Other',
    ],

    // Programs
    'program' => [
        'name' => 'Aid Program Name',
        'description' => 'Description',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'benefit_type' => 'Benefit Type',
        'benefit_date' => 'Benefit Date',
        'notes' => 'Notes',
        'active_hint' => 'Active (can receive distributions)',
        'allow_multiple' => 'Allow multiple distributions per household',
    ],

    // Programs (admin)
    'programs' => [
        'title' => 'Aid Programs',
        'new' => 'New Program',
        'table' => [
            'name' => 'Program Name',
            'period' => 'Period',
            'distributions' => 'Distributions',
            'status' => 'Status',
            'actions' => 'Actions',
        ],
        'period_from' => 'From :date',
        'period_range' => ':from - :to',
        'period_ongoing' => 'Ongoing',
        'multi' => '(multi)',
        'no_programs' => 'No programs yet',
    ],

    // Households (admin)
    'households_admin' => [
        'title' => 'Households',
        'add' => 'Add Household',
        'search_placeholder' => 'Search name, ID, phone...',
        'all_status' => 'All Status',
        'all_regions' => 'All Regions',
        'all_housing' => 'All Housing',
        'table' => [
            'head' => 'Head of Household',
            'national_id' => 'National ID',
            'region' => 'Region',
            'members' => 'Members',
            'status' => 'Status',
            'actions' => 'Actions',
        ],
        'no_results' => 'No households found',
        'record_distribution' => 'Record Distribution',
    ],

    // Distributions
    'distributions' => [
        'title' => 'Distributions',
        'record' => 'Record Distribution',
        'search_placeholder' => 'Search household...',
        'all_programs' => 'All Programs',
        'from_date' => 'From',
        'to_date' => 'To',
        'table' => [
            'household' => 'Household',
            'program' => 'Program',
            'date' => 'Date',
            'recorded_by' => 'Recorded By',
            'actions' => 'Actions',
        ],
        'no_results' => 'No distributions found',
        'delete_confirm' => 'Delete this distribution?',
    ],

    // Tracking table
    'tracking' => [
        'program' => 'Program',
        'benefit_type' => 'Benefit Type',
        'status' => 'Status',
        'last_updated' => 'Last Updated',
        'benefit_date' => 'Benefit Date',
        'date' => 'Date',
        'notes' => 'Notes',
    ],

    // Language
    'language' => [
        'ar' => 'Arabic',
        'en' => 'English',
        'switch' => 'Switch Language',
        'short_ar' => 'ع',
        'short_en' => 'EN',
    ],

    // Dates
    'today' => 'Today',
    'yesterday' => 'Yesterday',
    'this_month' => 'This Month',

    // Loading
    'loading' => 'Loading...',
    'please_wait' => 'Please wait...',

    // General helpers
    'general' => [
        'unknown' => 'Unknown',
        'unknown_region' => 'Unknown Region',
        'optional' => 'Optional',
        'system' => 'System',
    ],

    // Exports
    'exports' => [
        'households' => [
            'national_id' => 'National ID',
            'head_name' => 'Head Name',
            'region' => 'Region',
            'address' => 'Address',
            'housing_type' => 'Housing Type',
            'primary_phone' => 'Primary Phone',
            'secondary_phone' => 'Secondary Phone',
            'status' => 'Status',
            'members_count' => 'Members Count',
            'member_names' => 'Member Names',
            'registered_date' => 'Registered Date',
        ],
        'distributions' => [
            'date' => 'Date',
            'program' => 'Program',
            'national_id' => 'National ID',
            'head_name' => 'Head Name',
            'region' => 'Region',
            'phone' => 'Phone',
            'recorded_by' => 'Recorded By',
            'notes' => 'Notes',
        ],
    ],

];
