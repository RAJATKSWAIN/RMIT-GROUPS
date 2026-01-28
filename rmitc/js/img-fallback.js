<script>
/**
 * RMIT  — Missing image fallback
 * - Replaces broken <img> with a fallback image
 * - Scopes to specific path prefixes (e.g., images/students, uploads)
 * - Skips elements marked with data-no-fallback="true"
 * - Prevents infinite loops if fallback fails
 */

(function () {
  // Configure once
  const FALLBACK_URL = 'https://rmitgroupsorg.infinityfree.me/assets/images/no-photo.png';
  const INCLUDE_PATHS = [
    '/rmitc/images/students/', // student photos
    '/rmitc/uploads/',          // general uploads
    '/rmitc/images/faculty/'
  ];

  // Helper: should this URL be handled?
  function shouldHandle(url) {
    try {
      // Normalize to pathname
      const a = document.createElement('a');
      a.href = url;
      const path = a.pathname || url;
      return INCLUDE_PATHS.some(prefix => path.indexOf(prefix) === 0);
    } catch (e) {
      return false;
    }
  }

  // Apply fallback to a single <img>
  function applyFallback(img) {
    if (!img || img.dataset.noFallback === 'true') return;

    // Avoid loops: if already set to fallback, stop
    if (img.dataset.fallbackApplied === 'true') return;

    // Only handle scoped paths
    if (!shouldHandle(img.currentSrc || img.src)) return;

    // Mark and swap
    img.dataset.fallbackApplied = 'true';
    img.src = FALLBACK_URL;
    // Optional: add a class for styling if needed
    img.classList.add('img-fallback');
  }

  // Attach error handler to existing images
  function bindImage(img) {
    if (!img || img.dataset.fallbackBound === 'true') return;
    img.dataset.fallbackBound = 'true';
    img.addEventListener('error', function () {
      applyFallback(img);
    }, { once: true });
  }

  // Bind to all current images
  function initAll() {
    document.querySelectorAll('img').forEach(bindImage);
  }

  // Observe DOM for dynamically added images
  const mo = new MutationObserver(mutations => {
    mutations.forEach(m => {
      m.addedNodes.forEach(node => {
        if (node.nodeType !== 1) return;
        if (node.tagName === 'IMG') {
          bindImage(node);
        } else {
          node.querySelectorAll && node.querySelectorAll('img').forEach(bindImage);
        }
      });
    });
  });

  // Start
  document.addEventListener('DOMContentLoaded', function () {
    initAll();
    mo.observe(document.documentElement, { childList: true, subtree: true });
  });
})();
</script>