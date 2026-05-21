package com.example.halalyticscompose.data.repository

import com.example.halalyticscompose.data.remote.AiApiService
import com.example.halalyticscompose.data.remote.AiChatRequest

class AiChatRepository(
    private val api: AiApiService,
) {
    suspend fun sendMessage(message: String): Result<String> {
        return runCatching {
            val response = api.chat(AiChatRequest(message))
            if (response.success && response.data != null) {
                response.data.message
            } else {
                response.message ?: "AI sedang sibuk, coba lagi."
            }
        }
    }
}
