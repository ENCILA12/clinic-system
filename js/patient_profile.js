document.addEventListener('DOMContentLoaded', function() {
    
    // --- Edit Modal Logic ---
    const modal = document.getElementById('editModal');
    const openBtn = document.getElementById('editPatientBtn');
    const closeBtn = document.getElementById('closeEditModalBtn');
    const cancelBtn = document.getElementById('cancelEditModalBtn');

    function openModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        
        // Remove action=edit from URL if it exists, without reloading page
        const url = new URL(window.location);
        url.searchParams.delete('action');
        window.history.pushState({}, '', url);
    }

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    // Auto-calculate age for edit form
    const editBdayInput = document.getElementById('editBdayInput');
    const editAgeInput = document.getElementById('editAgeInput');
    
    if(editBdayInput) {
        editBdayInput.addEventListener('change', function() {
            if (this.value) {
                const birthDate = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
                editAgeInput.value = age > 0 ? age : 0;
            } else {
                editAgeInput.value = '';
            }
        });
    }

    // --- Form Submission Logic ---
    const editPatientForm = document.getElementById('editPatientForm');
    
    if(editPatientForm) {
        editPatientForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(editPatientForm);
            const saveBtn = editPatientForm.querySelector('button[type="submit"]');
            const originalText = saveBtn.innerText;
            
            saveBtn.innerText = 'Updating...';
            saveBtn.disabled = true;

            fetch('api/update_patient.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert(data.message);
                    closeModal();
                    location.reload(); 
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating.');
            })
            .finally(() => {
                saveBtn.innerText = originalText;
                saveBtn.disabled = false;
            });
        });
    }
    // --- Dental Chart Logic ---
    const teeth = document.querySelectorAll('.tooth');
    const toothModal = document.getElementById('toothModal');
    const closeToothBtn = document.getElementById('closeToothModalBtn');
    const displayNum = document.getElementById('modalToothNumberDisplay');
    const inputNum = document.getElementById('modalToothNumberInput');
    const statusSelect = document.getElementById('modalToothStatus');
    const historyList = document.getElementById('toothHistoryList');

    function closeToothModal() {
        if(toothModal) {
            toothModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if(closeToothBtn) closeToothBtn.addEventListener('click', closeToothModal);
    if(toothModal) {
        toothModal.addEventListener('click', function(e) {
            if (e.target === toothModal) closeToothModal();
        });
    }

    if(teeth.length > 0) {
        teeth.forEach(t => {
            t.addEventListener('click', function() {
                const num = this.getAttribute('data-tooth');
                const currentTitle = this.getAttribute('title') || '';
                const statusMatch = currentTitle.match(/Tooth \d+: (.+)/);
                const currentStatus = statusMatch ? statusMatch[1] : 'Healthy';

                if(displayNum) displayNum.innerText = num;
                if(inputNum) inputNum.value = num;
                if(statusSelect) statusSelect.value = currentStatus;
                
                // Open Modal
                if(toothModal) {
                    toothModal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }

                // Fetch History
                if(historyList) {
                    historyList.innerHTML = '<div style="padding:12px; text-align:center; color:gray;">Loading history...</div>';
                    
                    const patientIdInput = document.querySelector('#addToothRecordForm input[name="patient_id"]');
                    const patientId = patientIdInput ? patientIdInput.value : '';
                    
                    fetch('api/get_tooth_history.php?patient_id=' + patientId + '&tooth_number=' + num)
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            if(data.data.length > 0) {
                                historyList.innerHTML = '';
                                data.data.forEach(item => {
                                    let dateObj = new Date(item.created_at);
                                    let dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                                    
                                    let html = '<div class="history-item">';
                                    html += '<div class="history-date">' + dateStr + '</div>';
                                    html += '<div class="history-title">Status: ' + item.status + '</div>';
                                    if(item.treatment) html += '<div class="history-desc">Treatment: ' + item.treatment + '</div>';
                                    if(item.diagnosis) html += '<div class="history-desc">Diagnosis: ' + item.diagnosis + '</div>';
                                    if(item.dentist_name) html += '<div class="history-desc" style="font-size:12px; margin-top:8px;"><i class="fa-solid fa-user-doctor"></i> ' + item.dentist_name + '</div>';
                                    html += '</div>';
                                    historyList.innerHTML += html;
                                });
                            } else {
                                historyList.innerHTML = '<div style="padding:12px; text-align:center; color:gray;">No previous records for this tooth.</div>';
                            }
                        } else {
                            historyList.innerHTML = '<div style="padding:12px; text-align:center; color:red;">Failed to load history.</div>';
                        }
                    })
                    .catch(err => {
                        historyList.innerHTML = '<div style="padding:12px; text-align:center; color:red;">Error connecting to server.</div>';
                    });
                }
            });
        });
    }

    // Submit New Tooth Record
    const addToothForm = document.getElementById('addToothRecordForm');
    if(addToothForm) {
        addToothForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.innerText = 'Saving...';
            btn.disabled = true;

            const formData = new FormData(this);
            fetch('api/save_tooth_record.php', {
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
                btn.innerText = 'Save Tooth Record';
                btn.disabled = false;
            });
        });
    }
});

    // --- Upload Attachment Form ---
    const uploadForm = document.getElementById('uploadAttachmentForm');
    if(uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('uploadBtn');
            btn.innerText = 'Uploading...';
            btn.disabled = true;

            const formData = new FormData(this);
            fetch('api/upload_attachment.php', {
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
            .catch(err => alert('An error occurred during upload.'))
            .finally(() => {
                btn.innerText = 'Upload';
                btn.disabled = false;
            });
        });
    }
