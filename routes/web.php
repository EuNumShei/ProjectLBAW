<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\UserLikeController;
use App\Http\Controllers\UserLikeCommentController;
use App\Http\Controllers\UserFriendController;
use App\Http\Controllers\UserBlockedController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CommentLikeNotificationController;
use App\Http\Controllers\GroupInviteNotificationController;
use App\Http\Controllers\FriendRequestNotificationController;
use App\Http\Controllers\PostShareNotificationController;
use App\Http\Controllers\PostLikeNotificationController;
use App\Http\Controllers\PostCommentNotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;

use App\Http\Controllers\MailController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupportController;

use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::redirect('/', '/login');

Route::get('/home', [PostController::class, 'publicPosts'])->name('home');
Route::get('/post/{id}', [PostController::class, 'show'])->name('post');
Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile');


Route::get('/search', [SearchController::class, 'results'])->name('search.results');
Route::get('/search/members', [SearchController::class, 'searchMembers'])->name('search.members');
Route::get('/search/invites', [SearchController::class, 'searchInvites'])->name('search.invites');

Route::get('/for-you', [PostController::class, 'friendsAndGroupsPosts'])->name('forYou');
Route::get('/public-posts', [PostController::class, 'publicPosts'])->name('publicPosts');

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/logout', 'logout')->name('logout.get');

});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

//recover password:
Route::post('/password/recover', [MailController::class, 'send'])->name('password.recover');

Route::get('/password/recover', function () {
    return view('auth.recover');
})->name('recovery');

