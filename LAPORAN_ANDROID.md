# 🟢 HALALYTICS - Laporan Aplikasi Android

## 1. IDENTITAS PROJECT

### Nama Project
```
HALALYTICS - Android Application
Smart AI-Based Halal & Healthy Food Scanner
```

### Platform
- **OS**: Android 8.0+ (API Level 26+)
- **Bahasa**: Kotlin
- **UI Framework**: Jetpack Compose
- **Architecture**: MVVM + Clean Architecture
- **DI**: Dagger Hilt
- **Minimum SDK**: 26
- **Target SDK**: 34

### Tim Pengembang
- **Android Developer**: [Nama]
- **UI/UX Designer**: [Nama]
- **QA Tester**: [Nama]

---

## 2. LATAR BELAKANG MASALAH

### Masalah yang Diselesaikan

#### 1. Kesulitan Verifikasi Produk Halal Mobile
- 70% konsumen muslim bingung dengan istilah bahan kimia (E-numbers)
- Produk tanpa barcode sulit diverifikasi di lapangan
- Tidak ada aplikasi mobile yang komprehensif untuk verifikasi halal
- Data BPOM dan MUI tidak terintegrasi dalam aplikasi mobile

#### 2. Keterbatasan Tracking Kesehatan Mobile
- Tidak ada aplikasi yang menggabungkan verifikasi halal dan tracking kesehatan
- Konsumen sulit tracking asupan harian secara praktis
- Tidak ada sistem pengingat obat yang terintegrasi
- Manajemen kesehatan keluarga sulit dilakukan via mobile

#### 3. Kebutuhan Aplikasi Mobile Modern
- Perlu UI modern dengan Jetpack Compose
- Perlu offline capability untuk area tanpa internet
- Perlu biometric authentication untuk keamanan
- Perlu real-time notifications
- Perlu integrasi dengan berbagai API eksternal

### Statistik Pendukung
- 87% konsumen Indonesia peduli dengan kehalalan produk
- 65% tidak mengerti arti E-numbers pada label
- 40% produk di pasar tanpa verifikasi halal jelas
- Indonesia ranked #2 untuk obesitas di Asia Tenggara
- 95% pengguna smartphone Indonesia menggunakan Android

---

## 3. SOLUSI YANG DITAWARKAN

### HALALYTICS Android App Menyediakan:

#### 🔍 Smart Scanner
- Barcode scanner dengan ML Kit
- OCR untuk produk tanpa barcode
- Multi-format support (QR, Barcode, Data Matrix)
- Real-time detection dengan auto-focus
- Vibration dan sound feedback

#### ✅ Halal Verification
- Database lokal 50.000+ produk
- Integrasi API BPOM & MUI real-time
- AI analysis untuk bahan tidak dikenal
- Deteksi bahan haram/syubhat
- E-number explanation

#### 💊 Health Tracking
- Tracking asupan harian (gula, garam, kalori, air)
- Medicine reminder dengan smart scheduling
- Health profile personal
- BMI calculator
- Streak tracking untuk kebiasaan sehat

#### 👨‍👩‍👧‍👦 Family Box
- Multi-profile untuk seluruh keluarga
- One-scan safety check untuk semua anggota
- Allergy detection per anggota
- Dietary restrictions management
- Family health dashboard

#### 📊 Dashboard Kesehatan
- Visual analytics dengan grafik interaktif
- Daily summary cards
- Weekly trends
- Health insights personal
- Goal progress tracking

#### 🔒 Security Features
- Biometric authentication (fingerprint/face)
- Secure token storage
- Encrypted local database
- Session management
- Auto-logout

#### 🌐 Offline Capability
- Room database untuk local storage
- Background sync dengan WorkManager
- Queue management untuk offline actions
- Conflict resolution
- Data persistence

---

## 4. ARSITEKTUR APLIKASI ANDROID

### High-Level Architecture
```
┌─────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                   │
├─────────────────────────────────────────────────────────┤
│  Jetpack Compose UI Screens                            │
│  - MainActivity                                        │
│  - LoginScreen                                         │
│  - DashboardScreen                                     │
│  - ScanScreen                                          │
│  - ResultScreen                                        │
│  - HealthDashboardScreen                               │
│  - ProfileScreen                                       │
│  - FamilyBoxScreen                                     │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────────────┐
│                    VIEWMODEL LAYER                       │
├─────────────────────────────────────────────────────────┤
│  Jetpack ViewModel                                     │
│  - MainViewModel                                       │
│  - ScanViewModel                                       │
│  - HealthViewModel                                      │
│  - ProfileViewModel                                    │
│  - FamilyViewModel                                     │
│  - NotificationViewModel                              │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────────────┐
│                    DOMAIN LAYER                          │
├─────────────────────────────────────────────────────────┤
│  Use Cases                                             │
│  - ScanProductUseCase                                  │
│  - GetProductDetailsUseCase                            │
│  - SaveScanHistoryUseCase                              │
│  - TrackHealthMetricsUseCase                           │
│  - ManageFamilyUseCase                                 │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────────────┐
│                    DATA LAYER                           │
├─────────────────────────────────────────────────────────┤
│  Repository Pattern                                    │
│  - ProductRepository                                   │
│  - HealthRepository                                    │
│  - UserRepository                                      │
│  - FamilyRepository                                    │
│                                                         │
│  Local Data Sources                                    │
│  - Room Database                                       │
│  - SharedPreferences                                   │
│  - DataStore                                           │
│                                                         │
│  Remote Data Sources                                   │
│  - Retrofit API Service                               │
│  - Firebase Realtime DB                                │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────────────┐
│                    DEPENDENCY INJECTION                 │
├─────────────────────────────────────────────────────────┤
│  Dagger Hilt                                           │
│  - AppModule                                          │
│  - NetworkModule                                      │
│  - DatabaseModule                                     │
│  - ViewModelModule                                    │
└─────────────────┬───────────────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────────────┐
│                    EXTERNAL SERVICES                    │
├─────────────────────────────────────────────────────────┤
│  - Laravel Backend API                                 │
│  - Firebase Cloud Messaging                            │
│  - Firebase Realtime Database                         │
│  - Google ML Kit (Barcode, OCR)                        │
└─────────────────────────────────────────────────────────┘
```

