import './bootstrap';
import Alpine from 'alpinejs';
import * as lucide from 'lucide';

window.Alpine = Alpine;
window.lucide = lucide;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons({ icons: lucide.icons });
});

document.addEventListener('livewire:navigated', () => {
    lucide.createIcons({ icons: lucide.icons });
});