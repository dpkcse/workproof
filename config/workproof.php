<?php

$mainDomain = env('SAAS_MAIN_DOMAIN', env('APP_DOMAIN', env('APP_MAIN_DOMAIN', 'workproof.com')));
$tenantDomainSuffix = env('TENANT_DOMAIN_SUFFIX', env('APP_SUBDOMAIN_ROOT', $mainDomain));
$adminDomain = env('ADMIN_DOMAIN', env('APP_ADMIN_DOMAIN', 'admin.'.$mainDomain));

return [
    'edition' => env('APP_EDITION', 'saas'),
    'saas_enabled' => (bool) env('SAAS_ENABLED', true),
    'billing_enabled' => (bool) env('BILLING_ENABLED', true),
    'multi_tenant_enabled' => (bool) env('MULTI_TENANT_ENABLED', true),
    'local_ai_enabled' => (bool) env('LOCAL_AI_ENABLED', false),
    'domains' => [
        'main' => $mainDomain,
        'subdomain_root' => $tenantDomainSuffix,
        'tenant_suffix' => $tenantDomainSuffix,
        'admin' => $adminDomain,
    ],
    'defaults' => [
        'workspace_status' => env('DEFAULT_WORKSPACE_STATUS', 'trial'),
        'trial_days' => (int) env('DEFAULT_TRIAL_DAYS', 14),
        'timezone' => env('DEFAULT_TIMEZONE', 'Asia/Dhaka'),
        'currency' => env('DEFAULT_CURRENCY', 'BDT'),
    ],
    'features' => [
        'saas' => (bool) env('SAAS_ENABLED', true),
        'billing' => (bool) env('BILLING_ENABLED', true),
        'multi_tenant' => (bool) env('MULTI_TENANT_ENABLED', true),
        'local_ai' => (bool) env('LOCAL_AI_ENABLED', false),
    ],
    'tasks' => ['max_attachment_kb' => env('WORKPROOF_TASK_ATTACHMENT_MAX_KB', 10240)],
];
