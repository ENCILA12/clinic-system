document.addEventListener('DOMContentLoaded', function() {
    
    // --- Modal Logic ---
    const modal = document.getElementById('inventoryModal');
    const openAddBtn = document.getElementById('addInventoryBtn');
    const updateBtns = document.querySelectorAll('.update-stock-btn');
    const closeBtn = document.getElementById('closeInvModalBtn');
    const cancelBtn = document.getElementById('cancelInvModalBtn');
    const title = document.getElementById('modalTitle');
    
    // Form Inputs
    const invId = document.getElementById('invId');
    const invName = document.getElementById('invName');
    const invStock = document.getElementById('invStock');
    const invMin = document.getElementById('invMin');
    const invExp = document.getElementById('invExp');

    function closeModal() {
        if(modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if(openAddBtn) {
        openAddBtn.addEventListener('click', function() {
            title.innerText = 'Add New Item';
            invId.value = '';
            invName.value = '';
            invStock.value = '0';
            invMin.value = '10';
            invExp.value = '';
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if(updateBtns) {
        updateBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                title.innerText = 'Update Item';
                
                invId.value = this.getAttribute('data-id');
                invName.value = this.getAttribute('data-name');
                invStock.value = this.getAttribute('data-stock');
                invMin.value = this.getAttribute('data-min');
                
                const exp = this.getAttribute('data-exp');
                invExp.value = exp ? exp : '';
                
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
    const form = document.getElementById('inventoryForm');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('saveInvBtn');
            btn.innerText = 'Saving...';
            btn.disabled = true;

            const formData = new FormData(this);
            fetch('api/save_inventory.php', {
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
                btn.innerText = 'Save Item';
                btn.disabled = false;
            });
        });
    }
    // --- Delete Logic ---
    const deleteBtns = document.querySelectorAll('.delete-stock-btn');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            if(confirm('Are you sure you want to delete ' + name + ' from the inventory? This cannot be undone.')) {
                const formData = new FormData();
                formData.append('id', id);
                
                fetch('api/delete_inventory.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert(name + ' deleted successfully.');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => alert('An error occurred.'));
            }
        });
    });
});
