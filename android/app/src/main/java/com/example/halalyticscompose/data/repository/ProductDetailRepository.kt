package com.example.halalyticscompose.data.repository

import com.example.halalyticscompose.data.remote.ProductDetailApiService
import com.example.halalyticscompose.data.remote.ProductDetailData

class ProductDetailRepository(
    private val api: ProductDetailApiService,
) {
    suspend fun fetch(barcode: String): Result<ProductDetailData> = runCatching {
        val response = api.getProductDetail(barcode)
        if (!response.success || response.data == null) {
            error(response.message ?: "Gagal memuat detail produk")
        }
        response.data
    }
}
