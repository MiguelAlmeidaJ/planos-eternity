(() => {
  'use strict';
  const body = document.querySelector('#sortable-plans');
  if (!body || !body.querySelector('tr[data-id]')) return;
  const error = document.querySelector('#plan-order-error');
  const status = document.querySelector('#plan-order-feedback');
  let dragging = null;
  let pending = false;
  let saved = Array.from(body.querySelectorAll('tr[data-id]'), row => row.dataset.id);

  function rows() { return [...body.querySelectorAll('tr[data-id]')]; }
  function refreshPositions() {
    rows().forEach((row, i) => {
      row.querySelector('.plan-position').textContent = String(i + 1);
    });
  }
  function showError(message) {
    if (!error) return;
    error.textContent = message;
    error.classList.remove('d-none');
    status.textContent = message;
  }
  function clearError() { error?.classList.add('d-none'); }

  async function persist() {
    const current = rows().map(row => row.dataset.id);
    if (current.join(',') === saved.join(',')) return;
    if (pending) return;
    pending = true;
    body.setAttribute('aria-busy', 'true');
    clearError();
    status.textContent = 'Salvando nova ordem…';
    const payload = new URLSearchParams({
      action: 'reorder', csrf: body.dataset.csrf, ids: JSON.stringify(current.map(Number))
    });
    try {
      const response = await fetch('/admin/planos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', 'Accept': 'application/json' },
        credentials: 'same-origin',
        body: payload
      });
      const result = await response.json();
      if (!response.ok || !result.ok) throw new Error(result.message || 'Não foi possível salvar a ordem.');
      saved = current;
      status.textContent = 'Nova ordem salva com sucesso.';
    } catch (e) {
      const original = new Map(rows().map(row => [row.dataset.id, row]));
      saved.forEach(id => body.appendChild(original.get(id)));
      refreshPositions();
      showError(e.message || 'Falha ao salvar a ordem. Tente novamente.');
    } finally {
      pending = false;
      body.removeAttribute('aria-busy');
    }
  }

  body.addEventListener('dragstart', event => {
    const row = event.target.closest('tr[data-id]');
    if (!row || pending) { event.preventDefault(); return; }
    dragging = row;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', row.dataset.id);
    row.classList.add('is-dragging');
  });
  body.addEventListener('dragover', event => {
    if (!dragging || pending) return;
    const target = event.target.closest('tr[data-id]');
    if (!target || target === dragging) return;
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
    const midpoint = target.getBoundingClientRect().top + target.getBoundingClientRect().height / 2;
    body.insertBefore(dragging, event.clientY > midpoint ? target.nextSibling : target);
  });
  body.addEventListener('drop', event => { if (dragging) event.preventDefault(); });
  body.addEventListener('dragend', () => {
    if (!dragging) return;
    dragging.classList.remove('is-dragging');
    dragging = null;
    refreshPositions();
    persist();
  });

  body.addEventListener('keydown', event => {
    const handle = event.target.closest('.plan-drag-handle');
    if (!handle || !['ArrowUp', 'ArrowDown'].includes(event.key) || pending) return;
    event.preventDefault();
    const row = handle.closest('tr[data-id]');
    if (event.key === 'ArrowUp' && row.previousElementSibling?.matches('tr[data-id]')) {
      body.insertBefore(row, row.previousElementSibling);
    } else if (event.key === 'ArrowDown' && row.nextElementSibling?.matches('tr[data-id]')) {
      body.insertBefore(row.nextElementSibling, row);
    } else return;
    refreshPositions();
    handle.focus();
    persist();
  });
})();
