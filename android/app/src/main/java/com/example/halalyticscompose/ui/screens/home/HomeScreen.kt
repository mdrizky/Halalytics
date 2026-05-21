package com.example.halalyticscompose.ui.screens.home

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Button
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
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
) {
    Scaffold(topBar = { HalalyticsTopBar() }) { padding ->
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
            Button(onClick = onOpenScan) { Text(stringResource(R.string.scan)) }
            Button(onClick = onOpenAiChat) { Text(stringResource(R.string.ai_chat)) }
            Button(onClick = onOpenProfile) { Text(stringResource(R.string.profile)) }
        }
    }
}
