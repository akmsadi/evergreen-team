new Swiper(".hero-slider", {
    loop: true,

    autoplay: {
        delay: 4000,
        disableOnInteraction: false,
    },

    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },

    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },

    effect: "fade",
    fadeEffect: {
        crossFade: true,
    },

    speed: 1000,
});

const swiper = new Swiper(".mySwiper", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: "auto",
    loop: true,

    // Responsive 3D parameter adjustments
    breakpoints: {
        // Mobile / default settings (under 768px wide)
        320: {
            coverflowEffect: {
                rotate: 0,
                stretch: -15, // Looser pull for smaller cards
                depth: 100, // Less depth so side cards stay visible on mobile layout
                modifier: 1,
                slideShadows: false,
            },
        },
        // Tablet layouts (768px and up)
        768: {
            coverflowEffect: {
                rotate: 0,
                stretch: -30,
                depth: 130,
                modifier: 1.1,
                slideShadows: false,
            },
        },
        // Widescreen layouts (1200px and up)
        1200: {
            coverflowEffect: {
                rotate: 0,
                stretch: -40, // Tight stacking for large 420px cards
                depth: 160, // Full 3D push back
                modifier: 1.2,
                slideShadows: false,
            },
        },
    },

    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});

/**
 * Back to Top Button
 * */
(() => {
    const backToTopButton = document.getElementById("backToTopButton");

    if (!backToTopButton) {
        return;
    }

    const toggleBackToTop = () => {
        backToTopButton.classList.toggle("is-visible", window.scrollY > 320);
    };

    backToTopButton.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    });

    toggleBackToTop();
    window.addEventListener("scroll", toggleBackToTop, {
        passive: true,
    });
})();

/**
 * Countdown timer
 */
(() => {
    // Initialize countdown timer for next match
    const daysEl = document.getElementById("countdown-days");
    const hoursEl = document.getElementById("countdown-hours");
    const minsEl = document.getElementById("countdown-mins");
    const secsEl = document.getElementById("countdown-secs");
    const nextMatchDateEl = document.getElementById("nextMatchDate");

    // Only run if countdown elements exist (next match is available)
    if (!daysEl || !nextMatchDateEl) {
        return;
    }

    const pad = (n) => String(n).padStart(2, "0");

    const updateCountdown = () => {
        const nextMatchDate = new Date(parseInt(nextMatchDateEl.dataset.ts));
        const now = new Date();
        const difference = nextMatchDate - now;

        if (difference <= 0) {
            daysEl.textContent = "00";
            hoursEl.textContent = "00";
            minsEl.textContent = "00";
            secsEl.textContent = "00";
            return;
        }

        daysEl.textContent = pad(Math.floor(difference / (1000 * 60 * 60 * 24)));
        hoursEl.textContent = pad(Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
        minsEl.textContent = pad(Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60)));
        secsEl.textContent = pad(Math.floor((difference % (1000 * 60)) / 1000));
    };

    updateCountdown();
    setInterval(updateCountdown, 1000);
})();

/**
 * Events Gallery
 */
(() => {
    const events = window.eventsGalleryData;
    if (!Array.isArray(events) || !events.length) return;

    const grid = document.getElementById("eventImagesGrid");
    const noImages = document.getElementById("eventNoImages");
    const listItems = document.querySelectorAll(".event-list-item");
    let lightbox = null;

    function renderEvent(index) {
        const event = events[index];
        grid.innerHTML = "";

        if (!event.images.length) {
            noImages.style.display = "";
            if (lightbox) {
                lightbox.destroy();
                lightbox = null;
            }
            return;
        }

        noImages.style.display = "none";

        event.images.forEach((img) => {
            const a = document.createElement("a");
            a.href = img.src;
            a.className = "event-gallery-link glightbox";
            a.dataset.gallery = "event-" + event.id;
            a.dataset.description = img.caption || "";
            a.dataset.title = img.caption || "";

            const el = document.createElement("img");
            el.src = img.src;
            el.alt = img.caption || event.title;
            el.loading = "lazy";

            a.appendChild(el);
            grid.appendChild(a);
        });

        if (lightbox) lightbox.destroy();
        lightbox = GLightbox({
            selector: ".event-gallery-link",
            loop: true,
            touchNavigation: true,
        });
    }

    listItems.forEach((btn, i) => {
        btn.addEventListener("click", () => {
            listItems.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            renderEvent(i);
        });
    });

    renderEvent(0);
})();
