package com.example.halalyticscompose.ui.screens.home

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.navigationBarsPadding
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.FavoriteBorder
import androidx.compose.material.icons.filled.Mic
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import com.example.halalyticscompose.ui.components.HalalyticsTopBar
import com.example.halalyticscompose.ui.components.PremiumHeroSection
import com.example.halalyticscompose.ui.theme.HalalyticsColors

@Composable
fun HomeScreen(
    username: String,
    onOpenScan: () -> Unit,
    onOpenAiChat: () -> Unit,
    onOpenProfile: () -> Unit,
    onOpenSettings: () -> Unit,
    onOpenHistory: () -> Unit,
    onOpenArticle: () -> Unit,
    onOpenDonation: () -> Unit,
) {
    Scaffold(
        topBar = { HalalyticsTopBar(subtitle = "Welcome, $username") },
        bottomBar = {
            FloatingBottomBar(
                items = listOf("Home", "Scan", "AI", "Profile"),
                onTap = { item ->
                    when (item) {
                        "Scan" -> onOpenScan()
                        "AI" -> onOpenAiChat()
                        "Profile" -> onOpenProfile()
                    }
                },
            )
        },
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .background(Color(0xFFF7FAFC))
                .padding(padding)
                .navigationBarsPadding()
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(14.dp),
        ) {
            PremiumHeroSection(
                title = "Premium Smart Marketplace",
                subtitle = "Obat, vitamin, healthy food, skincare, dan AI suggestion dalam satu app.",
            )

            SmartSearchBar(onVoice = onOpenAiChat)
            PromoCarousel()
            BentoCategories()

            SectionTitle("Trending Products")
            ProductCard("Vitamin C + Zinc", "4.9", "-20%", "Rp49.000")
            ProductCard("Healthy Oat Drink", "4.8", "-15%", "Rp28.000")

            SectionTitle("Healthy Recommendations")
            SkeletonRecommendation()
            SkeletonRecommendation()

            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                SmallActionCard("Article", onOpenArticle)
                SmallActionCard("History", onOpenHistory)
                SmallActionCard("Donation", onOpenDonation)
                SmallActionCard("Settings", onOpenSettings)
            }
            Spacer(modifier = Modifier.height(80.dp))
        }
    }
}

@Composable
private fun SmartSearchBar(onVoice: () -> Unit) {
    Card(shape = RoundedCornerShape(20.dp), colors = CardDefaults.cardColors(containerColor = Color.White), modifier = Modifier.fillMaxWidth()) {
        Row(
            modifier = Modifier.padding(horizontal = 14.dp, vertical = 12.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(10.dp),
        ) {
            Icon(Icons.Default.Search, contentDescription = "Search", tint = Color(0xFF64748B))
            Text("Cari obat, skincare, healthy food...", color = Color(0xFF64748B), modifier = Modifier.weight(1f))
            Box(
                modifier = Modifier
                    .size(34.dp)
                    .background(HalalyticsColors.Primary, CircleShape)
                    .clickable { onVoice() },
                contentAlignment = Alignment.Center,
            ) {
                Icon(Icons.Default.Mic, contentDescription = "Voice", tint = Color.White)
            }
        }
    }
}

@Composable
private fun PromoCarousel() {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(24.dp),
        colors = CardDefaults.cardColors(containerColor = Color.Transparent),
    ) {
        Box(
            modifier = Modifier
                .background(Brush.horizontalGradient(listOf(Color(0xFF10B981), Color(0xFF0EA5E9))))
                .padding(16.dp),
        ) {
            Column(verticalArrangement = Arrangement.spacedBy(6.dp)) {
                Text("Flash Sale AI Health", color = Color.White, style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold)
                Text("Diskon sampai 40% + rekomendasi personal harian", color = Color.White)
            }
        }
    }
}

