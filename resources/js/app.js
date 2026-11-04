import Alpine from 'alpinejs';
import Lenis from 'lenis';
import 'lenis/dist/lenis.css';

document.documentElement.classList.add('js');

window.Alpine = Alpine;
Alpine.start();

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Loader: hide after window load, but never before data-min and never later than data-max.
const loader = document.getElementById('site-loader');
if (loader) {
    const min = Number(loader.dataset.min) || 600;
    const max = Number(loader.dataset.max) || 3000;
    const started = performance.now();
    let done = false;
    const hide = () => {
        if (done) return;
        done = true;
        loader.classList.add('is-hidden');
        try { sessionStorage.setItem('loaderSeen', '1'); } catch (e) {}
        setTimeout(() => loader.remove(), 700);
    };
    window.addEventListener('load', () => setTimeout(hide, Math.max(0, min - (performance.now() - started))));
    setTimeout(hide, max);
}

// Smooth scroll
if (!reduceMotion) {
    const lenis = new Lenis({ duration: 1.1, smoothWheel: true });
    const raf = (time) => { lenis.raf(time); requestAnimationFrame(raf); };
    requestAnimationFrame(raf);
    window.lenis = lenis;
}

// Scroll reveal
const revealEls = document.querySelectorAll('[data-reveal]');
if ('IntersectionObserver' in window && !reduceMotion) {
    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
    revealEls.forEach((el) => io.observe(el));
} else {
    revealEls.forEach((el) => el.classList.add('is-visible'));
}
