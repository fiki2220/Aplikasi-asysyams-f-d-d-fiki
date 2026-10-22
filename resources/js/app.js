import './bootstrap';
import Alpine from 'alpinejs';

// Alpine Plugins
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';

// Register plugins SEBELUM Alpine.start()
Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(persist);

// Expose Alpine ke window (untuk debugging & akses dari inline script)
window.Alpine = Alpine;

// Start Alpine
Alpine.start();