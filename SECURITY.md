# Security Policy

## Supported Versions

Only the latest commit on the `main` branch receives security updates. There are no LTS or versioned releases at this time.

| Version       | Supported |
| ------------- | --------- |
| main (latest) | ✅ Active |

## Reporting a Vulnerability

If you discover a security vulnerability, **do not open a public issue**. Instead, email the maintainer directly:

**Email:** munthasirrahman@gmail.com

Please include the following details in your report:

- Type of vulnerability
- Steps to reproduce
- Affected endpoints or files
- Any proof-of-concept code (if applicable)

You should receive a response within **48 hours**. If the issue is confirmed, a fix will be prioritized and deployed as soon as possible.

## Security Practices

- All state-changing routes are protected with CSRF tokens
- Authentication uses session-based HTTP-only cookies
- Role-based middleware restricts access (Superadmin / Admin / Staff)
- Rate limiting is applied to login, stock, and checkout endpoints
- SQL injection is prevented via Eloquent ORM (no raw queries in business logic)
- Blade templates auto-escape output; `@json()` is used for inline JavaScript data binding
- Sensitive data (tracking pixel tokens) are encrypted at rest using Laravel's `encrypt()`
- Stock mutations use pessimistic locking (`lockForUpdate`) within database transactions
- Webhook endpoints verify HMAC signatures before processing payloads

## Reported Issues

| ID  | Issue                       | Status |
| --- | --------------------------- | ------ |
| —   | No open security advisories | ✅     |
