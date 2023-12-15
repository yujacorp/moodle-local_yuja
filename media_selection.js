(function() {

	function loadCSS(url)
	{
		var css = document.createElement("link");
		css.setAttribute("rel", "stylesheet");
		css.setAttribute("type", "text/css");
		css.setAttribute("href", url);
		(document.body || document.head || document.documentElement).appendChild(css);
	}
	
    loadCSS(location.origin + "/local/yuja/media_selection.css");
    
    require(['jquery'], function($) {

        var closeButton = $('<a>').click(function(){
            $(".yuja-overlay").removeClass('yuja-overlay-visible');
            $("#yujaVideoChooserIFrame").attr('src',"");
        });

        if ($('.yuja-overlay') == null || $('.yuja-overlay').length == 0) {
            $(document.body || document.head || document.documentElement).append(
                "<div class='yuja-overlay yuja-overlay-visible'>"
                + "<div class='yuja-overlay-inner'>"
                + "</div></div>");

            $('.yuja-overlay-inner').append(closeButton).append("<iframe id='yujaVideoChooserIFrame' width='800' height='600'></iframe>");

        } else {
            $(".yuja-overlay").addClass('yuja-overlay-visible');
        }

    });
	
})();