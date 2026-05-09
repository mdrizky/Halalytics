<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\Notification;
use App\Models\ScanModel;
use App\Models\ReportModel;
use App\Models\OCRProduct;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = app(NotificationService::class);
        
        // Mock broadcasting to avoid actual WebSocket connections during tests
        Broadcast::fake();
    }

    /**
     * 📢 Test sendNotification creates notification
     */
    public function test_send_notification_creates_notification(): void
    {
        $user = User::factory()->create();
        $type = 'test';
        $title = 'Test Notification';
        $message = 'Test message';
        $data = ['key' => 'value'];

        $notification = $this->notificationService->sendNotification($user, $type, $title, $message, $data);

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals($user->id_user, $notification->user_id);
        $this->assertEquals($type, $notification->type);
        $this->assertEquals($title, $notification->title);
        $this->assertEquals($message, $notification->message);
        $this->assertEquals($data, $notification->data);
        $this->assertFalse($notification->read);

        // Verify broadcast was called
        Broadcast::assertDispatched(\App\Events\NotificationSent::class);
    }

    /**
     * 🎯 Test sendScanNotification
     */
    public function test_send_scan_notification(): void
    {
        $user = User::factory()->create();
        $scan = ScanModel::factory()->create([
            'user_id' => $user->id_user,
            'nama_produk' => 'Test Product',
            'status_halal' => 'halal'
        ]);

        $this->notificationService->sendScanNotification($user, $scan);

        $notification = Notification::where('user_id', $user->id_user)->first();
        $this->assertNotNull($notification);
        $this->assertEquals('scan_completed', $notification->type);
        $this->assertStringContains('✅', $notification->title);
        $this->assertStringContains('Test Product', $notification->message);
    }

    /**
     * 📝 Test sendReportNotification
     */
    public function test_send_report_notification(): void
    {
        $user = User::factory()->create();
        $product = \App\Models\ProductModel::factory()->create();
        $report = ReportModel::factory()->create([
            'user_id' => $user->id_user,
            'product_id' => $product->id_product
        ]);

        $this->notificationService->sendReportNotification($user, $report);

        $notification = Notification::where('user_id', $user->id_user)->first();
        $this->assertNotNull($notification);
        $this->assertEquals('report_submitted', $notification->type);
        $this->assertStringContains('📝', $notification->title);
    }

    /**
     * 📷 Test sendOCRNotification
     */
    public function test_send_ocr_notification(): void
    {
        $user = User::factory()->create();
        $ocrProduct = OCRProduct::factory()->create([
            'user_id' => $user->id_user,
            'product_name' => 'OCR Product',
            'halal_status' => 'halal'
        ]);

        $this->notificationService->sendOCRNotification($user, $ocrProduct);

        $notification = Notification::where('user_id', $user->id_user)->first();
        $this->assertNotNull($notification);
        $this->assertEquals('ocr_completed', $notification->type);
        $this->assertStringContains('✅', $notification->title);
        $this->assertStringContains('OCR Product', $notification->message);
    }

    /**
     * 🏆 Test sendAchievementNotification
     */
    public function test_send_achievement_notification(): void
    {
        $user = User::factory()->create(['onboarding_points' => 100]);
        $achievement = 'First Scan';
        $points = 10;

        $this->notificationService->sendAchievementNotification($user, $achievement, $points);

        $notification = Notification::where('user_id', $user->id_user)->first();
        $this->assertNotNull($notification);
        $this->assertEquals('achievement_unlocked', $notification->type);
        $this->assertStringContains('🏆', $notification->title);
        $this->assertStringContains("$points points", $notification->message);
        $this->assertEquals($points, $notification->data['points']);
    }

    /**
     * 🎯 Test sendOnboardingNotification
     */
    public function test_send_onboarding_notification(): void
    {
        $user = User::factory()->create([
            'onboarding_progress' => [
                'step1' => ['completed' => true],
                'step2' => ['completed' => true],
            ]
        ]);
        $step = 'Complete Profile';
        $points = 5;

        $this->notificationService->sendOnboardingNotification($user, $step, $points);

        $notification = Notification::where('user_id', $user->id_user)->first();
        $this->assertNotNull($notification);
        $this->assertEquals('onboarding_progress', $notification->type);
        $this->assertStringContains('✅', $notification->title);
        $this->assertStringContains($step, $notification->message);
        $this->assertArrayHasKey('progress_percentage', $notification->data);
    }

    /**
     * 🔔 Test sendSystemNotification
     */
    public function test_send_system_notification(): void
    {
        // Create admin users
        $admin1 = User::factory()->create(['role' => 'admin', 'active' => true]);
        $admin2 = User::factory()->create(['role' => 'admin', 'active' => true]);
        $regularUser = User::factory()->create(['role' => 'user', 'active' => true]);

        $title = 'System Maintenance';
        $message = 'System will be down for maintenance';
        $data = ['priority' => 'high'];

        $this->notificationService->sendSystemNotification($title, $message, $data);

        // Only admins should receive notifications
        $adminNotifications = Notification::whereIn('user_id', [$admin1->id_user, $admin2->id_user])->get();
        $userNotifications = Notification::where('user_id', $regularUser->id_user)->get();

        $this->assertEquals(2, $adminNotifications->count());
        $this->assertEquals(0, $userNotifications->count());

        foreach ($adminNotifications as $notification) {
            $this->assertEquals('system', $notification->type);
            $this->assertEquals($title, $notification->title);
            $this->assertEquals($message, $notification->message);
            $this->assertEquals('high', $notification->data['priority']);
        }
    }

    /**
     * 📊 Test getUserNotifications
     */
    public function test_get_user_notifications(): void
    {
        $user = User::factory()->create();
        
        // Create notifications
        Notification::factory()->count(3)->create(['user_id' => $user->id_user]);
        Notification::factory()->count(2)->create(['user_id' => User::factory()->create()->id_user]); // Other user's notifications

        $notifications = $this->notificationService->getUserNotifications($user);

        $this->assertCount(3, $notifications);
        $this->assertEquals('desc', $notifications[0]['created_at'] <=> $notifications[2]['created_at']);
    }

    /**
     * 🔢 Test getUnreadCount
     */
    public function test_get_unread_count(): void
    {
        $user = User::factory()->create();
        
        // Create mix of read and unread notifications
        Notification::factory()->count(3)->create(['user_id' => $user->id_user, 'read' => false]);
        Notification::factory()->count(2)->create(['user_id' => $user->id_user, 'read' => true]);

        $unreadCount = $this->notificationService->getUnreadCount($user);

        $this->assertEquals(3, $unreadCount);
    }

    /**
     * ✅ Test markAsRead
     */
    public function test_mark_as_read(): void
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id_user, 'read' => false]);

        $result = $this->notificationService->markAsRead($notification->id, $user);

        $this->assertTrue($result);
        
        $notification->refresh();
        $this->assertTrue($notification->read);
    }

    /**
     * ✅ Test markAsRead with wrong user
     */
    public function test_mark_as_read_wrong_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user1->id_user, 'read' => false]);

        $result = $this->notificationService->markAsRead($notification->id, $user2);

        $this->assertFalse($result);
        
        $notification->refresh();
        $this->assertFalse($notification->read);
    }

    /**
     * ✅ Test markAllAsRead
     */
    public function test_mark_all_as_read(): void
    {
        $user = User::factory()->create();
        
        // Create unread notifications
        Notification::factory()->count(5)->create(['user_id' => $user->id_user, 'read' => false]);

        $markedCount = $this->notificationService->markAllAsRead($user);

        $this->assertEquals(5, $markedCount);
        
        $unreadCount = Notification::where('user_id', $user->id_user)->where('read', false)->count();
        $this->assertEquals(0, $unreadCount);
    }

    /**
     * 🗑️ Test deleteNotification
     */
    public function test_delete_notification(): void
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id_user]);

        $result = $this->notificationService->deleteNotification($notification->id, $user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    /**
     * 🗑️ Test deleteNotification with wrong user
     */
    public function test_delete_notification_wrong_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user1->id_user]);

        $result = $this->notificationService->deleteNotification($notification->id, $user2);

        $this->assertFalse($result);
        $this->assertDatabaseHas('notifications', ['id' => $notification->id]);
    }

    /**
     * 🧹 Test cleanOldNotifications
     */
    public function test_clean_old_notifications(): void
    {
        // Create notifications with different ages
        $oldNotification = Notification::factory()->create(['created_at' => now()->subDays(35)]);
        $recentNotification = Notification::factory()->create(['created_at' => now()->subDays(10)]);

        $deleted = $this->notificationService->cleanOldNotifications(30);

        $this->assertEquals(1, $deleted);
        $this->assertDatabaseMissing('notifications', ['id' => $oldNotification->id]);
        $this->assertDatabaseHas('notifications', ['id' => $recentNotification->id]);
    }

    /**
     * 📈 Test getNotificationStats
     */
    public function test_get_notification_stats(): void
    {
        // Create various notifications
        Notification::factory()->count(5)->create(['read' => false]);
        Notification::factory()->count(3)->create(['read' => true]);
        Notification::factory()->count(2)->create(['type' => 'scan_completed', 'read' => false]);
        Notification::factory()->count(1)->create(['type' => 'report_submitted', 'read' => false]);

        $stats = $this->notificationService->getNotificationStats();

        $this->assertEquals(11, $stats['total_notifications']);
        $this->assertEquals(8, $stats['unread_notifications']);
        $this->assertArrayHasKey('by_type', $stats);
        $this->assertArrayHasKey('notifications_today', $stats);
        $this->assertArrayHasKey('notifications_this_week', $stats);
    }
}
