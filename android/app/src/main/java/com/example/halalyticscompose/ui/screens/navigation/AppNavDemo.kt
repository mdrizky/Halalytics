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

@Composable
fun AppNavDemo() {
    val navController = rememberNavController()
    var aiState by remember { mutableStateOf(AiChatUiState()) }
    var detailState by remember { mutableStateOf(ProductDetailUiState()) }
    var scanLoading by remember { mutableStateOf(false) }
    var scanError by remember { mutableStateOf<String?>(null) }

    NavHost(navController = navController, startDestination = DemoRoutes.SPLASH) {
        composable(DemoRoutes.SPLASH) {
            SplashScreen(onFinished = { navController.navigate(DemoRoutes.LOGIN) { popUpTo(DemoRoutes.SPLASH) { inclusive = true } } })
        }
        composable(DemoRoutes.LOGIN) {
            LoginScreen(isLoading = false, error = null, onLogin = { username, password ->
                if (username.isNotBlank() && password.length >= 8) navController.navigate(DemoRoutes.HOME)
            })
        }
        composable(DemoRoutes.HOME) {
            HomeScreen(
                username = "daffa",
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
            AIChatScreen(uiState = aiState, onSendMessage = { message -> aiState = AiChatUiState(reply = "AI: $message") })
        }
        composable(DemoRoutes.PROFILE) {
            EditProfileScreen("daffa", "24", "-", "-", onSave = { _, _, _, _ -> navController.popBackStack() })
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
