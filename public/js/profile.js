document.addEventListener('DOMContentLoaded', function() {
    const sendRequestForms = document.querySelectorAll('.send-request-form');
    sendRequestForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const url = this.action;
            const method = this.method;
            const button = this.querySelector('button');
            const spinner = button.querySelector('.spinner');

            button.disabled = true;
            button.innerHTML = '<span class="spinner"><i class="fas fa-spinner fa-spin"></i></span>';

            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                return response.json().then(data => {
                    return { status: response.status, body: data };
                });
            })
            .then(({ status, body }) => {
                if (status === 200 || status === 201) {
                    if (body.success && body.friendRequestNotification) {
                        button.innerHTML = '<i class="fas fa-check"></i>';
                    } else {
                        console.error('Error:', body.message || 'Unknown error');
                        button.innerHTML = '<span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Send Friend Request';
                        button.disabled = false;
                    }
                } else {
                    console.error('Error:', body.message || 'Unknown error');
                    button.innerHTML = '<span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Send Friend Request';
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                button.innerHTML = '<span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Send Friend Request';
                button.disabled = false;
            });
        });
    });

    const acceptRequestForms = document.querySelectorAll('.accept-request-form');
    acceptRequestForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const url = this.action;
            const method = this.method;
            const button = this.querySelector('button');
            const spinner = button.querySelector('.spinner');

            button.disabled = true;
            button.innerHTML = '<span class="spinner"><i class="fas fa-spinner fa-spin"></i></span>';

            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                return response.json().then(data => {
                    return { status: response.status, body: data };
                });
            })
            .then(({ status, body }) => {
                if (status === 200 || status === 201) {
                    if (body.request_status === 'accepted') {
                        button.innerHTML = '<i class="fas fa-check"></i>';
                    } else {
                        console.error('Error:', body.message || 'Unknown error');
                        button.innerHTML = '<span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Accept Friend Request';
                        button.disabled = false;
                    }
                } else {
                    console.error('Error:', body.message || 'Unknown error');
                    button.innerHTML = '<span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Accept Friend Request';
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                button.innerHTML = '<span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Accept Friend Request';
                button.disabled = false;
            });
        });
    });

    const cancelRequestForms = document.querySelectorAll('.cancel-request-form');
    cancelRequestForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const url = this.action;
            const method = this.method;
            const button = this.querySelector('button');
            const spinner = button.querySelector('.spinner');

            button.disabled = true;
            button.innerHTML = '<span class="spinner"><i class="fas fa-spinner fa-spin"></i></span>';

            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                if (response.status === 204) {
                    return { success: true };
                }
                return response.text().then(text => {
                    return text ? JSON.parse(text) : {};
                });
            })
            .then(({ success, status, body }) => {
                if (success || status === 200 || status === 201) {
                    button.innerHTML = '<i class="fas fa-check"></i>';
                } else {
                    console.error('Error:', body.message || 'Unknown error');
                    button.innerHTML = 'Cancel Friend Request';
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                button.innerHTML = 'Cancel Friend Request';
                button.disabled = false;
            });
        });
    });
});

function toggleMutualFriendsPopup() {
    const popup = document.getElementById('mutualFriendsPopup');
    if (popup) {
        popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
    }
}