// ==========================================
// MAIN.JS - HMTI
// Hamburger menu, dropdown toggle, theme switcher
// ==========================================

document.addEventListener("DOMContentLoaded", function () {
  const html = document.documentElement;
  const hamburgerBtn = document.getElementById("hamburgerBtn");
  const navMenu = document.getElementById("navMenu");
  const themeToggle = document.getElementById("themeToggle");
  const mobileThemeToggle = document.getElementById("mobileThemeToggle");

  // ==========================================
  // THEME SWITCHER
  // ==========================================

  /**
   * Set theme dan simpan ke localStorage
   */
  function setTheme(theme) {
    html.setAttribute("data-theme", theme);
    localStorage.setItem("hmti-theme", theme);
    // Update label mobile
    const label = document.querySelector(".theme-label");
    if (label) {
      label.textContent = theme === "dark" ? "Mode Terang" : "Mode Gelap";
    }
  }

  /**
   * Baca preferensi tema: localStorage > system preference > default light
   */
  function getPreferredTheme() {
    const stored = localStorage.getItem("hmti-theme");
    if (stored === "dark" || stored === "light") return stored;
    // Cek preferensi sistem operasi
    if (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) {
      return "dark";
    }
    return "light";
  }

  // Terapkan tema saat halaman dimuat
  setTheme(getPreferredTheme());

  // Toggle tema (desktop)
  if (themeToggle) {
    themeToggle.addEventListener("click", function () {
      const current = html.getAttribute("data-theme");
      setTheme(current === "dark" ? "light" : "dark");
    });
  }

  // Toggle tema (mobile - di dalam nav menu)
  if (mobileThemeToggle) {
    mobileThemeToggle.addEventListener("click", function () {
      const current = html.getAttribute("data-theme");
      setTheme(current === "dark" ? "light" : "dark");
    });
  }

  // ==========================================
  // HAMBURGER MENU
  // ==========================================

  if (hamburgerBtn && navMenu) {
    hamburgerBtn.addEventListener("click", function () {
      hamburgerBtn.classList.toggle("active");
      navMenu.classList.toggle("active");
    });
  }

  // ==========================================
  // DROPDOWN TOGGLE (Mobile)
  // ==========================================

  const dropdownToggles = document.querySelectorAll(".dropdown-toggle");

  dropdownToggles.forEach(function (toggle) {
    toggle.addEventListener("click", function () {
      if (window.innerWidth <= 860) {
        const dropdown = this.nextElementSibling;
        dropdown.classList.toggle("open");
        this.classList.toggle("rotate");
      }
    });
  });

  // ==========================================
  // TUTUP MENU SAAT KLIK DI LUAR
  // ==========================================

  document.addEventListener("click", function (e) {
    if (!navMenu || !hamburgerBtn) return;
    const isClickInsideNav = navMenu.contains(e.target) || hamburgerBtn.contains(e.target);
    if (!isClickInsideNav && navMenu.classList.contains("active")) {
      navMenu.classList.remove("active");
      hamburgerBtn.classList.remove("active");
    }
  });

  // ==========================================
  // DENGARKAN PERUBAHAN PREFERENSI SISTEM
  // ==========================================

  if (window.matchMedia) {
    const darkModeMedia = window.matchMedia("(prefers-color-scheme: dark)");
    darkModeMedia.addEventListener("change", function (e) {
      // Hanya ganti otomatis jika user belum menyimpan preferensi manual
      if (!localStorage.getItem("hmti-theme")) {
        setTheme(e.matches ? "dark" : "light");
      }
    });
  }
});

