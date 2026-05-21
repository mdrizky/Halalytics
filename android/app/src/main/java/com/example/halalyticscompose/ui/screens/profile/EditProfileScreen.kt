package com.example.halalyticscompose.ui.screens.profile

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Button
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.material3.TopAppBar
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.compose.ui.res.stringResource
import com.example.halalyticscompose.R
import com.example.halalyticscompose.ui.theme.HalalyticsColors

@Composable
fun EditProfileScreen(
    initialName: String,
    initialAge: String,
    initialDiseases: String,
    initialAllergies: String,
    onSave: (name: String, age: String, diseases: String, allergies: String) -> Unit,
) {
    var name by rememberSaveable { mutableStateOf(initialName) }
    var age by rememberSaveable { mutableStateOf(initialAge) }
    var diseases by rememberSaveable { mutableStateOf(initialDiseases) }
    var allergies by rememberSaveable { mutableStateOf(initialAllergies) }

    Scaffold(
        topBar = {
            TopAppBar(title = { Text(stringResource(R.string.edit_profile), color = HalalyticsColors.Text) })
        },
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding)
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp),
        ) {
            Text(text = stringResource(R.string.health_profile), style = MaterialTheme.typography.titleMedium, color = HalalyticsColors.Text)

            OutlinedTextField(
                value = name,
                onValueChange = { name = it },
                label = { Text(stringResource(R.string.full_name)) },
                modifier = Modifier.fillMaxWidth(),
            )
            OutlinedTextField(
                value = age,
                onValueChange = { age = it },
                label = { Text(stringResource(R.string.age)) },
                modifier = Modifier.fillMaxWidth(),
            )
            OutlinedTextField(
                value = diseases,
                onValueChange = { diseases = it },
                label = { Text(stringResource(R.string.diseases)) },
                modifier = Modifier.fillMaxWidth(),
            )
            OutlinedTextField(
                value = allergies,
                onValueChange = { allergies = it },
                label = { Text(stringResource(R.string.allergies)) },
                modifier = Modifier.fillMaxWidth(),
            )

            Button(
                onClick = { onSave(name, age, diseases, allergies) },
                modifier = Modifier.fillMaxWidth(),
            ) {
                Text(stringResource(R.string.save), color = HalalyticsColors.Background)
            }
        }
    }
}
