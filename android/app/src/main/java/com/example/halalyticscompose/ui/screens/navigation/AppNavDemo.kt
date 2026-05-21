package com.example.halalyticscompose.ui.screens.navigation

import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import com.example.halalyticscompose.presentation.viewmodel.AiChatUiState
import com.example.halalyticscompose.ui.screens.ai.AIChatScreen
import com.example.halalyticscompose.ui.screens.auth.LoginScreen
import com.example.halalyticscompose.ui.screens.home.HomeScreen
import com.example.halalyticscompose.ui.screens.profile.EditProfileScreen
import com.example.halalyticscompose.ui.screens.splash.SplashScreen

enum class DemoRoute { SPLASH, LOGIN, HOME, AI, PROFILE }

@Composable
fun AppNavDemo() {
    var route by remember { mutableStateOf(DemoRoute.SPLASH) }
    var aiState by remember { mutableStateOf(AiChatUiState()) }

    when (route) {
        DemoRoute.SPLASH -> SplashScreen(onFinished = { route = DemoRoute.LOGIN })
        DemoRoute.LOGIN -> LoginScreen(
            isLoading = false,
            error = null,
            onLogin = { username, password ->
                if (username.isNotBlank() && password.length >= 8) route = DemoRoute.HOME
            },
        )
        DemoRoute.HOME -> HomeScreen(
            username = "daffa",
            onOpenScan = {},
            onOpenAiChat = { route = DemoRoute.AI },
            onOpenProfile = { route = DemoRoute.PROFILE },
        )
        DemoRoute.AI -> AIChatScreen(
            uiState = aiState,
            onSendMessage = { message ->
                aiState = AiChatUiState(reply = "AI: $message")
            },
        )
        DemoRoute.PROFILE -> EditProfileScreen(
            initialName = "daffa",
            initialAge = "24",
            initialDiseases = "-",
            initialAllergies = "-",
            onSave = { _, _, _, _ -> route = DemoRoute.HOME },
        )
    }
}
