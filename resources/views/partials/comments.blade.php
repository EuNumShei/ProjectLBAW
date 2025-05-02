<div class="comments">
    <h4>Comments</h4>
    @foreach($post->comments as $comment)
        <div class="comment" data-comment-id="{{ $comment->id }}">
            <a href="{{ route('profile', ['id' => $comment->author->id]) }}" class="comment-user-link">
                <img src="{{ asset($comment->author->profile_pic) ?? 'https://via.placeholder.com/40' }}" alt="User Image" class="comment-user-pic">
            </a>
            <div class="text">
                <div class="comment-header">
                    <a href="{{ route('profile', ['id' => $comment->author->id]) }}" class="comment-user-link">
                        <span>{{ '' . $comment->author->full_name }}</span>
                    </a>
                    <div class="comment-right-div">
                        <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                        @if(Auth::check() && Auth::id() == $comment->author->id)
                        <div class="comment-actions">
                            <button class="ellipsis-btn"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu">
                                <button class="edit-btn"><i class="fas fa-edit"></i> Edit</button>
                                <button class="delete-btn"><i class="fas fa-trash-alt"></i> Delete</button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <p class="comment-content">{{ $comment->content }}</p>
                <form class="edit-comment-form" style="display: none;" action="{{ route('comment.update', $comment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <textarea name="content" class="edit-comment-textarea">{{ $comment->content }}</textarea>
                    <button type="submit" class="edit-comment-submit-btn">Save</button>
                    <button type="button" class="edit-comment-cancel-btn">Cancel</button>
                </form>
                @php
                    $isLiked = $comment->likes->contains('user_id', Auth::id());
                @endphp
                <form class="like-form" id="like-form-{{ $comment->id }}" data-comment-id="{{ $comment->id }}" action="{{ route('userlikecomments.toggle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                    <button type="submit" class="like-btn {{ $isLiked ? 'active' : '' }}">
                        <div class="spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></div>
                        <i class="fas fa-thumbs-up"></i> <span>{{ $comment->likes->count() }}</span> Likes
                    </button>
                </form>
            </div>
        </div>
    @endforeach
    @if(Auth::check())
    <div class="comment">
        <a href="{{ route('profile', ['id' => Auth::user()->id]) }}" class="comment-user-link">
            <img src="{{ asset(Auth::user()->profile->profile_pic) ?? 'https://via.placeholder.com/40' }}" alt="User Image" class="comment-user-pic" id="user-profile-pic">
        </a>
        <div class="text">
            <form id="comment-form" action="{{ route('comment.store') }}" method="POST" class="comment-form">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <textarea name="content" class="comment-textarea" placeholder="Add a comment..." required></textarea>
                <button type="submit" class="comment-submit-btn">Post Comment</button>
            </form>
        </div>
    </div>
    @endif
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        let commentForm = document.getElementById('comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', function(event) {
                event.preventDefault();
                sendCreateCommentRequest();
            });
        }
    
        document.addEventListener('submit', function(event) {
            if (event.target.matches('form[id^="like-form-"]')) {
                event.preventDefault();
                handleCommentLikeRequest(event.target);
            }
        });
    
        document.addEventListener('click', function(event) {
            if (event.target.closest('.ellipsis-btn')) {
                event.stopPropagation();
                let dropdownMenu = event.target.closest('.ellipsis-btn').nextElementSibling;
                dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            } else if (event.target.closest('.edit-btn')) {
                event.stopPropagation();
                let commentElement = event.target.closest('.comment');
                let commentContent = commentElement.querySelector('.comment-content');
                let editForm = commentElement.querySelector('.edit-comment-form');
                commentContent.style.display = 'none';
                editForm.style.display = 'block';
            } else if (event.target.closest('.edit-comment-cancel-btn')) {
                event.stopPropagation();
                let commentElement = event.target.closest('.comment');
                let commentContent = commentElement.querySelector('.comment-content');
                let editForm = commentElement.querySelector('.edit-comment-form');
                commentContent.style.display = 'block';
                editForm.style.display = 'none';
            } else if (event.target.closest('.delete-btn')) {
                event.stopPropagation();
                let commentId = event.target.closest('.comment').dataset.commentId;
                handleDeleteCommentRequest(commentId);
            } else {
                let dropdownMenus = document.querySelectorAll('.dropdown-menu');
                dropdownMenus.forEach(function(menu) {
                    menu.style.display = 'none';
                });
            }
        });
    
        document.addEventListener('submit', function(event) {
            if (event.target.matches('.edit-comment-form')) {
                event.preventDefault();
                handleEditCommentRequest(event.target);
            }
        });
    });
    
    function sendCreateCommentRequest() {
        let form = document.getElementById('comment-form');
        let postId = form.querySelector('input[name="post_id"]').value;
        let content = form.querySelector('textarea[name="content"]').value;

        let submitBtn = form.querySelector('.comment-submit-btn');
        let spinner = document.createElement('i');
        spinner.classList.add('fas', 'fa-spinner', 'fa-spin');

        submitBtn.textContent = '';
        submitBtn.appendChild(spinner);
        submitBtn.disabled = true;

        let data = {
            post_id: postId,
            content: content,
        };
    
        sendAjaxRequest('POST', form.action, data, commentAddedHandler);
    }
    
    function commentAddedHandler() {
        if (this.status != 201) {
            alert('Error adding comment');
            let submitBtn = document.querySelector('.comment-submit-btn');
            submitBtn.textContent = 'Post Comment';
            submitBtn.disabled = false;
            return;
        }
    
        let comment = JSON.parse(this.responseText);
        let newComment = createCommentElement(comment);
    
        let commentsContainer = document.querySelector('.comments');
        commentsContainer.insertBefore(newComment, commentsContainer.querySelector('.comment-form').parentElement.parentElement);
    
        let submitBtn = document.querySelector('.comment-submit-btn');
        submitBtn.textContent = 'Post Comment';
        submitBtn.disabled = false;


        document.getElementById('comment-form').reset();
    }
    
    function createCommentElement(comment) {
        let profilePicUrl = document.getElementById('user-profile-pic').src;
    
        let newComment = document.createElement('div');
        newComment.classList.add('comment');
        newComment.dataset.commentId = comment.id;
        newComment.innerHTML = `
            <a href="/profile/${comment.author_id}" class="comment-user-link">
                <img src="${profilePicUrl ?? 'https://via.placeholder.com/40'}" alt="User Image" class="comment-user-pic">
            </a>
            <div class="text">
                <div class="comment-header">
                    <a href="/profile/${comment.author_id}" class="comment-user-link">
                        <span>${comment.author.full_name}</span>
                    </a>
                    <div class="comment-right-div">                    
                        <span class="comment-time">Just Now</span>
                        <div class="comment-actions">
                            <button class="ellipsis-btn"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu">
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="comment-content">${comment.content}</p>
                <form class="edit-comment-form" style="display: none;" action="/comment/${comment.id}" method="POST">
                    <textarea name="content" class="edit-comment-textarea">${comment.content}</textarea>
                    <button type="submit" class="edit-comment-submit-btn">Save</button>
                    <button type="button" class="edit-comment-cancel-btn">Cancel</button>
                </form>
                <form class="like-form" id="like-form-${comment.id}" data-comment-id="${comment.id}" action="/userlikecomments/toggle" method="POST">
                    <input type="hidden" name="comment_id" value="${comment.id}">
                    <button type="submit" class="like-btn">
                        <div class="spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></div>
                        <i class="fas fa-thumbs-up"></i> <span>0</span> Likes
                    </button>
                </form>
            </div>
        `;
        return newComment;
    }
    
    function handleCommentLikeRequest(form) {
        let commentId = form.querySelector('input[name="comment_id"]').value;
        let likeForms = document.querySelectorAll(`form[id^="like-form-"][data-comment-id="${commentId}"]`);
    
        likeForms.forEach(function(form) {
            let likeBtn = form.querySelector('.like-btn');
            let spinner = form.querySelector('.spinner');
            likeBtn.disabled = true;
            spinner.style.display = 'inline-block';
        });
    
        sendLikeToggleRequest(form);
    }
    
    function sendLikeToggleRequest(form) {
        let commentId = form.querySelector('input[name="comment_id"]').value;
    
        let data = {
            comment_id: commentId
        };
    
        sendAjaxRequest('POST', form.action, data, function() {
            likeToggleHandler.call(this, commentId);
        });
    }
    
    function likeToggleHandler(commentId) {
        let likeForms = document.querySelectorAll(`form[id^="like-form-"][data-comment-id="${commentId}"]`);
    
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
    
    function handleEditCommentRequest(form) {
        let commentId = form.closest('.comment').dataset.commentId;
        let content = form.querySelector('textarea[name="content"]').value;
    
        let data = {
            content: content,
        };
    
        sendAjaxRequest('PUT', form.action, data, function() {
            if (this.status != 200) {
                alert('Error editing comment');
                return;
            }
    
            let response = JSON.parse(this.responseText);
            let commentElement = document.querySelector(`.comment[data-comment-id="${commentId}"]`);
            let commentContent = commentElement.querySelector('.comment-content');
            commentContent.textContent = response.content;
    
            let editForm = commentElement.querySelector('.edit-comment-form');
            commentContent.style.display = 'block';
            editForm.style.display = 'none';
        });
    }
    
    function handleDeleteCommentRequest(commentId) {
        let commentElement = document.querySelector(`.comment[data-comment-id="${commentId}"]`);
    
        sendAjaxRequest('DELETE', `/comment/${commentId}`, {}, function() {
            if (this.status != 204) {
                alert('Error deleting comment');
                return;
            }
    
            commentElement.remove();
        });
    }
    
</script>
