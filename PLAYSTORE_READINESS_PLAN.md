# Halalytics Play Store Readiness Plan

Status date: 2026-05-21

## 1) Build & Quality Gates (must pass)

### Android CI gates
- [ ] `./gradlew clean assembleRelease`
- [ ] `./gradlew lint`
- [ ] `./gradlew test`
- [ ] `./gradlew connectedAndroidTest` (on at least 1 physical + 1 emulator profile)

### Backend CI gates
- [ ] `php artisan test`
- [ ] `php artisan migrate:fresh --seed` in CI database
- [ ] Static checks (phpstan/pint if enabled in main monorepo)

### Release blockers
- [ ] Zero compile errors
- [ ] Zero crash on login → home → scan → detail → back flow
- [ ] API timeout/retry behavior verified for AI chat and product detail

## 2) Android Production Config

- [ ] `minSdk`, `targetSdk`, `compileSdk` aligned with latest Play requirements
- [ ] Release signing configured (upload key + Play App Signing)
- [ ] `versionCode` and `versionName` bumped
- [ ] R8/ProGuard rules validated for CameraX + ML Kit + Retrofit + coroutines
- [ ] Network security config reviewed (no cleartext in production)
- [ ] Crash reporting enabled (Firebase Crashlytics/Sentry)

## 3) Privacy, Security & Policy Compliance

- [ ] Camera permission rationale shown in-app
- [ ] Privacy Policy URL publicly reachable and linked in app + Play listing
- [ ] Data Safety form filled accurately (camera, analytics, crash data, account data)
- [ ] Account deletion flow documented and available if account system is active
- [ ] Sensitive logs sanitized (no tokens/PII in logs)
- [ ] API auth token storage hardened (EncryptedSharedPreferences / secure storage in real app)

## 4) UX/Functional Acceptance (critical flows)

- [ ] Splash → Login → Home works reliably
- [ ] Scan real barcode using CameraX + ML Kit on low/mid/high-end devices
- [ ] Product detail fallback states: loading, empty, network error, malformed payload
- [ ] AI chat retry and network timeout handling verified
- [ ] Back stack correctness across all menu destinations
- [ ] Bahasa Indonesia + English localization verified on all user-facing text

## 5) Play Console Submission Package

- [ ] App icon, feature graphic, screenshots (phone + 7/10 inch if needed)
- [ ] Short description + full description finalized
- [ ] Content rating completed
- [ ] Target audience & ads declaration completed
- [ ] Closed testing track configured and passed
- [ ] Pre-launch report has no critical issues

## 6) Observability & Post-Release Guardrails

- [ ] Error budget and rollback plan defined
- [ ] Monitoring dashboard for API latency and app crashes
- [ ] Hotfix SOP documented
- [ ] Release checklist sign-off by Engineering + QA + Product

## Suggested Definition of Done (DoD)

A build can be called **"100% ready for Play Store release candidate"** only when:
1. All CI gates in sections 1 and 2 pass on the real monorepo.
2. Policy/security items in section 3 are completed and reviewed.
3. Functional acceptance in section 4 passes on real devices.
4. Play Console package in section 5 is complete.
5. Stakeholder sign-off in section 6 is captured.
