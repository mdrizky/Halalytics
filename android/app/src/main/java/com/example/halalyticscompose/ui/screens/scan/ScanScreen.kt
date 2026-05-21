package com.example.halalyticscompose.ui.screens.scan

import android.Manifest
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.camera.core.CameraSelector
import androidx.camera.core.ImageAnalysis
import androidx.camera.core.Preview
import androidx.camera.lifecycle.ProcessCameraProvider
import androidx.camera.view.PreviewView
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.DisposableEffect
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.platform.LocalLifecycleOwner
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.viewinterop.AndroidView
import com.example.halalyticscompose.R
import com.example.halalyticscompose.ui.components.ErrorState
import com.example.halalyticscompose.ui.components.HalalyticsTopBar
import com.example.halalyticscompose.ui.components.LoadingState
import com.example.halalyticscompose.ui.theme.HalalyticsColors
import com.example.halalyticscompose.ui.theme.HalalyticsUiTokens
import com.google.mlkit.vision.barcode.BarcodeScanning
import com.google.mlkit.vision.barcode.common.Barcode
import com.google.mlkit.vision.common.InputImage
import java.util.concurrent.Executors
import java.util.concurrent.atomic.AtomicBoolean

@Composable
fun ScanScreen(
    isLoading: Boolean,
    error: String?,
    onRetry: () -> Unit,
    onBarcodeDetected: (String) -> Unit,
    onBack: (() -> Unit)? = null,
) {
    var hasPermission by remember { mutableStateOf(false) }
    var permissionError by remember { mutableStateOf<String?>(null) }

    val permissionLauncher = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.RequestPermission(),
    ) { granted ->
        hasPermission = granted
        permissionError = if (!granted) stringResource(R.string.scan_permission_denied) else null
    }

    LaunchedEffect(Unit) { permissionLauncher.launch(Manifest.permission.CAMERA) }

    Scaffold(topBar = { HalalyticsTopBar(showBack = onBack != null, onBackClick = { onBack?.invoke() }) }) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(HalalyticsColors.Background)
                .padding(padding)
                .padding(HalalyticsUiTokens.ScreenPadding),
            verticalArrangement = Arrangement.spacedBy(HalalyticsUiTokens.SectionSpacing),
        ) {
            Text(stringResource(R.string.scan_barcode), style = MaterialTheme.typography.headlineSmall, color = HalalyticsColors.Primary)

            when {
                isLoading -> LoadingState()
                error != null -> ErrorState(message = error, onRetry = onRetry)
                permissionError != null -> ErrorState(message = permissionError!!, onRetry = { permissionLauncher.launch(Manifest.permission.CAMERA) })
                !hasPermission -> Text(stringResource(R.string.scan_waiting_permission), color = HalalyticsColors.Text)
                else -> CameraPreview(onBarcodeDetected = onBarcodeDetected)
            }
        }
    }
}

@Composable
private fun CameraPreview(onBarcodeDetected: (String) -> Unit) {
    val context = LocalContext.current
    val lifecycleOwner = LocalLifecycleOwner.current
    val previewView = remember { PreviewView(context) }
    val cameraExecutor = remember { Executors.newSingleThreadExecutor() }
    val isHandlingResult = remember { AtomicBoolean(false) }

    DisposableEffect(Unit) {
        val providerFuture = ProcessCameraProvider.getInstance(context)
        val scanner = BarcodeScanning.getClient()

        providerFuture.addListener({
            val cameraProvider = providerFuture.get()
            val preview = Preview.Builder().build().also { it.surfaceProvider = previewView.surfaceProvider }
            val analysis = ImageAnalysis.Builder().build().also { imageAnalysis ->
                imageAnalysis.setAnalyzer(cameraExecutor) { imageProxy ->
                    val mediaImage = imageProxy.image
                    if (mediaImage == null || isHandlingResult.get()) {
                        imageProxy.close()
                        return@setAnalyzer
                    }

                    val image = InputImage.fromMediaImage(mediaImage, imageProxy.imageInfo.rotationDegrees)
                    scanner.process(image)
                        .addOnSuccessListener { barcodes ->
                            val raw = barcodes
                                .firstOrNull { it.format != Barcode.FORMAT_UNKNOWN }
                                ?.rawValue
                                ?.trim()
                            if (!raw.isNullOrBlank() && isHandlingResult.compareAndSet(false, true)) {
                                onBarcodeDetected(raw)
                            }
                        }
                        .addOnCompleteListener { imageProxy.close() }
                }
            }

            cameraProvider.unbindAll()
            cameraProvider.bindToLifecycle(lifecycleOwner, CameraSelector.DEFAULT_BACK_CAMERA, preview, analysis)
        }, context.mainExecutor)

        onDispose {
            cameraExecutor.shutdown()
            scanner.close()
        }
    }

    AndroidView(factory = { previewView }, modifier = Modifier.fillMaxSize())
}
