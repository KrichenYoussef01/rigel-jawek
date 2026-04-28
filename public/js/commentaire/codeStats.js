let pieChart = null, barChart = null;

function updateCharts(labels, data) {
    if (!labels || !labels.length) return;

    const pieCanvas = document.getElementById('pieChart');
    const barCanvas = document.getElementById('barChart');

    if (!pieCanvas || !barCanvas) {
        console.warn('❌ Canvas introuvable');
        return;
    }

    if (pieChart) { pieChart.destroy(); pieChart = null; }
    if (barChart) { barChart.destroy(); barChart = null; }

    const colors = [
        '#6366f1','#8b5cf6','#a855f7','#d946ef','#ec4899',
        '#f43f5e','#ef4444','#f97316','#f59e0b','#eab308',
        '#84cc16','#10b981','#14b8a6','#06b6d4','#0ea5e9','#3b82f6'
    ];

    pieChart = new Chart(pieCanvas.getContext('2d'), {
        type: 'pie',
        data: {
            labels,
            datasets: [{ data, backgroundColor: colors, borderWidth: 0 }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'right' } }
        }
    });

    barChart = new Chart(barCanvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Commandes',
                data,
                backgroundColor: '#6366f1',
                borderRadius: 8,
                barPercentage: 0.7
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    console.log('✅ Charts rendus', { labels, data });
}

// ✅ Lit uniquement #code-stats-table
function chartsFromTable() {
    const rows = document.querySelectorAll('#code-stats-table tbody tr');
    if (!rows.length) return;

    const labels = [], data = [];
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 2) {
            labels.push(cells[0].textContent.trim());
            data.push(parseInt(cells[1].textContent.trim()) || 0);
        }
    });

    console.log('📊 Table lue →', { labels, data });
    if (labels.length) updateCharts(labels, data);
}

document.addEventListener('livewire:init', () => {

    // Init au chargement
    setTimeout(chartsFromTable, 200);

    // Après chaque update du composant
    Livewire.hook('commit', ({ component, succeed }) => {
        succeed(() => {
            if (component.name === 'util.code-stats') {
                setTimeout(chartsFromTable, 100);
            }
        });
    });

    // Bouton Actualiser
    window.addEventListener('force-stats-refresh', () => {
        setTimeout(chartsFromTable, 100);
    });
});