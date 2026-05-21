package com.example.halalyticscompose.data.remote

import retrofit2.http.Body
import retrofit2.http.POST

interface AiApiService {
    @POST("api/ai/chat")
    suspend fun chat(@Body request: AiChatRequest): AiChatResponse
}

data class AiChatRequest(
    val message: String,
)

data class AiChatResponse(
    val success: Boolean,
    val data: AiChatData?,
    val message: String? = null,
)

data class AiChatData(
    val intent: String,
    val message: String,
)
