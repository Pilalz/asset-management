// resources/js/pages/profileSignature.js

import SignaturePad from 'signature_pad';

// Pastikan kode ini hanya berjalan jika elemen canvas ada
if (document.getElementById('signature-pad')) {
    
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)'
    });

    const existingSignature = JSON.parse(canvas.getAttribute('data-signature'));

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        if (existingSignature) {
            signaturePad.fromDataURL(existingSignature);
        } else {
            signaturePad.clear();
        }
    }

    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    document.getElementById('clear-signature').addEventListener('click', () => {
        signaturePad.clear();
    });

    document.getElementById('signature-form').addEventListener('submit', (event) => {
        if (signaturePad.isEmpty()) {
            alert("Please provide a signature first.");
            event.preventDefault();
        } else {
            document.getElementById('signature-data').value = signaturePad.toDataURL('image/svg+xml');
        }
    });
}