@Composable
private fun BentoCategories() {
    Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
        SectionTitle("Categories")
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
            BentoTile("Medicines", Modifier.weight(1f).height(92.dp))
            BentoTile("Vitamins", Modifier.weight(1f).height(92.dp))
        }
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
            BentoTile("Healthy Food", Modifier.weight(1f).height(72.dp))
            BentoTile("Drinks", Modifier.weight(1f).height(72.dp))
            BentoTile("Skincare", Modifier.weight(1f).height(72.dp))
        }
    }
}

@Composable
private fun BentoTile(label: String, modifier: Modifier) {
    Card(modifier = modifier, shape = RoundedCornerShape(20.dp), colors = CardDefaults.cardColors(containerColor = Color.White)) {
        Box(modifier = Modifier.fillMaxSize().padding(12.dp), contentAlignment = Alignment.BottomStart) {
            Text(label, fontWeight = FontWeight.SemiBold, color = HalalyticsColors.Text)
        }
    }
}

@Composable
private fun ProductCard(name: String, rating: String, discount: String, price: String) {
    Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(22.dp), colors = CardDefaults.cardColors(containerColor = Color.White)) {
        Row(modifier = Modifier.padding(14.dp), verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(12.dp)) {
            Box(modifier = Modifier.size(72.dp).background(Color(0xFFE2E8F0), RoundedCornerShape(16.dp)))
            Column(modifier = Modifier.weight(1f), verticalArrangement = Arrangement.spacedBy(4.dp)) {
                Text(discount, color = Color(0xFFEA580C), fontWeight = FontWeight.Bold)
                Text(name, fontWeight = FontWeight.Bold)
                Text("⭐ $rating   •   $price", color = Color(0xFF64748B))
            }
            Icon(Icons.Default.FavoriteBorder, contentDescription = "Favorite", tint = Color(0xFF94A3B8))
        }
    }
}

@Composable
private fun SkeletonRecommendation() {
    Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(18.dp), colors = CardDefaults.cardColors(containerColor = Color(0xFFEFF6FF))) {
        Row(modifier = Modifier.padding(14.dp), horizontalArrangement = Arrangement.spacedBy(10.dp), verticalAlignment = Alignment.CenterVertically) {
            Box(modifier = Modifier.size(44.dp).background(Color(0xFFDBEAFE), CircleShape))
            Column(verticalArrangement = Arrangement.spacedBy(6.dp), modifier = Modifier.weight(1f)) {
                Box(modifier = Modifier.fillMaxWidth(0.7f).height(10.dp).background(Color(0xFFBFDBFE), RoundedCornerShape(8.dp)))
                Box(modifier = Modifier.fillMaxWidth(0.45f).height(10.dp).background(Color(0xFFBFDBFE), RoundedCornerShape(8.dp)))
            }
        }
    }
}

@Composable
private fun FloatingBottomBar(items: List<String>, onTap: (String) -> Unit) {
    Box(modifier = Modifier.fillMaxWidth().padding(horizontal = 20.dp, vertical = 10.dp)) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .background(Color.White.copy(alpha = 0.92f), RoundedCornerShape(30.dp))
                .border(1.dp, Color(0xFFE2E8F0), RoundedCornerShape(30.dp))
                .padding(vertical = 12.dp),
            horizontalArrangement = Arrangement.SpaceEvenly,
            verticalAlignment = Alignment.CenterVertically,
        ) {
            items.forEach { item ->
                Text(item, modifier = Modifier.clickable { onTap(item) }, color = HalalyticsColors.Text, fontWeight = FontWeight.SemiBold)
            }
        }
    }
}

@Composable
private fun SectionTitle(text: String) {
    Text(text, style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.Bold, color = HalalyticsColors.Text)
}

@Composable
private fun SmallActionCard(label: String, onClick: () -> Unit) {
    Card(
        modifier = Modifier.weight(1f).clickable { onClick() },
        shape = RoundedCornerShape(14.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
    ) {
        Box(modifier = Modifier.padding(vertical = 10.dp), contentAlignment = Alignment.Center) {
            Text(label, color = HalalyticsColors.Text, fontWeight = FontWeight.SemiBold)
        }
    }
}