### Project Structure
```
HalalyticsCompose/
├── app/
│   ├── src/
│   │   ├── main/
│   │   │   ├── java/com/example/halalyticscompose/
│   │   │   │   ├── HalalyticsApplication.kt
│   │   │   │   ├── MainActivity.kt
│   │   │   │   ├── ai/
│   │   │   │   │   └── GeminiAnalyzer.kt
│   │   │   │   ├── data/
│   │   │   │   │   ├── local/
│   │   │   │   │   │   ├── database/
│   │   │   │   │   │   │   ├── AppDatabase.kt
│   │   │   │   │   │   │   ├── entities/
│   │   │   │   │   │   │   │   ├── UserHealthProfileEntity.kt
│   │   │   │   │   │   │   │   ├── HaramIngredientEntity.kt
│   │   │   │   │   │   │   │   ├── CachedScanResult.kt
│   │   │   │   │   │   │   │   └── Consumption.kt
│   │   │   │   │   │   │   └── dao/
│   │   │   │   │   │   │       ├── ConsumptionDao.kt
│   │   │   │   │   │   │       └── CachedScanResultDao.kt
│   │   │   │   │   │   └── preferences/
│   │   │   │   │   │       └── UserPreferences.kt
│   │   │   │   │   ├── remote/
│   │   │   │   │   │   ├── api/
│   │   │   │   │   │   │   ├── HalalyticsApiService.kt
│   │   │   │   │   │   │   └── dto/
│   │   │   │   │   │   │       ├── ProductDto.kt
│   │   │   │   │   │   │       ├── ScanResultDto.kt
│   │   │   │   │   │   │       └── HealthMetricDto.kt
│   │   │   │   │   │   └── firebase/
│   │   │   │   │   │       └── FirebaseService.kt
│   │   │   │   │   └── repository/
│   │   │   │   │       ├── ProductRepository.kt
│   │   │   │   │       ├── HealthRepository.kt
│   │   │   │   │       ├── UserRepository.kt
│   │   │   │   │       └── FamilyRepository.kt
│   │   │   │   ├── di/
│   │   │   │   │   ├── AppModule.kt
│   │   │   │   │   ├── NetworkModule.kt
│   │   │   │   │   ├── DatabaseModule.kt
│   │   │   │   │   └── ViewModelModule.kt
│   │   │   │   ├── domain/
│   │   │   │   │   ├── model/
│   │   │   │   │   │   ├── Product.kt
│   │   │   │   │   │   ├── ScanResult.kt
│   │   │   │   │   │   └── HealthMetric.kt
│   │   │   │   │   └── usecase/
│   │   │   │   │       ├── ScanProductUseCase.kt
│   │   │   │   │       ├── GetProductDetailsUseCase.kt
│   │   │   │   │       └── TrackHealthMetricsUseCase.kt
│   │   │   │   ├── healthcare/
│   │   │   │   │   ├── model/
│   │   │   │   │   │   └── HealthModels.kt
│   │   │   │   │   ├── screens/
│   │   │   │   │   │   ├── HealthScannerScreen.kt
│   │   │   │   │   │   ├── HealthProfileScreen.kt
│   │   │   │   │   │   └── AnalysisResultScreen.kt
│   │   │   │   │   └── viewmodel/
│   │   │   │   │       └── HealthScannerViewModel.kt
│   │   │   │   ├── feature/
│   │   │   │   │   ├── expansion/
│   │   │   │   │   │   ├── model/
│   │   │   │   │   │   │   └── ExpansionModels.kt
│   │   │   │   │   │   ├── ui/
│   │   │   │   │   │   │   └── CommunityScreens.kt
│   │   │   │   │   │   ├── socket/
│   │   │   │   │   │   │   └── ChatWebSocketManager.kt
│   │   │   │   │   │   ├── network/
│   │   │   │   │   │   │   └── ExpansionApiService.kt
│   │   │   │   │   │   └── viewmodel/
│   │   │   │   │   │       └── CommunityViewModel.kt
│   │   │   │   ├── messaging/
│   │   │   │   │   └── HalalyticsFirebaseMessagingService.kt
│   │   │   │   ├── navigation/
│   │   │   │   │   └── HalalyticsNavigation.kt
│   │   │   │   ├── services/
│   │   │   │   │   ├── FirebaseRealtimeListener.kt
│   │   │   │   │   └── HalalyticsFirebaseMessagingService.kt
│   │   │   │   ├── ui/
│   │   │   │   │   ├── screens/
│   │   │   │   │   │   ├── LoginScreen.kt
│   │   │   │   │   │   ├── DashboardScreen.kt
│   │   │   │   │   │   ├── ScanScreen.kt
│   │   │   │   │   │   ├── ResultScreen.kt
│   │   │   │   │   │   ├── ProfileScreen.kt
│   │   │   │   │   │   └── HealthDashboardScreen.kt
│   │   │   │   │   ├── components/
│   │   │   │   │   │   ├── MainLayout.kt
│   │   │   │   │   │   ├── ProductCard.kt
│   │   │   │   │   │   ├── HealthMetricCard.kt
│   │   │   │   │   │   └── ScannerView.kt
│   │   │   │   │   └── theme/
│   │   │   │   │       ├── Color.kt
│   │   │   │   │       ├── Theme.kt
│   │   │   │   │       └── Type.kt
│   │   │   │   ├── utils/
│   │   │   │   │   ├── LanguageManager.kt
│   │   │   │   │   ├── BiometricAuthHelper.kt
│   │   │   │   │   └── Extensions.kt
│   │   │   │   └── viewmodel/
│   │   │   │       └── MainViewModel.kt
│   │   │   ├── res/
│   │   │   │   ├── drawable/
│   │   │   │   ├── layout/
│   │   │   │   ├── values/
│   │   │   │   │   ├── colors.xml
│   │   │   │   │   ├── strings.xml
│   │   │   │   │   └── themes.xml
│   │   │   │   └── values-night/
│   │   │   └── AndroidManifest.xml
│   │   ├── build.gradle.kts
│   │   └── proguard-rules.pro
│   └── build.gradle.kts
├── gradle/
├── local.properties
├── gradle.properties
├── gradlew
├── gradlew.bat
└── settings.gradle.kts
```

