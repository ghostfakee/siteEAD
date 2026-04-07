const accordionBtns = [...document.querySelectorAll('[data-accordion-toggle]')];

accordionBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
        const key = btn.dataset.accordionToggle;
        const body = document.querySelector(`[data-accordion-body="${key}"]`);
        const isOpen = !body.hidden;

        // Close all
        accordionBtns.forEach((b) => {
            b.classList.remove('is-open');
            const bBody = document.querySelector(`[data-accordion-body="${b.dataset.accordionToggle}"]`);
            if (bBody) bBody.hidden = true;
        });

        // Open clicked if it was closed
        if (!isOpen) {
            btn.classList.add('is-open');
            body.hidden = false;
        }
    });
});
