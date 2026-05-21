package com.example.halalyticscompose.ui.screens.navigation

import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.example.halalyticscompose.data.remote.ProductDetailData
import com.example.halalyticscompose.presentation.viewmodel.AiChatUiState
import com.example.halalyticscompose.presentation.viewmodel.ProductDetailUiState
import com.example.halalyticscompose.ui.screens.ai.AIChatScreen
import com.example.halalyticscompose.ui.screens.article.ArticleScreen
import com.example.halalyticscompose.ui.screens.auth.LoginScreen
import com.example.halalyticscompose.ui.screens.donation.DonationScreen
import com.example.halalyticscompose.ui.screens.history.HistoryScreen
import com.example.halalyticscompose.ui.screens.home.HomeScreen
import com.example.halalyticscompose.ui.screens.product.ProductDetailScreen
import com.example.halalyticscompose.ui.screens.profile.EditProfileScreen
import com.example.halalyticscompose.ui.screens.scan.ScanScreen
import com.example.halalyticscompose.ui.screens.settings.SettingsScreen
import com.example.halalyticscompose.ui.screens.splash.SplashScreen

object DemoRoutes {
    const val SPLASH = "splash"
    const val LOGIN = "login"
    const val HOME = "home"
    const val SCAN = "scan"
    const val AI = "ai"
    const val PROFILE = "profile"
    const val SETTINGS = "settings"
    const val HISTORY = "history"
    const val ARTICLE = "article"
    const val DONATION = "donation"
    const val PRODUCT_DETAIL = "product_detail"
}

private enum class DemoIntent {
    HALAL_QUESTION,
    MEDICINE_QUESTION,
    DISEASE_QUESTION,
    NUTRITION_QUESTION,
    SKINCARE_QUESTION,
    SCAN_ANALYSIS,
    BMI_CALCULATION,
    GENERAL_HEALTH,
}

private fun classifyDemoIntent(message: String): DemoIntent {
    val text = message.lowercase()
    return when {
        text.matches(Regex("^\\d{8,13}$")) -> DemoIntent.SCAN_ANALYSIS
        listOf("halal", "haram", "syubhat").any(text::contains) -> DemoIntent.HALAL_QUESTION
        listOf("obat", "efek samping", "dosis").any(text::contains) -> DemoIntent.MEDICINE_QUESTION
        listOf("gejala", "penyakit", "tbc", "diabetes", "hipertensi").any(text::contains) -> DemoIntent.DISEASE_QUESTION
        listOf("diet", "kalori", "gizi", "protein", "serat").any(text::contains) -> DemoIntent.NUTRITION_QUESTION
        listOf("bmi", "indeks massa tubuh", "berat badan", "tinggi badan").any(text::contains) -> DemoIntent.BMI_CALCULATION
        listOf("skincare", "kulit", "jerawat").any(text::contains) -> DemoIntent.SKINCARE_QUESTION
        else -> DemoIntent.GENERAL_HEALTH
    }
}



private fun calculateBmiFromPrompt(prompt: String, profileBmi: Double?): Pair<Double, String>? {
    val regex = Regex("""(?:berat|bb)\s*(\d{2,3}(?:[.,]\d+)?)\D+(?:tinggi|tb)\s*(\d{2,3}(?:[.,]\d+)?)""")
    val match = regex.find(prompt.lowercase())
    if (match == null) {
        val bmi = profileBmi ?: return null
        val category = when {
            bmi < 18.5 -> "Kurus"
            bmi < 25.0 -> "Normal"
            bmi < 30.0 -> "Overweight"
            else -> "Obesitas"
        }
        return bmi to category
    }
    val weight = match.groupValues[1].replace(',', '.').toDoubleOrNull() ?: return null
    val rawHeight = match.groupValues[2].replace(',', '.').toDoubleOrNull() ?: return null
    val heightMeter = if (rawHeight > 3) rawHeight / 100.0 else rawHeight

    if (weight !in 20.0..300.0 || heightMeter !in 1.0..2.5) return null

    val bmi = weight / (heightMeter * heightMeter)
    val category = when {
        bmi < 18.5 -> "Kurus"
        bmi < 25.0 -> "Normal"
        bmi < 30.0 -> "Overweight"
        else -> "Obesitas"
    }
    return bmi to category
}

