(function() {
    try {
        window.openUserDetails = function(userId) {
            const user = window.usersData[userId];
            if (!user) { console.warn('Utilisateur introuvable:', userId); return; }

            document.getElementById('modalUserName').textContent     = user.name;
            document.getElementById('modalFullName').textContent     = user.name;
            document.getElementById('modalEmail').textContent        = user.email;
            document.getElementById('modalPlan').textContent         = user.plan.toUpperCase();
            document.getElementById('modalCreatedAt').textContent    = user.createdAt;
            document.getElementById('modalLiveCount').textContent    = user.totalLives;
            document.getElementById('modalCommandCount').textContent = user.totalCommands;
            document.getElementById('modalCommentCount').textContent = user.totalComments;
            document.getElementById('modalExportCount').textContent  = user.totalExports;

            const historyDiv = document.getElementById('modalMonthlyHistory');
            if (!user.monthlyUsage || user.monthlyUsage.length === 0) {
                historyDiv.innerHTML = '<p class="text-gray-500 text-center py-4">Aucune donnée mensuelle</p>';
            } else {
                historyDiv.innerHTML = user.monthlyUsage.map(item => `
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-indigo-600">Mois : ${item.month}</span>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center text-sm">
                            <div><span class="text-gray-500">🎥</span><br><span class="font-bold">${item.lives}</span></div>
                            <div><span class="text-gray-500">📦</span><br><span class="font-bold">${item.commands}</span></div>
                            <div><span class="text-gray-500">💬</span><br><span class="font-bold">${item.comments}</span></div>
                            <div><span class="text-gray-500">📤</span><br><span class="font-bold">${item.exports}</span></div>
                        </div>
                    </div>
                `).join('');
            }

            const modal = document.getElementById('userDetailsModal');
            if (modal) modal.classList.remove('hidden');
        };

        window.closeUserDetails = function() {
            const modal = document.getElementById('userDetailsModal');
            if (modal) modal.classList.add('hidden');
        };

        window.closeUserDetailsIfClickedOutside = function(event) {
            if (event.target === document.getElementById('userDetailsModal')) {
                window.closeUserDetails();
            }
        };

        window.sendMessageToUser = function() {
            const userName  = document.getElementById('modalUserName').textContent;
            const userEmail = document.getElementById('modalEmail').textContent;
            alert(`📧 Envoyer un message à ${userName} (${userEmail}) – fonctionnalité à implémenter.`);
        };

    } catch(e) {
        console.error('Erreur script users-modal:', e);
    }
})();