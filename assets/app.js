import './stimulus_bootstrap.js';
import './bootstrap.js';
import './styles/app.css';

// Menu mobile (voir base.html.twig)
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');

    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('hidden') === false;
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    // Ferme les messages flash après quelques secondes
    document.querySelectorAll('[data-flash]').forEach((el) => {
        setTimeout(() => {
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        }, 6000);
    });
});
