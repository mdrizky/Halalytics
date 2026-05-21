package com.example.halalyticscompose.data.preferences

import android.content.Context
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map
import javax.inject.Inject
import javax.inject.Singleton

val Context.languageDataStore: DataStore<Preferences> by preferencesDataStore(name = "language_prefs")

@Singleton
class LanguagePreferences @Inject constructor(
    @ApplicationContext private val context: Context,
) {
    companion object {
        val LANGUAGE_KEY = stringPreferencesKey("selected_language")
        const val LANG_ID = "id"
        const val LANG_EN = "en"
    }

    val selectedLanguage: Flow<String> = context.languageDataStore.data.map { preferences ->
        preferences[LANGUAGE_KEY] ?: LANG_ID
    }

    suspend fun saveLanguage(languageCode: String) {
        context.languageDataStore.edit { preferences ->
            preferences[LANGUAGE_KEY] = languageCode
        }
    }
}
