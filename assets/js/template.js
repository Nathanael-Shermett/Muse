// Run as soon as the document is loaded.
$(document).ready(function()
{
	start_masonry();

	// Load links with a nifty animation.
	$(document).on('click', '#sidebar a, #main a', function(e)
	{
		// Only change the link behavior if the CTRL, ALT, and SHIFT keys are not pressed. This is to prevent us
		// from messing up useful browser features (such as CTRL+click to open a link in a new tab).
		//
		// Also ensure the loading icon is not visible. If it's visible, it means we're already working on a
		// navigation request, or that something else otherwise went wrong and needs to be corrected.
		if (!e.ctrlKey && !e.altKey && !e.shiftKey
			&& $('#loading_icon').css('opacity') <= 0)
		{
			setTimeout(function()
			{
				$('main').fadeTo('fast', .25);
				$('#loading_icon').fadeTo(500, 1);
			}, 1000);
		}
	});

	// Hide the loading icon on page show. This prevents the loading icon from persisting after clicking a link
	// and then pressing the browser "back" or "forward" buttons.
	$(window).on('pageshow', function()
	{
		$('main').fadeTo('fast', 1);
		$('#loading_icon').fadeTo(500, 0);
	});
});

// Initializes masonry, where applicable.
function start_masonry()
{
	// Masonry, where applicable.
	$('#masonry').masonry(
		{
			columnWidth : '.card',
			gutter : 20,
			itemSelector : '.card',
		},
	);

	setTimeout(function()
	{
		$('#masonry').masonry().layout();
	}, 200);
}