<?php

// Non-keyed messages.
$messages = [
	'About',
	'Account Settings',
	'A Community for Thinkers',
	'Comments',
	'Contribute to Muse on {GitHub}!',
	'Create an Account',
	'Create Account',
	'Date Posted',
	'Delete',
	'Designed and developed by {Nathanael}',
	'Edit',
	'Gravatar for {email}',
	'Icons provided by {Icons8}',
	'Innovation',
	'Logged in',
	'Logout',
	'Original Thread',
	'Philosophy',
	'Politics',
	'Religion',
	'Science',
	'Society',
	'Strategy',
	'Submit',
	'Technology',
	'Update Account',
	'Written by {username}',
];

// This is the default locale, so all non-keyed messages should have identical keys and values.
$translator = [];
foreach ($messages as $i => $message)
{
	$translator[ $message ] = $message;
}

// Keyed messages.
$messages = [

	// Flash messages.
	'comment.delete.already_deleted' => 'The comment you are trying to delete has actually already been deleted. No further action is necessary on your part.',
	'comment.delete.csrf_invalid' => 'An unauthorized attempt was made to delete a comment, but we intercepted it. You are most likely receiving this message because you clicked a link you shouldn\'t have. If you believe you are receiving this message in error, please try again.',
	'comment.delete.does_not_exist' => 'The comment you are attempting to delete does not exist.',
	'comment.delete.must_be_logged_in' => 'You must be logged in to delete a comment.',
	'comment.delete.not_authorized' => 'You are not authorized to delete this comment.',
	'comment.delete.only_administrators_can_delete_other_administrators' => 'Only administrators are allowed to delete other administrators\' comments.',
	'comment.delete.orphaned' => 'The comment you are attempting to delete does not seem to correspond with an actual post. Therefore, it cannot be deleted.',
	'comment.delete.post_deleted' => 'The comment you are trying to delete belongs to a post that has been deleted, and as such the comment cannot be removed.',
	'comment.delete.success' => 'The comment has been deleted.',

	'comment.edit.already_deleted' => 'The comment you are trying to edit has been deleted and can no longer be changed.',
	'comment.edit.does_not_exist' => 'The comment you are attempting to edit does not exist.',
	'comment.edit.must_be_logged_in' => 'You must be logged in to edit a comment.',
	'comment.edit.not_authorized' => 'You are not authorized to edit this comment.',
	'comment.edit.only_administrators_can_edit_other_administrators' => 'Only administrators are allowed to edit other administrators\' comments.',
	'comment.edit.orphaned' => 'The comment you are attempting to edit does not seem to correspond with an actual post. Therefore, it cannot be edited.',
	'comment.edit.placeholder' => 'Type your edited comment here.',
	'comment.edit.post_deleted' => 'The comment you are trying to edit belongs to a post that has been deleted, and as such the comment cannot be changed.',

	'comment.new.constraint.not_blank' => 'Your comment cannot be blank.',
	'comment.new.constraint.length.min' => 'Your comment must be at least { limit } characters long.',

	'comment.new.placeholder' => 'Write your comment here.',
	'comment.new.success' => 'Your comment has been posted.',

	'post.delete.csrf_invalid' => 'An unauthorized attempt was made to delete this post, but we intercepted it. You are most likely receiving this message because you clicked a link you shouldn\'t have. If you believe you are receiving this message in error, please try again.',
	'post.delete.must_be_logged_in' => 'You must be logged in to delete a post.',
	'post.delete.not_authorized' => 'You are not authorized to delete this post.',
	'post.delete.only_administrators_can_delete_other_administrators' => 'Only administrators are allowed to delete other administrators\' posts.',
	'post.delete.success' => 'The post has been deleted.',

	'post.edit.must_be_logged_in' => 'You must be logged in to edit a post.',
	'post.edit.not_authorized' => 'You are not authorized to edit this post.',
	'post.edit.only_administrators_can_edit_other_administrators' => 'Only administrators are allowed to edit other administrators\' posts.',
	'post.edit.success' => 'The post has been edited successfully.',

	'post.new.abstract.constraint.max' => 'Your abstract must be { limit } characters or less.',
	'post.new.abstract.constraint.min' => 'Abstracts are optional. However, if you wish to provide one, please make it more thorough.',
	'post.new.abstract.placeholder' => 'Abstract (optional; 150 characters or less)',
	'post.new.categories.constraint.max' => 'You may not select more than two categories.',
	'post.new.categories.constraint.min' => 'Please select at least one category (but not more than two).',
	'post.new.categories.placeholder' => 'Categories (2 max)',
	'post.new.content.constraint.min' => 'Your post must be at least { limit } characters long.',
	'post.new.content.constraint.not_blank' => 'Please provide a post body.',
	'post.new.content.placeholder' => 'Please provide a thorough explanation of your thoughts.',
	'post.new.title.constraint.length.max' => 'Your post\'s title cannot be longer than { limit } characters.',
	'post.new.title.constraint.length.min' => 'Your post\'s title must be at least { limit } characters long.',
	'post.new.title.constraint.not_blank' => 'Please enter a title.',
	'post.new.title.placeholder' => 'Title!',
	'post.new.must_be_logged_in' => 'You must be logged in to write posts on Muse. If you do not have an account with us, we suggest creating one. It is a one-time process and it is very easy.',

	'post.view.already_deleted' => 'The post you are trying to view has been deleted and can no longer be viewed.',
	'post.view.does_not_exist' => 'The post you are trying to view does not exist.',

	'user.edit.current_password.constraint.user_password' => 'The password you entered is incorrect.',
	'user.edit.email.constraint.email' => 'Please enter a valid email address.',
	'user.edit.email.constraint.max' => 'Your email address cannot be longer than { limit } characters.',
	'user.edit.email.constraint.min' => 'Your email address cannot be shorter than { limit } characters long.',
	'user.edit.password.constraint.invalid' => 'The provided passwords did not match.',
	'user.edit.password.constraint.length.max' => 'Your password cannot be longer than { limit } characters.',
	'user.edit.password.constraint.length.min' => 'For security reasons, your password must be at least { limit } characters long.',
	'user.edit.username.constraint.length.max' => 'Your username cannot be longer than { limit } characters.',
	'user.edit.username.constraint.length.min' => 'Your username must be at least { limit } characters long.',

	'user.edit.access_level.administrator' => '1. Administrator',
	'user.edit.access_level.banned' => '4. Banned',
	'user.edit.access_level.invalid' => 'The access level you selected is invalid.',
	'user.edit.access_level.moderator' => '2. Moderator',
	'user.edit.access_level.select' => 'Select an Access Level:',
	'user.edit.access_level.updated' => '{username}\'s access level has been updated.',
	'user.edit.access_level.user' => '3. User (default)',
	'user.edit.email_taken' => 'The email you provided belongs to another user. Your email has not been changed.',
	'user.edit.email_updated' => 'Your email has been updated.',
	'user.edit.must_be_logged_in' => 'You must be logged in to access the "edit profile" page.',
	'user.edit.new_password' => 'New Password',
	'user.edit.new_password_again' => 'New Password (again)',
	'user.edit.not_authorized_to_add_administrators' => 'You are not authorized to add new administrators.',
	'user.edit.not_authorized_to_add_moderators' => 'You are not authorized to add new moderators.',
	'user.edit.no_changes' => 'You did not provide any new data. Therefore, no changes have been made.',
	'user.edit.only_administrators_can_edit_other_administrators' => 'Only site administrators can edit other administrators and moderators.',
	'user.edit.only_administrators_can_edit_other_users' => 'Only site administrators and moderators can edit other users.',
	'user.edit.password_placeholder' => 'Please enter your current password.',
	'user.edit.password_updated' => 'Your password has been updated.',
	'user.edit.username_taken' => 'The username you provided belongs to another user. Your username has not been changed.',
	'user.edit.username_updated' => 'Your username has been updated.',

	'user.login.already_logged_in' => 'Just a heads up—you are currently logged in as {username}. If you are trying to switch accounts and log in as someone else, you can still do so below. Otherwise, no further action is necessary.',
	'user.login.logout_success' => 'You have been successfully logged out.',
	'user.login.invalid_credentials' => 'The username and password you entered did not match any existing accounts.',

	'user.register.email.constraint.email' => 'Please enter a valid email address.',
	'user.register.email.constraint.length.max' => 'Your email address cannot be longer than { limit } characters.',
	'user.register.email.constraint.length.min' => 'Your email address must be at least { limit } characters long.',
	'user.register.email.constraint.not_blank' => 'Please enter an email address.',
	'user.register.password.constraint.invalid' => 'The provided passwords did not match.',
	'user.register.password.constraint.length.max' => 'Your password cannot be longer than { limit } characters.',
	'user.register.password.constraint.length.min' => 'For security reasons, your password must be at least { limit } characters long.',
	'user.register.password.constraint.not_blank' => 'Please enter a password.',
	'user.register.password.placeholder' => 'Password',
	'user.register.password.placeholder_again' => 'Password (again)',
	'user.register.username.constraint.length.max' => 'Your username cannot be longer than { limit } characters.',
	'user.register.username.constraint.length.min' => 'Your username must be at least { limit } characters long.',
	'user.register.username.constraint.not_blank' => 'Please enter a username.',
	'user.register.already_logged_in' => 'You are currently logged in. In order to make a new account, please log out first.',
	'user.register.does_not_exist' => 'This user does not exist.',

	// Miscellaneous messages.
	'profile.member_since' => 'Member since {date}',
	'profile.if_you_want_to_change_email' => 'If you want to change it, simply click on your current profile picture (as shown) and set up your Gravatar account.',
	'profile.to_display_your_profile' => 'To display your profile picture, Muse uses the public Gravatar associated with your email	address. Your email address is:',
];

// Return the result.
$translator = array_merge($translator, $messages);

return $translator;