---

## 5. TEKNOLOGI ANDROID

### Core Technologies
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| Kotlin | 1.9.10 | Bahasa Pemrograman |
| Jetpack Compose | 1.5.0 | UI Framework |
| Android SDK | 34 | Target SDK |
| Min SDK | 26 | Minimum SDK |

### Architecture Components
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| ViewModel | 2.6.0 | UI State Management |
| LiveData | 2.6.0 | Observable Data |
| Room | 2.6.0 | Local Database |
| WorkManager | 2.8.0 | Background Tasks |
| Navigation Compose | 2.7.0 | Navigation |
| Hilt | 2.51.1 | Dependency Injection |

### Networking
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| Retrofit | 2.9.0 | HTTP Client |
| OkHttp | 4.11.0 | HTTP Client |
| Gson | 2.10.1 | JSON Serialization |
| Coroutines | 1.7.0 | Asynchronous Programming |

### ML & Vision
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| ML Kit Barcode | 17.0.0 | Barcode Scanning |
| ML Kit OCR | 16.0.0 | Text Recognition |
| CameraX | 1.3.0 | Camera Management |

### Firebase
| Teknologi | Fungsi |
|-----------|--------|
| Firebase Cloud Messaging | Push Notifications |
| Firebase Realtime Database | Real-time Data Sync |
| Firebase Authentication | Authentication |

### UI Libraries
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| Accompanist | 0.32.0 | Compose Utilities |
| Coil | 2.4.0 | Image Loading |
| Lottie | 6.0.0 | Animations |

### Testing
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| JUnit | 4.13.2 | Unit Testing |
| Mockito | 5.0.0 | Mocking |
| Espresso | 3.5.1 | UI Testing |
| Compose Testing | 1.5.0 | Compose UI Testing |

---

## 6. FITUR APLIKASI ANDROID

### 1. Smart Scanner

#### Barcode Scanner
```kotlin
@Composable
fun BarcodeScanner(
    onBarcodeDetected: (String) -> Unit
) {
    val context = LocalContext.current
    val lifecycleOwner = LocalLifecycleOwner.current
    
    val barcodeScanner = remember {
        BarcodeScannerProcessor(context) { barcode ->
            onBarcodeDetected(barcode)
        }
    }
    
    AndroidView(
        factory = { ctx ->
            PreviewView(ctx).apply {
                scaleType = PreviewView.ScaleType.FILL_CENTER
            }
        },
        modifier = Modifier.fillMaxSize()
    ) { previewView ->
        barcodeScanner.startScanner(previewView, lifecycleOwner)
    }
}
```

#### OCR Scanner
```kotlin
@Composable
fun OCRScanner(
    onTextDetected: (String) -> Unit
) {
    val context = LocalContext.current
    val imagePicker = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent()
    ) { uri ->
        uri?.let {
            processImageWithOCR(context, it) { text ->
                onTextDetected(text)
            }
        }
    }
    
    Button(onClick = { imagePicker.launch("image/*") }) {
        Text("Pilih Gambar untuk OCR")
    }
}
```

### 2. Dashboard

#### Main Dashboard
```kotlin
@Composable
fun DashboardScreen(
    viewModel: DashboardViewModel = hiltViewModel()
) {
    val userState by viewModel.userState.collectAsState()
    val recentScans by viewModel.recentScans.collectAsState()
    val healthMetrics by viewModel.healthMetrics.collectAsState()
    
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp)
    ) {
        // User Profile Header
        UserProfileHeader(userState)
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Quick Stats Cards
        LazyRow {
            item { QuickStatCard("Scans", recentScans.size) }
            item { QuickStatCard("Health Score", healthMetrics.score) }
            item { QuickStatCard("Water", "${healthMetrics.water}ml") }
            item { QuickStatCard("Streak", "${healthMetrics.streak} days") }
        }
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Recent Scans
        RecentScansSection(recentScans)
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Health Insights
        HealthInsightsSection(healthMetrics)
    }
}
```

### 3. Result Analysis

#### Product Result Screen
```kotlin
@Composable
fun ProductResultScreen(
    scanResult: ScanResult,
    onSave: () -> Unit,
    onFavorite: () -> Unit,
    onShare: () -> Unit
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp)
    ) {
        // Product Image
        AsyncImage(
            model = scanResult.product.imageUrl,
            contentDescription = null,
            modifier = Modifier
                .fillMaxWidth()
                .height(200.dp)
                .clip(RoundedCornerShape(12.dp))
        )
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Product Name & Brand
        Text(
            text = scanResult.product.name,
            style = MaterialTheme.typography.headlineMedium
        )
        Text(
            text = scanResult.product.brand,
            style = MaterialTheme.typography.bodyLarge,
            color = MaterialTheme.colorScheme.onSurfaceVariant
        )
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Halal Status Badge
        HalalStatusBadge(scanResult.halalStatus)
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Health Score
        HealthScoreCard(scanResult.healthScore)
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Ingredients List
        IngredientsList(scanResult.ingredients)
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Nutrition Facts
        NutritionFactsCard(scanResult.nutrition)
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Action Buttons
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceEvenly
        ) {
            Button(onClick = onSave) {
                Icon(Icons.Default.Save, contentDescription = null)
                Spacer(modifier = Modifier.width(8.dp))
                Text("Save")
            }
            Button(onClick = onFavorite) {
                Icon(Icons.Default.Favorite, contentDescription = null)
                Spacer(modifier = Modifier.width(8.dp))
                Text("Favorite")
            }
            Button(onClick = onShare) {
                Icon(Icons.Default.Share, contentDescription = null)
                Spacer(modifier = Modifier.width(8.dp))
                Text("Share")
            }
        }
    }
}
```

