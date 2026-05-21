package com.example.halalyticscompose.ui.screens.article

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
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
fun ArticleScreen(onBack: (() -> Unit)? = null) {
    Scaffold(topBar = { HalalyticsTopBar(showBack = onBack != null, onBackClick = { onBack?.invoke() }) }) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(HalalyticsColors.Background)
                .padding(padding)
                .padding(HalalyticsUiTokens.ScreenPadding),
            verticalArrangement = Arrangement.spacedBy(HalalyticsUiTokens.SectionSpacing),
        ) {
            Text(stringResource(R.string.article), style = MaterialTheme.typography.headlineSmall, color = HalalyticsColors.Primary)
            val articles = listOf(
                "Panduan pilih produk halal dengan label BPJPH",
                "Cek gula, garam, dan lemak sebelum membeli makanan",
                "Tips membaca komposisi produk agar lebih aman",
            )
            articles.forEach { title ->
                Card(
                    modifier = Modifier.fillMaxWidth().clickable { },
                    shape = RoundedCornerShape(16.dp),
                    colors = CardDefaults.cardColors(containerColor = androidx.compose.ui.graphics.Color.White),
                ) {
                    Column(Modifier.padding(14.dp)) {
                        Text(title, style = MaterialTheme.typography.titleMedium, color = HalalyticsColors.Text)
                        Text("Sumber: Public Health Feed", style = MaterialTheme.typography.bodySmall, color = HalalyticsColors.Primary)
                    }
                }
            }
        }
    }
}
