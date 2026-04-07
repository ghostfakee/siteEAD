const carousel = document.querySelector('[data-carousel]');

if (carousel) {
    const slides = [...carousel.querySelectorAll('.hero__slide')];
    const dots = [...document.querySelectorAll('[data-carousel-dot]')];
    const prev = document.querySelector('[data-carousel-prev]');
    const next = document.querySelector('[data-carousel-next]');
    let current = 0;
    let timer = null;

    const render = (index) => {
        slides.forEach((slide, slideIndex) => slide.classList.toggle('is-active', slideIndex === index));
        dots.forEach((dot, dotIndex) => dot.classList.toggle('is-active', dotIndex === index));
        current = index;
    };

    const move = (direction) => render((current + direction + slides.length) % slides.length);
    const restart = () => {
        window.clearInterval(timer);
        timer = window.setInterval(() => move(1), 6000);
    };

    prev?.addEventListener('click', () => { move(-1); restart(); });
    next?.addEventListener('click', () => { move(1); restart(); });
    dots.forEach((dot, index) => dot.addEventListener('click', () => { render(index); restart(); }));
    restart();
}

const modal = document.querySelector('[data-video-modal]');
const iframe = document.querySelector('[data-modal-iframe]');
const heading = document.querySelector('[data-modal-heading]');

const closeModal = () => {
    if (!modal || !iframe) return;
    modal.hidden = true;
    iframe.src = '';
};

document.querySelectorAll('[data-modal-video]').forEach((button) => {
    button.addEventListener('click', () => {
        if (!modal || !iframe || !heading) return;
        heading.textContent = button.dataset.modalTitle || 'Video';
        iframe.src = button.dataset.modalVideo || '';
        modal.hidden = false;
    });
});

document.querySelectorAll('[data-modal-close]').forEach((button) => button.addEventListener('click', closeModal));
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeModal();
});
