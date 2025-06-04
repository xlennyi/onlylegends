const moreBtn = document.getElementById('more-button');
const moreMenu = document.getElementById('more-menu');

moreBtn.addEventListener('click', () => {
    moreMenu.classList.toggle('hidden');
});

document.addEventListener('click', function (e) {
    if (!moreBtn.contains(e.target) && !moreMenu.contains(e.target)) {
        moreMenu.classList.add('hidden');
    }
});