### 4. Health Dashboard

#### Health Metrics Tracking
```kotlin
@Composable
fun HealthDashboardScreen(
    viewModel: HealthViewModel = hiltViewModel()
) {
    val dailyIntake by viewModel.dailyIntake.collectAsState()
    val weeklyTrends by viewModel.weeklyTrends.collectAsState()
    
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp)
    ) {
        // Daily Summary
        Text(
            text = "Daily Summary",
            style = MaterialTheme.typography.headlineSmall
        )
        
        Spacer(modifier = Modifier.height(8.dp))
        
        // Water Intake
        WaterIntakeCard(dailyIntake.water)
        
        Spacer(modifier = Modifier.height(8.dp))
        
        // Sugar Intake
        SugarIntakeCard(dailyIntake.sugar)
        
        Spacer(modifier = Modifier.height(8.dp))
        
        // Sodium Intake
        SodiumIntakeCard(dailyIntake.sodium)
        
        Spacer(modifier = Modifier.height(8.dp))
        
        // Calories
        CaloriesCard(dailyIntake.calories)
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Weekly Trends
        Text(
            text = "Weekly Trends",
            style = MaterialTheme.typography.headlineSmall
        )
        
        Spacer(modifier = Modifier.height(8.dp))
        
        WeeklyTrendsChart(weeklyTrends)
    }
}
```

### 5. Family Box

#### Family Management
```kotlin
@Composable
fun FamilyBoxScreen(
    viewModel: FamilyViewModel = hiltViewModel()
) {
    val familyMembers by viewModel.familyMembers.collectAsState()
    val selectedMember by viewModel.selectedMember.collectAsState()
    
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp)
    ) {
        // Family Members Horizontal Scroll
        LazyRow {
            items(familyMembers) { member ->
                FamilyMemberCard(
                    member = member,
                    isSelected = member.id == selectedMember?.id,
                    onSelect = { viewModel.selectMember(member) }
                )
            }
        }
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Selected Member Profile
        selectedMember?.let { member ->
            FamilyMemberProfile(member)
        }
        
        Spacer(modifier = Modifier.height(16.dp))
        
        // Add Family Member Button
        Button(
            onClick = { viewModel.showAddMemberDialog() },
            modifier = Modifier.fillMaxWidth()
        ) {
            Icon(Icons.Default.Add, contentDescription = null)
            Spacer(modifier = Modifier.width(8.dp))
            Text("Add Family Member")
        }
    }
}
```

### 6. Medicine Reminder

#### Reminder Management
```kotlin
@Composable
fun MedicineReminderScreen(
    viewModel: MedicineViewModel = hiltViewModel()
) {
    val reminders by viewModel.reminders.collectAsState()
    
    LazyColumn(
        modifier = Modifier.fillMaxSize(),
        contentPadding = PaddingValues(16.dp)
    ) {
        item {
            Text(
                text = "Medicine Reminders",
                style = MaterialTheme.typography.headlineMedium
            )
        }
        
        items(reminders) { reminder ->
            MedicineReminderCard(
                reminder = reminder,
                onToggle = { viewModel.toggleReminder(reminder) },
                onDelete = { viewModel.deleteReminder(reminder) }
            )
        }
        
        item {
            Spacer(modifier = Modifier.height(16.dp))
            
            Button(
                onClick = { viewModel.showAddReminderDialog() },
                modifier = Modifier.fillMaxWidth()
            ) {
                Icon(Icons.Default.Add, contentDescription = null)
                Spacer(modifier = Modifier.width(8.dp))
                Text("Add Reminder")
            }
        }
    }
}
```

---

## 7. FLOWCHART APLIKASI ANDROID

### User Registration Flow
```
USER OPEN APP
        ↓
   [SPLASH SCREEN]
        ↓
   CHECK LOGIN STATUS
        ↓
   [LOGGED IN]    [NOT LOGGED IN]
        ↓              ↓
   DASHBOARD     LOGIN SCREEN
        ↓              ↓
              USER ENTER CREDENTIALS
              ↓
              VALIDATE INPUT
              ↓
              [VALID]    [INVALID]
              ↓            ↓
           API CALL     SHOW ERROR
              ↓
           [SUCCESS]    [FAILED]
              ↓            ↓
           SAVE TOKEN   SHOW ERROR
              ↓
           NAVIGATE TO DASHBOARD
```

### Product Scan Flow
```
USER NAVIGATE TO SCAN
        ↓
   [SCAN SCREEN]
        ↓
   SELECT SCAN MODE
        ↓
┌───────┴───────┐
│               │
BARCODE SCAN   OCR SCAN
│               │
↓               ↓
OPEN CAMERA    PICK IMAGE
│               │
↓               ↓
DETECT BARCODE  PROCESS IMAGE
│               │
↓               ↓
BARCODE FOUND?  TEXT EXTRACTED?
│               │
↓               ↓
[YES]    [NO]   [YES]    [NO]
│         ↓      │         ↓
│    RETRY      │    RETRY
│         ↓      │         ↓
└─────┬────┘     └────┬────┘
      ↓               ↓
   SEND BARCODE/TEXT TO API
      ↓
   [ONLINE]    [OFFLINE]
      ↓            ↓
   API CALL    CHECK LOCAL DB
      ↓            ↓
   [SUCCESS]   [FOUND]
      ↓            ↓
   SAVE RESULT  SHOW RESULT
      ↓            ↓
   SHOW RESULT
```

