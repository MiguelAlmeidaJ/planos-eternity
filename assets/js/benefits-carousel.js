(() => {
  const component = document.querySelector('[data-benefits-carousel]');
  if (!component) return;
  const track = component.querySelector('[data-benefits-track]');
  const cards = track.querySelectorAll('.et-benefit-card');
  const step = direction => {
    if (!cards.length) return;
    const width = cards[0].getBoundingClientRect().width;
    const gap = parseFloat(getComputedStyle(track).columnGap) || 16;
    track.scrollBy({left: direction * (width + gap), behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'});
  };
  component.querySelector('[data-benefits-prev]')?.addEventListener('click', () => step(-1));
  component.querySelector('[data-benefits-next]')?.addEventListener('click', () => step(1));
})();
