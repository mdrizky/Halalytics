# 🎨 HALALYTICS Premium Design System Specification

**Phase 3: Complete UI/UX Redesign**  
**Version:** 1.0  
**Status:** In Progress  
**Date:** May 20, 2026

---

## Table of Contents

1. [Design Philosophy](#design-philosophy)
2. [Brand Identity](#brand-identity)
3. [Color System](#color-system)
4. [Typography](#typography)
5. [Spacing & Layout](#spacing--layout)
6. [Components](#components)
7. [Patterns & Interactions](#patterns--interactions)
8. [Screens Implementation](#screens-implementation)
9. [Mobile Guidelines](#mobile-guidelines)

---

## Design Philosophy

### Core Principles

**🏥 Health-Tech Premium**
- Modern, trustworthy, medical-grade aesthetic
- Minimal but powerful interface
- Focus on health data clarity

**🚀 Silicon Valley Startup**
- Clean, bold, futuristic
- Cutting-edge but accessible
- Premium polish in every detail

**🤖 AI-SaaS Modern**
- Conversational and approachable
- Sophisticated AI integration
- Smart defaults and predictions

**✨ Medical Futuristic**
- Professional yet innovative
- Data-driven visualization
- Elegant complexity handling

### Design Goals

- ✅ Comparable to Stripe Dashboard, Notion, Linear, Figma, Airbnb
- ✅ Premium feel in every interaction
- ✅ Instill trust for health-sensitive data
- ✅ Support complex health information seamlessly
- ✅ Enable AI assistance naturally

---

## Brand Identity

### Logo & Branding

**New HALALYTICS Logo**
```
Brand Name: HALALYTICS
Tagline: Health Intelligence, Powered by AI
Primary Color: Emerald Green (#10B981)
Secondary Color: Deep Teal (#0F766E)
Accent Color: Mint (#6EE7B7)
```

### Logo Evolution
```
Old: Generic health icon + text
New: Modern geometric health badge with AI accent
     - Emerald-to-Teal gradient
     - Subtle AI spark element
     - Premium, confidence-inspiring
```

### Brand Applications
- App icon: Geometric emerald diamond with AI spark
- Splash screen: Cinematic, animated, premium
- Onboarding: Smooth, engaging, modern
- Navigation bar: Sleek, minimal, intentional

### Logo Variations
```
Primary: Full logo with text (app store)
Compact: Icon only (navigation, favicons)
Monochrome: Black/white for versatility
Inverted: White on dark backgrounds
Stacked: Logo above text (splash screen)
```

---

## Color System

### Primary Colors

| Color | Hex | RGB | Usage | Tailwind |
|-------|-----|-----|-------|----------|
| **Emerald** | #10B981 | 16,185,145 | Primary brand, CTAs | emerald-500 |
| **Emerald-Dark** | #059669 | 5,150,105 | Hover states, active | emerald-600 |
| **Emerald-Light** | #D1FAE5 | 209,250,229 | Backgrounds, hover | emerald-100 |
| **Teal** | #0F766E | 15,118,110 | Secondary, accents | teal-700 |
| **Teal-Dark** | #134E4A | 19,78,74 | Subtle, muted | teal-900 |
| **Mint** | #6EE7B7 | 110,231,183 | Highlights, success | emerald-300 |

### Neutral Colors

| Color | Hex | RGB | Usage |
|-------|-----|-----|-------|
| **Slate-900** | #0F172A | 15,23,42 | Text, primary content |
| **Slate-800** | #1E293B | 30,41,59 | Secondary text |
| **Slate-600** | #475569 | 71,85,105 | Tertiary text, borders |
| **Slate-400** | #94A3B8 | 148,163,184 | Disabled states |
| **Slate-200** | #E2E8F0 | 226,232,240 | Borders, dividers |
| **Slate-100** | #F1F5F9 | 241,245,249 | Light backgrounds |
| **Slate-50** | #F8FAFC | 248,250,252 | Lightest backgrounds |
| **White** | #FFFFFF | 255,255,255 | Primary backgrounds |

### Semantic Colors

| Purpose | Color | Hex | Usage |
|---------|-------|-----|-------|
| **Success** | Green | #10B981 | Confirmations, positive data |
| **Warning** | Amber | #F59E0B | Warnings, caution alerts |
| **Error** | Red | #EF4444 | Errors, destructive actions |
| **Info** | Blue | #3B82F6 | Information, help |
| **Neutral** | Slate | #64748B | Neutral info |

### Glassmorphism Colors

```kotlin
// Jetpack Compose glassmorphism implementation
val GlassMaterial = Color(0xFFFFFFFF).copy(alpha = 0.8f)  // White with 80% opacity
val GlassDark = Color(0xFF0F172A).copy(alpha = 0.6f)     // Dark with 60% opacity
val GlassEmphasis = Color(0xFF10B981).copy(alpha = 0.1f) // Emerald tint
```

---

## Typography

### Font Stack

**Primary Font Family: Inter**
- Modern, legible, geometric
- Excellent for screens and readability
- Used in Stripe, Figma, Linear

**Secondary Font Family: Poppins** (Headings)
- Bold, friendly, premium
- Strong visual hierarchy
- Modern and approachable

### Type Scale

| Level | Name | Size | Weight | Line Height | Usage |
|-------|------|------|--------|-------------|-------|
| **Display** | H1 | 48sp | Bold (700) | 56sp | App title, main hero |
| **Headline** | H2 | 36sp | Bold (700) | 44sp | Screen titles |
| **Subheading** | H3 | 24sp | SemiBold (600) | 32sp | Section headers |
| **Title** | H4 | 20sp | SemiBold (600) | 28sp | Card titles |
| **Subtitle** | H5 | 16sp | Medium (500) | 24sp | Subtitles, labels |
| **Body** | Body Large | 16sp | Regular (400) | 24sp | Primary content |
| **Body** | Body Medium | 14sp | Regular (400) | 22sp | Secondary content |
| **Caption** | Caption | 12sp | Regular (400) | 18sp | Helpers, hints |
| **Overline** | Label | 11sp | SemiBold (600) | 16sp | Tags, badges |

### Text Styles

```kotlin
// Jetpack Compose text styles
val displayLarge = TextStyle(
    fontSize = 48.sp,
    fontWeight = FontWeight.Bold,
    fontFamily = poppinsFamily,
    letterSpacing = (-0.5).sp
)

val headlineLarge = TextStyle(
    fontSize = 36.sp,
    fontWeight = FontWeight.Bold,
    fontFamily = poppinsFamily
)

val bodyLarge = TextStyle(
    fontSize = 16.sp,
    fontWeight = FontWeight.Normal,
    fontFamily = interFamily,
    lineHeight = 24.sp
)
```

---

## Spacing & Layout

### Spacing Scale

```
Base Unit: 4dp

4dp   → xxs   (closest spacing)
8dp   → xs    (tight spacing)
12dp  → sm    (small spacing)
16dp  → md    (standard spacing)
24dp  → lg    (loose spacing)
32dp  → xl    (generous spacing)
48dp  → 2xl   (very generous)
64dp  → 3xl   (extra generous)
```

### Grid System

```
Screen width: 412dp (Pixel 6a standard)
Margin: 16dp on each side
Usable width: 380dp

Grid columns:
- 1 column layouts: Full width (380dp)
- 2 column layouts: 182dp each (1dp gap)
- 3 column layouts: 123dp each (1dp gap)
- 4 column layouts: 92dp each (1dp gap)
```

### Corner Radius

| Size | Usage | Tailwind |
|------|-------|----------|
| 0dp | Sharp (buttons text only) | rounded-none |
| 4dp | Subtle (small elements) | rounded-sm |
| 8dp | Small (inputs, pills) | rounded |
| 12dp | Medium (cards, buttons) | rounded-lg |
| 16dp | Large (containers, modals) | rounded-xl |
| 20dp | Extra large (hero images) | rounded-2xl |
| 24dp | Maximum (full shapes) | rounded-3xl |

### Shadows & Elevation

```kotlin
// Elevation tokens for Material Design 3
val elevation0 = shadowElevation(0.dp)      // Flat
val elevation1 = shadowElevation(1.dp)      // Subtle
val elevation2 = shadowElevation(3.dp)      // Slight lift
val elevation3 = shadowElevation(6.dp)      // Card hover
val elevation4 = shadowElevation(8.dp)      // Modal
val elevation5 = shadowElevation(12.dp)     // Floating action

// Shadow definition
fun shadowElevation(elevation: Dp): Shadow = 
    Shadow(
        color = Color(0x0F172A).copy(alpha = (elevation.value / 12f) * 0.1f),
        offset = DpOffset(0.dp, elevation / 2),
        blurRadius = elevation
    )
```

---

## Components

### Buttons

#### Primary Button
```kotlin
@Composable
fun PrimaryButton(
    text: String,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
    enabled: Boolean = true,
    isLoading: Boolean = false
) {
    // Emerald background, white text
    // Rounded corners: 12dp
    // Height: 48dp
    // Padding: 16dp horizontal, 12dp vertical
    // Hover: Darker emerald (#059669)
    // Active: Even darker (#047857)
    // Disabled: Slate-400, 50% opacity
    // Loading: Spinner animation
}
```

#### Secondary Button
```kotlin
@Composable
fun SecondaryButton(
    text: String,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
    enabled: Boolean = true
) {
    // White background, teal text
    // Border: 1dp teal
    // Rounded corners: 12dp
    // Height: 48dp
    // Hover: Light teal background
    // Active: Darker teal
    // Disabled: Slate-400 text, 50% opacity
}
```

#### Ghost Button
```kotlin
@Composable
fun GhostButton(
    text: String,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    // No background, teal text
    // No border in rest state
    // Hover: Teal background 10% opacity
    // Active: Teal background 20% opacity
    // Height: 44dp (slightly smaller)
}
```

#### Icon Button
```kotlin
@Composable
fun IconButton(
    icon: Painter,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
    tint: Color = Color.Black,
    isLoading: Boolean = false
) {
    // Circular, 48dp × 48dp
    // No background (transparent)
    // Hover: Light slate background
    // Icon size: 24dp
    // Loading: Rotation animation
}
```

### Input Fields

#### Text Input
```kotlin
@Composable
fun TextInputField(
    value: String,
    onValueChange: (String) -> Unit,
    label: String,
    placeholder: String = "",
    modifier: Modifier = Modifier,
    error: String? = null,
    isPassword: Boolean = false,
    helper: String? = null
) {
    // Height: 48dp
    // Border: 1dp slate-200 (rest), emerald-500 (focus)
    // Rounded: 12dp
    // Padding: 16dp horizontal, 12dp vertical
    // Background: White (rest), white (focus)
    // Text: slate-900, 16sp
    // Label: 12sp, slate-600, above field
    // Placeholder: 16sp, slate-400
    // Helper text: 12sp, slate-500 (or red if error)
    // Focus: Green border 2dp, shadow elevation 1
}
```

#### Search Input
```kotlin
@Composable
fun SearchInput(
    value: String,
    onValueChange: (String) -> Unit,
    onSearch: (String) -> Unit,
    modifier: Modifier = Modifier,
    placeholder: String = "Search..."
) {
    // Icon left: Search 20dp, slate-500
    // Icon right: Clear (X) on text present
    // Same styling as TextInputField
    // Return key: Search trigger
}
```

#### Checkbox
```kotlin
@Composable
fun CheckboxField(
    checked: Boolean,
    onCheckedChange: (Boolean) -> Unit,
    label: String,
    modifier: Modifier = Modifier
) {
    // Size: 20dp × 20dp
    // Border: 2dp (unchecked), filled (checked)
    // Color: emerald-500
    // Rounded: 4dp
    // Label: 14sp, slate-900, right of checkbox
}
```

#### Radio Button
```kotlin
@Composable
fun RadioButtonField(
    selected: Boolean,
    onClick: () -> Unit,
    label: String,
    modifier: Modifier = Modifier
) {
    // Size: 20dp diameter
    // Border: 2dp (unselected)
    // Color: emerald-500
    // Label: 14sp, slate-900
}
```

### Cards

#### Standard Card
```kotlin
@Composable
fun Card(
    modifier: Modifier = Modifier,
    onClick: (() -> Unit)? = null,
    backgroundColor: Color = Color.White,
    content: @Composable () -> Unit
) {
    // Background: White
    // Border: 1dp slate-200
    // Rounded: 16dp
    // Padding: 16dp
    // Shadow: elevation 1 (raised feel)
    // Hover: elevation 3, slight scale up
    // onClick: Ripple effect
}
```

#### Glassmorphism Card
```kotlin
@Composable
fun GlassCard(
    modifier: Modifier = Modifier,
    content: @Composable () -> Unit
) {
    // Background: White 80% opacity
    // Backdrop: Blur effect (12dp)
    // Border: 1dp white 50% opacity
    // Rounded: 20dp
    // Shadow: Subtle dark shadow
    // Used for overlays, floating elements
}
```

#### Health Data Card
```kotlin
@Composable
fun HealthDataCard(
    title: String,
    value: String,
    unit: String,
    icon: Painter,
    status: HealthStatus = HealthStatus.NORMAL
) {
    // Status color-coded background
    // Large value display
    // Icon: 32dp
    // Trend indicator: ↑ ↓ for changes
    // Last updated time
}
```

### Navigation

#### Bottom Navigation
```kotlin
@Composable
fun BottomNavigation(
    selectedItem: Int,
    onItemSelected: (Int) -> Unit
) {
    // Height: 64dp
    // Items: Home, Scan, AI Chat, Nutrition, Profile
    // Icon: 24dp
    // Label: 11sp (only selected item)
    // Background: White
    // Border-top: 1dp slate-200
    // Selected: Emerald icon
    // Unselected: Slate-600 icon
    // Animation: Smooth icon change
}
```

#### Top App Bar
```kotlin
@Composable
fun TopAppBar(
    title: String,
    onNavigationClick: (() -> Unit)? = null,
    actions: @Composable () -> Unit = {}
) {
    // Height: 64dp
    // Background: White
    // Border-bottom: 1dp slate-200
    // Title: 20sp, bold
    // Nav icon: Back or menu (24dp)
    // Actions: Right-aligned icons
}
```

### Dialogs & Modals

#### Standard Dialog
```kotlin
@Composable
fun Dialog(
    title: String,
    message: String,
    onDismiss: () -> Unit,
    onConfirm: () -> Unit,
    modifier: Modifier = Modifier
) {
    // Background: GlassCard style
    // Rounded: 20dp
    // Width: 320dp max
    // Padding: 24dp
    // Title: H4 (20sp, bold)
    // Message: Body large (16sp)
    // Buttons: Primary + Secondary
}
```

#### Bottom Sheet
```kotlin
@Composable
fun BottomSheet(
    onDismiss: () -> Unit,
    modifier: Modifier = Modifier,
    content: @Composable () -> Unit
) {
    // Height: Dynamic, 50-90% screen
    // Rounded: 20dp top corners only
    // Drag handle: Center top (40dp × 4dp)
    // Background: White
    // Padding: 24dp
    // Swipe down: Dismiss
}
```

### Loaders & States

#### Skeleton Loader
```kotlin
@Composable
fun SkeletonLoader(
    modifier: Modifier = Modifier,
    type: SkeletonType = SkeletonType.CARD
) {
    // Background: Slate-200
    // Animation: Shimmer from left to right
    // Duration: 1.5 seconds
    // Types: CARD, TEXT_LINE, CIRCLE, RECTANGLE
    // Used in all list views while loading
}
```

#### Loading Spinner
```kotlin
@Composable
fun LoadingSpinner(
    modifier: Modifier = Modifier,
    size: Dp = 48.dp,
    strokeWidth: Dp = 4.dp
) {
    // Color: Emerald-500
    // Rotation animation: Continuous 360°
    // Duration: 1 second
    // Stroke: 4dp
    // Size: Customizable (48dp default)
}
```

#### Empty State
```kotlin
@Composable
fun EmptyState(
    icon: Painter,
    title: String,
    message: String,
    action: @Composable () -> Unit = {}
) {
    // Icon: 64dp, slate-400
    // Title: H3 (24sp, bold)
    // Message: Body (16sp, slate-600)
    // Action button: Optional CTA
    // Vertical centering with 64dp top margin
}
```

---

## Patterns & Interactions

### Page Transitions

#### Slide Transition
```kotlin
// Forward navigation: Slide in from right
// Back navigation: Slide out to right
// Duration: 300ms
// Easing: EaseInOut
```

#### Fade Transition
```kotlin
// For modal/overlay appear/disappear
// Duration: 200ms
// Alpha: 0 → 1
```

#### Scale Transition
```kotlin
// For dialog/popup appearances
// Duration: 250ms
// Scale: 0.9 → 1.0
```

### Micro-Animations

#### Button Press
```kotlin
// Scale: 0.98 on press
// Duration: 100ms
// Easing: Spring
// Color: Darker shade
```

#### Ripple Effect
```kotlin
// On tap: Ripple spreads from tap point
// Duration: 400ms
// Color: Emerald 20% opacity
```

#### Floating Action Button
```kotlin
// Float: 16dp above bottom nav
// Scale animation: Entrance at 0.5 → 1.0
// On scroll: Hide when scrolling down, show when up
// Size: 56dp diameter
```

#### Pull-to-Refresh
```kotlin
// Threshold: 80dp
// Icon rotation: 0° → 360°
// Refresh animation: Loading spinner
// Duration: Entire refresh operation
```

### Feedback

#### Toast Notifications
```kotlin
// Position: Bottom (80dp from bottom)
// Duration: 3 seconds (adjustable)
// Animation: Slide up entrance, fade out exit
// Styling: Slate-900 text on white background
// Types: INFO, SUCCESS, WARNING, ERROR
```

#### Success Feedback
```kotlin
// Checkmark: Green animation
// Scale: 0 → 1.2 → 1.0
// Color: Emerald-500
// Duration: 600ms
```

#### Error Feedback
```kotlin
// Red border: Shake animation
// Duration: 400ms
// Amplitude: 4dp horizontal
// Color: Red-500
```

---

## Screens Implementation

### 1. Splash Screen

**Design:**
- Full screen, cinematic feel
- HALALYTICS logo (stacked, centered)
- Gradient background: Emerald to Teal
- Animated logo entrance (scale + fade)
- Animated text entrance (slide from bottom)
- Pulse animation on logo
- App name + tagline below logo

**Flow:**
```
1. Logo appears (scale 0 → 1, duration 600ms)
2. Text slides from bottom (300ms)
3. Logo pulses (1.5s loop)
4. Auto-navigate to next screen (3s total)
```

**File:** `HalalyticsCompose/app/src/main/java/com/example/halalyticscompose/ui/screens/SplashScreen.kt`

### 2. Onboarding

**Screens:**
```
Screen 1: Welcome
- Headline: "Welcome to HALALYTICS"
- Subheading: "Your Personal Health AI"
- Illustration: Gradient shapes (emerald/teal)
- Next button

Screen 2: Features
- Title: "AI-Powered Health"
- 3 feature cards with icons:
  - 🤖 AI Nutrition Assistant
  - 📊 Health Analytics
  - 🔍 Smart Scanning
- Next button, Skip button

Screen 3: Permissions
- Title: "Grant Permissions"
- 3 permission cards:
  - Camera (for food scanning)
  - Location (for local recommendations)
  - Health (for data sync)
- Allow button

Screen 4: Demo Account
- Title: "Try Demo First?"
- Demo button (gray), Sign Up button (emerald)
- Option to continue as guest
```

**Animations:**
- Page transitions: Slide + fade
- Illustrations: Subtle parallax scroll
- Buttons: Scale on press

**File:** `HalalyticsCompose/app/src/main/java/com/example/halalyticscompose/ui/screens/OnboardingScreen.kt`

### 3. Login Screen

**Components:**
- Header: HALALYTICS logo (24dp) + "Login to Your Account"
- Email input
- Password input
- Forgot password link (teal)
- Login button (primary, 48dp)
- Divider: "or continue with"
- Google login button
- Facebook login button
- Sign up link: "Don't have account? Sign up" (teal)
- Demo account button (ghost)

**States:**
- Idle: All fields empty
- Focused: Active field highlighted
- Loading: Button spinner + disabled fields
- Error: Red error message under field
- Success: Fade to next screen

**Features:**
- Email validation (real-time)
- Password visibility toggle
- Remember me checkbox
- Keyboard handling: Next button focus movement
- Auto-focus email field

**File:** `HalalyticsCompose/app/src/main/java/com/example/halalyticscompose/ui/screens/auth/LoginScreen.kt`

### 4. Sign Up Screen

**Components:**
- Header: "Create Account"
- Full name input
- Email input
- Password input (with strength indicator)
- Confirm password input
- Terms checkbox
- Sign up button
- Already have account? Login link

**Password Strength:**
```
0-20: Weak (red)
20-50: Fair (orange)
50-80: Good (yellow)
80-100: Strong (green)

Indicators:
- At least 8 characters
- Contains uppercase
- Contains lowercase
- Contains number
- Contains special char
```

**File:** `HalalyticsCompose/app/src/main/java/com/example/halalyticscompose/ui/screens/auth/SignUpScreen.kt`

### 5. Home Screen

**Components:**
- Top app bar: "Welcome, [Name]" + notification icon
- Health summary card (glassmorphism):
  - Large circular progress (health score 0-100)
  - Status text (Healthy, At Risk, etc.)
  - Last updated time
- Quick stats: 3 cards in row
  - Water intake
  - Steps
  - Sleep hours
- "Today's Nutrition" card:
  - Calories consumed vs goal
  - Macros breakdown (pie chart)
  - Add meal button
- "Upcoming" section:
  - Next consultation with nutritionist
  - Next health check reminder
- Bottom nav: Home (active), Scan, AI, Nutrition, Profile

**Skeleton Loaders:**
- All cards show skeleton while loading
- Smooth fade-in animation
- Staggered loading (cards load sequentially)

**File:** `HalalyticsCompose/app/src/main/java/com/example/halalyticscompose/ui/screens/home/HomeScreen.kt`

### 6. AI Chat Screen

**Components:**
- Top app bar: "AI Nutrition Assistant"
- Chat messages area (scrollable):
  - User messages (right, emerald background)
  - AI messages (left, slate background with avatar)
  - Typing indicator (three bouncing dots)
  - Code/tables rendering support
- Input area (sticky bottom):
  - Text input with auto-expand
  - Send button (icon, teal)
  - Attachment button (optional)
- Quick actions below input:
  - "Analyze my meal"
  - "Nutrition plan"
  - "Health tips"

**Features:**
- Markdown rendering
- Typing animation
- Message timestamps
- Copy message text (icon appears on hover)
- Swipe up to access history
- Smooth scroll-to-bottom on new message

**File:** `HalalyticsCompose/app/src/main/java/com/example/halalyticscompose/ui/screens/ai/AiChatScreen.kt`

### 7. Food Scan Screen

**Components:**
- Camera preview (full screen background)
- Top app bar: "Scan Food"
- Scan frame: Centered square with border (emerald)
- Bottom sheet:
  - Recent scans (horizontal scroll)
  - History link
  - Album link
- Floating action button: Gallery access

**Scan Flow:**
1. Camera loads with permission check
2. User frames food in square
3. Tap to capture
4. Loading spinner (3 seconds)
5. Results screen shows nutritional info

**File:** `HalalyticsCompose/app/src/main/java/com/example/halalyticscompose/ui/screens/scan/ScanScreen.kt`

### 8. Nutrition Tracker

**Components:**
- Top app bar: "Nutrition Tracker" + date picker
- Daily summary card:
  - Calories: Large value with goal
  - Progress bar (emerald)
  - Remaining calories
- Macro breakdown:
  - 3 circular progress rings (Protein, Carbs, Fat)
  - Percentage for each
- Meal list (by time):
  - Breakfast section
  - Lunch section
  - Dinner section
  - Snacks section
  - Each item: Food name, calories, time
  - Swipe to delete
- Add meal button (floating)

**Features:**
- Date navigation (prev/next day)
- Weekly view toggle
- Search meals
- Meal history
- Quick add (common meals)

**File:** `HalalyticsCompose/app/src/main/java/com/example/halalyticscompose/ui/screens/nutrition/NutritionTrackerScreen.kt`

### 9. Profile Screen

**Components:**
- Top app bar: "Profile" + edit icon
- Profile header:
  - Avatar (80dp, circular)
  - Name
  - Email
  - Health goal badge
- Health info cards:
  - Age, Height, Weight
  - Blood type, Gender
  - Activity level
- Sections:
  - Account: Password, Email, 2FA
  - Health Data: Linked devices, Data export
  - Preferences: Units, Reminders, Notifications
  - Support: FAQ, Report Issue, About
  - Logout button (ghost)

**File:** `HalalyticsCompose/app/src/main/java/com/example/halalyticscompose/ui/screens/profile/ProfileScreen.kt`

### 10. Settings Screen

**Components:**
- Top app bar: "Settings"
- General section:
  - App theme (Light/Dark toggle)
  - Language selector
  - Notification toggle
  - Background sync toggle
- Health section:
  - Privacy level selector
  - Data sharing preferences
  - Health app sync (Apple Health, Google Health)
- About section:
  - App version
  - Build number
  - Open source licenses

**File:** `HalalyticsCompose/app/src/main/java/com/example/halalyticscompose/ui/screens/settings/SettingsScreen.kt`

---

## Mobile Guidelines

### Screen Sizes Supported

```
Tablets: 600dp+
Phones: 360dp - 600dp
Focus: Pixel 6a (412dp)
```

### Responsive Behavior

```
Layout: Single column (phones), Multi-column (tablets)
Spacing: Adjust margins at breakpoints
Icons: Scale appropriately
Typography: Same sizes across devices
```

### Landscape Orientation

```
Support: Phones in landscape
Adjust: Reduce top/bottom padding
Content: Center-focused layout
Test: Pixel 6a in landscape (412dp wide)
```

### Accessibility

```
Minimum touch target: 48dp
Color contrast: WCAG AA (4.5:1 minimum)
Font size: Minimum 12sp
Focus indicators: Visible keyboard navigation
Dark mode: Full support with color inversion
Text scaling: Support up to 200%
```

### Safe Areas

```
Status bar: ~24dp (top)
Bottom nav: 64dp (bottom)
Content padding: 16dp (sides)
Notch accommodation: Add top padding if present
```

---

## Implementation Checklist

### Design Tokens
- [ ] Colors.kt - All color definitions
- [ ] Typography.kt - All text styles
- [ ] Dimensions.kt - Spacing, sizes, radius
- [ ] Shadows.kt - Elevation definitions
- [ ] Theme.kt - Material 3 theme setup

### Components
- [ ] PrimaryButton.kt
- [ ] SecondaryButton.kt
- [ ] GhostButton.kt
- [ ] IconButton.kt
- [ ] TextInputField.kt
- [ ] SearchInput.kt
- [ ] CheckboxField.kt
- [ ] RadioButtonField.kt
- [ ] Card.kt
- [ ] GlassCard.kt
- [ ] HealthDataCard.kt
- [ ] BottomNavigation.kt
- [ ] TopAppBar.kt
- [ ] Dialog.kt
- [ ] BottomSheet.kt
- [ ] SkeletonLoader.kt
- [ ] LoadingSpinner.kt
- [ ] EmptyState.kt

### Screens (Priority Order)
- [ ] SplashScreen.kt (NEW)
- [ ] OnboardingScreen.kt (NEW)
- [ ] LoginScreen.kt (REDESIGN)
- [ ] SignUpScreen.kt (REDESIGN)
- [ ] HomeScreen.kt (REDESIGN)
- [ ] AiChatScreen.kt (REDESIGN)
- [ ] ScanScreen.kt (REDESIGN)
- [ ] NutritionTrackerScreen.kt (REDESIGN)
- [ ] ProfileScreen.kt (REDESIGN)
- [ ] SettingsScreen.kt (REDESIGN)

### Assets
- [ ] HALALYTICS logo (all variants)
- [ ] App icon (192dp, 512dp)
- [ ] Splash screen assets
- [ ] Onboarding illustrations
- [ ] Feature icons
- [ ] Empty state illustrations

### Navigation
- [ ] Update NavGraph.kt
- [ ] Add new navigation routes
- [ ] Setup deeplinks
- [ ] Test back navigation

---

## Resources & Inspiration

**Design References:**
- Stripe Dashboard: stripe.com/dashboard
- Notion: notion.so
- Linear: linear.app
- Figma: figma.com
- Airbnb: airbnb.com

**Jetpack Compose Resources:**
- Material 3 Guidelines
- Compose Documentation
- Codelab samples

**Color Tools:**
- Coolors.co
- ColorMind.io
- WebAIM Contrast Checker

---

## Next Steps

1. ✅ Create design system spec (this document)
2. ⏳ Build design tokens (Colors, Typography, Dimensions)
3. ⏳ Create reusable components
4. ⏳ Redesign priority screens (Splash, Onboarding, Login, Home)
5. ⏳ Implement other screens
6. ⏳ QA and polish
7. ⏳ Move to Phase 4: Role-Based Architecture

---

**Design System Version:** 1.0  
**Last Updated:** May 20, 2026  
**Status:** Specification Complete - Ready for Implementation
