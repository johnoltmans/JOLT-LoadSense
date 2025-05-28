document.addEventListener('DOMContentLoaded', function () {
  const preloader = document.getElementById('jolt-preloader');
  if (!preloader) return;

  const mode = JOLTLoadSenseData?.mode || 'real';

  if (mode === 'fake') {
    // Fake mode: altijd 3 seconden tonen
    setTimeout(() => {
      preloader.classList.add('fade-out');
      setTimeout(() => preloader.remove(), 500);
    }, 3000);
  } else {
    // Real mode: wacht op volledige load
    window.addEventListener('load', () => {
      preloader.classList.add('fade-out');
      setTimeout(() => preloader.remove(), 500);
    });
  }
});
