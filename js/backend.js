(function( $ ) {
	"use strict";


	$(".proVersion").click(function(e){
		e.preventDefault();

		$("#WholeProdDynWooCommerceModal").slideDown();
	});
		$("#WholeProdDynWooCommerceModal .close").click(function(e){
			e.preventDefault();
			$("#WholeProdDynWooCommerceModal").fadeOut();
		});

		var modal = document.getElementById('WholeProdDynWooCommerceModal');

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target === modal) {
				modal.style.display = "none";
			}
		}

	$('.WholeProdDynWooCommerce .nav-tab-wrapper a').on('click',function(e){
		e.preventDefault();

		if($(this).hasClass('proVersion') ){
			//do nothing
		}else{
			var url = $(this).attr("href");
			$('.WholeProdDynWooCommerce').addClass('loading');
			$("body").load($(this).attr("href"),function(){
				window.history.replaceState("object or string", "Title", url );
			});
		}
	});

})( jQuery )