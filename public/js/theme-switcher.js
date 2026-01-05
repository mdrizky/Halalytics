/**
 * Theme Switcher - Universal Theme Toggle System
 * Supports Light and Dark themes with localStorage persistence
 */

(function() {
    'use strict';
    
    // Theme configuration
    const THEMES = {
        dark: {
            bg: '#0F172A',
            card: '#1E293B',
            border: '#334155',
            text: '#F1F5F9',
            textSecondary: '#94A3B8',
            hover: 'rgba(255, 255, 255, 0.05)',
            shadow: '0 5px 20px rgba(0, 0, 0, 0.3)'
        },
        light: {
            bg: '#F8F9FA',
            card: '#FFFFFF',
            border: '#E5E7EB',
            text: '#1F2937',
            textSecondary: '#6B7280',
            hover: 'rgba(0, 0, 0, 0.05)',
            shadow: '0 5px 20px rgba(0, 0, 0, 0.1)'
        }
    };
    
    // Initialize theme system
    function initThemeSwitcher() {
        const html = document.documentElement;
        const savedTheme = localStorage.getItem('theme') || 'dark';
        
        // Apply saved theme
        setTheme(savedTheme);
        
        // Add theme switcher button if it doesn't exist
        if (!document.getElementById('themeSwitcher')) {
            createThemeSwitcherButton();
        }
        
        // Bind event listeners
        const themeSwitcher = document.getElementById('themeSwitcher');
        if (themeSwitcher) {
            themeSwitcher.addEventListener('click', toggleTheme);
        }
    }
    
    // Set theme
    function setTheme(theme) {
        const html = document.documentElement;
        const config = THEMES[theme];
        
        if (!config) return;
        
        // Set data-theme attribute
        html.setAttribute('data-theme', theme);
        
        // Set CSS variables
        html.style.setProperty('--bg-color', config.bg);
        html.style.setProperty('--card-bg', config.card);
        html.style.setProperty('--border-color', config.border);
        html.style.setProperty('--text-primary', config.text);
        html.style.setProperty('--text-secondary', config.textSecondary);
        html.style.setProperty('--hover-bg', config.hover);
        html.style.setProperty('--shadow', config.shadow);
        
        // Update gradient
        if (theme === 'dark') {
            html.style.setProperty('--gradient-bg', 'linear-gradient(135deg, #1E293B, #0F172A)');
        } else {
            html.style.setProperty('--gradient-bg', 'linear-gradient(135deg, #FFFFFF, #F3F4F6)');
        }
        
        // Save to localStorage
        localStorage.setItem('theme', theme);
        
        // Update icon if exists
        updateThemeIcon(theme);
        
        // Dispatch custom event
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }
    
    // Toggle theme
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    }
    
    // Update theme icon
    function updateThemeIcon(theme) {
        const icon = document.getElementById('themeIcon');
        if (icon) {
            if (theme === 'dark') {
                icon.className = 'bi bi-moon-stars-fill';
            } else {
                icon.className = 'bi bi-sun-fill';
            }
        }
    }
    
    // Create theme switcher button if it doesn't exist
    function createThemeSwitcherButton() {
        // Try to find a suitable place to add the button
        const navHeader = document.querySelector('.nav-header');
        const header = document.querySelector('header');
        
        if (navHeader || header) {
            const container = navHeader || header;
            const button = document.createElement('button');
            button.id = 'themeSwitcher';
            button.className = 'theme-switcher';
            button.title = 'Toggle Theme';
            button.innerHTML = '<i class="bi bi-moon-stars-fill" id="themeIcon"></i>';
            button.addEventListener('click', toggleTheme);
            
            // Try to find nav-control or similar to place button
            const navControl = container.querySelector('.nav-control');
            if (navControl && navControl.parentElement) {
                navControl.parentElement.insertBefore(button, navControl);
            } else {
                container.appendChild(button);
            }
        }
    }
    
    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initThemeSwitcher);
    } else {
        initThemeSwitcher();
    }
    
    // Export for global access
    window.ThemeSwitcher = {
        setTheme: setTheme,
        toggleTheme: toggleTheme,
        getTheme: function() {
            return document.documentElement.getAttribute('data-theme') || 'dark';
        }
    };
})();