// ==========================================
// INTERAKSI TAMBAHAN (Interactive UI)
// Scroll reveal, sticky header, back-to-top,
// ripple, counter animasi, scroll indicator
// ==========================================
(function () {
  const prefersReduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // ---------- SCROLL REVEAL (animasi muncul saat di-scroll) ----------
  if ("IntersectionObserver" in window) {
    const revealSelectors = [
      ".section-heading",
      ".kegiatan-card",
      ".departemen-card",
      ".galeri-item",
      ".misi-item",
      ".misi-title",
      ".visi-card",
      ".konten-block",
      ".suara-card",
      ".struktur-group",
      ".kontak-info-card",
      ".kontak-social-row",
      ".maps-wrapper",
      ".detail-hero",
      ".detail-content",
      ".pagination",
      ".suara-guarantee",
      // CATATAN: .carousel-card & .dept-panel TIDAK di-reveal karena
      // transform/display-nya krusial untuk carousel & tab.
    ];
    const elements = document.querySelectorAll(revealSelectors.join(","));

    elements.forEach(function (el) {
      el.classList.add("reveal");
      // Kalau sudah di dalam viewport saat halaman dimuat → langsung tampil
      // (dilakukan sinkron supaya tidak ada flash)
      const rect = el.getBoundingClientRect();
      if (rect.top < window.innerHeight && rect.bottom > 0) {
        el.classList.add("in-view");
      }
    });

    if (prefersReduced) {
      elements.forEach(function (el) {
        el.classList.add("in-view");
      });
    } else {
      const io = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add("in-view");
              io.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.12, rootMargin: "0px 0px -40px 0px" },
      );
      elements.forEach(function (el) {
        io.observe(el);
      });
    }
  }

  // ---------- STICKY HEADER (bayangan + mengecil saat di-scroll) ----------
  const siteHeader = document.querySelector(".site-header");
  if (siteHeader) {
    const onScrollHeader = function () {
      siteHeader.classList.toggle("scrolled", window.scrollY > 12);
    };
    onScrollHeader();
    window.addEventListener("scroll", onScrollHeader, { passive: true });
  }

  // ---------- BACK TO TOP ----------
  const backToTop = document.getElementById("backToTop");
  if (backToTop) {
    const toggleBackToTop = function () {
      backToTop.classList.toggle("visible", window.scrollY > 350);
    };
    toggleBackToTop();
    window.addEventListener("scroll", toggleBackToTop, { passive: true });
    backToTop.addEventListener("click", function () {
      if (prefersReduced) {
        window.scrollTo({ top: 0 });
        return;
      }
      // Roket kecil "terbang" dulu, baru halaman digulir ke atas (lucu & unik)
      const rect = this.getBoundingClientRect();
      const rocket = document.createElement("span");
      rocket.className = "btt-rocket";
      rocket.textContent = "\uD83D\uDE80";
      rocket.style.left = rect.left + rect.width / 2 + "px";
      rocket.style.top = rect.top + "px";
      document.body.appendChild(rocket);
      rocket.addEventListener("animationend", function () {
        rocket.remove();
        window.scrollTo({ top: 0 });
      });
    });
  }

  // ---------- RIPPLE EFFECT (efek gelombang saat tombol diklik) ----------
  if (!prefersReduced) {
    document.querySelectorAll(".btn-cta, .btn-hero, .btn-back").forEach(function (btn) {
      btn.addEventListener("pointerdown", function (e) {
        const rect = this.getBoundingClientRect();
        const d = Math.max(rect.width, rect.height);
        const span = document.createElement("span");
        span.className = "ripple";
        span.style.width = span.style.height = d + "px";
        span.style.left = e.clientX - rect.left - d / 2 + "px";
        span.style.top = e.clientY - rect.top - d / 2 + "px";
        this.appendChild(span);
        setTimeout(function () {
          span.remove();
        }, 600);
      });
    });
  }

  // ---------- COUNTER ANIMASI (angka statistik, mis. dashboard admin) ----------
  const counters = document.querySelectorAll("[data-count]");
  if (counters.length && !prefersReduced && "IntersectionObserver" in window) {
    const cio = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          const el = entry.target;
          cio.unobserve(el);
          const target = parseInt(el.getAttribute("data-count"), 10) || 0;
          const dur = 900;
          const start = performance.now();
          const step = function (now) {
            const p = Math.min((now - start) / dur, 1);
            const eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
            el.textContent = Math.round(target * eased);
            if (p < 1) {
              requestAnimationFrame(step);
            } else {
              el.textContent = target;
            }
          };
          requestAnimationFrame(step);
        });
      },
      { threshold: 0.5 },
    );
    counters.forEach(function (c) {
      cio.observe(c);
    });
  } else {
    // fallback: tampilkan angka asli
    counters.forEach(function (c) {
      c.textContent = c.getAttribute("data-count") || "0";
    });
  }

  // ---------- SCROLL INDICATOR (hero beranda) ----------
  const scrollInd = document.querySelector(".scroll-indicator");
  if (scrollInd) {
    scrollInd.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(scrollInd.getAttribute("href"));
      if (target) {
        target.scrollIntoView({ behavior: prefersReduced ? "auto" : "smooth" });
      }
    });
  }
})();

