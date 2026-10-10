import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.Chart = Chart;
window.createIcons = createIcons;
window.lucideIcons = icons;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});
