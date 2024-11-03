document.addEventListener('DOMContentLoaded', () => {
    const openButtons = document.querySelectorAll('[data-dialog-open]');
    const closeButtons = document.querySelectorAll('[data-dialog-close]');

    openButtons.forEach((openButton) => {
        openButton.addEventListener('click', () => {
            const dialogId = openButton.getAttribute('data-dialog-open');
            const dialog = document.getElementById(dialogId);
            if (dialog) {
                dialog.showModal();
            }
        });
    });

    closeButtons.forEach((closeButton) => {
        closeButton.addEventListener('click', () => {
            const dialogId = closeButton.getAttribute('data-dialog-close');
            const dialog = document.getElementById(dialogId);
            if (dialog) {
                dialog.close();
            }
        });
    });

    document.querySelectorAll('dialog').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            const rect = dialog.getBoundingClientRect();
            const isInDialog = (
                rect.top <= event.clientY && event.clientY <= rect.bottom &&
                rect.left <= event.clientX && event.clientX <= rect.right
            );
            if (!isInDialog) {
                dialog.close();
            }
        });
    });
});
