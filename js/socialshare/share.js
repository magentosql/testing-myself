function sharePageToFacebook(evt, url) {
        
        if(url == null || url == "") {
            url = location.href;
        }        
        
	window.open(                
		'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(url), 
		'facebook-share-dialog', 
		'width=626,height=436'
	);
	
	if (evt) sharePreventEvent(evt);
}

function sharePageToTwitter(evt, url) {
        
        if(url == null || url == "") {
            url = location.href;
        }
    
	window.open(
		'https://twitter.com/share?url='+encodeURIComponent(url), 
		'twitter-share-dialog', 
		'width=626,height=285'
	);
	
	if (evt) sharePreventEvent(evt);
}

function sharePageToGooglePlus(evt, url) {
        
        if(url == null || url == "") {
            url = location.href;
        }
    
	window.open(
		'https://plus.google.com/share?url='+encodeURIComponent(url), 
		'google-plus-share-dialog', 
		'width=626,height=436'
	);
	
	if (evt) sharePreventEvent(evt);
}

function sharePageToPinterest(evt, url, image) {
	var featured_image = image;
	
	if (null == featured_image) {
		jQuery(function ($) { 
			$(".featured-image img").each(function () {
				featured_image = $(this).attr("src");
			});
		});
	}
	               
	if (null == featured_image) {
		console.log("No featured image to pin");
		return;
	}
        
        if(url == null || url == "") {
            url = location.href;
        }
	
	window.open(
		'http://pinterest.com/pin/create/button/?url='+encodeURIComponent(url)
		+'&media='+encodeURIComponent(featured_image), 
		'pinterest-share-dialog', 
		'width=750,height=316'
	);
	
	if (evt) sharePreventEvent(evt);
}

function sharePreventEvent(evt) {
	try {
		evt.preventDefault();
	} catch (e) {}
}
