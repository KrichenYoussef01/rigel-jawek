let seconds = 30;
    const countdownEl = document.getElementById('countdown');
    const progressEl  = document.getElementById('progress');

    const interval = setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;
        progressEl.style.width  = (seconds / 30 * 100) + '%';
        if (seconds <= 0) { clearInterval(interval); window.location.reload(); }
    }, 1000);