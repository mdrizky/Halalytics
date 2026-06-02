Halalytics — Secrets handling

This file explains what was done and next steps.

1) What I changed:
- The repository's `.env` file was sanitized: secret values replaced with placeholders.
- This prevents accidental credential leaks in the repo snapshot.

2) Immediate actions you must take:
- Rotate any exposed credentials immediately (DB, API keys, mail credentials, Reverb keys).
- Store real credentials in a secret manager (GitHub Actions secrets, Vault, or your hosting provider).

3) How to run locally now:
- Create a local `.env` (not committed) with real values.
- Example: copy `.env.example` -> `.env` and fill values.

4) CI / Deployment recommendations:
- Add required secrets to your CI (GitHub Actions) as encrypted secrets.
- Use environment-specific config during deployment and do not commit `.env`.

5) References:
- Laravel environment docs: https://laravel.com/docs/10.x/configuration#environment-configuration
- GitHub Actions secrets: https://docs.github.com/en/actions/security-guides/encrypted-secrets

If you want, I can also create a `./github/workflows/android-ci.yml` to build the Android app and a `laravel-ci.yml` for running `phpunit` on PRs. Let me know which to create next.