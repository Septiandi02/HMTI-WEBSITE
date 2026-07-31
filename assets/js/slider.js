// ==========================================
// SLIDER.JS - Coverflow Carousel
// Card tengah = fokus (besar, opacity penuh)
// Card kiri/kanan = mundur (kecil, opacity redup)
// Card lainnya = disembunyikan (hanya 3 yang terlihat)
// ==========================================

document.addEventListener("DOMContentLoaded", function () {
  const carousels = document.querySelectorAll("[data-carousel]");

  carousels.forEach(function (carousel) {
    const cards = Array.from(carousel.querySelectorAll(".carousel-card"));
    const btnPrev = carousel.querySelector(".carousel-arrow.prev");
    const btnNext = carousel.querySelector(".carousel-arrow.next");
    const total = cards.length;

    // Tidak ada card atau cuma 1 → tidak perlu carousel aktif, sembunyikan panah
    if (total === 0) {
      if (btnPrev) btnPrev.style.display = "none";
      if (btnNext) btnNext.style.display = "none";
      return;
    }
    if (total === 1) {
      cards[0].style.transform = "translate(-50%, -50%) scale(1)";
      cards[0].style.opacity = "1";
      cards[0].style.zIndex = "3";
      if (btnPrev) btnPrev.style.display = "none";
      if (btnNext) btnNext.style.display = "none";
      return;
    }

    let currentIndex = 0;

    function render() {
      cards.forEach(function (card, i) {
        // Hitung jarak terpendek ke currentIndex (mempertimbangkan wraparound melingkar)
        let diff = i - currentIndex;
        if (diff > total / 2) diff -= total;
        if (diff < -total / 2) diff += total;

        const cardWidth = cards[0].offsetWidth || 200;
        const gap = cardWidth * 0.75; // jarak antar posisi card

        card.classList.remove("is-active");

        if (diff === 0) {
          card.style.transform = "translate(-50%, -50%) translateX(0) scale(1)";
          card.style.opacity = "1";
          card.style.zIndex = "3";
          card.style.pointerEvents = "auto";
          card.classList.add("is-active");
        } else if (diff === -1 || diff === total - 1) {
          card.style.transform = `translate(-50%, -50%) translateX(-${gap}px) scale(0.8)`;
          card.style.opacity = "0.45";
          card.style.zIndex = "2";
          card.style.pointerEvents = "auto";
        } else if (diff === 1 || diff === -(total - 1)) {
          card.style.transform = `translate(-50%, -50%) translateX(${gap}px) scale(0.8)`;
          card.style.opacity = "0.45";
          card.style.zIndex = "2";
          card.style.pointerEvents = "auto";
        } else {
          // Card lain disembunyikan total, hanya 3 yang tampil
          const dir = diff < 0 ? -1 : 1;
          card.style.transform = `translate(-50%, -50%) translateX(${dir * gap * 1.6}px) scale(0.6)`;
          card.style.opacity = "0";
          card.style.zIndex = "1";
          card.style.pointerEvents = "none";
        }
      });
    }

    function goTo(newIndex) {
      currentIndex = (newIndex + total) % total;
      render();
    }

    if (btnPrev) btnPrev.addEventListener("click", () => goTo(currentIndex - 1));
    if (btnNext) btnNext.addEventListener("click", () => goTo(currentIndex + 1));

    // Klik card kiri/kanan langsung membawanya ke tengah
    cards.forEach(function (card, i) {
      card.addEventListener("click", function () {
        if (i !== currentIndex) goTo(i);
      });
    });

    // Swipe di mobile
    let touchStartX = 0;
    carousel.addEventListener(
      "touchstart",
      function (e) {
        touchStartX = e.touches[0].clientX;
      },
      { passive: true },
    );

    carousel.addEventListener(
      "touchend",
      function (e) {
        const touchEndX = e.changedTouches[0].clientX;
        const delta = touchEndX - touchStartX;
        if (delta > 40) goTo(currentIndex - 1);
        else if (delta < -40) goTo(currentIndex + 1);
      },
      { passive: true },
    );

    render();
    window.addEventListener("resize", render);
  });
});
