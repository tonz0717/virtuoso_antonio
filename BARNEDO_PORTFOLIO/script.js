// Lightbox functionality example (simple version)
document.querySelectorAll('.gallery img').forEach(img => {
    img.addEventListener('click', () => {
        const lightbox = document.createElement('div');
        lightbox.classList.add('lightbox');
        const imgClone = img.cloneNode();
        lightbox.appendChild(imgClone);
        lightbox.addEventListener('click', () => {
            lightbox.remove();
        });
        document.body.appendChild(lightbox);
    });
});
