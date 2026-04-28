 const container = document.getElementById('particles');
    const colors = ['rgba(26,123,191,0.3)', 'rgba(47,168,232,0.2)', 'rgba(232,84,26,0.25)', 'rgba(255,122,61,0.15)'];
    for (let i = 0; i < 30; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const size = Math.random() * 4 + 1;
        p.style.cssText = `
            left: ${Math.random() * 100}%;
            width: ${size}px; height: ${size}px;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            animation-duration: ${Math.random() * 15 + 10}s;
            animation-delay: ${Math.random() * 10}s;
        `;
        container.appendChild(p);
    }