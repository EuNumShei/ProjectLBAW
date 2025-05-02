document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-accept').forEach(button => {
        button.addEventListener('click', function() {
            const notificationItem = this.closest('.notification-item');
            const notificationId = notificationItem.dataset.notificationId;
            const notificationType = notificationItem.dataset.notificationType;

            let url = '';
            if (notificationType === 'friend_request') {
                url = `/friendrequestnotifications/${notificationId}`;
            } else if (notificationType === 'group_invite') {
                url = `/groupinvitenotifications/${notificationId}`;
            }

            const spinner = document.createElement('span');
            spinner.classList.add('spinner');
            spinner.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            notificationItem.querySelector('.notification-actions').appendChild(spinner);

            this.style.display = 'none';
            notificationItem.querySelector('.btn-decline').style.display = 'none';
            notificationItem.querySelector('.btn-view-all').style.display = 'none';

            fetch(url, {
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
                    notificationItem.querySelector('.notification-actions').innerHTML = '<p><strong>Accepted</strong></p>';
                } else {
                    alert('Failed to accept the request.');
                    spinner.remove();
                    this.style.display = 'inline-block';
                    notificationItem.querySelector('.btn-decline').style.display = 'inline-block';
                    notificationItem.querySelector('.btn-view-all').style.display = 'inline-block';
                }
            });
        });
    });

    document.querySelectorAll('.btn-decline').forEach(button => {
        button.addEventListener('click', function() {
            const notificationItem = this.closest('.notification-item');
            const notificationId = notificationItem.dataset.notificationId;
            const notificationType = notificationItem.dataset.notificationType;

            let url = '';
            if (notificationType === 'friend_request') {
                url = `/friendrequestnotifications/${notificationId}`;
            } else if (notificationType === 'group_invite') {
                url = `/groupinvitenotifications/${notificationId}`;
            }

            const spinner = document.createElement('span');
            spinner.classList.add('spinner');
            spinner.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            notificationItem.querySelector('.notification-actions').appendChild(spinner);

            this.style.display = 'none';
            notificationItem.querySelector('.btn-accept').style.display = 'none';
            notificationItem.querySelector('.btn-view-all').style.display = 'none';

            fetch(url, {
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
                    notificationItem.querySelector('.notification-actions').innerHTML = '<p><strong>Rejected</strong></p>';
                } else {
                    alert('Failed to reject the request.');
                    spinner.remove();
                    this.style.display = 'inline-block';
                    notificationItem.querySelector('.btn-accept').style.display = 'inline-block';
                    notificationItem.querySelector('.btn-view-all').style.display = 'inline-block';
                }
            });
        });
    });
});