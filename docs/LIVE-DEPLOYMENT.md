# Live deployment: status and requirements

**No public live application URL has been deployed by this repository.** GitHub provides the source/download/documentation location. GitHub Pages cannot execute the PHP application or provide its MariaDB database.

The repository includes a local Docker Compose setup and a Windows launcher. A real interviewer-facing URL needs a PHP/container hosting account, a database, an HTTPS domain and deployment-environment verification.

## Required services

- PHP 8.2-compatible application runtime with Apache/rewrite support and the extensions required by the supplied application.
- A separate MySQL/MariaDB database, reachable privately by the application.
- Persistent storage for the database and uploaded files.
- Writable PDF temporary/session storage and a deliberate single-instance or shared-session strategy.
- An HTTPS domain, configured as the canonical `APP_URL`.

Set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `APP_URL` and `SMART_SCHOOL_APP_KEY` in the hosting provider's environment/secret controls. Use a dedicated database user scoped to the demonstration schema. Never publish a database port, root password, runtime `.env`, provider API secret or private key in Git.

The local Compose file intentionally binds its web port to `127.0.0.1`; starting it is not equivalent to publishing an Internet service. Adapt provider networking/ingress rather than assuming local port bindings create a hosted URL.

## Public interview-demo boundaries

Use a disposable, isolated fictional dataset. Do not connect the demonstration to a real school's database, production credentials or personal documents. The published local demo accounts are known to everyone who reads this repository.

Before publishing reusable role credentials, review and restrict maintenance routes for installation, add-on uploads, updates, SQL restore and backups. Hiding a menu does not disable its direct route. Keep source code read-only to the web process except for explicitly required runtime/upload directories, and prevent uploaded PHP/PHTML/PHAR execution.

A public Super Admin account must not grant visitors access to production-like infrastructure or unrestricted maintenance operations. Prefer a bounded demonstration role for business workflows, with owner-only maintenance administration. Review outbound/private networking for integrations such as Multi Branch.

Existing AJAX/CSRF behavior requires targeted compatibility and security checks; do not assume a global security-toggle change has been validated. Configure reverse-proxy trust and HTTPS cookies for the actual provider environment.

## Provider-independent deployment sequence

1. Create the PHP/Apache service and a private MySQL/MariaDB service in the chosen hosting account.
2. Configure fresh environment secrets and the application's canonical HTTPS URL.
3. Import the fictional `database.sql` into an empty demonstration database.
4. Attach persistent upload storage and configure required writable directories.
5. Apply private-path, uploaded-script and maintenance-route restrictions suitable for the public demo.
6. Verify service health, staff login, student/parent login and sibling selection.
7. Test attendance save, homework submission/evaluation, fee collection/receipt and library issue/return in that hosted environment.
8. Generate a normal marksheet, a custom progress card and a bulk PDF. Check uploaded files, QR assets and session persistence.
9. Publish the verified HTTPS URL and only the deliberate public-demo credentials. Record its hosting limitations and reset policy.

Docker configuration validation alone does not establish a successful image build or hosted deployment. Native Windows/browser checks do not substitute for these provider checks.

## Integrations and credentials

Firebase's actual service-account file is excluded from the public source. Start from its `.example.json` template and obtain your own provider credential through the relevant provider. Maps requires `GOOGLE_MAPS_API_KEY`. Payment gateways, Zoom, Google Meet and WhatsApp require their own accounts, callback URLs, permissions and test configuration.

Keep notifications and real transactions disabled during a public fictional demonstration unless the exact test destination and provider environment are deliberately configured. Do not expose provider secret screens to public demo visitors.

Once a hosting account and deployment destination are available, this checklist provides the concrete path to an interviewer-facing link. Until the hosted checks pass, share the repository as a local-run demonstration, not as a live application.
