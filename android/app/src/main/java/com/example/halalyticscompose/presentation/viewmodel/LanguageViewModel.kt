package com.example.halalyticscompose.presentation.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.halalyticscompose.data.preferences.LanguagePreferences
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.SharingStarted
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.stateIn
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class LanguageViewModel @Inject constructor(
    private val languagePreferences: LanguagePreferences,
) : ViewModel() {
    val currentLanguage: StateFlow<String> = languagePreferences.selectedLanguage.stateIn(
        scope = viewModelScope,
        started = SharingStarted.WhileSubscribed(5000),
        initialValue = LanguagePreferences.LANG_ID,
    )

    fun setLanguage(languageCode: String) {
        viewModelScope.launch {
            languagePreferences.saveLanguage(languageCode)
        }
    }
}
