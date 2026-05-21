package com.example.halalyticscompose.presentation.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.halalyticscompose.data.repository.ProductDetailRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class ScanUiState(
    val hasCameraPermission: Boolean = false,
    val isLoading: Boolean = false,
    val scannedBarcode: String? = null,
    val error: String? = null,
)

class ScanViewModel(
    private val repository: ProductDetailRepository,
) : ViewModel() {
    private val _uiState = MutableStateFlow(ScanUiState())
    val uiState: StateFlow<ScanUiState> = _uiState.asStateFlow()

    fun updatePermission(granted: Boolean) {
        _uiState.value = _uiState.value.copy(
            hasCameraPermission = granted,
            error = if (!granted) "Izin kamera ditolak." else null,
        )
    }

    fun onBarcodeDetected(barcode: String) {
        if (barcode.isBlank()) return
        _uiState.value = _uiState.value.copy(scannedBarcode = barcode, error = null)
    }

    fun resolveScannedBarcode() {
        val code = _uiState.value.scannedBarcode ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)
            repository.fetch(code)
                .onSuccess {
                    _uiState.value = _uiState.value.copy(isLoading = false)
                }
                .onFailure {
                    _uiState.value = _uiState.value.copy(isLoading = false, error = "Produk tidak ditemukan / koneksi bermasalah.")
                }
        }
    }

    fun clearError() {
        _uiState.value = _uiState.value.copy(error = null)
    }
}
