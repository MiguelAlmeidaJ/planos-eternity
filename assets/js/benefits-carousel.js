(() => {
  'use strict';

  const component = document.querySelector('[data-benefits-carousel]');
  if (!component) return;

  const track = component.querySelector('[data-benefits-track]');
  const cards = [...track.querySelectorAll('.et-benefit-card')];
  const prev = component.querySelector('[data-benefits-prev]');
  const next = component.querySelector('[data-benefits-next]');
  if (!track || cards.length === 0) return;

  const DELAY_MS = 5500;
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let timer = null;

  const maxScroll = () => Math.max(0, track.scrollWidth - track.clientWidth);
  const offset = card => card.offsetLeft - cards[0].offsetLeft;

  // Last valid stop: do not show an empty portion of the carousel.
  const lastIndex = () => {
    const max = maxScroll();
    if (max < 2) return 0;
    let index = 0;
    for (let i = 1; i < cards.length; i++) {
      if (offset(cards[i]) > max + 2) break;
      index = i;
    }
    return index;
  };

  const activeIndex = () => {
    let best = 0;
    let distance = Infinity;
    const current = track.scrollLeft;
    for (let i = 0; i <= lastIndex(); i++) {
      const difference = Math.abs(offset(cards[i]) - current);
      if (difference < distance) {
        distance = difference;
        best = i;
      }
    }
    return best;
  };

  function refresh() {
    const usable = maxScroll() > 2;
    if (prev) {
      prev.disabled = !usable;
      prev.setAttribute('aria-disabled', String(!usable));
    }
    if (next) {
      next.disabled = !usable;
      next.setAttribute('aria-disabled', String(!usable));
    }
  }

  function show(index) {
    const last = lastIndex();
    if (last === 0) return;
    const wrapped = index > last ? 0 : (index < 0 ? last : index);
    track.scrollTo({
      left: Math.min(offset(cards[wrapped]), maxScroll()),
      behavior: reduceMotion.matches ? 'instant' : 'smooth'
    });
  }

  function stop() {
    if (timer !== null) window.clearInterval(timer);
    timer = null;
  }

  function play() {
    stop();
    refresh();
    if (cards.length < 2 || maxScroll() < 2 || reduceMotion.matches || document.hidden) return;
    // Hold while keyboard focus is inside, so the selected link does not move away.
    if (component.matches(':focus-within')) return;
    timer = window.setInterval(() => {
      if (!component.matches(':focus-within') && !document.hidden) {
        show(activeIndex() + 1);
      }
    }, DELAY_MS);
  }

  prev?.addEventListener('click', () => { show(activeIndex() - 1); play(); });
  next?.addEventListener('click', () => { show(activeIndex() + 1); play(); });
  track.addEventListener('scroll', refresh, { passive: true });
  component.addEventListener('focusin', stop);
  component.addEventListener('focusout', () => window.setTimeout(play, 0));
  document.addEventListener('visibilitychange', play);
  window.addEventListener('resize', play);
  if (typeof reduceMotion.addEventListener === 'function') {
    reduceMotion.addEventListener('change', play);
  }

  refresh();
  play();
})();
