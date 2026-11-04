(() => {
  'use strict';

  const carousel = document.querySelector('[data-banner-carousel]');
  if (!carousel) return;

  const slides = [...carousel.querySelectorAll('[data-banner-slide]')];
  const dots = [...carousel.querySelectorAll('[data-banner-dot]')];
  if (slides.length < 2) return;

  const INTERVAL_MS = 5500;
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let current = 0;
  let timer = null;

  function show(index) {
    current = (index + slides.length) % slides.length;
    slides.forEach((slide, i) => {
      slide.hidden = i !== current;
      slide.setAttribute('aria-hidden', String(i !== current));
      // Links in inactive slides should not be reachable by keyboard.
      if ('inert' in slide) slide.inert = i !== current;
    });
    dots.forEach((dot, i) => {
      dot.classList.toggle('is-active', i === current);
      dot.setAttribute('aria-current', String(i === current));
    });
  }

  function stop() {
    if (timer !== null) window.clearInterval(timer);
    timer = null;
  }

  function play() {
    stop();
    if (document.hidden || reducedMotion.matches) return;
    timer = window.setInterval(() => show(current + 1), INTERVAL_MS);
  }

  function navigate(index) {
    show(index);
    play();
  }

  carousel.querySelector('[data-banner-prev]')?.addEventListener('click', () => navigate(current - 1));
  carousel.querySelector('[data-banner-next]')?.addEventListener('click', () => navigate(current + 1));
  dots.forEach((dot, i) => dot.addEventListener('click', () => navigate(i)));

  // A passagem é automática mesmo com o mouse sobre o banner.
  // Pausamos apenas quando a aba não está visível ou o usuário prefere movimento reduzido.
  document.addEventListener('visibilitychange', play);
  if (typeof reducedMotion.addEventListener === 'function') {
    reducedMotion.addEventListener('change', play);
  } else if (typeof reducedMotion.addListener === 'function') {
    reducedMotion.addListener(play);
  }

  show(0);
  play();
})();
