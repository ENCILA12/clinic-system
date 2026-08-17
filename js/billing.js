document.addEventListener('DOMContentLoaded', function() {
    
    // --- Modal Logic ---
    const modal = document.getElementById('billingModal');
    const openBtn = document.getElementById('addBillingBtn');
    const closeBtn = document.getElementById('closeBillingModalBtn');
    const cancelBtn = document.getElementById('cancelBillingModalBtn');

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
            calculateTotal(); // Reset totals
        });
    }

    if(closeBtn) closeBtn.addEventListener('click', closeModal);
    if(cancelBtn) cancelBtn.addEventListener('click', closeModal);

    if(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    }

    // --- Dynamic Items Logic ---
    const serviceSelect = document.getElementById('serviceSelect');
    const addServiceBtn = document.getElementById('addServiceBtn');
    const tbody = document.getElementById('invoiceItemsBody');

    if(addServiceBtn && serviceSelect && tbody) {
        addServiceBtn.addEventListener('click', function() {
            const serviceName = serviceSelect.value.trim();
            if(!serviceName) return;

            let defaultPrice = 0;
            const datalist = document.getElementById('serviceList');
            if (datalist) {
                const options = Array.from(datalist.options);
                const match = options.find(opt => opt.value === serviceName);
                if (match) {
                    defaultPrice = match.getAttribute('data-price') || 0;
                }
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="text" class="form-control" name="service_name[]" value="${serviceName}" required style="padding:4px 8px;">
                </td>
                <td>
                    <input type="number" class="form-control qty-input" name="qty[]" value="1" min="1" required style="padding:4px 8px;">
                </td>
                <td>
                    <input type="number" class="form-control price-input" name="price[]" value="${defaultPrice}" min="0" step="0.01" required style="padding:4px 8px;">
                </td>
                <td class="amount-cell">₱<span>${Number(defaultPrice).toFixed(2)}</span></td>
                <td style="text-align:center;">
                    <button type="button" class="remove-item-btn"><i class="fa-solid fa-trash"></i></button>
                </td>
            `;

            tbody.appendChild(tr);
            serviceSelect.value = ''; // reset dropdown
            calculateTotal();

            // Attach event listeners to new row
            const qtyInput = tr.querySelector('.qty-input');
            const priceInput = tr.querySelector('.price-input');
            const removeBtn = tr.querySelector('.remove-item-btn');

            qtyInput.addEventListener('input', calculateTotal);
            priceInput.addEventListener('input', calculateTotal);
            removeBtn.addEventListener('click', function() {
                tr.remove();
                calculateTotal();
            });
        });
    }

    // --- Calculation Logic ---
    const inputDiscount = document.getElementById('inputDiscount');
    const inputPaid = document.getElementById('inputPaid');
    
    if(inputDiscount) inputDiscount.addEventListener('input', calculateTotal);
    if(inputPaid) inputPaid.addEventListener('input', calculateTotal);

    function calculateTotal() {
        let subtotal = 0;
        
        // Calculate each row
        const rows = document.querySelectorAll('#invoiceItemsBody tr');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const amount = qty * price;
            
            row.querySelector('.amount-cell span').innerText = amount.toFixed(2);
            subtotal += amount;
        });

        const discount = parseFloat(document.getElementById('inputDiscount').value) || 0;
        const total = Math.max(0, subtotal - discount);
        const paid = parseFloat(document.getElementById('inputPaid').value) || 0;
        const balance = Math.max(0, total - paid);

        // Update displays
        document.getElementById('displaySubtotal').innerText = subtotal.toFixed(2);
        document.getElementById('displayTotal').innerText = total.toFixed(2);
        document.getElementById('displayBalance').innerText = balance.toFixed(2);

        // Update hidden inputs
        document.getElementById('inputSubtotal').value = subtotal.toFixed(2);
        document.getElementById('inputTotal').value = total.toFixed(2);
        document.getElementById('inputBalance').value = balance.toFixed(2);
    }

    // --- Form Submit ---
    const addBillingForm = document.getElementById('addBillingForm');
    if(addBillingForm) {
        addBillingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rows = document.querySelectorAll('#invoiceItemsBody tr');
            if(rows.length === 0) {
                alert("Please add at least one service to the invoice.");
                return;
            }

            const btn = document.getElementById('saveInvoiceBtn');
            btn.innerText = 'Saving...';
            btn.disabled = true;

            const formData = new FormData(this);
            fetch('api/save_billing.php', {
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
                btn.innerText = 'Save Invoice';
                btn.disabled = false;
            });
        });
    }
});

    // --- HMO Toggle Logic ---
    const paymentMethodSelect = document.getElementById('paymentMethodSelect');
    const hmoFields = document.getElementById('hmoFields');
    
    if(paymentMethodSelect && hmoFields) {
        paymentMethodSelect.addEventListener('change', function() {
            if(this.value === 'HMO') {
                hmoFields.style.display = 'block';
                // Automatically set amount paid to 0 since HMO handles it later
                const inputPaid = document.getElementById('inputPaid');
                if(inputPaid) {
                    inputPaid.value = 0;
                    inputPaid.setAttribute('readonly', 'true');
                    inputPaid.style.backgroundColor = '#f1f5f9';
                    calculateTotal();
                }
            } else {
                hmoFields.style.display = 'none';
                const inputPaid = document.getElementById('inputPaid');
                if(inputPaid) {
                    inputPaid.removeAttribute('readonly');
                    inputPaid.style.backgroundColor = '';
                }
            }
        });
    }