// ==========================================
// FITUR INTERAKTIF
// Progres scroll, coretan heading, konfeti easter egg,
// toast selamat datang, cincin progres back-to-top
// ==========================================
(function () {
  const prefersReduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // ---------- BILAH PROGRES SCROLL + CINCIN BACK-TO-TOP ----------
  const progressBar = document.getElementById("scrollProgress");
  const bttRing = document.querySelector(".back-to-top .btt-ring-fg");
  const RING_C = 2 * Math.PI * 20; // r = 20

  function updateScrollChrome() {
    const max = document.documentElement.scrollHeight - window.innerHeight;
    const p = max > 0 ? Math.min(window.scrollY / max, 1) : 0;
    if (progressBar) progressBar.style.width = p * 100 + "%";
    if (bttRing) bttRing.style.strokeDashoffset = RING_C * (1 - p);
  }
  window.addEventListener("scroll", updateScrollChrome, { passive: true });
  window.addEventListener("resize", updateScrollChrome);
  updateScrollChrome();

  // ---------- GARIS CORETAN TANGAN DI BAWAH JUDUL SECTION ----------
  document.querySelectorAll(".section-heading").forEach(function (head) {
    if (head.querySelector(".scribble")) return;
    const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    svg.setAttribute("class", "scribble");
    svg.setAttribute("viewBox", "0 0 120 14");
    svg.setAttribute("preserveAspectRatio", "none");
    svg.setAttribute("aria-hidden", "true");
    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
    path.setAttribute("d", "M4,9 C16,3 26,11 38,7 C50,3 60,10 72,6 C84,3 94,9 106,6 C112,4 115,7 117,6");
    svg.appendChild(path);
    head.appendChild(svg);
  });

  // ---------- TOAST ----------
  let toastTimer = null;
  function tampilkanToast(pesan) {
    let toast = document.getElementById("siteToast");
    if (!toast) {
      toast = document.createElement("div");
      toast.id = "siteToast";
      toast.className = "site-toast";
      document.body.appendChild(toast);
    }
    toast.textContent = pesan;
    toast.classList.add("show");
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () {
      toast.classList.remove("show");
    }, 3200);
  }

  // ---------- KONFETI (easter egg: ketik "hmti") ----------
  const PALETTE = ["#e05252", "#e8a317", "#2f9e6e", "#3b82f6", "#8b5cf6", "#ec4899"];

  function konfeti() {
    if (prefersReduced) return;
    for (let i = 0; i < 90; i++) {
      const piece = document.createElement("div");
      piece.className = "confetti-piece";
      piece.style.left = Math.random() * 100 + "vw";
      piece.style.background = PALETTE[Math.floor(Math.random() * PALETTE.length)];
      piece.style.width = 6 + Math.random() * 6 + "px";
      piece.style.height = 8 + Math.random() * 6 + "px";
      piece.style.animationDelay = Math.random() * 0.6 + "s";
      piece.style.animationDuration = 2.2 + Math.random() * 1.8 + "s";
      piece.style.setProperty("--drift", (Math.random() * 120 - 60).toFixed(1) + "px");
      piece.style.setProperty("--spin", (Math.random() * 720 - 360).toFixed(0) + "deg");
      document.body.appendChild(piece);
      piece.addEventListener("animationend", function () {
        piece.remove();
      });
    }
    bunyiPop();
    tampilkanToast("Yeay, kamu nemu rahasia HMTI! \uD83C\uDF89");
  }

  let typed = "";
  document.addEventListener("keydown", function (e) {
    const tag = (e.target.tagName || "").toLowerCase();
    if (tag === "input" || tag === "textarea" || e.target.isContentEditable) {
      typed = "";
      return;
    }
    if (e.key.length !== 1) return;
    typed = (typed + e.key.toLowerCase()).slice(-4);
    if (typed === "hmti") {
      konfeti();
      typed = "";
    }
  });

  // ---------- SUARA "POP" KECIL (easter egg) ----------
  function bunyiPop() {
    try {
      const AC = window.AudioContext || window.webkitAudioContext;
      if (!AC) return;
      const ctx = new AC();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = "triangle";
      osc.frequency.setValueAtTime(620, ctx.currentTime);
      osc.frequency.exponentialRampToValueAtTime(930, ctx.currentTime + 0.07);
      gain.gain.setValueAtTime(0.09, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.14);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.16);
    } catch (err) {
      /* abaikan kalau browser memblokir audio */
    }
  }

  // ---------- TOAST SELAMAT DATANG (kunjungan pertama, pesan acak) ----------
  if (!localStorage.getItem("hmti-welcomed")) {
    localStorage.setItem("hmti-welcomed", "1");
    const SAPAAN_WELCOME = ["Halo! Selamat datang di website HMTI \uD83D\uDC4B", "Hai! Asik banget bisa mampir ke sini \uD83D\uDE04", "Halo-halo! Selamat datang di HMTI \u2728", "Yok kenalan! Ini website resmi HMTI \uD83E\uDD1D"];
    setTimeout(function () {
      tampilkanToast(SAPAAN_WELCOME[Math.floor(Math.random() * SAPAAN_WELCOME.length)]);
    }, 900);
  }

  // ---------- SPARKLE SAAT KLIK (sentuhan lucu) ----------
  if (!prefersReduced) {
    const SPARK = ["\u2728", "\u2B50", "\uD83D\uDCAB", "\uD83C\uDF1F"];
    document.addEventListener("click", function (e) {
      // jangan munculkan sparkle saat klik elemen interaktif
      if (e.target.closest("a, button, input, textarea, select")) return;
      for (let i = 0; i < 6; i++) {
        const s = document.createElement("span");
        s.className = "click-sparkle";
        s.textContent = SPARK[Math.floor(Math.random() * SPARK.length)];
        s.style.left = e.clientX + "px";
        s.style.top = e.clientY + "px";
        s.style.setProperty("--sx", (Math.random() * 80 - 40).toFixed(0) + "px");
        s.style.setProperty("--sy", (-40 - Math.random() * 60).toFixed(0) + "px");
        s.style.animationDelay = i * 0.03 + "s";
        document.body.appendChild(s);
        s.addEventListener("animationend", function () {
          s.remove();
        });
      }
    });
  }

  // ---------- JAM DIGITAL DI FOOTER (zona WIB) ----------
  const footerClock = document.getElementById("footerClock");
  if (footerClock) {
    const pad = function (n) {
      return String(n).padStart(2, "0");
    };
    // Selalu tampilkan waktu WIB (Asia/Jakarta), bukan zona waktu pengunjung
    let fmtWib = null;
    try {
      fmtWib = new Intl.DateTimeFormat("en-GB", {
        timeZone: "Asia/Jakarta",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
      });
    } catch (err) {
      fmtWib = null;
    }
    function updateClock() {
      let teksJam;
      if (fmtWib) {
        const parts = fmtWib.formatToParts(new Date());
        const ambil = function (t) {
          const p = parts.find(function (x) {
            return x.type === t;
          });
          return p ? p.value : "00";
        };
        teksJam = ambil("hour") + ":" + ambil("minute") + ":" + ambil("second");
      } else {
        // fallback: hitung UTC+7 manual (WIB)
        const d = new Date(new Date().getTime() + 7 * 3600 * 1000);
        teksJam = pad(d.getUTCHours()) + ":" + pad(d.getUTCMinutes()) + ":" + pad(d.getUTCSeconds());
      }
      footerClock.textContent = "Pukul " + teksJam + " WIB";
    }
    updateClock();
    setInterval(updateClock, 1000);
  }

  // ---------- TILT 3D HALUS PADA KARTU (kesan disentuh tangan) ----------
  const punyaKursor = window.matchMedia && window.matchMedia("(hover: hover) and (pointer: fine)").matches;
  if (punyaKursor && !prefersReduced) {
    document.querySelectorAll(".kegiatan-card, .departemen-card").forEach(function (card) {
      card.classList.add("tilt-card");
      card.addEventListener("pointermove", function (e) {
        const r = this.getBoundingClientRect();
        const px = (e.clientX - r.left) / r.width - 0.5; // -0.5 .. 0.5
        const py = (e.clientY - r.top) / r.height - 0.5;
        this.style.setProperty("--rx", (-py * 6).toFixed(2) + "deg");
        this.style.setProperty("--ry", (px * 8).toFixed(2) + "deg");
      });
      card.addEventListener("pointerleave", function () {
        this.style.setProperty("--rx", "0deg");
        this.style.setProperty("--ry", "0deg");
      });
    });
  }

  // ---------- JUDUL TAB BERUBAH SAAT DIKECILKAN (manusiawi & lucu) ----------
  const judulAsli = document.title;
  let judulBerubah = false;
  document.addEventListener("visibilitychange", function () {
    if (document.hidden) {
      judulBerubah = true;
      document.title = "Kembali lagi yuk \uD83D\uDC40";
    } else if (judulBerubah) {
      document.title = judulAsli;
      judulBerubah = false;
    }
  });
  window.addEventListener("focus", function () {
    if (judulBerubah) {
      document.title = judulAsli;
      judulBerubah = false;
    }
  });

  // ---------- KLIK TANGAN MELAMBAI → SALAM ----------
  const waveHand = document.querySelector(".wave-hand");
  if (waveHand) {
    waveHand.style.cursor = "pointer";
    waveHand.title = "Salam HMTI!";
    waveHand.addEventListener("click", function () {
      tampilkanToast("Salam HMTI! \uD83D\uDC4B");
    });
  }

  // ---------- TOMBOL "?" → BANTUAN CEPAT ----------
  document.addEventListener("keydown", function (e) {
    const tag = (e.target.tagName || "").toLowerCase();
    if (tag === "input" || tag === "textarea" || e.target.isContentEditable) return;
    if (e.key === "?" || (e.shiftKey && e.key === "/")) {
      tampilkanToast('Rahasia HMTI: coba ketik "hmti" \uD83D\uDC40');
    }
  });
})();
