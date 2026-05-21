<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

/**
 * 🛡️ Enterprise Input Validation Service
 * Provides comprehensive input validation to prevent XSS, SQL injection, and other attacks
 */
class SecurityValidationService
{
    /**
     * Validate and sanitize email
     */
    public static function validateEmail(string $email): ?string
    {
        $email = trim(strtolower($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        // Additional checks
        if (strlen($email) > 255) {
            return null;
        }

        return $email;
    }

    /**
     * Validate and sanitize username
     */
    public static function validateUsername(string $username): ?string
    {
        $username = trim($username);

        // Check length
        if (strlen($username) < 3 || strlen($username) > 255) {
            return null;
        }

        // Allow only alphanumeric, underscores, and hyphens
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            return null;
        }

        return $username;
    }

    /**
     * Validate password strength
     */
    public static function validatePasswordStrength(string $password): array
    {
        $result = [
            'valid' => true,
            'score' => 0,
            'feedback' => [],
        ];

        // Minimum length
        if (strlen($password) < 8) {
            $result['valid'] = false;
            $result['feedback'][] = 'Password must be at least 8 characters';
        } else {
            $result['score'] += 20;
        }

        // Maximum length
        if (strlen($password) > 128) {
            $result['valid'] = false;
            $result['feedback'][] = 'Password must not exceed 128 characters';
        }

        // Uppercase letters
        if (preg_match('/[A-Z]/', $password)) {
            $result['score'] += 20;
        } else {
            $result['feedback'][] = 'Add uppercase letters for better security';
        }

        // Lowercase letters
        if (preg_match('/[a-z]/', $password)) {
            $result['score'] += 20;
        } else {
            $result['feedback'][] = 'Add lowercase letters for better security';
        }

        // Numbers
        if (preg_match('/[0-9]/', $password)) {
            $result['score'] += 20;
        } else {
            $result['feedback'][] = 'Add numbers for better security';
        }

        // Special characters
        if (preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $password)) {
            $result['score'] += 20;
        } else {
            $result['feedback'][] = 'Add special characters for better security';
        }

        return $result;
    }

    /**
     * Validate phone number
     */
    public static function validatePhoneNumber(string $phone): ?string
    {
        $phone = preg_replace('/[^0-9+()-]/', '', $phone);

        // Check length (7-15 digits is international standard)
        $digitsOnly = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digitsOnly) < 7 || strlen($digitsOnly) > 15) {
            return null;
        }

        return $phone;
    }

    /**
     * Validate URL
     */
    public static function validateUrl(string $url): ?string
    {
        $url = trim($url);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Only allow http and https
        $parsed = parse_url($url);
        if (!isset($parsed['scheme']) || !in_array($parsed['scheme'], ['http', 'https'])) {
            return null;
        }

        return $url;
    }

    /**
     * Sanitize HTML string (remove dangerous tags)
     */
    public static function sanitizeHtml(string $html): string
    {
        // Use allowlist approach - only allow specific tags
        $allowed = '<b><i><u><strong><em><p><br><ul><ol><li><a><img>';

        return strip_tags($html, $allowed);
    }

    /**
     * Escape HTML entities
     */
    public static function escapeHtml(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate JSON
     */
    public static function validateJson(string $json): ?array
    {
        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $decoded;
    }

    /**
     * Validate blood type
     */
    public static function validateBloodType(string $bloodType): ?string
    {
        $valid = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'A', 'B', 'AB', 'O'];

        $bloodType = strtoupper(trim($bloodType));

        return in_array($bloodType, $valid) ? $bloodType : null;
    }

    /**
     * Validate gender
     */
    public static function validateGender(string $gender): ?string
    {
        $gender = strtolower(trim($gender));
        $valid = ['male', 'female', 'other'];

        return in_array($gender, $valid) ? $gender : null;
    }

    /**
     * Validate age
     */
    public static function validateAge(int $age): ?int
    {
        if ($age < 1 || $age > 150) {
            return null;
        }

        return $age;
    }

    /**
     * Validate weight in kg
     */
    public static function validateWeight(float $weight): ?float
    {
        if ($weight < 1 || $weight > 500) {
            return null;
        }

        return round($weight, 2);
    }

    /**
     * Validate height in cm
     */
    public static function validateHeight(float $height): ?float
    {
        if ($height < 50 || $height > 300) {
            return null;
        }

        return round($height, 2);
    }

    /**
     * Validate barcode/EAN
     */
    public static function validateBarcode(string $barcode): ?string
    {
        $barcode = trim(preg_replace('/[^0-9]/', '', $barcode));

        // Valid barcode lengths: 8, 12, 13, 14, 17, 18
        $validLengths = [8, 12, 13, 14, 17, 18];

        if (!in_array(strlen($barcode), $validLengths)) {
            return null;
        }

        return $barcode;
    }

    /**
     * Create comprehensive validation rules for user profile
     */
    public static function getUserProfileRules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-,A,B,AB,O',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:150',
            'height' => 'nullable|numeric|min:50|max:300',
            'weight' => 'nullable|numeric|min:1|max:500',
            'allergy' => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:2000',
            'diet_preference' => 'nullable|in:omnivore,vegetarian,vegan,pescatarian',
            'activity_level' => 'nullable|in:sedentary,lightly_active,moderately_active,very_active,extra_active',
            'goal' => 'nullable|in:lose_weight,maintain_weight,gain_weight,improve_health',
        ];
    }

    /**
     * Create comprehensive validation rules for user registration
     */
    public static function getRegistrationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'username' => 'required|string|max:255|unique:users,username',
            'phone' => 'nullable|string|max:20',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-,A,B,AB,O',
            'allergy' => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Create comprehensive validation rules for product data
     */
    public static function getProductRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:20|unique:products,barcode',
            'brand' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:kategoris,id_kategori',
            'ingredients' => 'nullable|array',
            'nutrition' => 'nullable|array',
            'halal_status' => 'nullable|in:halal,haram,mushbooh',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ];
    }
}
