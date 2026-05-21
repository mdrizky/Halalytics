package com.example.halalyticscompose.ui.screens.donation

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
import com.example.halalyticscompose.ui.components.PremiumHeroSection
import com.example.halalyticscompose.ui.theme.HalalyticsColors
import com.example.halalyticscompose.ui.theme.HalalyticsUiTokens

@Composable
fun DonationScreen(onBack: (() -> Unit)? = null) {
    Scaffold(topBar = { HalalyticsTopBar(showBack = onBack != null, onBackClick = { onBack?.invoke() }) }) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(HalalyticsColors.Background)
                .padding(padding)
                .padding(HalalyticsUiTokens.ScreenPadding),
            verticalArrangement = Arrangement.spacedBy(HalalyticsUiTokens.SectionSpacing),
        ) {
            PremiumHeroSection(
                title = "Donation Impact Hero",
                subtitle = "Dukung kampanye kesehatan dan halal dengan progress real-time.",
            )
            Text("Campaign aktif, target, dan progress akan sinkron dari backend.", color = HalalyticsColors.Text)
            Text(stringResource(R.string.donation), style = MaterialTheme.typography.headlineSmall, color = HalalyticsColors.Primary)
            Text(stringResource(R.string.donate_now), color = HalalyticsColors.Text)
        }
    }
}