### Offline Sync Flow
```
USER PERFORMS ACTION OFFLINE
        ↓
   SAVE TO LOCAL DATABASE
        ↓
   MARK AS PENDING SYNC
        ↓
   ADD TO SYNC QUEUE
        ↓
   DEVICE COMES ONLINE
        ↓
   WORKMANAGER DETECTS
        ↓
   PROCESS SYNC QUEUE
        ↓
   FOR EACH PENDING ITEM:
        ↓
   SEND TO API
        ↓
   [SUCCESS]    [FAILED]
        ↓            ↓
   MARK AS SYNCED  RETRY LATER
        ↓
   UPDATE LOCAL DATABASE
        ↓
   SYNC COMPLETED
```

### Notification Flow
```
FIREBASE SENDS PUSH NOTIFICATION
        ↓
   APP RECEIVES NOTIFICATION
        ↓
   [APP IN FOREGROUND]    [APP IN BACKGROUND]
        ↓                         ↓
   SHOW IN-APP NOTIFICATION   SHOW SYSTEM NOTIFICATION
        ↓                         ↓
   USER TAPS NOTIFICATION
        ↓
   NAVIGATE TO RELEVANT SCREEN
        ↓
   MARK NOTIFICATION AS READ
        ↓
   UPDATE NOTIFICATION COUNT
```

---

## 8. IMPLEMENTASI TEKNIS

### Dependency Injection (Hilt)
```kotlin
@Module
@InstallIn(SingletonComponent::class)
object NetworkModule {
    
    @Provides
    @Singleton
    fun provideRetrofit(): Retrofit {
        return Retrofit.Builder()
            .baseUrl(BuildConfig.API_BASE_URL)
            .addConverterFactory(GsonConverterFactory.create())
            .client(provideOkHttpClient())
            .build()
    }
    
    @Provides
    @Singleton
    fun provideOkHttpClient(): OkHttpClient {
        return OkHttpClient.Builder()
            .addInterceptor(AuthInterceptor())
            .addInterceptor(LoggingInterceptor())
            .connectTimeout(30, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .build()
    }
    
    @Provides
    @Singleton
    fun provideApiService(retrofit: Retrofit): HalalyticsApiService {
        return retrofit.create(HalalyticsApiService::class.java)
    }
}

@Module
@InstallIn(SingletonComponent::class)
object DatabaseModule {
    
    @Provides
    @Singleton
    fun provideDatabase(@ApplicationContext context: Context): AppDatabase {
        return Room.databaseBuilder(
            context,
            AppDatabase::class.java,
            "halalytics_database"
        ).build()
    }
    
    @Provides
    fun provideProductDao(database: AppDatabase): ProductDao {
        return database.productDao()
    }
    
    @Provides
    fun provideConsumptionDao(database: AppDatabase): ConsumptionDao {
        return database.consumptionDao()
    }
}
```

### Repository Pattern
```kotlin
@Singleton
class ProductRepository @Inject constructor(
    private val apiService: HalalyticsApiService,
    private val productDao: ProductDao,
    private val cachedScanResultDao: CachedScanResultDao
) {
    
    suspend fun scanProduct(barcode: String): Result<ScanResult> {
        return try {
            // Check local cache first
            val cached = cachedScanResultDao.getByBarcode(barcode)
            if (cached != null && isCacheValid(cached.timestamp)) {
                return Result.success(cached.toScanResult())
            }
            
            // Fetch from API
            val response = apiService.scanProduct(barcode)
            if (response.isSuccessful) {
                val scanResult = response.body()!!
                
                // Save to local database
                cachedScanResultDao.insert(
                    CachedScanResultEntity(
                        barcode = barcode,
                        scanResult = scanResult,
                        timestamp = System.currentTimeMillis()
                    )
                )
                
                Result.success(scanResult)
            } else {
                Result.failure(Exception("API Error"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
    
    private fun isCacheValid(timestamp: Long): Boolean {
        val cacheAge = System.currentTimeMillis() - timestamp
        return cacheAge < TimeUnit.HOURS.toMillis(24)
    }
}
```

### ViewModel with State Management
```kotlin
@HiltViewModel
class ScanViewModel @Inject constructor(
    private val productRepository: ProductRepository,
    private val userRepository: UserRepository
) : ViewModel() {
    
    private val _scanState = mutableStateOf<ScanState>(ScanState.Idle)
    val scanState: State<ScanState> = _scanState
    
    private val _recentScans = mutableStateOf<List<ScanResult>>(emptyList())
    val recentScans: State<List<ScanResult>> = _recentScans
    
    init {
        loadRecentScans()
    }
    
    fun scanProduct(barcode: String) {
        viewModelScope.launch {
            _scanState.value = ScanState.Loading
            
            when (val result = productRepository.scanProduct(barcode)) {
                is Result.Success -> {
                    _scanState.value = ScanState.Success(result.data)
                    saveScanHistory(result.data)
                }
                is Result.Failure -> {
                    _scanState.value = ScanState.Error(result.exception.message ?: "Unknown error")
                }
            }
        }
    }
    
    private fun saveScanHistory(scanResult: ScanResult) {
        viewModelScope.launch {
            // Save to local database
            // Update recent scans list
            _recentScans.value = listOf(scanResult) + _recentScans.value
        }
    }
    
    private fun loadRecentScans() {
        viewModelScope.launch {
            _recentScans.value = productRepository.getRecentScans()
        }
    }
}

sealed class ScanState {
    object Idle : ScanState()
    object Loading : ScanState()
    data class Success(val scanResult: ScanResult) : ScanState()
    data class Error(val message: String) : ScanState()
}
```

