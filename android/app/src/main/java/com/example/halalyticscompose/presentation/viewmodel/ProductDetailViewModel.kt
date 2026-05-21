package com.example.halalyticscompose.presentation.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.halalyticscompose.data.remote.ProductDetailData
import com.example.halalyticscompose.data.repository.ProductDetailRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class ProductDetailUiState(
    val isLoading: Boolean = false,
    val detail: ProductDetailData? = null,
    val error: String? = null,
)

class ProductDetailViewModel(
    private val repository: ProductDetailRepository,
) : ViewModel() {
    private val _uiState = MutableStateFlow(ProductDetailUiState())
    val uiState: StateFlow<ProductDetailUiState> = _uiState.asStateFlow()

    fun load(barcode: String) {
        if (barcode.isBlank()) return

        viewModelScope.launch {
            _uiState.value = ProductDetailUiState(isLoading = true)
            repository.fetch(barcode)
                .onSuccess { data ->
                    _uiState.value = ProductDetailUiState(detail = data)
                }
                .onFailure {
                    _uiState.value = ProductDetailUiState(error = "Detail produk gagal dimuat")
                }
        }
    }
}
