// assets/gcb_theme/js/offcanvas.js  (FINAL)

document.addEventListener('DOMContentLoaded', () => {
  if (window.__gcbOffcanvasInit) return;
  window.__gcbOffcanvasInit = true;

  const panel = document.getElementById('offCanvasArea');
  if (!panel) return;

  const toggles = Array.from(document.querySelectorAll('.js-offcanvas-toggle'));
  if (toggles.length === 0) return;

  let overlay = document.getElementById('offCanvasOverlay') || document.querySelector('.offcanvas-overlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.id = 'offCanvasOverlay';
    overlay.className = 'offcanvas-overlay';
    document.body.appendChild(overlay);
  }

  const open = () => {
    panel.classList.add('is-open');
    overlay.classList.add('is-active');
    document.body.classList.add('no-scroll','off-canvas-open');
    toggles.forEach(b => b.setAttribute('aria-expanded','true'));
    panel.setAttribute('aria-hidden','false');
  };

  const close = () => {
    panel.classList.remove('is-open');
    overlay.classList.remove('is-active');
    document.body.classList.remove('no-scroll','off-canvas-open');
    toggles.forEach(b => b.setAttribute('aria-expanded','false'));
    panel.setAttribute('aria-hidden','true');
  };

  const toggle = e => { e && e.preventDefault(); (panel.classList.contains('is-open') ? close : open)(); };

  toggles.forEach(b => b.addEventListener('click', toggle));
  overlay.addEventListener('click', close);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
  panel.querySelectorAll('a').forEach(a => a.addEventListener('click', close));
});
