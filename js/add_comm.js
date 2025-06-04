document.querySelectorAll('.show-comment-form').forEach(button => {
    button.addEventListener('click', () => {
        const postId = button.dataset.postId;
        const form = document.getElementById('comment-form-' + postId);
        if (form.style.display === 'none') {
            form.style.display = 'block';
            button.textContent = 'Ukryj formularz';
        } else {
            form.style.display = 'none';
            button.textContent = 'Dodaj komentarz';
        }
    });
});