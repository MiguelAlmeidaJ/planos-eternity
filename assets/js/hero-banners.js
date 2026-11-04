(() => {
  const carousel = document.querySelector('[data-banner-carousel]');
  if (!carousel) return;
  const slides = [...carousel.querySelectorAll('[data-banner-slide]')];
  const dots = [...carousel.querySelectorAll('[data-banner-dot]')];
  if (slides.length < 2) return;
  let active = 0;
  let timer = null;
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const show = index => {
    active = (index + slides.length) % slides.length;
    slides.forEach((slide, i) => { slide.hidden = i !== active; });
    dots.forEach((dot, i) => {
      dot.classList.toggle('is-active', i === active);
      dot.setAttribute('aria-current', String(i === active));
    });
  };
  const stop = () => { if (timer !== null) clearInterval(timer); timer = null; };
  const start = () => {
    stop();
    if (!reducedMotion.matches && !document.hidden && !carousel.matches(':hover') && !carousel.matches(':focus-within')) {
      timer = setInterval(() => show(active + 1), 5500);
    }
  };
  carousel.querySelector('[data-banner-prev]')?.addEventListener('click', () => { show(active - 1); start(); });
  carousel.querySelector('[data-banner-next]')?.addEventListener('click', () => { show(active + 1); start(); });
  dots.forEach((dot, i) => dot.addEventListener('click', () => { show(i); start(); }));
  carousel.addEventListener('mouseenter', stop);
  carousel.addEventListener('mouseleave', start);
  carousel.addEventListener('focusin', stop);
  carousel.addEventListener('focusout', () => queueMicrotask(start));
  document.addEventListener('visibilitychange', start);
  reducedMotion.addEventListener?.('change', start);
  start();
})();
