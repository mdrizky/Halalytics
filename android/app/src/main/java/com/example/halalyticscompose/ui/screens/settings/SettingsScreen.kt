package com.example.halalyticscompose.ui.screens.settings

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.res.stringResource
import com.example.halalyticscompose.R
import com.example.halalyticscompose.ui.components.HalalyticsTopBar
import com.example.halalyticscompose.ui.theme.HalalyticsColors
import com.example.halalyticscompose.ui.theme.HalalyticsUiTokens

@Composable
fun SettingsScreen() {
    Scaffold(topBar = { HalalyticsTopBar() }) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(HalalyticsColors.Background)
                .padding(padding)
                .padding(HalalyticsUiTokens.ScreenPadding),
            verticalArrangement = Arrangement.spacedBy(HalalyticsUiTokens.SectionSpacing),
        ) {
            Text("Bahasa, notifikasi, dan privasi terpusat di halaman ini.", color = HalalyticsColors.Text)
            Text(stringResource(R.string.settings), style = MaterialTheme.typography.headlineSmall, color = HalalyticsColors.Primary)
            Text(stringResource(R.string.language_setting), color = HalalyticsColors.Text)
            Text(stringResource(R.string.notification), color = HalalyticsColors.Text)
            Text(stringResource(R.string.privacy_policy), color = HalalyticsColors.Text)
        }
    }
}
