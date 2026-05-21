package com.example.halalyticscompose.ui.screens.product

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.Card
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.LinearProgressIndicator
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import com.example.halalyticscompose.presentation.viewmodel.ProductDetailUiState
import com.example.halalyticscompose.ui.components.ErrorState
import com.example.halalyticscompose.ui.components.HalalyticsTopBar
import com.example.halalyticscompose.ui.components.PremiumHeroSection
import com.example.halalyticscompose.ui.components.LoadingState
import com.example.halalyticscompose.ui.theme.HalalyticsColors
import com.example.halalyticscompose.ui.theme.HalalyticsUiTokens

@Composable
fun ProductDetailScreen(
    uiState: ProductDetailUiState,
    onRetry: () -> Unit,
) {
    Scaffold(topBar = { HalalyticsTopBar() }) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(HalalyticsColors.Background)
                .padding(padding),
        ) {
            PremiumHeroSection(
                title = "Product Intelligence Hero",
                subtitle = "Analisis premium untuk halal status, nutrisi, dan warning produk.",
                modifier = Modifier.padding(HalalyticsUiTokens.ScreenPadding),
            )
            when {
                uiState.isLoading -> LoadingState()
                uiState.error != null -> ErrorState(message = uiState.error, onRetry = onRetry)
                uiState.detail != null -> {
                    val d = uiState.detail
                    LazyColumn(
                        modifier = Modifier
                            .fillMaxSize()
                            .padding(HalalyticsUiTokens.ScreenPadding),
                        verticalArrangement = Arrangement.spacedBy(12.dp),
                    ) {
                        item { 
                            ScoreCard("Skor Halal", d.halal_status, d.halal_score / 100f)
                        }
                        item { 
                            ScoreCard("Skor Kesehatan", d.health_status, d.health_score / 100f)
                        }
                        item { DetailCard("Bahan Dominan", d.dominant_ingredient) }
                        item { DetailCard("Efek Jangka Pendek", d.short_term_effect) }
                        item { DetailCard("Efek Jangka Panjang", d.long_term_effect) }
                        item { DetailCard("Rekomendasi Personal", d.personalized_recommendation) }
                        item { DetailCard("Confidence", d.confidence) }
                        item { SourceChips(d.sources) }
                        if (d.warnings.isNotEmpty()) {
                            item { WarningCard(d.warnings) }
                        }
                    }
                }
                else -> ErrorState(message = "Data produk kosong", onRetry = onRetry)
            }
        }
    }
}

@Composable
private fun DetailCard(title: String, value: String) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(14.dp), verticalArrangement = Arrangement.spacedBy(4.dp)) {
            Text(title, style = MaterialTheme.typography.titleSmall, color = HalalyticsColors.Primary)
            Text(value, style = MaterialTheme.typography.bodyMedium, color = HalalyticsColors.Text)
        }
    }
}


@Composable
private fun ScoreCard(title: String, subtitle: String, progress: Float) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(14.dp), verticalArrangement = Arrangement.spacedBy(6.dp)) {
            Text(title, style = MaterialTheme.typography.titleSmall, color = HalalyticsColors.Primary)
            Text(subtitle, style = MaterialTheme.typography.bodyMedium, color = HalalyticsColors.Text)
            LinearProgressIndicator(progress = { progress.coerceIn(0f, 1f) }, modifier = Modifier.fillMaxWidth())
        }
    }
}

@Composable
private fun SourceChips(sources: List<String>) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(14.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text("Sources", style = MaterialTheme.typography.titleSmall, color = HalalyticsColors.Primary)
            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                sources.take(3).forEach { src ->
                    Card {
                        Text(src, modifier = Modifier.padding(horizontal = 10.dp, vertical = 6.dp), color = HalalyticsColors.Text)
                    }
                }
            }
        }
    }
}

@Composable
private fun WarningCard(warnings: List<String>) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(14.dp), verticalArrangement = Arrangement.spacedBy(6.dp)) {
            Text("Health Warnings", style = MaterialTheme.typography.titleSmall, color = HalalyticsColors.Primary)
            warnings.forEach {
                Text("• $it", style = MaterialTheme.typography.bodyMedium, color = HalalyticsColors.Text)
            }
        }
    }
}
