document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.accept').forEach(button => {
        button.addEventListener('click', function() {
            const friendRequestItem = this.closest('li');
            const friendRequestId = friendRequestItem.dataset.friendRequestId;

            const spinner = document.createElement('span');
            spinner.classList.add('spinner');
            spinner.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            friendRequestItem.appendChild(spinner);

            this.style.display = 'none';
            friendRequestItem.querySelector('.decline').style.display = 'none';

            fetch(`/friendrequestnotifications/${friendRequestId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    request_status: 'accepted'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.request_status === 'accepted') {
                    friendRequestItem.remove();
                } else {
                    alert('Failed to accept the request.');
                    spinner.remove();
                    this.style.display = 'inline-block';
                    friendRequestItem.querySelector('.decline').style.display = 'inline-block';
                }
            });
        });
    });

    document.querySelectorAll('.decline').forEach(button => {
        button.addEventListener('click', function() {
            const friendRequestItem = this.closest('li');
            const friendRequestId = friendRequestItem.dataset.friendRequestId;

            const spinner = document.createElement('span');
            spinner.classList.add('spinner');
            spinner.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            friendRequestItem.appendChild(spinner);

            this.style.display = 'none';
            friendRequestItem.querySelector('.accept').style.display = 'none';

            fetch(`/friendrequestnotifications/${friendRequestId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    request_status: 'rejected'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.request_status === 'rejected') {
                    friendRequestItem.remove();
                } else {
                    alert('Failed to reject the request.');
                    spinner.remove();
                    this.style.display = 'inline-block';
                    friendRequestItem.querySelector('.accept').style.display = 'inline-block';
                }
            });
        });
    });
});