package com.example.halalyticscompose.presentation.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.halalyticscompose.data.repository.AiChatRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class AiChatUiState(
    val isLoading: Boolean = false,
    val reply: String = "",
    val error: String? = null,
)

class AiChatViewModel(
    private val repository: AiChatRepository,
) : ViewModel() {

    private val _uiState = MutableStateFlow(AiChatUiState())
    val uiState: StateFlow<AiChatUiState> = _uiState.asStateFlow()

    fun askAi(message: String) {
        if (message.isBlank()) return

        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            repository.sendMessage(message)
                .onSuccess { reply ->
                    _uiState.value = AiChatUiState(isLoading = false, reply = reply)
                }
                .onFailure {
                    _uiState.value = AiChatUiState(
                        isLoading = false,
                        error = "Tidak dapat terhubung ke AI. Coba lagi.",
                    )
                }
        }
    }
}
