(function(){
    // Haal instellingen op vanuit localized object
    var mode = JOLTLoadSenseData.mode || 'real';

    function hidePreloader() {
        var preloader = document.getElementById('jolt-preloader');
        if(preloader) {
            preloader.style.display = 'none';
        }
    }

    if(mode === 'fake') {
        // Fake mode: altijd 3 seconden tonen
        window.addEventListener('load', function(){
            setTimeout(hidePreloader, 3000);
        });
    } else {
        // Real mode: wacht tot volledige load inclusief fonts, images, scripts
        window.addEventListener('load', function(){
            hidePreloader();
        });
    }
})();
