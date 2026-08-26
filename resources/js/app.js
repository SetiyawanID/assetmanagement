import './bootstrap';
import { Dropdown, Tooltip } from 'bootstrap';

document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
    new Tooltip(element);
});

document.querySelectorAll('.js-qr-popup').forEach((element) => {
    element.addEventListener('click', (event) => {
        event.preventDefault();
        const popup = window.open(
            element.href,
            'assetQrCodePopup',
            'popup=yes,width=560,height=420,resizable=yes,scrollbars=no'
        );

        popup?.focus();
    });
});
