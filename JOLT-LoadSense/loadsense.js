(function () {
    try {
        var mode = JOLTLoadSenseData.mode || 'real';
        var animation = JOLTLoadSenseData.animation || 'spinner';

        function hidePreloader() {
            var preloader = document.getElementById('jolt-preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        }

        function showPreloader() {
            var preloader = document.getElementById('jolt-preloader');
            if (preloader) {
                preloader.style.display = 'flex';
            }
        }

        if (mode === 'fake') {
            window.addEventListener('load', function () {
                showPreloader();
                setTimeout(hidePreloader, 3000);
            });
        } else {
            window.addEventListener('load', function () {
                hidePreloader();
            });
        }
    } catch (error) {
        console.error('Error in loadsense.js:', error);
    }
})();