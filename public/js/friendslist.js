document.addEventListener('DOMContentLoaded', function() {
    addFriendEventListeners();
});

function addFriendEventListeners() {
    let removeFriendForms = document.querySelectorAll('.remove-friend-form');
    [].forEach.call(removeFriendForms, function(form) {
        form.addEventListener('submit', sendRemoveFriendRequest);
    });
}

function sendRemoveFriendRequest(event) {
    event.preventDefault();
    
    let form = this;
    let friendId = form.closest('.friend-item').getAttribute('data-friend-id');
    let button = form.querySelector('.btn-remove');
    let originalButtonText = button.innerHTML;

    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    sendAjaxRequest('DELETE', form.action, null, function() {
        if (this.status === 200) {
            let response = JSON.parse(this.responseText);
            if (response.success) {
                let friendItem = document.querySelector(`.friend-item[data-friend-id="${friendId}"]`);
                friendItem.remove();
            } else {
                alert(response.error);
                button.innerHTML = originalButtonText;
                button.disabled = false;
            }
        } else {
            let response = JSON.parse(this.responseText);
            alert(response.error);
            button.innerHTML = originalButtonText;
            button.disabled = false;
        }
    });
}