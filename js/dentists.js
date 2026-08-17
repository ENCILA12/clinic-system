document.addEventListener('DOMContentLoaded', function() {
    
    // --- Modal Logic ---
    const modal = document.getElementById('dentistModal');
    const openAddBtn = document.getElementById('addDentistBtn');
    const updateBtns = document.querySelectorAll('.edit-dentist-btn');
    const closeBtn = document.getElementById('closeDentistModalBtn');
    const cancelBtn = document.getElementById('cancelDentistModalBtn');
    const title = document.getElementById('modalTitle');
    
    // Form Inputs
    const dentistId = document.getElementById('dentistId');
    const dentistName = document.getElementById('dentistName');
    const dentistSpec = document.getElementById('dentistSpec');
    const dentistPrc = document.getElementById('dentistPrc');
    const dentistSched = document.getElementById('dentistSched');
    const dentistFee = document.getElementById('dentistFee');
    const dentistAvail = document.getElementById('dentistAvail');

    function closeModal() {
        if(modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if(openAddBtn) {
        openAddBtn.addEventListener('click', function() {
            title.innerText = 'Add New Dentist';
            dentistId.value = '';
            dentistName.value = '';
            dentistSpec.value = '';
            dentistPrc.value = '';
            dentistSched.value = '';
            dentistFee.value = '500.00';
            dentistAvail.value = '1';
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if(updateBtns) {
        updateBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                title.innerText = 'Edit Dentist';
                
                dentistId.value = this.getAttribute('data-id');
                dentistName.value = this.getAttribute('data-name');
                dentistSpec.value = this.getAttribute('data-spec');
                dentistPrc.value = this.getAttribute('data-prc');
                dentistSched.value = this.getAttribute('data-sched');
                dentistFee.value = this.getAttribute('data-fee');
                dentistAvail.value = this.getAttribute('data-avail');
                
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });
    }

    if(closeBtn) closeBtn.addEventListener('click', closeModal);
    if(cancelBtn) cancelBtn.addEventListener('click', closeModal);

    if(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    }

    // --- Form Submit ---
    const form = document.getElementById('dentistForm');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('saveDentistBtn');
            btn.innerText = 'Saving...';
            btn.disabled = true;

            const formData = new FormData(this);
            fetch('api/save_dentist.php', {
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
            .catch(err => alert('An error occurred.'))
            .finally(() => {
                btn.innerText = 'Save Dentist';
                btn.disabled = false;
            });
        });
    }
});
