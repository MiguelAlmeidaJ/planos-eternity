(() => {
  const name = document.getElementById('plan-name');
  const slug = document.getElementById('plan-slug');
  if (!name || !slug) return;
  const generate = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 100);
  name.addEventListener('input', () => { slug.value = generate(name.value); });
})();
