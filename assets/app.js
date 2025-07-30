import './bootstrap.js';

// Import direct de tous les fichiers CSS dans l'ordre (PAS app.css)


// Import de app.css EN DERNIER pour les surcharges

import './styles/base/variables.css';
// import './styles/base/reset.css';
// import './styles/base/layout.css';
import './styles/components/buttons.css';
// import './styles/components/forms.css';
// import './styles/components/tables.css';
// import './styles/components/modals.css';
// import './styles/components/navigation.css';
// import './styles/pages/dashboard.css';
// import './styles/pages/users.css';
// import './styles/pages/children.css';
import './styles/pages/calendar.css';
// import './styles/pages/planning.css';
import './styles/pages/auth.css';
import './styles/app.css';

console.log('Page chargée avec AssetMapper');

// Désactiver le cache de Turbo si nécessaire
document.addEventListener('turbo:load', () => {
    console.log('Page chargée avec Turbo');
});


document.addEventListener('turbo:load', function() {
    document.querySelectorAll('.isopen-switch').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const id = this.dataset.id;
            const label = document.getElementById('isopen-label-' + id);
            if (this.checked) {
                label.textContent = 'Ouvert';
                label.classList.remove('closed');
                label.classList.add('open');
            } else {
                label.textContent = 'Fermé';
                label.classList.remove('open');
                label.classList.add('closed');
            }
            const calendarId = this.dataset.id;
            const isOpen = this.checked ? 1 : 0;

            fetch('/calendar/toggle-isopen/' + calendarId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token("toggle-isopen") }}'
                },
                body: JSON.stringify({ isopen: isOpen })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Erreur lors de la mise à jour');
                    this.checked = !this.checked; // revert
                }
            })
            .catch(() => {
                alert('Erreur lors de la mise à jour');
                this.checked = !this.checked; // revert
            });
        });
    });
});