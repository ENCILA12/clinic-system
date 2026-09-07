document.addEventListener('DOMContentLoaded', function() {
    
    // Modal Logic
    const modal = document.getElementById('treatmentModal');
    const openBtn = document.getElementById('addTreatmentBtn');
    const closeBtn = document.getElementById('closeTreatmentModalBtn');
    const cancelBtn = document.getElementById('cancelTreatmentModalBtn');

    function closeModal() {
        if(modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if(openBtn) {
        openBtn.addEventListener('click', function() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if(closeBtn) closeBtn.addEventListener('click', closeModal);
    if(cancelBtn) cancelBtn.addEventListener('click', closeModal);

    if(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    }

    // Form Submit
    const addTreatmentForm = document.getElementById('addTreatmentForm');
    if(addTreatmentForm) {
        addTreatmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addTreatmentForm);
            const btn = addTreatmentForm.querySelector('button[type="submit"]');
            btn.innerText = 'Saving...';
            btn.disabled = true;

            fetch('api/save_treatment.php', {
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
                btn.innerText = 'Save Treatment Record';
                btn.disabled = false;
            });
        });
    }
});

    // --- Materials Used Logic ---
    const materialSelect = document.getElementById('materialSelect');
    const materialQty = document.getElementById('materialQty');
    const addMaterialBtn = document.getElementById('addMaterialBtn');
    const materialsTableBody = document.getElementById('materialsTableBody');

    if(addMaterialBtn && materialSelect && materialsTableBody) {
        addMaterialBtn.addEventListener('click', function() {
            const selectedOpt = materialSelect.options[materialSelect.selectedIndex];
            if(!selectedOpt.value) return;
            
            const invId = selectedOpt.value;
            const invName = selectedOpt.getAttribute('data-name');
            const qty = parseInt(materialQty.value) || 1;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="hidden" name="material_id[]" value="${invId}">
                    <input type="hidden" name="material_name[]" value="${invName}">
                    ${invName}
                </td>
                <td>
                    <input type="number" name="material_qty[]" class="form-control" value="${qty}" min="1" style="padding:4px;" readonly>
                </td>
                <td style="text-align:center;">
                    <button type="button" class="remove-material-btn" style="color:var(--danger); background:none; border:none; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
                </td>
            `;

            materialsTableBody.appendChild(tr);
            materialSelect.value = '';
            materialQty.value = '';

            tr.querySelector('.remove-material-btn').addEventListener('click', function() {
                tr.remove();
            });
        });
    }
