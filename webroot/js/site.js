(function($) {
	
	var Page = {
		init: function(){
			//smooth scroll contact links
			var target = $('a[name="contact"]');
			$('a[href="#contact"]').click(function(e) {
		          e.preventDefault();
		          $('body').animate({scrollTop: target.offset().top}, 400, function() {
		            location.hash = 'contact';
		          });
		    });
			//form handler
			Form.init();
		}
	};
	
	var Form = {
		ele: null,
		request: null,
		requestParams: {},
		init: function(){
			Form.ele = $('#ContactForm');
			Form.requestParams = {
				type: Form.ele.attr('method'),
				url: Form.ele.attr('action'),
				data: {},
				dataType: 'json',
				success: Form.onSuccess,
				error: Form.onError,
				complete: Form.onResponse
			}
			Form.ele.submit(function(e){
				e.preventDefault();
				Form.onSubmit();
			});
			Form.toggleState(true);
			$('.contact-info .box-message').click(function(e){
				$(this).hide();
			});
		},
		toggleState: function(){
			var submit = Form.ele.find('input[type="submit"]');
			if (arguments.length > 0) {
				Form.ele.find('input:not([type="submit"]), textarea').removeAttr('readonly');
				if (submit.attr('disabled')) {
					submit.removeAttr('disabled');
					$('.contact-info .box-info').hide();
				}
				
			} else {
				Form.ele.find('input:not([type="submit"]), textarea').attr('readonly', 'readonly');
				submit.attr('disabled', 'disabled');
				$('.contact-info .box-info').show();
			}
		},
		onSubmit: function(){
			Form.request = $.ajax($.extend({}, Form.requestParams, {
				data: Form.ele.serialize()
			}));
			$('.contact-info .box-message').hide();
			Form.ele.find('.invalid').removeClass('invalid');
			Form.toggleState();
		},
		onSuccess: function(data, textStatus, jqXHR) {
			if (!(typeof data == 'object')) {
				$('.contact-info .box-error').show();
			} else if (data.success == true) {
				$('.contact-info .box-success').show();
			} else {
				$('.contact-info .box-warning').show();
				if (typeof data.errors == 'object') {
					$.each(data.errors, function(i, error){
						console.log(error, Form.ele.find('#' + error));
						if (Form.ele.find('#' + error)) {
							Form.ele.find('#' + error).addClass('invalid');
						}
					});
				}
			}
			
		},
		onError: function(jqXHR, textStatus, errorThrown){
			$('.contact-info .box-error').show();
		},
		onResponse: function(jqXHR, textStatus){
			Form.request = null;
			Form.toggleState(true);
		}
	};
	
	$(document).ready(function($) {
		Page.init();
	});
	
})(jQuery);