### Room Database Implementation
```kotlin
@Entity(tableName = "cached_scan_results")
data class CachedScanResultEntity(
    @PrimaryKey val barcode: String,
    val scanResult: String, // JSON string
    val timestamp: Long
)

@Dao
interface CachedScanResultDao {
    @Query("SELECT * FROM cached_scan_results WHERE barcode = :barcode")
    suspend fun getByBarcode(barcode: String): CachedScanResultEntity?
    
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insert(cachedScanResult: CachedScanResultEntity)
    
    @Query("DELETE FROM cached_scan_results WHERE timestamp < :expiryTime")
    suspend fun deleteExpired(expiryTime: Long)
    
    @Query("SELECT * FROM cached_scan_results ORDER BY timestamp DESC LIMIT 20")
    suspend fun getRecentScans(): List<CachedScanResultEntity>
}

@Database(
    entities = [
        CachedScanResultEntity::class,
        UserHealthProfileEntity::class,
        Consumption::class
    ],
    version = 1
)
@TypeConverters(Converters::class)
abstract class AppDatabase : RoomDatabase() {
    abstract fun cachedScanResultDao(): CachedScanResultDao
    abstract fun consumptionDao(): ConsumptionDao
}
```

### WorkManager for Background Sync
```kotlin
class SyncWorker(
    context: Context,
    workerParams: WorkerParameters
) : CoroutineWorker(context, workerParams) {
    
    @Inject
    lateinit var productRepository: ProductRepository
    
    override suspend fun doWork(): Result {
        return try {
            // Get all pending sync items
            val pendingItems = productRepository.getPendingSyncItems()
            
            // Sync each item
            pendingItems.forEach { item ->
                when (val result = productRepository.syncItem(item)) {
                    is Result.Success -> {
                        productRepository.markAsSynced(item.id)
                    }
                    is Result.Failure -> {
                        // Retry later
                        productRepository.incrementRetryCount(item.id)
                    }
                }
            }
            
            Result.success()
        } catch (e: Exception) {
            Result.failure()
        }
    }
}

// Schedule periodic sync
fun scheduleSyncWorker(context: Context) {
    val constraints = Constraints.Builder()
        .setRequiredNetworkType(NetworkType.CONNECTED)
        .setRequiresBatteryNotLow(true)
        .build()
    
    val syncRequest = PeriodicWorkRequestBuilder<SyncWorker>(
        15, TimeUnit.MINUTES
    )
        .setConstraints(constraints)
        .build()
    
    WorkManager.getInstance(context).enqueueUniquePeriodicWork(
        "sync_worker",
        ExistingPeriodicWorkPolicy.KEEP,
        syncRequest
    )
}
```

---

## 9. KEAMANAN APLIKASI

### Security Measures

#### 1. Biometric Authentication
```kotlin
class BiometricAuthHelper @Inject constructor(
    @ApplicationContext private val context: Context
) {
    private val biometricPrompt = BiometricPrompt(
        context as FragmentActivity,
        ContextCompat.getMainExecutor(context),
        object : BiometricPrompt.AuthenticationCallback() {
            override fun onAuthenticationSucceeded(result: BiometricPrompt.AuthenticationResult) {
                onAuthSuccess?.invoke()
            }
            
            override fun onAuthenticationFailed() {
                onAuthFailed?.invoke()
            }
            
            override fun onAuthenticationError(errorCode: Int, errString: CharSequence) {
                onAuthError?.invoke(errorCode, errString.toString())
            }
        }
    )
    
    private var onAuthSuccess: (() -> Unit)? = null
    private var onAuthFailed: (() -> Unit)? = null
    private var onAuthError: ((Int, String) -> Unit)? = null
    
    fun authenticate(
        onSuccess: () -> Unit,
        onFailed: () -> Unit,
        onError: (Int, String) -> Unit
    ) {
        onAuthSuccess = onSuccess
        onAuthFailed = onFailed
        onAuthError = onError
        
        val promptInfo = BiometricPrompt.PromptInfo.Builder()
            .setTitle("Biometric Authentication")
            .setSubtitle("Authenticate to access HALALYTICS")
            .setNegativeButtonText("Cancel")
            .build()
        
        biometricPrompt.authenticate(promptInfo)
    }
}
```

#### 2. Secure Token Storage
```kotlin
class SecureTokenManager @Inject constructor(
    @ApplicationContext private val context: Context
) {
    private val masterKey = MasterKey.Builder(context)
        .setKeyScheme(MasterKey.KeyScheme.AES256_GCM)
        .build()
    
    private val tokenPrefs = EncryptedSharedPreferences.create(
        context,
        "secure_token_prefs",
        masterKey,
        EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
        EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM
    )
    
    fun saveToken(token: String) {
        tokenPrefs.edit().putString("auth_token", token).apply()
    }
    
    fun getToken(): String? {
        return tokenPrefs.getString("auth_token", null)
    }
    
    fun clearToken() {
        tokenPrefs.edit().remove("auth_token").apply()
    }
}
```

#### 3. Network Security
```kotlin
class SSLPinningInterceptor : Interceptor {
    private val pinnedCertificates = listOf(
        "sha256/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA="
    )
    
    override fun intercept(chain: Interceptor.Chain): Response {
        val certificatePinner = CertificatePinner.Builder()
            .add("halalytics.id", *pinnedCertificates.toTypedArray())
            .build()
        
        val request = chain.request()
            .newBuilder()
            .build()
        
        return chain.withCertificatePinner(certificatePinner)
            .proceed(request)
    }
}
```

#### 4. Data Encryption
```kotlin
class DataEncryption @Inject constructor() {
    private val keyGenerator = KeyGenerator.getInstance(
        KeyProperties.KEY_ALGORITHM_AES, "AndroidKeyStore"
    )
    
    fun encryptData(data: String, alias: String): String {
        val cipher = Cipher.getInstance(TRANSFORMATION)
        cipher.init(Cipher.ENCRYPT_MODE, getSecretKey(alias))
        val encryptedBytes = cipher.doFinal(data.toByteArray())
        return Base64.encodeToString(encryptedBytes, Base64.DEFAULT)
    }
    
    fun decryptData(encryptedData: String, alias: String): String {
        val cipher = Cipher.getInstance(TRANSFORMATION)
        cipher.init(Cipher.DECRYPT_MODE, getSecretKey(alias))
        val decodedBytes = Base64.decode(encryptedData, Base64.DEFAULT)
        val decryptedBytes = cipher.doFinal(decodedBytes)
        return String(decryptedBytes)
    }
    
    companion object {
        private const val ANDROID_KEYSTORE = "AndroidKeyStore"
        private const val TRANSFORMATION = "AES/GCM/NoPadding"
    }
}
```

