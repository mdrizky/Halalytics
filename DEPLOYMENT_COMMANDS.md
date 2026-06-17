# Quick Deployment & Testing Commands

## Pre-Deployment Checklist

### 1. Verify All Code Compiles
```bash
php -l app/Http/Controllers/Admin/SystemHealthController.php
php -l app/Http/Controllers/Admin/StatsController.php
php -l app/Http/Controllers/Admin/AccessLogController.php
php -l app/Http/Controllers/Api/MealPlanController.php
php -l app/Http/Middleware/LogAccessMiddleware.php
php -l app/Http/Middleware/SetCacheHeaders.php
php -l app/Models/SyncConflict.php
php -l app/Models/MealPlan.php
php -l app/Models/AccessLog.php
```

### 2. Verify Routes Are Registered
```bash
php artisan route:list | grep -E "meal-plans|access-logs|health|dashboard/stats"
```

### 3. Check Migration Status
```bash
php artisan migrate:status | tail -10
```

## Deployment Steps

### Step 1: Pull Latest Code
```bash
git pull origin main
```

### Step 2: Install Dependencies
```bash
composer install --no-dev
```

### Step 3: Run Migrations
```bash
php artisan migrate --force
```

### Step 4: Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Step 5: Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: Verify Health Check
```bash
curl -s http://localhost/admin/health | jq .
```

## Testing Commands

### Test 1: Verify Promo Blogs Table
```bash
php artisan tinker
>>> DB::table('promo_blogs')->count()
=> 0
>>> exit
```

### Test 2: Test AI Chat Widget
```bash
# Should return promo.home with chat widget
curl -s http://localhost/ | grep "ai-chat-widget"
```

### Test 3: Test Role-Based Access
```bash
# Login as ahli_gizi and test
curl -s -H "Authorization: Bearer TOKEN" \
  http://localhost/api/nutrition/consultations/mine \
  | jq '.data[0].nutritionist_id'
```

### Test 4: Test File Upload
```bash
curl -X POST \
  -H "Authorization: Bearer TOKEN" \
  -F "attachment=@test.pdf" \
  -F "body=Test message" \
  -F "sender_role=user" \
  http://localhost/api/nutrition/consultations/1/messages
```

### Test 5: Test API Key Validation
```bash
curl -s http://localhost/admin/health | jq '.checks.gemini_api'
```

### Test 6: Test Dashboard Stats Endpoint
```bash
curl -s -H "Authorization: Bearer TOKEN" \
  http://localhost/admin/dashboard/stats | jq '.data'
```

### Test 7: Test Sync Conflict Resolution
```bash
curl -X POST \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "logs": [{
      "barcode": "1234567890",
      "product_name": "Test Product",
      "halal_status": "halal",
      "scanned_at": 1718033400000
    }]
  }' \
  http://localhost/api/sync/scans
```

### Test 8: Test Meal Plans
```bash
# Create meal plan
curl -X POST \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 2,
    "title": "Weekly Diet Plan",
    "meals": [{
      "day": 1,
      "meal_type": "breakfast",
      "items": [{"name": "Eggs", "quantity": "2"}]
    }],
    "duration_days": 7,
    "start_date": "2026-06-11"
  }' \
  http://localhost/api/nutrition/meal-plans
```

### Test 9: Test Access Logs
```bash
# View access logs
curl -s -H "Authorization: Bearer ADMIN_TOKEN" \
  "http://localhost/admin/access-logs/?resource_type=nutrition_consultation" \
  | jq '.data'

# View statistics
curl -s -H "Authorization: Bearer ADMIN_TOKEN" \
  "http://localhost/admin/access-logs/statistics/overview" \
  | jq '.data'
```

### Test 10: Test Cache Headers
```bash
# Check product endpoint cache headers
curl -s -I http://localhost/api/products/1 | grep -i cache-control

# Should show: Cache-Control: public, max-age=300
```

## Performance Monitoring

