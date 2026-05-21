package com.example.halalyticscompose.ui.components

import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.material3.TopAppBar
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.unit.dp
import androidx.compose.ui.viewinterop.AndroidView
import android.widget.ImageView
import com.example.halalyticscompose.R
import com.example.halalyticscompose.ui.theme.HalalyticsColors

@Composable
fun HalalyticsTopBar() {
    TopAppBar(
        title = {
            Row {
                AndroidView(factory = { context ->
                    ImageView(context).apply {
                        setImageResource(R.drawable.ic_halalytics_logo)
                        layoutParams = android.view.ViewGroup.LayoutParams(48, 48)
                    }
                }, modifier = Modifier.size(24.dp))
                Spacer(modifier = Modifier.width(8.dp))
                Text(
                    text = stringResource(R.string.app_name),
                    style = MaterialTheme.typography.titleMedium,
                    color = HalalyticsColors.Text,
                )
            }
        },
    )
}
