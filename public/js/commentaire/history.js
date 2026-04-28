
function confirmDeleteSession(id) {
    Swal.fire({
        title: 'Supprimer cette session ?',
        text: 'Tous les paniers associés seront supprimés. Cette action est irréversible !',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e8541a',
        cancelButtonColor: '#1a7bbf',
        confirmButtonText: 'Oui, supprimer !',
        cancelButtonText: 'Annuler',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/session/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Supprimé !', 'La session a été supprimée.', 'success');
                    // Recharger la page ou rafraîchir le composant Livewire
                    location.reload();
                } else {
                    Swal.fire('Erreur', data.message || 'Impossible de supprimer', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Erreur', err.message, 'error');
            });
        }
    });
}
function toggleAll(master) {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
            updateSelectedCount();
        }

function updateSelectedCount() {
            const checked = document.querySelectorAll('.row-check:checked');
            const btn     = document.getElementById('btn-delete-selected');
            const count   = document.getElementById('selected-count');
            count.textContent = checked.length;
            btn.style.display = checked.length > 0 ? 'inline-flex' : 'none';
            const all = document.querySelectorAll('.row-check');
            document.getElementById('check-all').checked = all.length > 0 && checked.length === all.length;
        }

        function confirmDeleteSession(id) {
            Swal.fire({
                title: 'Supprimer cette session ?',
                text: 'Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor:  '#6b7280',
                confirmButtonText:  'Supprimer',
                cancelButtonText:   'Annuler',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/session/${id}`, {
                        method:  'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Supprimé !', 'La session a été supprimée.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Erreur', data.message || 'Impossible de supprimer.', 'error');
                        }
                    });
                }
            });
        }

        function confirmDeleteSelected() {
            const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
            if (!ids.length) return;

            Swal.fire({
                title: `Supprimer ${ids.length} session(s) ?`,
                text: 'Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor:  '#6b7280',
                confirmButtonText:  `Supprimer (${ids.length})`,
                cancelButtonText:   'Annuler',
            }).then(result => {
                if (!result.isConfirmed) return;
                Promise.all(ids.map(id =>
                    fetch(`/session/${id}`, {
                        method:  'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    }).then(r => r.json())
                )).then(() => {
                    Swal.fire('Supprimé !', `${ids.length} session(s) supprimée(s).`, 'success')
                        .then(() => location.reload());
                });
            });
        }