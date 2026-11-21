# Render Free interview demonstration

This deployment runs Apache/PHP and a small loopback-only MariaDB instance in one disposable Free web-service container. There is no separately billed database or persistent disk. It uses the repository's fictional dataset and its deliberately public demo-role accounts.

The `render.yaml` Blueprint selects **Free**, this Dockerfile, a generated application key and `/site/login` as the health endpoint. Render supplies `PORT` and `RENDER_EXTERNAL_URL`; the entrypoint derives the canonical HTTPS application URL. Database credentials are randomly generated in memory at startup and are not logged or stored in Git.

**Important behavior:** Free service inactivity, cold starts, redeploys or replacement of the ephemeral container can reset data and uploaded files back to the included fictional snapshot. The URL may take roughly a minute to wake after idle. This is an interview demonstration, not durable school hosting. No real school data or paid provider credentials should be entered.

All eight representative roles are available for business workflows. Deployment-only Apache restrictions block add-on/source installation and updating, database backup/restore and remote multi-branch connection administration. The original local application's source and features are not changed by these runtime guards.

The database is bound only to `127.0.0.1`; the public service exposes Apache on Render's web port. The application account has privileges only on its disposable demo schema. Application source remains root-owned; PHP can write only required upload/cache/log/temp/session/captcha directories. PHP execution inside uploads is denied.

The service is configured for Render Free's constrained memory: four Apache workers, a 128 MB per-request PHP limit and a 32 MB InnoDB pool. Concurrent expensive PDFs or imports may still exceed available resources and must be tested on the hosted instance.

The container build and hosted behavior must be verified before describing this deployment as live. The actual Render login, repository authorization and deployed URL are supplied by the hosting workflow, not by this source file.
