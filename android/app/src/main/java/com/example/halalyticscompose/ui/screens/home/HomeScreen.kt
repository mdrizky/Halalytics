package com.example.halalyticscompose.ui.screens.home

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.unit.dp
import com.example.halalyticscompose.R
import com.example.halalyticscompose.ui.components.HalalyticsTopBar
import com.example.halalyticscompose.ui.theme.HalalyticsColors

@Composable
fun HomeScreen(
    username: String,
    onOpenScan: () -> Unit,
    onOpenAiChat: () -> Unit,
    onOpenProfile: () -> Unit,
    onOpenSettings: () -> Unit,
    onOpenHistory: () -> Unit,
    onOpenArticle: () -> Unit,
    onOpenDonation: () -> Unit,
) {
    Scaffold(topBar = { HalalyticsTopBar(subtitle = "Welcome, $username") }) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(HalalyticsColors.Background)
                .padding(padding)
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp),
        ) {
            Text(
                text = stringResource(R.string.hello_user, username),
                style = MaterialTheme.typography.titleLarge,
                color = HalalyticsColors.Text,
            )
            Card(
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(20.dp),
                modifier = Modifier.fillMaxWidth(),
            ) {
                Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
                    Text("Kategori Produk", fontWeight = FontWeight.Bold)
                    Row(horizontalArrangement = Arrangement.spacedBy(10.dp), modifier = Modifier.fillMaxWidth()) {
                        listOf("Makanan", "Minuman", "Kosmetik", "Obat").forEach {
                            Box(
                                modifier = Modifier
                                    .weight(1f)
                                    .height(70.dp)
                                    .border(1.dp, Color(0xFFB7D9CF), RoundedCornerShape(16.dp))
                                    .background(Color(0xFFEAF7F1), RoundedCornerShape(16.dp)),
                            ) { Text(it, modifier = Modifier.padding(8.dp)) }
                        }
                    }
                }
            }
            Spacer(modifier = Modifier.height(4.dp))
            Button(onClick = onOpenScan) { Text(stringResource(R.string.scan)) }
            Button(onClick = onOpenAiChat) { Text(stringResource(R.string.ai_chat)) }
            Button(onClick = onOpenProfile) { Text(stringResource(R.string.profile)) }
            Button(onClick = onOpenSettings) { Text(stringResource(R.string.settings)) }
            Button(onClick = onOpenHistory) { Text(stringResource(R.string.history)) }
            Button(onClick = onOpenArticle) { Text(stringResource(R.string.article)) }
            Button(onClick = onOpenDonation) { Text(stringResource(R.string.donation)) }
        }
    }
}
