window.addEventListener('load', function () {
  const preloader = document.getElementById('jolt-preloader');
  if (preloader) {
    preloader.classList.add('fade-out');
    setTimeout(() => {
      preloader.remove();
    }, 500); // wacht tot fade-out klaar is
  }
});