private fun buildDemoAiReply(intent: DemoIntent, prompt: String, username: String, age: String, profileBmi: Double?): String {
    val header = "Profil pengguna: $username, usia $age tahun."
    return when (intent) {
        DemoIntent.HALAL_QUESTION -> """
            Fokus Halal untuk: $prompt
            $header

            • Cek komposisi bahan, emulsifier, flavor, dan sumber gelatin.
            • Jika tidak ada sertifikat halal valid, status terbaik adalah perlu verifikasi.
            • Hindari klaim pasti halal/haram tanpa bukti label resmi atau sumber BPJPH/MUI.
        """.trimIndent()

        DemoIntent.MEDICINE_QUESTION -> """
            Info Obat untuk: $prompt
            $header

            • Jelaskan fungsi obat dan efek samping umum yang perlu dipantau.
            • Jangan ubah dosis tanpa arahan dokter/apoteker.
            • Jika muncul reaksi alergi, sesak napas, atau bengkak, segera cari bantuan medis.
        """.trimIndent()

        DemoIntent.DISEASE_QUESTION -> {
            val p = prompt.lowercase()
            val redFlags = listOf("pingsan", "sesak", "nyeri dada", "kejang", "muntah darah", "bab hitam")
            val urgent = redFlags.any { p.contains(it) }
            val likelyCause = when {
                p.contains("pingsan") -> "Kemungkinan sinkop (tekanan darah turun/dehidrasi/kelelahan)."
                p.contains("demam") && p.contains("batuk") -> "Kemungkinan infeksi saluran napas, perlu evaluasi jika >3 hari."
                p.contains("nyeri perut") -> "Kemungkinan gangguan pencernaan, pantau dehidrasi dan nyeri memberat."
                else -> "Perlu evaluasi klinis untuk memastikan penyebab keluhan."
            }
            val urgentText = if (urgent) "🚨 RED FLAG: segera ke IGD/dokter sekarang jika gejala aktif atau berulang." else "Belum tampak red flag berat, tetap pantau progres gejala 24 jam."
            """
            Analisis Keluhan Medis: $prompt
            $header

            Ringkasan: $likelyCause
            Tindak lanjut: $urgentText

            Rekomendasi awal aman:
            • Istirahat, hidrasi cukup, dan hindari aktivitas berat sementara.
            • Catat durasi gejala, pemicu, dan gejala penyerta untuk konsultasi dokter.
            • Jangan konsumsi obat keras tanpa evaluasi tenaga medis.
            """.trimIndent()
        }

        DemoIntent.NUTRITION_QUESTION -> """
            Rekomendasi Nutrisi untuk: $prompt
            $header

            • Prioritaskan porsi seimbang: protein, serat, lemak baik, dan karbohidrat kompleks.
            • Batasi gula tambahan dan sodium harian.
            • Evaluasi kebiasaan makan selama 7 hari untuk personalisasi lanjut.
        """.trimIndent()

        DemoIntent.SKINCARE_QUESTION -> """
            Edukasi Skincare untuk: $prompt
            $header

            • Fokus bahan aktif, potensi iritasi, dan kecocokan tipe kulit.
            • Mulai dari frekuensi rendah untuk bahan aktif kuat.
            • Hentikan pemakaian jika iritasi berat dan konsultasi profesional kesehatan kulit.
        """.trimIndent()

        DemoIntent.BMI_CALCULATION -> {
            val bmiResult = calculateBmiFromPrompt(prompt, profileBmi)
            if (bmiResult == null) {
                """
                Kalkulator BMI
                $header

                Format input belum valid. Gunakan contoh: "BB 58 TB 165" atau "berat 58 tinggi 165".
                Saya akan hitung BMI, kategori, dan saran personal secara aman.
                """.trimIndent()
            } else {
                val (bmi, category) = bmiResult
                """
                Hasil Kalkulator BMI
                $header

                BMI Anda: ${"%.2f".format(bmi)}
                Kategori: $category
                Red-flag: ${if (bmi < 16.0 || bmi >= 35.0) "Risiko tinggi, konsultasi dokter segera." else "Tidak ada red-flag ekstrem."}

                Saran AI Coach:
                • Fokus pola makan seimbang dan aktivitas fisik rutin minimal 150 menit/minggu.
                • Pantau berat tiap minggu, jangan target turun/naik ekstrem.
                • Jika ada komorbid, konsultasikan target BMI ke tenaga medis.
                """.trimIndent()
            }
        }

        DemoIntent.SCAN_ANALYSIS -> """
            Analisis Barcode: $prompt
            $header

            • Data produk akan dianalisis untuk skor halal + skor kesehatan.
            • Cek komposisi, alergen, gula, sodium, dan tingkat pemrosesan.
            • Hasil ini edukatif, verifikasi label resmi tetap disarankan.
        """.trimIndent()

        DemoIntent.GENERAL_HEALTH -> """
            Jawaban Kesehatan Umum untuk: $prompt
            $header

            • Saya bisa bantu topik halal, nutrisi, penyakit, obat, skincare, dan analisis produk.
            • Tulis pertanyaan lebih spesifik agar rekomendasi lebih tepat.
        """.trimIndent()
    }
}


