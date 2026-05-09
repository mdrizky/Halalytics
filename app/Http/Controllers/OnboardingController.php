<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OnboardingStep;
use App\Models\ProductModel;
use App\Models\ScanModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OnboardingController extends Controller
{
    /**
     * 🎯 Get onboarding progress for user
     */
    public function getProgress(): JsonResponse
    {
        $user = Auth::user();
        $steps = $this->getOnboardingSteps();
        $completedSteps = [];
        
        foreach ($steps as $stepKey => $step) {
            $isCompleted = $this->checkStepCompletion($user, $stepKey);
            $completedSteps[$stepKey] = $isCompleted;
        }
        
        $totalSteps = count($steps);
        $completedCount = count(array_filter($completedSteps));
        $progressPercentage = $totalSteps > 0 ? ($completedCount / $totalSteps) * 100 : 0;
        
        return response()->json([
            'success' => true,
            'data' => [
                'progress_percentage' => round($progressPercentage, 1),
                'completed_steps' => $completedCount,
                'total_steps' => $totalSteps,
                'steps' => array_map(function($stepKey, $step) use ($completedSteps) {
                    return array_merge($step, [
                        'key' => $stepKey,
                        'completed' => $completedSteps[$stepKey] ?? false
                    ]);
                }, array_keys($steps), $steps),
                'next_step' => $this->getNextStep($completedSteps, $steps),
                'is_completed' => $progressPercentage >= 100,
            ]
        ]);
    }
    
    /**
     * ✅ Mark onboarding step as completed
     */
    public function completeStep(Request $request): JsonResponse
    {
        $request->validate([
            'step_key' => 'required|string|in:' . implode(',', array_keys($this->getOnboardingSteps()))
        ]);
        
        $user = Auth::user();
        $stepKey = $request->step_key;
        
        // Store completion in user metadata (using JSON field)
        $onboardingData = $user->onboarding_progress ?? [];
        $onboardingData[$stepKey] = [
            'completed' => true,
            'completed_at' => now()->toISOString(),
        ];
        
        $user->onboarding_progress = $onboardingData;
        $user->save();
        
        // Award points for completing onboarding steps
        $this->awardOnboardingPoints($user, $stepKey);
        
        return response()->json([
            'success' => true,
            'message' => 'Step completed successfully!',
            'data' => [
                'step_completed' => $stepKey,
                'points_awarded' => $this->getStepPoints($stepKey),
            ]
        ]);
    }
    
    /**
     * 🎁 Get onboarding rewards and achievements
     */
    public function getRewards(): JsonResponse
    {
        $user = Auth::user();
        $onboardingData = $user->onboarding_progress ?? [];
        
        $achievements = [];
        $totalPoints = 0;
        
        foreach ($this->getOnboardingSteps() as $stepKey => $step) {
            if (isset($onboardingData[$stepKey]['completed'])) {
                $points = $this->getStepPoints($stepKey);
                $totalPoints += $points;
                
                $achievements[] = [
                    'step' => $stepKey,
                    'title' => $step['title'],
                    'description' => $step['description'],
                    'points' => $points,
                    'completed_at' => $onboardingData[$stepKey]['completed_at'],
                    'icon' => $step['icon'] ?? '✅',
                ];
            }
        }
        
        // Check for milestone achievements
        $milestones = $this->getMilestoneAchievements($totalPoints);
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_points' => $totalPoints,
                'achievements' => $achievements,
                'milestones' => $milestones,
                'level' => $this->getUserLevel($totalPoints),
                'next_level_points' => $this->getNextLevelPoints($totalPoints),
            ]
        ]);
    }
    
    /**
     * 📋 Get all onboarding steps definition
     */
    private function getOnboardingSteps(): array
    {
        return [
            'profile_complete' => [
                'title' => 'Complete Your Profile',
                'description' => 'Add your personal information to get personalized recommendations',
                'icon' => '👤',
                'points' => 10,
                'action' => 'profile',
                'priority' => 1,
            ],
            'first_scan' => [
                'title' => 'Scan Your First Product',
                'description' => 'Use the scanner to check if a product is halal',
                'icon' => '📷',
                'points' => 20,
                'action' => 'scanner',
                'priority' => 2,
            ],
            'add_favorite' => [
                'title' => 'Add to Favorites',
                'description' => 'Save products you frequently use for quick access',
                'icon' => '⭐',
                'points' => 15,
                'action' => 'favorites',
                'priority' => 3,
            ],
            'explore_categories' => [
                'title' => 'Explore Categories',
                'description' => 'Browse different product categories',
                'icon' => '📂',
                'points' => 10,
                'action' => 'categories',
                'priority' => 4,
            ],
            'read_article' => [
                'title' => 'Read Health Article',
                'description' => 'Learn about halal and healthy lifestyle',
                'icon' => '📖',
                'points' => 15,
                'action' => 'articles',
                'priority' => 5,
            ],
            'share_app' => [
                'title' => 'Share Halalytics',
                'description' => 'Help others discover halal products',
                'icon' => '📤',
                'points' => 25,
                'action' => 'share',
                'priority' => 6,
            ],
            'enable_notifications' => [
                'title' => 'Enable Notifications',
                'description' => 'Get updates about new halal products and tips',
                'icon' => '🔔',
                'points' => 10,
                'action' => 'settings',
                'priority' => 7,
            ],
            'rate_app' => [
                'title' => 'Rate Our App',
                'description' => 'Help us improve with your feedback',
                'icon' => '⭐',
                'points' => 20,
                'action' => 'rate',
                'priority' => 8,
            ],
        ];
    }
    
    /**
     * 🔍 Check if a specific step is completed
     */
    private function checkStepCompletion(User $user, string $stepKey): bool
    {
        // Check stored completion first
        $onboardingData = $user->onboarding_progress ?? [];
        if (isset($onboardingData[$stepKey]['completed'])) {
            return true;
        }
        
        // Dynamic checking based on user data
        switch ($stepKey) {
            case 'profile_complete':
                return !empty($user->full_name) && 
                       !empty($user->email) && 
                       !empty($user->phone) &&
                       $user->email_verified_at;
                       
            case 'first_scan':
                return $user->scans()->count() > 0;
                
            case 'add_favorite':
                return $user->favorites()->count() > 0;
                
            case 'explore_categories':
                // Check if user has viewed at least 3 different categories
                return $user->scans()
                    ->with('product.kategori')
                    ->get()
                    ->pluck('product.kategori_id')
                    ->filter()
                    ->unique()
                    ->count() >= 3;
                
            case 'read_article':
                // This would require tracking article views
                // For now, check if user has been active for more than 1 day
                return $user->created_at->diffInDays(now()) >= 1;
                
            case 'enable_notifications':
                return $user->notif_enabled ?? false;
                
            default:
                return false;
        }
    }
    
    /**
     * 🎯 Get next incomplete step
     */
    private function getNextStep(array $completedSteps, array $allSteps): ?array
    {
        foreach ($allSteps as $stepKey => $step) {
            if (!($completedSteps[$stepKey] ?? false)) {
                return array_merge($step, ['key' => $stepKey]);
            }
        }
        return null;
    }
    
    /**
     * 🏆 Award points for completing steps
     */
    private function awardOnboardingPoints(User $user, string $stepKey): void
    {
        $points = $this->getStepPoints($stepKey);
        
        // Add points to user's total (assuming there's a points system)
        $user->increment('onboarding_points', $points);
        
        // Log the achievement
        activity('onboarding_completed')
            ->by($user)
            ->withProperties([
                'step' => $stepKey,
                'points' => $points,
            ])
            ->log("Completed onboarding step: {$stepKey}");
    }
    
    /**
     * 💎 Get points for a step
     */
    private function getStepPoints(string $stepKey): int
    {
        $steps = $this->getOnboardingSteps();
        return $steps[$stepKey]['points'] ?? 0;
    }
    
    /**
     * 🎖️ Get milestone achievements
     */
    private function getMilestoneAchievements(int $totalPoints): array
    {
        $milestones = [
            50 => ['title' => 'Beginner', 'description' => 'Started your halal journey', 'icon' => '🌱'],
            100 => ['title' => 'Explorer', 'description' => 'Learning and growing', 'icon' => '🌿'],
            200 => ['title' => 'Expert', 'description' => 'Halal knowledge master', 'icon' => '🌳'],
            500 => ['title' => 'Ambassador', 'description' => 'Spreading halal awareness', 'icon' => '🌟'],
        ];
        
        $achievedMilestones = [];
        foreach ($milestones as $requiredPoints => $milestone) {
            if ($totalPoints >= $requiredPoints) {
                $achievedMilestones[] = $milestone;
            }
        }
        
        return $achievedMilestones;
    }
    
    /**
     * 📊 Get user level based on points
     */
    private function getUserLevel(int $points): string
    {
        if ($points >= 500) return 'Ambassador';
        if ($points >= 200) return 'Expert';
        if ($points >= 100) return 'Explorer';
        if ($points >= 50) return 'Beginner';
        return 'Newcomer';
    }
    
    /**
     * 🎯 Get points needed for next level
     */
    private function getNextLevelPoints(int $currentPoints): int
    {
        if ($currentPoints < 50) return 50 - $currentPoints;
        if ($currentPoints < 100) return 100 - $currentPoints;
        if ($currentPoints < 200) return 200 - $currentPoints;
        if ($currentPoints < 500) return 500 - $currentPoints;
        return 0; // Max level
    }
}
