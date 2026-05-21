package com.example.halalyticscompose.ui.screens.ai

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.Button
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateListOf
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.unit.dp
import com.example.halalyticscompose.R
import com.example.halalyticscompose.presentation.viewmodel.AiChatUiState
import com.example.halalyticscompose.ui.components.ErrorState
import com.example.halalyticscompose.ui.components.HalalyticsTopBar
import com.example.halalyticscompose.ui.components.LoadingState
import com.example.halalyticscompose.ui.theme.HalalyticsColors

data class ChatBubble(val role: String, val text: String)

@Composable
fun AIChatScreen(
    uiState: AiChatUiState,
    onSendMessage: (String) -> Unit,
) {
    var currentMessage by remember { mutableStateOf("") }
    val history = remember { mutableStateListOf<ChatBubble>() }

    if (uiState.reply.isNotBlank() && history.lastOrNull()?.text != uiState.reply) {
        history.add(ChatBubble("ai", uiState.reply))
    }

    Scaffold(topBar = { HalalyticsTopBar() }) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(HalalyticsColors.Background)
                .padding(padding)
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp),
        ) {
            Text(stringResource(R.string.ai_chat), style = MaterialTheme.typography.headlineSmall, color = HalalyticsColors.Primary)

            when {
                uiState.isLoading -> LoadingState()
                uiState.error != null -> ErrorState(message = uiState.error, onRetry = {
                    if (currentMessage.isNotBlank()) onSendMessage(currentMessage)
                })
            }

            LazyColumn(
                modifier = Modifier
                    .weight(1f)
                    .fillMaxWidth(),
                verticalArrangement = Arrangement.spacedBy(8.dp),
            ) {
                items(history) { msg ->
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = if (msg.role == "user") Arrangement.End else Arrangement.Start,
                    ) {
                        Text(
                            text = msg.text,
                            modifier = Modifier
                                .background(if (msg.role == "user") HalalyticsColors.Primary else HalalyticsColors.Background)
                                .padding(12.dp),
                            color = if (msg.role == "user") HalalyticsColors.Background else HalalyticsColors.Text,
                        )
                    }
                }
            }

            Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                OutlinedTextField(
                    value = currentMessage,
                    onValueChange = { currentMessage = it },
                    modifier = Modifier.weight(1f),
                    label = { Text(stringResource(R.string.type_message)) },
                )
                Button(onClick = {
                    val msg = currentMessage.trim()
                    if (msg.isNotBlank()) {
                        history.add(ChatBubble("user", msg))
                        onSendMessage(msg)
                        currentMessage = ""
                    }
                }) {
                    Text(stringResource(R.string.send), color = HalalyticsColors.Background)
                }
            }
        }
    }
}
