/* ================================================================
   Night Mode — AKUVISA
   Simpan preferensi di localStorage agar persisten antar halaman
   ================================================================ */

(function () {
  var STORAGE_KEY = 'akuvisa_night_mode';

  function isNight() {
    return localStorage.getItem(STORAGE_KEY) === '1';
  }

  function applyMode(night) {
    if (night) {
      document.body.classList.add('night-mode');
    } else {
      document.body.classList.remove('night-mode');
    }
    var btn = document.getElementById('nightModeToggle');
    if (btn) {
      btn.title = night ? 'Mode Siang' : 'Mode Malam';
      btn.innerHTML = night ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    }
  }

  function toggle() {
    var night = !isNight();
    localStorage.setItem(STORAGE_KEY, night ? '1' : '0');
    applyMode(night);
  }

  function bindToggleBtn() {
    var btn = document.getElementById('nightModeToggle');
    if (!btn) return;
    // Hapus listener lama supaya tidak double-bind
    btn.removeEventListener('click', toggle);
    btn.addEventListener('click', toggle);
  }

  document.addEventListener('DOMContentLoaded', function () {
    applyMode(isNight());

    // Aktifkan transisi CSS hanya setelah halaman selesai render
    // agar tidak ada flash animasi putih → gelap saat navigasi
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        document.body.classList.add('transitions-enabled');
      });
    });

    bindToggleBtn();

    // ── Solusi utama bug: scripts.js template melakukan
    //    $(body).off('click').on('click', ...) saat resize window,
    //    yang menghapus SEMUA native event listener di body termasuk
    //    listener turunannya (event delegation).
    //
    //    Solusi: pasang listener langsung di tombol (bukan delegation),
    //    DAN re-bind ulang setiap kali jQuery selesai override body click.
    //    Kita gunakan MutationObserver untuk re-bind jika navbar berubah,
    //    dan re-bind setiap window resize (saat scripts.js reset body click).
    // ──────────────────────────────────────────────────────────────
    var observer = new MutationObserver(function () {
      bindToggleBtn();
    });
    var navbar = document.querySelector('.main-navbar');
    if (navbar) {
      observer.observe(navbar, { childList: true, subtree: true });
    }

    // Re-bind setiap kali window di-resize
    // (scripts.js memanggil toggleLayout → $(body).off('click').on(...))
    window.addEventListener('resize', function () {
      setTimeout(bindToggleBtn, 300);
    });
  });

  // Expose globally so inline onclick can call it
  window.toggleNightMode = toggle;
})();