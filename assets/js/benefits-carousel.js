(() => {
  'use strict';
  const component = document.querySelector('[data-benefits-carousel]');
  if (!component) return;
  const track = component.querySelector('[data-benefits-track]');
  const cards = [...track.querySelectorAll('.et-benefit-card')];
  if (!cards.length) return;

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const prev = component.querySelector('[data-benefits-prev]');
  const next = component.querySelector('[data-benefits-next]');

  function currentIndex() {
    return cards.reduce((closest, card, index) =>
      Math.abs(card.offsetLeft - track.offsetLeft - track.scrollLeft) <
      Math.abs(cards[closest].offsetLeft - track.offsetLeft - track.scrollLeft) ? index : closest, 0);
  }
  function refresh() {
    const i = currentIndex();
    if (prev) prev.disabled = i === 0;
    if (next) next.disabled = i === cards.length - 1;
  }
  function move(direction) {
    const nextIndex = Math.max(0, Math.min(cards.length - 1, currentIndex() + direction));
    cards[nextIndex].scrollIntoView({
      behavior: reducedMotion.matches ? 'instant' : 'smooth',
      block: 'nearest',
      inline: 'start'
    });
  }

  prev?.addEventListener('click', () => move(-1));
  next?.addEventListener('click', () => move(1));
  track.addEventListener('scroll', refresh, {passive:true});
  window.addEventListener('resize', refresh);
  refresh();
})();
