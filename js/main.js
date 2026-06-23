/* LOGO HOVER EFFECT */
const logo = document.querySelector(".nav-logo img");

if (logo && window.matchMedia("(hover: hover)").matches) {
    const isDarkHover =
        document.querySelector(".article-header") ||
        document.querySelector(".category-header");
    const hoverLogo = isDarkHover
        ? "assets/img/logo222.png"
        : "assets/img/logo22.png";
    logo.addEventListener("mouseenter", () => {
        logo.src = hoverLogo;
    });
    logo.addEventListener("mouseleave", () => {
        logo.src = "assets/img/logo2.png";
    });
}

/* COUNTER ANIMATION */
const counters = document.querySelectorAll(".counter");

const animateCounter = (counter) => {
    const target = Number(counter.dataset.target);
    const duration = 1200;
    const startTime = performance.now();

    const update = (currentTime) => {
        const progress = Math.min((currentTime - startTime) / duration, 1);
        const value = Math.floor(progress * target);

        counter.textContent = value;

        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            counter.textContent = target;
        }
    };

    requestAnimationFrame(update);
};

const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounter(entry.target);
            obs.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.4
});

counters.forEach(counter => observer.observe(counter));

/* BUSCADOR NUEVASVOCES */
const voicesSearchInput = document.querySelector("#voicesSearchInput");
const voiceCards = document.querySelectorAll(".voice-profile-card");
const voicesEmptyState = document.querySelector("#voicesEmptyState");

if (voicesSearchInput && voiceCards.length) {
    voicesSearchInput.addEventListener("input", () => {
        const query = voicesSearchInput.value.trim().toLowerCase();
        let visibleCards = 0;

        voiceCards.forEach((card) => {
            const searchableText = card.textContent.toLowerCase();
            const isVisible = searchableText.includes(query);

            card.classList.toggle("is-hidden", !isVisible);

            if (isVisible) visibleCards++;
        });

        if (voicesEmptyState) {
            voicesEmptyState.classList.toggle("is-visible", visibleCards === 0);
        }
    });
}

/* ==========================
   FAQ ACCORDION
========================== */

const faqItems = document.querySelectorAll(".faq-item");

faqItems.forEach((item) => {

    const question = item.querySelector(".faq-question");

    question.addEventListener("click", () => {

        const isOpen = item.classList.contains("is-open");

        faqItems.forEach((faq) => {
            faq.classList.remove("is-open");
        });

        if (!isOpen) {
            item.classList.add("is-open");
        }

    });

});

/* RESPONSIVE DOTS CAROUSEL INDEX EN FOCO */
const focusTrack = document.querySelector(".focus-track");
const focusCards = document.querySelectorAll(".focus-track .article-card");

const focusDots = document.querySelectorAll(
    window.innerWidth <= 767
        ? ".mobile-dots button"
        : window.innerWidth <= 1199
            ? ".tablet-dots button"
            : ".desktop-dots button"
);

const focusPrev = document.querySelector(".carousel-btn-prev");
const focusNext = document.querySelector(".carousel-btn-next");

if (focusTrack && focusCards.length && focusDots.length) {
    let currentFocusIndex = 0;

    const goToFocusCard = (index) => {

        const maxSlides = focusDots.length;

        if (index >= maxSlides) {
            currentFocusIndex = 0;
        } else if (index < 0) {
            currentFocusIndex = maxSlides - 1;
        } else {
            currentFocusIndex = index;
        }

        focusCards[currentFocusIndex].scrollIntoView({
            behavior: "smooth",
            inline: "start",
            block: "nearest"
        });

        focusDots.forEach(dot => dot.classList.remove("active"));

        focusDots[currentFocusIndex]?.classList.add("active");
    };

    focusDots.forEach((dot, index) => {
        dot.addEventListener("click", () => {
            goToFocusCard(index);
        });
    });

    if (focusNext) {
        focusNext.addEventListener("click", () => {
            goToFocusCard(currentFocusIndex + 1);
        });
    }

    if (focusPrev) {
        focusPrev.addEventListener("click", () => {
            goToFocusCard(currentFocusIndex - 1);
        });
    }

    focusTrack.addEventListener("scroll", () => {
        const gap = window.innerWidth <= 768 ? 22 : 44;
        const cardWidth = focusCards[0].offsetWidth + gap;
        const index = Math.round(focusTrack.scrollLeft / cardWidth);

        currentFocusIndex = Math.max(0, Math.min(index, focusDots.length - 1));

        focusDots.forEach(dot => dot.classList.remove("active"));

        if (focusDots[currentFocusIndex]) {
            focusDots[currentFocusIndex].classList.add("active");
        }
    });
}