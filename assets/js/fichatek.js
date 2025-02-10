document.addEventListener('DOMContentLoaded', () => {
    const openers = document.querySelectorAll('.opener');

    openers.forEach(opener => {
        opener.addEventListener('click', () => {
            const nextPanel = opener.nextElementSibling;
            while (nextPanel && nextPanel.classList.contains('hidden-panel')) {
                nextPanel.classList.toggle('visible');
                nextPanel = nextPanel.nextElementSibling;
            }
        });
    });
});