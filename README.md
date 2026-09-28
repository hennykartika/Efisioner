# Efisioner

Efisioner is a web platform for research questionnaires. Researchers publish a
multiple-choice survey, set how many respondents they need and how much each
respondent earns, and pay for it through Midtrans. Respondents answer open
surveys and collect the reward in their balance.

## Features

- **Researchers** create a survey, add questions (two to four options each),
  pay the reward pool plus a platform fee, and follow the results: a chart per
  question, the list of respondents with their demographic details, and a
  printable report.
- **Respondents** register once, answer any open survey they have not answered
  yet, and see every reward in their balance history.
- **Administrators** see all surveys, can close a live survey and can delete
  unpaid drafts.

A survey only goes live after Midtrans confirms the payment, and each
respondent can be paid once per survey. Both rules are enforced inside
database transactions, not just in the UI.

## Stack

- PHP 8.2 or newer, no framework. Composer is used for autoloading and tests.
- MySQL 8.0.16+ or MariaDB 10.6+ through PDO.
- Bootstrap 5.3 and Chart.js 4 from jsDelivr, pinned with Subresource Integrity.
- Midtrans Snap (redirect flow) for payments.

## Getting started

```bash
git clone https://github.com/hennykartika/Efisioner.git
cd Efisioner
composer install
cp .env.example .env
```

Create the database and a restricted account for the app. Edit the password in
`database/app-user.sql` first.

```bash
mysql -u root -p -e "CREATE DATABASE efisioner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -p efisioner < database/schema.sql
mysql -u root -p < database/app-user.sql
```

Fill in `.env`. For local work set `APP_ENV=local` and, if you want stack
traces in the browser, `APP_DEBUG=true`. Then start the development server:

```bash
composer serve        # http://localhost:8000
```

Register an account in the browser and make it an administrator:

```bash
php bin/make-admin.php <username>
```

### Trying it without Midtrans keys

Payments need Midtrans sandbox keys. If you do not have them yet, you can
publish a draft by recording a manual payment from the command line:

```bash
php bin/mark-survey-paid.php <survey-id> <reference>
```

The same command is meant for real reconciliation, for example a bank
transfer that was checked by hand. It is only available from the CLI.

## Payments

1. Create a Midtrans account and copy the sandbox **Server Key** into
   `MIDTRANS_SERVER_KEY`.
2. In the Midtrans dashboard, set the payment notification URL to
   `https://<your-domain>/payments/midtrans/notification`.
3. Set `MIDTRANS_IS_PRODUCTION=true` and use the production key when going live.

How a payment flows:

- "Pay and publish" creates a `pending` row in `payments` with the order id,
  the amount and a snapshot of the target and reward, then redirects the
  researcher to the Midtrans payment page.
- Midtrans calls the notification URL. The app checks the `signature_key`,
  then asks the Midtrans status API for the real state of the order before
  changing anything. A notification alone never activates a survey.
- On `settlement` (or `capture` with `fraud_status=accept`) the survey is
  activated with the target and reward that were paid for, even if the draft
  was edited after checkout started. Amount mismatches are logged and ignored.
- The redirect back from Midtrans is not trusted either. It only triggers the
  same status check, which is also how local development works when the
  notification cannot reach your machine.

## Deployment

- Point the web server's document root at `public/`. Nothing else in the
  repository should be reachable over HTTP. The root `.htaccess` denies all
  requests as a fallback for shared hosting where the document root cannot be
  changed; in that case the site lives under `/public` and `APP_URL` must
  include it.
- Serve the site over HTTPS. When `APP_URL` starts with `https://`, session
  cookies are marked `Secure` and HSTS is sent.
- Use `APP_ENV=production` and `APP_DEBUG=false`. The app refuses to start
  with debug enabled in production.
- Recommended `php.ini` settings: `display_errors=Off`, `log_errors=On`,
  `expose_php=Off`.
- Keep MySQL off the public internet (bind it to `127.0.0.1` or a private
  network) and connect with the account from `database/app-user.sql`, which
  can only read and write rows.
- Behind a reverse proxy or CDN, make the web server restore the client IP
  (for example `mod_remoteip`), otherwise login throttling sees every user as
  the same address.

Example Nginx server block:

```nginx
server {
    listen 443 ssl;
    server_name efisioner.example;
    root /var/www/efisioner/public;
    index index.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ /\.(?!well-known/) {
        deny all;
    }

    location = /index.php {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ \.php$ {
        return 404;
    }
}
```

## Tests

```bash
composer test
composer lint
```

Unit tests need nothing else. Integration tests run against a real
MySQL/MariaDB database and **empty it on every run**, so the database name must
end in `_test` (the suite refuses to run otherwise). Connection settings come
from the `TEST_DB_*` variables in `phpunit.xml.dist` and can be overridden in
the environment.

GitHub Actions runs the linter and the test suite on PHP 8.2 and 8.4 against
MySQL 8, and scans the full git history for leaked secrets with gitleaks.

## Project layout

```
bin/            CLI tools (make-admin, mark-survey-paid)
config/         route table
database/       schema and the least-privilege database account
public/         document root: front controller and static assets
src/
  Controller/   HTTP controllers, one per area
  Domain/       entities, enums and business rules
  Http/         request, response, router, session
  Payment/      payment gateway interface and the Midtrans client
  Repository/   all SQL lives here, always with bound parameters
  Security/     auth, CSRF, password hashing, login throttling, headers, access policy
  Service/      multi-step operations that run in a transaction
  Validation/   input validation
  View/         template renderer
templates/      PHP templates; every value is escaped with e()
tests/          unit and integration tests
```

## Security

| Concern | How it is handled |
| --- | --- |
| Secrets | Only in `.env` (git-ignored) or real environment variables. No keys in the code. gitleaks runs in CI. |
| SQL injection | PDO with native prepared statements everywhere. The only dynamic SQL uses fixed column lists. |
| XSS | All output goes through `e()`. No inline scripts, a strict Content-Security-Policy, and chart data passed as escaped JSON attributes. |
| CSRF | A per-session token on every POST. The Midtrans notification is the only exception and is verified by signature plus a status call. |
| Passwords | bcrypt (cost 12), never shown again after registration, login timing is the same for unknown usernames. |
| Brute force | 5 failed logins per account and IP, 20 per IP, in a 15 minute window. |
| Sessions | HttpOnly, SameSite=Lax, Secure over HTTPS, strict mode, new id after login and password change, 2 hour idle timeout. Only the user id is stored. |
| Access control | Checked on the server for every request. Other people's surveys answer 404; everything under `/admin` requires the admin role. |
| Errors | Users see a generic page; details go to the server log. Debug output is impossible in production. |
| Uploads | The application does not accept file uploads. |

## Known limitations

- There is no "forgot password" email yet, because it needs a mail provider.
  Signed-in users can change their password.
- Reward payouts to respondents are not automated. The reward ledger already
  supports negative entries for when withdrawals are added.
- Refunds and chargebacks are written to the log for manual handling; they do
  not change a published survey automatically.
- Registration is not rate limited yet.
