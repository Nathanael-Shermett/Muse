<?php

// Non-keyed messages.
$messages = [
	'About',
	'Abstract',
	'Abstract (optional; 150 characters or less)',
	'Account Settings',
	'A Community for Thinkers',
	'Categories (2 max)',
	'Comments',
	'Content',
	'Contribute to Muse on %GitHub%.',
	'Create an Account',
	'Create Account',
	'Date Posted',
	'Delete',
	'Designed and developed by %Nathanael%.',
	'Edit',
	'Email',
	'Gravatar for %email%',
	'Icons provided by %Icons8%.',
	'Innovation',
	'Logged in',
	'Logout',
	'Member since %date%',
	'New Password',
	'New Password (again)',
	'(optional; 150 characters or less)',
	'Original Thread',
	'Password',
	'Password (again)',
	'Philosophy',
	'Please enter your current password.',
	'Please provide a thorough explanation of your thoughts.',
	'Politics',
	'Religion',
	'Science',
	'Society',
	'Strategy',
	'Submit',
	'Technology',
	'Title',
	'Update Account',
	'Username',
	'Type your edited comment here.',
	'Write your comment here.',
	'Written by %username%',
];

// This is the default locale, so all non-keyed messages should have identical keys and values.
$translator = [];
foreach ($messages as $i => $message)
{
	$translator[ $message ] = $message;
}

// Keyed messed.
$messages = [
	'if.you.want.to.change.email' => 'If you want to change it, simply click on your current profile picture (to the right) and set up your Gravatar account.',
	'to.display.your.profile' => 'To display your profile picture, Muse uses the public Gravatar associated with your email	address. Your email address is:',
];

// Return the result.
$translator = array_merge($translator, $messages);

return $translator;