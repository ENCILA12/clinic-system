document.addEventListener('DOMContentLoaded', function() {
    
    // Set minimum date to today
    const dateInput = document.getElementById('aptDate');
    if(dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }

    const form = document.getElementById('publicBookingForm');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('submitBtn');
            btn.innerText = 'Submitting...';
            btn.disabled = true;

            const formData = new FormData(this);
            fetch('api/public_book.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    form.style.display = 'none';
                    document.getElementById('successState').style.display = 'block';
                } else {
                    alert('Error: ' + data.message);
                    btn.innerText = 'Submit Request';
                    btn.disabled = false;
                }
            })
            .catch(err => {
                alert('An error occurred while connecting to the server.');
                btn.innerText = 'Submit Request';
                btn.disabled = false;
            });
        });
    }
});