private fun computeBmi(weightKg: String, heightCm: String): Double? {
    val w = weightKg.replace(',', '.').toDoubleOrNull() ?: return null
    val hCm = heightCm.replace(',', '.').toDoubleOrNull() ?: return null
    if (w !in 20.0..300.0 || hCm !in 100.0..250.0) return null
    val hm = hCm / 100.0
    return w / (hm * hm)
}

private fun bmiCoachTarget(category: String): String = when (category) {
    "Kurus" -> "Target 2 Bulan: Naik 2-4 kg secara bertahap"
    "Normal" -> "Target 2 Bulan: Pertahankan berat badan"
    "Overweight" -> "Target 2 Bulan: Turun 2-4 kg secara aman"
    else -> "Target 2 Bulan: Turun 3-6 kg + evaluasi dokter"
}
@Composable
fun AppNavDemo() {
    val navController = rememberNavController()
    var aiState by remember { mutableStateOf(AiChatUiState()) }
    var detailState by remember { mutableStateOf(ProductDetailUiState()) }
    var scanLoading by remember { mutableStateOf(false) }
    var scanError by remember { mutableStateOf<String?>(null) }
    var activeUsername by remember { mutableStateOf("User") }
    var activeCondition by remember { mutableStateOf("Tidak ada riwayat penyakit") }
    var activeAge by remember { mutableStateOf("24") }
    var activeAllergies by remember { mutableStateOf("Tidak ada alergi") }
    var activeWeightKg by remember { mutableStateOf("58") }
    var activeHeightCm by remember { mutableStateOf("165") }
    var activeBmi by remember { mutableStateOf(21.2) }
    var voiceInputState by remember { mutableStateOf<String?>(null) }
    var voiceTranscript by remember { mutableStateOf<String?>(null) }

    NavHost(navController = navController, startDestination = DemoRoutes.SPLASH) {
        composable(DemoRoutes.SPLASH) {
            SplashScreen(onFinished = { navController.navigate(DemoRoutes.LOGIN) { popUpTo(DemoRoutes.SPLASH) { inclusive = true } } })
        }
        composable(DemoRoutes.LOGIN) {
            LoginScreen(isLoading = false, error = null, onLogin = { username, password ->
                if (username.isNotBlank() && password.length >= 8) {
                    activeUsername = username
                    navController.navigate(DemoRoutes.HOME)
                }
            })
        }
        composable(DemoRoutes.HOME) {
            HomeScreen(
                username = activeUsername,
                onOpenScan = { navController.navigate(DemoRoutes.SCAN) },
                onOpenAiChat = { navController.navigate(DemoRoutes.AI) },
                onOpenProfile = { navController.navigate(DemoRoutes.PROFILE) },
                onOpenSettings = { navController.navigate(DemoRoutes.SETTINGS) },
                onOpenHistory = { navController.navigate(DemoRoutes.HISTORY) },
                onOpenArticle = { navController.navigate(DemoRoutes.ARTICLE) },
                onOpenDonation = { navController.navigate(DemoRoutes.DONATION) },
            )
        }
        composable(DemoRoutes.SCAN) {
            ScanScreen(
                isLoading = scanLoading,
                error = scanError,
                onRetry = { scanError = null },
                onBarcodeDetected = { barcode ->
                    scanLoading = true
                    detailState = ProductDetailUiState(isLoading = true)
                    detailState = ProductDetailUiState(
                        detail = ProductDetailData(
                            barcode = barcode,
                            halal_status = "Kemungkinan Halal (AI Analysis)",
                            halal_score = 82,
                            health_status = "Perlu Perhatian",
                            health_score = 48,
                            dominant_ingredient = "Gula",
                            short_term_effect = "Jika dikonsumsi berlebihan dapat meningkatkan lonjakan gula darah.",
                            long_term_effect = "Konsumsi berlebih jangka panjang dapat meningkatkan risiko resistensi insulin.",
                            personalized_recommendation = "Batasi konsumsi 1 porsi kecil dan kombinasikan dengan makanan tinggi serat.",
                            confidence = "medium",
                            sources = listOf("OpenFoodFacts", "Rule-Based Analyzer v1"),
                            warnings = listOf("Tinggi gula", "Kalori kosong"),
                        ),
                    )
                    scanLoading = false
                    navController.navigate(DemoRoutes.PRODUCT_DETAIL)
                },
                onBack = { navController.popBackStack() },
            )
        }
        composable(DemoRoutes.AI) {
            AIChatScreen(
                uiState = aiState,
                bmiValue = activeBmi,
                voiceInputState = voiceInputState,
                voiceTranscript = voiceTranscript,
                onVoiceInputRequest = {
                    voiceInputState = "🎙️ Listening..."
                    voiceTranscript = "Apa gejala TBC dan kapan harus ke dokter?"
                    voiceInputState = "✅ Voice note captured"
                },
                onSendMessage = { message ->
                    val prompt = message.trim()
                    aiState = if (prompt.isBlank()) {
                        AiChatUiState(reply = "Silakan tulis bahan makanan yang ingin kamu masak.")
                    } else {
                        val intent = classifyDemoIntent(prompt)
                        AiChatUiState(
                            reply = buildDemoAiReply(intent, prompt, activeUsername, activeAge, activeBmi),
                        )
                    }
                },
            )
        }
        composable(DemoRoutes.PROFILE) {
            EditProfileScreen(activeUsername, activeAge, activeWeightKg, activeHeightCm, activeCondition, activeAllergies, onBack = { navController.popBackStack() }, onSave = { name, age, weightKg, heightCm, diseases, allergies ->
                activeUsername = name
                activeAge = age
                activeWeightKg = weightKg
                activeHeightCm = heightCm
                activeBmi = computeBmi(weightKg, heightCm) ?: activeBmi
                activeCondition = diseases
                activeAllergies = allergies
                navController.popBackStack()
            })
        }
        composable(DemoRoutes.SETTINGS) { SettingsScreen(onBack = { navController.popBackStack() }) }
        composable(DemoRoutes.HISTORY) { HistoryScreen(onBack = { navController.popBackStack() }) }
        composable(DemoRoutes.ARTICLE) { ArticleScreen(onBack = { navController.popBackStack() }) }
        composable(DemoRoutes.DONATION) { DonationScreen(onBack = { navController.popBackStack() }) }
        composable(DemoRoutes.PRODUCT_DETAIL) {
            ProductDetailScreen(uiState = detailState, onRetry = { navController.navigate(DemoRoutes.SCAN) })
        }
    }
}
