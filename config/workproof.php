<?php
return [
    'edition' => env('APP_EDITION', 'saas'),
    'saas_enabled' => (bool) env('SAAS_ENABLED', true),
    'billing_enabled' => (bool) env('BILLING_ENABLED', true),
    'multi_tenant_enabled' => (bool) env('MULTI_TENANT_ENABLED', true),
    'local_ai_enabled' => (bool) env('LOCAL_AI_ENABLED', false),
    'domains' => ['main'=>env('APP_MAIN_DOMAIN','naxasworkproof.com'),'subdomain_root'=>env('APP_SUBDOMAIN_ROOT','app.naxasworkproof.com'),'admin'=>env('APP_ADMIN_DOMAIN','admin.naxasworkproof.com')],
    'defaults' => ['workspace_status'=>env('DEFAULT_WORKSPACE_STATUS','trial'),'trial_days'=>(int) env('DEFAULT_TRIAL_DAYS',14),'timezone'=>env('DEFAULT_TIMEZONE','Asia/Dhaka'),'currency'=>env('DEFAULT_CURRENCY','BDT')],
    'features' => ['saas'=> (bool) env('SAAS_ENABLED', true), 'billing'=>(bool) env('BILLING_ENABLED', true), 'multi_tenant'=>(bool) env('MULTI_TENANT_ENABLED', true), 'local_ai'=>(bool) env('LOCAL_AI_ENABLED', false)],
    'tasks' => ['max_attachment_kb' => env('WORKPROOF_TASK_ATTACHMENT_MAX_KB', 10240)],
];
