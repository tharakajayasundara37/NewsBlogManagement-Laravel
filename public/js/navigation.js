(() => {
    const nav = document.querySelector('.modern-nav');
    if (!nav) return;

    const mobileToggle = nav.querySelector('.mobile-toggle');
    const menu = nav.querySelector('.nav-menu');
    const categoryToggle = nav.querySelector('.category-toggle');
    const categoryItem = nav.querySelector('.nav-item');

    const closeCategories = () => {
        categoryItem.classList.remove('categories-open');
        categoryToggle.setAttribute('aria-expanded', 'false');
    };
    const closeMenu = () => {
        menu.classList.remove('open');
        mobileToggle.setAttribute('aria-expanded', 'false');
        mobileToggle.setAttribute('aria-label', 'Open navigation');
        closeCategories();
    };

    mobileToggle.addEventListener('click', () => {
        const open = menu.classList.toggle('open');
        mobileToggle.setAttribute('aria-expanded', String(open));
        mobileToggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
        if (!open) closeCategories();
    });

    categoryToggle.addEventListener('click', () => {
        const open = categoryItem.classList.toggle('categories-open');
        categoryToggle.setAttribute('aria-expanded', String(open));
    });

    document.addEventListener('click', (event) => {
        if (!categoryItem.contains(event.target)) closeCategories();
        if (!nav.contains(event.target)) closeMenu();
    });

    nav.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        if (categoryToggle.getAttribute('aria-expanded') === 'true') {
            closeCategories();
            categoryToggle.focus();
        } else if (menu.classList.contains('open')) {
            closeMenu();
            mobileToggle.focus();
        }
    });

    window.matchMedia('(min-width: 1101px)').addEventListener('change', closeMenu);
})();