Route::get('/password/reset', [PasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [PasswordController::class, 'reset'])->name('password.update');



// Profile
Route::middleware(['auth','notBanned'])->group(function () {
    
    
    //Route::get('/notifications', function () {
    //    return view('pages.notifications');
    //})->name('notifications');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');



    Route::get('/friends-list', [UserFriendController::class, 'index'])->name('userfriends.index');
    Route::delete('/userfriend/{friend_id}', [UserFriendController::class, 'destroy'])->name('userfriends.destroy');

    Route::get('friendRequests', [FriendRequestNotificationController::class, 'index'])->name('friendRequests');

    Route::get('/blocked-list', [UserBlockedController::class, 'index'])->name('userblocked.index');
    //Route::post('/userblocked/{user_id}/{blocked_user_id}', [UserBlockedController::class, 'store'])->name('userblocked.store');
    Route::delete('/userblocked/{blocked_user_id}', [UserBlockedController::class, 'destroy'])->name('userblocked.destroy');




    Route::controller(ProfileController::class)->group(function () { 
        //Route::get('/profile', 'index')->name('profile.index');
        //Route::get('/profile/create', 'create')->name('profile.create');
        //Route::post('/profile', 'store')->name('profile.store');
        Route::get('/profile/{id}/edit', 'edit')->name('profile.edit');
        Route::put('/profile/{id}', 'update')->name('profile.update');
        Route::delete('/profile/{id}', 'destroy')->name('profile.destroy');
        //Route::get('/profile/{id}/view', 'getById')->name('profile.view');
        Route::put('/update-privacy', 'updatePrivacy')->name('profile.privacy');

    });

    //Group

Route::controller(GroupController::class)->group(function () {
    Route::get('/groups', 'index')->name('groups.index');
    Route::get('/groups', 'index')->name('groups.index');
    Route::get('/groups/create', 'create')->name('groups.create');
    Route::post('/groups', 'store')->name('groups.store');
    Route::get('/groups/{id}', 'show')->name('groups.show');
    Route::get('/groups/{id}/edit', 'edit')->name('groups.edit');
    Route::put('/groups/{id}', 'update')->name('groups.update');
    Route::delete('/groups/{id}', 'destroy')->name('groups.destroy');
});

    // Settings
    Route::controller(SettingsController::class)->group(function () {
        Route::get('/settings', 'index')->name('settings.index');
        Route::get('/settings/account', 'account')->name('settings.account');
        Route::get('/settings/change_password', 'changePassword')->name('settings.change_password');
        Route::get('/settings/delete_account', 'deleteAccount')->name('settings.delete_account');
        Route::get('/settings/profile_settings', 'profileSettings')->name('settings.profile_settings');
        Route::get('/settings/blocked_accounts', 'blockedAccounts')->name('settings.blocked_accounts');
    });

// Post

Route::controller(PostController::class)->group(function () {
    Route::get('/post/{id}', 'show')->name('post');
    //Route::get('/post', 'index')->name('post.index');
    Route::post('/post', 'store')->name('post.store');
    Route::get('/post/{id}/edit', 'edit')->name('post.edit');
    Route::put('/post/{id}', 'update')->name('post.update');
    Route::delete('/post/{id}', 'destroy')->name('post.destroy');
});

    // Support
    Route::controller(SupportController::class)->group(function () {
        Route::get('/support', 'index')->name('support');
        Route::get('/features', 'features')->name('features');
        Route::get('/about_us', 'about_us')->name('about_us');
        Route::get('/contact_us', 'contact_us')->name('contact_us');
    });


//change password
Route::put('/change-password', [UserController::class, 'changePassword'])->name('users.update');
Route::delete('/delete-account', [UserController::class, 'deleteAccount'])->name('users.destroy');



Route::resource('posts', PostController::class)->except(['index']);

Route::resource('comment', CommentController::class);
Route::delete('groupUsers/{group_id}', [GroupUserController::class, 'destroy'])->name('groupUsers.destroy');
Route::delete('groupUsers/{group_id}/{user_id}', [GroupUserController::class, 'removeUser'])->name('groupUsers.destroyOther');
//Route::resource('groupusers', GroupUserController::class);

    //Route::get('userlikes', [UserLikeController::class, 'index'])->name('userlikes.index');
    Route::post('userlike', [UserLikeController::class, 'store'])->name('userlike.store');
    //Route::get('userlikes/{user_id}/{post_id}', [UserLikeController::class, 'show'])->name('userlikes.show');
    //Route::put('userlikes/{user_id}/{post_id}', [UserLikeController::class, 'update'])->name('userlikes.update');
    Route::delete('userlike/{user_id}/{post_id}', [UserLikeController::class, 'destroy'])->name('userlike.destroy');
    Route::post('userlike/toggle', [UserLikeController::class, 'toggle'])->name('userlike.toggle');

    //Route::get('userlikecomments', [UserLikeCommentController::class, 'index'])->name('userlikecomments.index');
    Route::post('userlikecomments/toggle', [UserLikeCommentController::class, 'toggle'])->name('userlikecomments.toggle');
    //Route::get('userlikecomments/{user_id}/{comment_id}', [UserLikeCommentController::class, 'show'])->name('userlikecomments.show');
    //Route::put('userlikecomments/{user_id}/{comment_id}', [UserLikeCommentController::class, 'update'])->name('userlikecomments.update');
    Route::delete('userlikecomments/{user_id}/{comment_id}', [UserLikeCommentController::class, 'destroy'])->name('userlikecomments.destroy');

    Route::get('userfriends/{id}', [UserFriendController::class, 'show'])->name('userfriends.show');
    //Route::post('userfriends', [UserFriendController::class, 'store'])->name('userfriends.store');
    //Route::get('userfriends/{user_id}/{friend_id}', [UserFriendController::class, 'show'])->name('userfriends.show');
    //Route::put('userfriends/{user_id}/{friend_id}', [UserFriendController::class, 'update'])->name('userfriends.update');
    //Route::delete('userfriends/{user_id}/{friend_id}', [UserFriendController::class, 'destroy'])->name('userfriends.destroy');


    //Route::get('userblocked', [UserBlockedController::class, 'index'])->name('userblocked.index');
    Route::post('userblocked', [UserBlockedController::class, 'store'])->name('userblocked.store');
    //Route::get('userblocked/{user_id}/{blocked_user_id}', [UserBlockedController::class, 'show'])->name('userblocked.show');
    //Route::put('userblocked/{user_id}/{blocked_user_id}', [UserBlockedController::class, 'update'])->name('userblocked.update');
    //Route::delete('userblocked/{user_id}/{blocked_user_id}', [UserBlockedController::class, 'destroy'])->name('userblocked.destroy');


    Route::put('groupinvitenotifications/{notificationId}', [GroupInviteNotificationController::class, 'update'])->name('groupinvitenotifications.update');
    Route::post('groupinvitenotification', [GroupInviteNotificationController::class, 'store'])->name('groupinvitenotifications.store');


    
Route::resource('shares', ShareController::class)->only(['store','destroy']);
Route::resource('notifications', NotificationController::class)->only(['index', 'destroy']);
//Route::resource('commentlikenotifications', CommentLikeNotificationController::class);
Route::resource('friendrequestnotifications', FriendRequestNotificationController::class)->except(['create', 'destroy', 'edit']);
//Route::resource('postsharenotifications', PostShareNotificationController::class);
//Route::resource('postlikenotifications', PostLikeNotificationController::class);
//Route::resource('postcommentnotifications', PostCommentNotificationController::class);

//Route::resource('userblocked', UserBlockedController::class);

// Admin Panel Routes
Route::middleware(['auth', 'isAdmin', 'notBanned'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/create', [AdminController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{id}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [AdminController::class, 'update'])->name('admin.users.update');
});

/*Route::middleware(['banned'])->group(function () {
    Route::get('/banned', function () {
        return view('pages.banned');
    })->name('banned');
});*/

});