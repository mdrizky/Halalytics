package com.example.halalyticscompose.ui.screens.auth

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp
import com.example.halalyticscompose.ui.theme.HalalyticsColors
import com.example.halalyticscompose.ui.theme.HalalyticsUiTokens

@Composable
fun LoginScreen(
    isLoading: Boolean,
    error: String?,
    onLogin: (username: String, password: String) -> Unit,
) {
    var username by rememberSaveable { mutableStateOf("") }
    var password by rememberSaveable { mutableStateOf("") }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(HalalyticsColors.Background)
            .padding(HalalyticsUiTokens.ScreenPadding),
        verticalArrangement = Arrangement.Center,
    ) {
        Text(
            text = "Halalytics",
            style = MaterialTheme.typography.headlineMedium,
            fontWeight = FontWeight.Bold,
            color = HalalyticsColors.Primary,
        )
        Spacer(modifier = Modifier.height(8.dp))
        Text(
            text = "Masuk dengan username",
            style = MaterialTheme.typography.bodyMedium,
            color = HalalyticsColors.Text,
        )
        Spacer(modifier = Modifier.height(16.dp))

        Card(
            shape = HalalyticsUiTokens.CardRadius,
            colors = CardDefaults.cardColors(containerColor = Color.White),
            modifier = Modifier.fillMaxWidth(),
        ) {
            Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
                OutlinedTextField(
                    value = username,
                    onValueChange = { username = it },
                    label = { Text("Username") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth(),
                )

                OutlinedTextField(
                    value = password,
                    onValueChange = { password = it },
                    label = { Text("Password") },
                    singleLine = true,
                    visualTransformation = PasswordVisualTransformation(),
                    modifier = Modifier.fillMaxWidth(),
                )

                if (error != null) {
                    Text(text = error, color = HalalyticsColors.Text)
                }

                Button(
                    onClick = { onLogin(username.trim(), password) },
                    enabled = !isLoading,
                    shape = HalalyticsUiTokens.ButtonRadius,
                    colors = ButtonDefaults.buttonColors(containerColor = HalalyticsColors.Primary),
                    modifier = Modifier.fillMaxWidth(),
                ) {
                    Text(if (isLoading) "Memuat..." else "Masuk")
                }
            }
        }
    }
}
