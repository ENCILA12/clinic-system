document.addEventListener('DOMContentLoaded', function() {
    
    // Modal Logic
    const modal = document.getElementById('aptModal');
    const openBtn = document.getElementById('addAptBtn');
    const closeBtn = document.getElementById('closeAptModalBtn');
    const cancelBtn = document.getElementById('cancelAptModalBtn');

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    if(openBtn) {
        openBtn.addEventListener('click', function() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if(closeBtn) closeBtn.addEventListener('click', closeModal);
    if(cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    // Form Submit
    const addAptForm = document.getElementById('addAptForm');
    if(addAptForm) {
        addAptForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addAptForm);
            const btn = addAptForm.querySelector('button[type="submit"]');
            btn.innerText = 'Booking...';
            btn.disabled = true;

            fetch('api/save_appointment.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred.');
            })
            .finally(() => {
                btn.innerText = 'Book Appointment';
                btn.disabled = false;
            });
        });
    }

    // Status Change
    const statusDropdowns = document.querySelectorAll('.status-dropdown');
    statusDropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            const aptId = this.getAttribute('data-id');
            const newStatus = this.value;
            const badgeSpan = this.parentElement;

            fetch('api/update_appointment_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: aptId, status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // Update badge color
                    badgeSpan.className = 'status-badge status-' + newStatus.toLowerCase().replace(' ', '');
                } else {
                    alert('Failed to update status: ' + data.message);
                    location.reload(); // Revert back
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error updating status.');
            });
        });
    });
});

window.deleteAppointment = function(id) {
    if (confirm("Are you sure you want to delete this appointment?")) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('api/delete_appointment.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => alert('An error occurred.'));
    }
};
