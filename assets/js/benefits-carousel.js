(() => {
  'use strict';
  const component = document.querySelector('[data-benefits-carousel]');
  if (!component) return;
  const track = component.querySelector('[data-benefits-track]');
  const cards = [...track.querySelectorAll('.et-benefit-card')];
  if (!cards.length) return;

  const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
  const prev = component.querySelector('[data-benefits-prev]');
  const next = component.querySelector('[data-benefits-next]');
  const cardOffset = card => card.offsetLeft - cards[0].offsetLeft;

  function currentIndex() {
    return cards.reduce((closest, card, index) =>
      Math.abs(cardOffset(card) - track.scrollLeft) <
      Math.abs(cardOffset(cards[closest]) - track.scrollLeft) ? index : closest, 0);
  }
  function refresh() {
    const i = currentIndex();
    if (prev) prev.disabled = i === 0;
    if (next) next.disabled = i === cards.length - 1;
  }
  function move(direction) {
    const index = Math.max(0, Math.min(cards.length - 1, currentIndex() + direction));
    track.scrollTo({
      left: cardOffset(cards[index]),
      behavior: motionPreference.matches ? 'instant' : 'smooth'
    });
  }

  prev?.addEventListener('click', () => move(-1));
  next?.addEventListener('click', () => move(1));
  track.addEventListener('scroll', refresh, {passive:true});
  window.addEventListener('resize', refresh);
  refresh();
})();
