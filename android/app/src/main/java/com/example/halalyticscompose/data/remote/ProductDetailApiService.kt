package com.example.halalyticscompose.data.remote

import retrofit2.http.GET
import retrofit2.http.Query

interface ProductDetailApiService {
    @GET("user/product-detail")
    suspend fun getProductDetail(
        @Query("barcode") barcode: String,
    ): ProductDetailResponse
}

data class ProductDetailResponse(
    val success: Boolean,
    val data: ProductDetailData?,
    val message: String? = null,
)

data class ProductDetailData(
    val barcode: String,
    val halal_status: String,
    val halal_score: Int,
    val health_status: String,
    val health_score: Int,
    val dominant_ingredient: String,
    val short_term_effect: String,
    val long_term_effect: String,
    val personalized_recommendation: String,
    val confidence: String,
    val sources: List<String> = emptyList(),
    val warnings: List<String> = emptyList(),
)