### Monitor Real-Time Dashboard
```bash
# Open browser dev tools Network tab
# Navigate to /admin
# Should see GET /admin/dashboard/stats every 5 seconds
# Should see <100ms response time
```

### Monitor Database Queries
```bash
# Enable query logging in .env
DB_LOG_QUERIES=true

# Check storage/logs/laravel.log for slow queries
tail -f storage/logs/laravel.log | grep "executed in"
```

### Check Cache Hit Rate
```bash
curl -s http://localhost/admin/cache/stats | jq '.data'
```

## Rollback Plan

### If Issues Found

```bash
# 1. Revert migrations
php artisan migrate:rollback

# 2. Revert code
git revert HEAD --no-edit

# 3. Clear caches
php artisan cache:clear

# 4. Restart services
php artisan queue:restart
```

## Production Monitoring

### Set Up Alerts For:

1. **Access Logs - Denied Requests**
```bash
# Monitor for suspicious access patterns
curl -s http://localhost/admin/access-logs/statistics/overview \
  | jq '.data.recent_denied'
```

2. **Health Check - API Key Status**
```bash
# Monitor for missing API keys
curl -s http://localhost/admin/health \
  | jq '.checks.gemini_api.status'
```

3. **Dashboard - Real-time Updates**
```bash
# Monitor polling success rate
# Should have <1% errors in application logs
```

4. **Sync Conflicts**
```bash
# Monitor for excessive conflicts
SELECT COUNT(*) FROM sync_conflicts 
WHERE resolved_at IS NOT NULL 
AND DATE(created_at) = CURDATE();
```

## Troubleshooting

### If Promo Blog Routes Return 404
```bash
php artisan route:clear
php artisan route:cache
php artisan migrate:status  # Verify migration ran
```

### If AI Chat Widget Not Appearing
```bash
# Check if layout includes component
grep -n "ai-chat-widget" resources/views/promo/layout.blade.php

# Check browser console for JavaScript errors
# Verify /ai/chat endpoint returns response
```

### If Role-Based Access Not Working
```bash
# Verify middleware is applied
php artisan route:list | grep nutrition

# Check user role in database
SELECT id_user, role FROM users WHERE id_user = 123;
```

### If File Upload Fails
```bash
# Check storage permissions
ls -la storage/app/consultations/

# Verify upload path exists
mkdir -p storage/app/consultations/attachments

# Set permissions
chmod -R 775 storage/
```

### If Dashboard Stats Endpoint Returns Error
```bash
# Verify StatsController exists
ls -la app/Http/Controllers/Admin/StatsController.php

# Check CacheService is working
php artisan tinker
>>> app(App\Services\CacheService::class)->getDashboardStats()
```

## Verification Checklist

- [ ] All migrations executed successfully
- [ ] Routes registered correctly
- [ ] Health check endpoint returns healthy status
- [ ] Promo blog routes accessible
- [ ] AI chat widget loads on promo pages
- [ ] Nutritionist can only see assigned consultations
- [ ] File attachments upload successfully
- [ ] Meal plans CRUD operations work
- [ ] Dashboard updates in real-time
- [ ] Access logs are being recorded
- [ ] Cache headers are present on API responses
- [ ] No PHP errors in logs
- [ ] No database errors in logs

## Performance Baseline

After deployment, establish baselines:

```bash
# Response Time Test
time curl -s http://localhost/admin/dashboard > /dev/null

# Concurrent Users Test
ab -n 1000 -c 10 http://localhost/api/products

# Cache Hit Rate
curl -s http://localhost/admin/cache/stats | jq '.data.hit_rate'

# Database Connection Count
curl -s http://localhost/admin/health | jq '.checks.database'
```

## Contact & Support

- **Emergency Issues**: Contact DevOps team
- **Questions**: Refer to PRODUCTION_FIXES_REPORT.md
- **Performance Issues**: Check application logs at storage/logs/laravel.log