---

## 10. OPTIMISASI PERFORMA

### Image Loading Optimization
```kotlin
@Composable
fun OptimizedImage(
    url: String,
    contentDescription: String?,
    modifier: Modifier = Modifier
) {
    val imageModel = ImageRequest.Builder(LocalContext.current)
        .data(url)
        .crossfade(true)
        .memoryCachePolicy(CachePolicy.ENABLED)
        .diskCachePolicy(CachePolicy.ENABLED)
        .size(Size.ORIGINAL)
        .build()
    
    AsyncImage(
        model = imageModel,
        contentDescription = contentDescription,
        modifier = modifier,
        loading = {
            CircularProgressIndicator()
        },
        error = {
            Icon(Icons.Default.Error, contentDescription = null)
        }
    )
}
```

### Lazy Loading
```kotlin
@Composable
fun OptimizedProductList(
    products: List<Product>,
    onProductClick: (Product) -> Unit
) {
    LazyColumn(
        contentPadding = PaddingValues(16.dp),
        verticalArrangement = Arrangement.spacedBy(8.dp)
    ) {
        items(
            items = products,
            key = { it.id }
        ) { product ->
            ProductCard(
                product = product,
                onClick = { onProductClick(product) }
            )
        }
    }
}
```

### Database Optimization
```kotlin
@Dao
interface OptimizedProductDao {
    @Query("SELECT * FROM products WHERE barcode = :barcode LIMIT 1")
    suspend fun getProductByBarcode(barcode: String): ProductEntity?
    
    @Query("SELECT * FROM products ORDER BY timestamp DESC LIMIT :limit")
    suspend fun getRecentProducts(limit: Int): List<ProductEntity>
    
    @Query("SELECT * FROM products WHERE name LIKE :query LIMIT 20")
    suspend fun searchProducts(query: String): List<ProductEntity>
    
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertProduct(product: ProductEntity)
    
    @Query("DELETE FROM products WHERE timestamp < :expiryTime")
    suspend fun deleteExpiredProducts(expiryTime: Long)
}
```

---

## 11. TESTING

### Unit Testing
```kotlin
class ProductRepositoryTest {
    private lateinit var repository: ProductRepository
    private lateinit var mockApiService: HalalyticsApiService
    private lateinit var mockProductDao: ProductDao
    
    @Before
    fun setup() {
        mockApiService = mock()
        mockProductDao = mock()
        repository = ProductRepository(mockApiService, mockProductDao)
    }
    
    @Test
    fun `scanProduct should return success when API returns success`() = runTest {
        // Given
        val barcode = "8991234567890"
        val expectedScanResult = ScanResult(/* ... */)
        whenever(mockApiService.scanProduct(barcode)).thenReturn(
            Response.success(expectedScanResult)
        )
        
        // When
        val result = repository.scanProduct(barcode)
        
        // Then
        assertTrue(result.isSuccess)
        assertEquals(expectedScanResult, result.getOrNull())
    }
    
    @Test
    fun `scanProduct should return cached result when available`() = runTest {
        // Given
        val barcode = "8991234567890"
        val cachedResult = ScanResult(/* ... */)
        whenever(mockProductDao.getByBarcode(barcode)).thenReturn(
            CachedScanResultEntity(barcode, "json", System.currentTimeMillis())
        )
        
        // When
        val result = repository.scanProduct(barcode)
        
        // Then
        assertTrue(result.isSuccess)
        // Verify API was not called
        verify(mockApiService, never()).scanProduct(any())
    }
}
```

### UI Testing
```kotlin
class ScanScreenTest {
    @get:Rule
    val composeTestRule = createComposeRule()
    
    @Test
    fun scanScreen_displaysScannerView() {
        composeTestRule.setContent {
            ScanScreen(
                onBarcodeDetected = {},
                onNavigateBack = {}
            )
        }
        
        composeTestRule.onNodeWithText("Scan Product").assertIsDisplayed()
    }
    
    @Test
    fun scanScreen_navigatesToResult_whenBarcodeDetected() {
        var detectedBarcode = ""
        
        composeTestRule.setContent {
            ScanScreen(
                onBarcodeDetected = { barcode -> detectedBarcode = barcode },
                onNavigateBack = {}
            )
        }
        
        // Simulate barcode detection
        // ...
        
        assertEquals("8991234567890", detectedBarcode)
    }
}
```

---

## 12. DEPLOYMENT

### Build Configuration
```kotlin
android {
    compileSdk 34
    
    defaultConfig {
        applicationId = "com.example.halalyticscompose"
        minSdk 26
        targetSdk 34
        versionCode 1
        versionName "1.0.0"
        
        testInstrumentationRunner "androidx.test.runner.AndroidJUnitRunner"
        vectorDrawables {
            useSupportLibrary = true
        }
    }
    
    buildTypes {
        release {
            isMinifyEnabled = true
            isShrinkResources = true
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
            
            buildConfigField("String", "API_BASE_URL", "\"https://api.halalytics.id\"")
        }
        
        debug {
            isMinifyEnabled = false
            buildConfigField("String", "API_BASE_URL", "\"https://dev-api.halalytics.id\"")
        }
    }
    
    compileOptions {
        sourceCompatibility JavaVersion.VERSION_17
        targetCompatibility JavaVersion.VERSION_17
    }
    
    kotlinOptions {
        jvmTarget = "17"
    }
    
    buildFeatures {
        compose = true
        buildConfig = true
    }
    
    composeOptions {
        kotlinCompilerExtensionVersion = "1.5.0"
    }
    
    packaging {
        resources {
            excludes += "/META-INF/{AL2.0,LGPL2.1}"
        }
    }
}
```

