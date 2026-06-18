# Phase 6 Production Readiness Notes

## Export packages
- Excel exports currently use the queued local export writer. For rich XLSX formatting install Laravel Excel: `composer require maatwebsite/excel`.
- PDF exports currently write a printable export artifact. For rendered PDF output install DomPDF: `composer require barryvdh/laravel-dompdf`, or keep using browser print views.

## Private/enterprise deployment
Use the same codebase with environment flags:
- `APP_EDITION=saas|private|enterprise`
- `SAAS_ENABLED=false` to disable public signup.
- `BILLING_ENABLED=false` to hide billing UI and disable billing-dependent flows.
- `MULTI_TENANT_ENABLED=false` to resolve a default workspace for private installs.
- `LOCAL_AI_ENABLED=true` to expose local AI settings when implemented.

## Security and storage
Report exports are stored on the private `local` disk under workspace-scoped paths and are downloaded only through permission-checked controllers. Do not symlink export directories into `public/storage`.

## Backups and restore placeholder
Enterprise deployments should define encrypted database backups, private storage backups, restore drills, and retention policies before launch.

## 2FA placeholder
The user security settings surface should reserve space for a future two-factor authentication enrollment workflow.
