package com.example.halalyticscompose.ui.screens.profile

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.navigationBarsPadding
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.res.stringResource
import com.example.halalyticscompose.R
import com.example.halalyticscompose.ui.components.HalalyticsTopBar
import com.example.halalyticscompose.ui.components.PremiumHeroSection
import com.example.halalyticscompose.ui.theme.HalalyticsColors

@Composable
fun EditProfileScreen(
    initialName: String,
    initialAge: String,
    initialWeightKg: String,
    initialHeightCm: String,
    initialDiseases: String,
    initialAllergies: String,
    onBack: () -> Unit = {},
    onSave: (name: String, age: String, weightKg: String, heightCm: String, diseases: String, allergies: String) -> Unit,
) {
    var name by rememberSaveable { mutableStateOf(initialName) }
    var age by rememberSaveable { mutableStateOf(initialAge) }
    var weightKg by rememberSaveable { mutableStateOf(initialWeightKg) }
    var heightCm by rememberSaveable { mutableStateOf(initialHeightCm) }
    var diseases by rememberSaveable { mutableStateOf(initialDiseases) }
    var allergies by rememberSaveable { mutableStateOf(initialAllergies) }

    Scaffold(topBar = { HalalyticsTopBar(showBack = true, subtitle = stringResource(R.string.profile_subtitle), onBackClick = onBack) }) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(Color(0xFFF2F7F5))
                .padding(padding)
                .navigationBarsPadding()
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp),
        ) {
            PremiumHeroSection(
                title = "Premium Profile Banner",
                subtitle = "Kelola identitas kesehatan dan preferensi AI kamu dengan aman.",
            )
            Card(
                shape = RoundedCornerShape(22.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                modifier = Modifier.fillMaxWidth(),
            ) {
                Column(modifier = Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
                    Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                        Box(
                            modifier = Modifier
                                .border(2.dp, Color(0xFF8CD4BF), CircleShape)
                                .background(Color(0xFFE7FFF7), CircleShape)
                                .padding(horizontal = 18.dp, vertical = 12.dp),
                        ) {
                            Text(name.take(1).ifBlank { "U" }, style = MaterialTheme.typography.titleLarge, color = HalalyticsColors.Primary)
                        }
                        Column {
                            Text(text = stringResource(R.string.health_profile), style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold, color = HalalyticsColors.Text)
                            Text(stringResource(R.string.profile_personal_recommendation_hint), style = MaterialTheme.typography.bodySmall, color = Color(0xFF5E7470))
                        }
                    }
                    OutlinedTextField(value = name, onValueChange = { name = it }, label = { Text(stringResource(R.string.full_name)) }, modifier = Modifier.fillMaxWidth(), singleLine = true)
                    OutlinedTextField(value = age, onValueChange = { age = it.filter { c -> c.isDigit() }.take(3) }, label = { Text(stringResource(R.string.age)) }, modifier = Modifier.fillMaxWidth(), singleLine = true)
                    OutlinedTextField(value = weightKg, onValueChange = { weightKg = it.filter { c -> c.isDigit() || c == '.' || c == ',' }.take(6) }, label = { Text("Berat Badan (kg)") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
                    OutlinedTextField(value = heightCm, onValueChange = { heightCm = it.filter { c -> c.isDigit() || c == '.' || c == ',' }.take(6) }, label = { Text("Tinggi Badan (cm)") }, modifier = Modifier.fillMaxWidth(), singleLine = true)
                    OutlinedTextField(value = diseases, onValueChange = { diseases = it }, label = { Text(stringResource(R.string.diseases)) }, modifier = Modifier.fillMaxWidth())
                    OutlinedTextField(value = allergies, onValueChange = { allergies = it }, label = { Text(stringResource(R.string.allergies)) }, modifier = Modifier.fillMaxWidth())
                }
            }

            Spacer(modifier = Modifier.height(8.dp))
            Button(
                onClick = { onSave(name.trim(), age.trim(), weightKg.trim(), heightCm.trim(), diseases.trim(), allergies.trim()) },
                modifier = Modifier.fillMaxWidth().height(52.dp),
                shape = RoundedCornerShape(16.dp),
                colors = ButtonDefaults.buttonColors(containerColor = HalalyticsColors.Primary),
                enabled = name.isNotBlank() && age.isNotBlank() && weightKg.isNotBlank() && heightCm.isNotBlank(),
            ) {
                Text(stringResource(R.string.save_changes), color = Color.White, fontWeight = FontWeight.Bold)
            }
        }
    }
}
