let dashboardUpdateInterval = null;
let isPollingPaused = false;
let lastUpdateTime = null;

const POLLING_INTERVAL = 5000;

function initRealtimeDashboard() {
    const pauseBtn = document.getElementById('pause-polling-btn');
    if (pauseBtn) {
        pauseBtn.addEventListener('click', togglePolling);
    }
    
    startPolling();
    updateLastUpdateTime();
}

function startPolling() {
    if (dashboardUpdateInterval) clearInterval(dashboardUpdateInterval);
    
    dashboardUpdateInterval = setInterval(() => {
        if (!isPollingPaused) {
            fetchDashboardStats();
        }
    }, POLLING_INTERVAL);
}

function togglePolling() {
    const btn = document.getElementById('pause-polling-btn');
    isPollingPaused = !isPollingPaused;
    
    if (btn) {
        if (isPollingPaused) {
            btn.textContent = 'Resume Updates';
            btn.classList.add('bg-gray-400');
            btn.classList.remove('bg-blue-500');
        } else {
            btn.textContent = 'Pause Updates';
            btn.classList.remove('bg-gray-400');
            btn.classList.add('bg-blue-500');
            fetchDashboardStats();
        }
    }
}

function fetchDashboardStats() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    fetch('/admin/dashboard/stats', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDashboardCards(data.data);
            updateLastUpdateTime();
        }
    })
    .catch(error => {
        console.error('Dashboard update failed:', error);
    });
}

function updateDashboardCards(data) {
    const kpiCards = {
        'total-users': data.total_users,
        'total-products': data.total_products,
        'total-scans': data.total_scans,
        'online-users': data.online_users,
    };
    
    for (const [id, value] of Object.entries(kpiCards)) {
        const element = document.getElementById(id);
        if (element) {
            const oldValue = parseInt(element.textContent.replace(/[^0-9]/g, ''));
            const newValue = parseInt(value);
            
            if (oldValue !== newValue) {
                element.textContent = formatNumber(newValue);
                animateCardUpdate(element.closest('.kpi-card'));
            }
        }
    }
    
    if (data.recent_scans) {
        updateRecentScans(data.recent_scans);
    }
    
    if (data.top_products) {
        updateTopProducts(data.top_products);
    }
}

function updateRecentScans(scans) {
    const container = document.getElementById('recent-scans-list');
    if (!container) return;
    
    const currentScans = container.querySelectorAll('[data-scan-id]');
    const existingIds = new Set([...currentScans].map(el => el.dataset.scanId));
    
    scans.forEach((scan, index) => {
        if (index < 5) {
            if (!existingIds.has(String(scan.id))) {
                const scanEl = createRecentScanElement(scan);
                container.insertBefore(scanEl, container.firstChild);
                animateCardUpdate(scanEl);
                
                if (container.children.length > 5) {
                    container.removeChild(container.lastChild);
                }
            }
        }
    });
}

function updateTopProducts(products) {
    const container = document.getElementById('top-products-list');
    if (!container) return;
    
    products.forEach((product, index) => {
        const productEl = container.querySelector(`[data-product-index="${index}"]`);
        if (productEl) {
            const countEl = productEl.querySelector('.scan-count');
            if (countEl) {
                const oldCount = parseInt(countEl.textContent);
                const newCount = product.scan_count;
                
                if (oldCount !== newCount) {
                    countEl.textContent = newCount;
                    animateCardUpdate(productEl);
                }
            }
        }
    });
}

function updateLastUpdateTime() {
    const el = document.getElementById('last-update-time');
    if (el) {
        const now = new Date();
        el.textContent = 'Last updated: ' + now.toLocaleTimeString();
        lastUpdateTime = now;
    }
}

function animateCardUpdate(element) {
    if (!element) return;
    
    element.style.backgroundColor = 'rgba(16, 185, 129, 0.1)';
    setTimeout(() => {
        element.style.backgroundColor = '';
    }, 500);
}

function createRecentScanElement(scan) {
    const el = document.createElement('div');
    el.className = 'scan-item p-3 border border-gray-200 rounded-lg';
    el.setAttribute('data-scan-id', scan.id);
    el.innerHTML = `
        <div class="flex justify-between items-start">
            <div>
                <p class="font-medium text-gray-900">${escapeHtml(scan.product_name || 'Product')}</p>
                <p class="text-sm text-gray-500">${scan.barcode}</p>
                <p class="text-xs text-gray-400">${formatTime(scan.created_at)}</p>
            </div>
            <span class="px-2 py-1 rounded text-xs font-medium ${getStatusClass(scan.status_halal)}">
                ${scan.status_halal}
            </span>
        </div>
    `;
    return el;
}

function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function getStatusClass(status) {
    const statusMap = {
        'halal': 'bg-green-100 text-green-800',
        'haram': 'bg-red-100 text-red-800',
        'syubhat': 'bg-yellow-100 text-yellow-800',
    };
    return statusMap[status] || 'bg-gray-100 text-gray-800';
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

document.addEventListener('DOMContentLoaded', initRealtimeDashboard);

window.addEventListener('beforeunload', () => {
    if (dashboardUpdateInterval) {
        clearInterval(dashboardUpdateInterval);
    }
});
