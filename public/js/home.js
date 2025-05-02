document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('submit', function(event) {
        if (event.target.matches('form[id^="like-form-"]')) {
            event.preventDefault();
            handleLikeRequest(event.target);
        }
    });

    document.addEventListener('click', function(event) {
        if (event.target.matches('.share-btn')) {
            event.preventDefault();
            let shareOptions = event.target.nextElementSibling;
            shareOptions.classList.toggle('show');
        }
    });

    document.addEventListener('submit', function(event) {
        if (event.target.matches('form.share-form')) {
            event.preventDefault();
            handleShareRequest(event.target);
        }
    });
});

function handleLikeRequest(form) {
    let postId = form.querySelector('input[name="post_id"]').value;
    let likeForms = document.querySelectorAll(`form[id^="like-form-"][data-post-id="${postId}"]`);

    likeForms.forEach(function(form) {
        let likeBtn = form.querySelector('.like-btn');
        let spinner = form.querySelector('.spinner');
        likeBtn.disabled = true;
        spinner.style.display = 'inline-block';
    });

    sendLikeToggleRequest(form);
}

function sendLikeToggleRequest(form) {
    let postId = form.querySelector('input[name="post_id"]').value;

    let data = {
        post_id: postId
    };

    sendAjaxRequest('POST', form.action, data, function() {
        likeToggleHandler.call(this, postId);
    });
}

function likeToggleHandler(postId) {
    let likeForms = document.querySelectorAll(`form[id^="like-form-"][data-post-id="${postId}"]`);

    likeForms.forEach(function(form) {
        let likeBtn = form.querySelector('.like-btn');
        let likeCount = likeBtn.querySelector('span');
        let spinner = form.querySelector('.spinner');
        likeBtn.disabled = false;
        spinner.style.display = 'none';

        if (this.status != 200 && this.status != 201) {
            alert('Error toggling like');
            return;
        }

        let response = JSON.parse(this.responseText);
        if (response.liked) {
            likeBtn.classList.add('active');
            likeCount.textContent = parseInt(likeCount.textContent) + 1;
        } else {
            likeBtn.classList.remove('active');
            likeCount.textContent = parseInt(likeCount.textContent) - 1;
        }
    }, this);
}



function handleShareRequest(form) {
    let postId = form.querySelector('input[name="post_id"]').value;

    let data = {
        post_id: postId
    };

    let shareButtons = document.querySelectorAll(`.share-btn[data-post-id="${postId}"]`);
    shareButtons.forEach(function(button) {
        let spinner = button.querySelector('.spinner');
        let shareCount = button.querySelector('.share-count');
        let shareIcon = button.querySelector('.fa-share');
        button.disabled = true;

        let shareForm = button.nextElementSibling.querySelector('form');
        let submitButton = shareForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        shareIcon.style.display = 'none';
        shareCount.style.display = 'none';
        spinner.style.display = 'inline-block';
    });

    sendAjaxRequest('POST', form.action, data, function() {
        shareAddedHandler.call(this, postId);
    });
}

function shareAddedHandler(postId) {
    let shareButtons = document.querySelectorAll(`.share-btn[data-post-id="${postId}"]`);
    shareButtons.forEach(function(button) {
        let spinner = button.querySelector('.spinner');
        let shareCount = button.querySelector('.share-count');
        let shareIcon = button.querySelector('.fa-share');
        button.disabled = false;
        let shareForm = button.nextElementSibling.querySelector('form');
        let submitButton = shareForm.querySelector('button[type="submit"]');
        submitButton.disabled = false;
        spinner.style.display = 'none';
        shareIcon.style.display = 'inline-block';
        shareCount.style.display = 'inline-block';

        if (this.status != 200 && this.status != 201) {
            alert('Error sharing post');
            return;
        }

        shareCount.textContent = parseInt(shareCount.textContent) + 1;
    }, this);
}