### Signing Configuration
```kotlin
android {
    signingConfigs {
        create("release") {
            storeFile = file("../keystore/release.jks")
            storePassword = System.getenv("KEYSTORE_PASSWORD")
            keyAlias = System.getenv("KEY_ALIAS")
            keyPassword = System.getenv("KEY_PASSWORD")
        }
    }
    
    buildTypes {
        release {
            signingConfig = signingConfigs.getByName("release")
        }
    }
}
```

### CI/CD Pipeline (GitHub Actions)
```yaml
name: Android CI

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  build:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Set up JDK 17
        uses: actions/setup-java@v3
        with:
          java-version: '17'
          distribution: 'temurin'
          
      - name: Grant execute permission for gradlew
        run: chmod +x gradlew
        
      - name: Build with Gradle
        run: ./gradlew build
        
      - name: Run Unit Tests
        run: ./gradlew test
        
      - name: Run Instrumented Tests
        uses: reactivecircus/android-emulator-runner@v2
        with:
          api-level: 29
          script: ./gradlew connectedAndroidTest
          
      - name: Upload APK
        uses: actions/upload-artifact@v3
        with:
          name: app-release
          path: app/build/outputs/apk/release/app-release.apk
```

---

## 13. KENDALA & SOLUSI

### Technical Challenges

#### 1. Camera Permission Handling
**Masalah**: Runtime permission request kompleks
**Solusi**:
- Gunakan Accompanist Permissions library
- Implement proper permission rationale
- Fallback ke gallery jika permission ditolak

#### 2. Offline Sync Complexity
**Masalah**: Conflict resolution saat sync
**Solusi**:
- Implement last-write-wins strategy
- Use timestamp for conflict resolution
- Provide user interface for manual resolution

#### 3. Memory Management
**Masalah**: Memory leak dengan camera dan image processing
**Solusi**:
- Proper lifecycle management
- Image compression
- Weak references untuk listeners
- Memory profiling dengan LeakCanary

#### 4. Battery Optimization
**Masalah**: Background sync menguras baterai
**Solusi**:
- Gunakan WorkManager dengan constraints
- Batch sync operations
- Implement adaptive sync frequency
- Use Doze mode friendly approach

### Resource Constraints

#### 5. APK Size
**Masalah**: APK size besar karena dependencies
**Solusi**:
- Enable App Bundle
- Implement dynamic feature modules
- ProGuard/R8 optimization
- Remove unused resources

#### 6. Development Time
**Masalah**: Waktu pengembangan terbatas
**Solusi**:
- Prioritas fitur core
- Gunakan library siap pakai
- Implement MVP dulu
- Iterative development

---

## 14. FITUR MASA DEPAN

### Short-term (3-6 bulan)
1. **Wearable Integration**
   - Smartwatch sync
   - Health data integration
   - Activity tracking
   - Heart rate monitoring

2. **AR Product Scanner**
   - AR overlay pada produk
   - 3D product visualization
   - Interactive product info
   - AR navigation di supermarket

3. **Voice Commands**
   - Voice search produk
   - Voice input untuk health tracking
   - Voice assistant integration
   - Multi-language voice support

### Medium-term (6-12 bulan)
4. **Advanced AI Features**
   - On-device ML model
   - Real-time food recognition
   - Custom diet recommendations
   - Meal planning AI

5. **Social Features**
   - Share scan results
   - Community challenges
   - Leaderboards
   - Social sharing integration

6. **Payment Integration**
   - In-app purchases
   - Subscription model
   - Premium features
   - Payment gateway integration

### Long-term (1-2 tahun)
7. **IoT Integration**
   - Smart fridge integration
   - Automatic inventory tracking
   - Smart kitchen appliances
   - Auto-ordering system

8. **Healthcare Integration**
   - Telemedicine integration
   - Electronic health records
   - Doctor consultation
   - Prescription management

9. **Global Expansion**
   - Multi-language support
   - Country-specific databases
   - Local certification integration
   - Cultural adaptation

---

## 15. KESIMPULAN

### Ringkasan Project
HALALYTICS Android App adalah aplikasi mobile modern yang menyediakan:
- Scanner produk cerdas dengan ML Kit
- Verifikasi halal komprehensif
- Tracking kesehatan personal
- Manajemen kesehatan keluarga
- Offline capability dengan Room Database
- Biometric authentication
- Real-time notifications
- UI modern dengan Jetpack Compose
- Arsitektur MVVM yang bersih
- Dependency Injection dengan Hilt

### Keunggulan
1. **Modern UI**: Jetpack Compose dengan Material Design 3
2. **Offline-First**: Tetap berfungsi tanpa internet
3. **AI-Powered**: Integrasi AI untuk analisis cerdas
4. **Secure**: Biometric auth dan encryption
5. **Performant**: Dioptimasi untuk performa tinggi
6. **Scalable**: Arsitektur yang siap untuk scale
7. **User-Friendly**: UI intuitif dan mudah digunakan

### Dampak
- Mempermudah verifikasi produk halal secara mobile
- Meningkatkan awareness kesehatan masyarakat
- Menyediakan tools praktis untuk tracking kesehatan
- Mendukung manajemen kesehatan keluarga
- Mendorong gaya hidup halal dan sehat

### Future Outlook
HALALYTICS Android App memiliki potensi untuk:
- Menjadi aplikasi halal terpopuler di Indonesia
- Integrasi dengan ekosistem kesehatan digital
- Ekspansi ke platform lain (iOS, Web)
- Menjadi standard industri untuk mobile halal verification

---

**Dokumen ini dibuat untuk keperluan laporan project HALALYTICS Android Application**
