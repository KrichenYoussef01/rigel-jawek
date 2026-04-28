function addCodeConfirm() {
    window.livewireComponent.call('addCode').then(() => {
        setTimeout(() => {
            const errorElement = document.querySelector('#newCode-error, .text-red-500');
            if (!errorElement) {
                Swal.fire({
                    title: 'Code ajouté !',
                    text: 'Le code article a été enregistré avec succès.',
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    timer: 2500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                });
            }
        }, 50);
    });
}

function saveEditConfirm() {
    window.livewireComponent.call('saveEdit').then(() => {
        setTimeout(() => {
            const errorElement = document.querySelector('#editCode-error, .text-red-500');
            if (!errorElement) {
                Swal.fire({
                    title: 'Modifié !',
                    text: 'Le code article a été mis à jour.',
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    timer: 2500,
                    showConfirmButton: false,
                });
            }
        }, 50);
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Supprimer ce code ?',
        text: 'Cette action est irréversible !',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e8541a',
        cancelButtonColor: '#1a7bbf',
        confirmButtonText: '<i class="fas fa-trash"></i> Oui, supprimer !',
        cancelButtonText: '<i class="fas fa-times"></i> Annuler',
    }).then((result) => {
        if (result.isConfirmed) {
            window.livewireComponent.call('deleteCode', id).then(() => {
                Swal.fire({
                    title: 'Supprimé !',
                    text: 'Le code a été supprimé.',
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    timer: 2500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    iconColor: '#e8541a',
                });
            });
        }
    });
}

function confirmDeleteAll() {
    Swal.fire({
        title: 'Supprimer TOUS les codes ?',
        text: '⚠️ Cette action est irréversible !',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e8541a',
        cancelButtonColor: '#1a7bbf',
        confirmButtonText: '<i class="fas fa-trash"></i> Oui, tout supprimer !',
        cancelButtonText: '<i class="fas fa-times"></i> Annuler',
    }).then((result) => {
        if (result.isConfirmed) {
            window.livewireComponent.call('deleteAll').then(() => {
                Swal.fire({
                    title: 'Tout supprimé !',
                    text: 'Tous les codes ont été supprimés.',
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    timer: 2500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    iconColor: '#e8541a',
                });
            });
        }
    });
}