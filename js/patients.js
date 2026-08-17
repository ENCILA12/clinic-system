document.addEventListener('DOMContentLoaded', function() {
    
    // --- Modal Logic ---
    const modal = document.getElementById('patientModal');
    const openBtn = document.getElementById('addPatientBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');

    function openModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // Close on clicking outside the modal content
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // --- Auto Calculate Age Logic ---
    const bdayInput = document.getElementById('bdayInput');
    const ageInput = document.getElementById('ageInput');

    bdayInput.addEventListener('change', function() {
        if (this.value) {
            const birthDate = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            
            // If birth month hasn't occurred this year, or it's the birth month but the day hasn't occurred yet
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            ageInput.value = age > 0 ? age : 0;
        } else {
            ageInput.value = '';
        }
    });
    // --- Form Submission Logic ---
    const addPatientForm = document.getElementById('addPatientForm');
    
    addPatientForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(addPatientForm);
        
        const saveBtn = addPatientForm.querySelector('button[type="submit"]');
        const originalText = saveBtn.innerText;
        saveBtn.innerText = 'Saving...';
        saveBtn.disabled = true;

        fetch('api/save_patient.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                closeModal();
                addPatientForm.reset();
                location.reload(); // Reload to see the new patient (will implement dynamic load later)
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving.');
        })
        .finally(() => {
            saveBtn.innerText = originalText;
            saveBtn.disabled = false;
        });
    });
});
