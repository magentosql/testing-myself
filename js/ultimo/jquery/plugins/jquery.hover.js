/*
 * HoverBanner - v1.0.0 - 2013-05-08
 * http://www.perspektive-web.de/
 * Copyright (c) 2014 Philipp Schöfer
 * Licensed MIT
 */


(function( $ ) {
    
  $.fn.hoverBanner = function() {
       
       return this.each(function() {            
            
            var sImgSrc = $(this).find('img').attr('src');
            var $clone = $('<div class="banner-hover" ><img src="" alt="" border="0" /></div>');
                $clone.find('img').attr('src', sImgSrc);                                        
                    
                $clone.css('display', 'none');
                $clone.css('height', $(this).height());
                $clone.find('img').css('position', 'absolute');
                $clone.find('img').css('top', 'auto');
                $clone.find('img').css('bottom', '0');
                $clone.appendTo(this);
            
                $(this).mouseenter(function(e) {                    
                    $(this).find('.banner-hover').stop().fadeIn();
                });
                
                $(this).mouseleave(function(e) {
                    $(this).find('.banner-hover').stop().fadeOut();
                });
            
       });
  };
    
  
})( jQuery );




