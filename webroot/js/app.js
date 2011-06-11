(function(){
	this.Li3Form = new Class({
		Implements: [Options, Events],
		options: {},
		form:null,
		initialize: function(form, options){
			this.form = document.id(form);
			this.setOptions(options);
		},
		submit: function(){
			this.form.submit();
		}
	});
	
	this.Li3Form.Request = new Class({
		Extends:Li3Form,
		request: null,
		initialize: function(form, options, update){
			this.parent(form, options);
			var self = this;
			
			var request = function(form, update, options){
				if (!form) {
					return false;
				}
				var _request = new Form.Request(form, update, options);
				_request.addEvent('success', function(){
					self.form = this.target.getElement('form');
					self.request = request(self.form, update, options);
				});
				return _request;
			}			
			this.request = request(form, update, options);
		},
		submit: function(){
			this.request.onSubmit();
		}
	});
	
	this.App = {
		fadeFlashMessages: function(){
			var flashMessages = $$('.flash-message:not(.nofade)');
			if (flashMessages) {
				flashMessages.set('morph', {duration: 'long', onComplete: function(){
					console.log(this.element);
				}});
				(function(){
					flashMessages.morph({
						height:0,
						paddingTop:0,
						paddingBottom:0,
						margin:0,
						opacity:0,
						borderBottomWidth:0
					});
				}).delay(3000);
			}
		},
		
		editAnnounce: function(){
			var announce = $('announce');
			var data = $('announceData');
			var display = $('announceDisplay');
			var text = display.getElement('.data');
			var none = display.getElement('.none');
			
			if (text.get('text').trim() == '') {
				none.show('inline');
			}
			
			var edit = function(){
				none.hide();
				display.hide();
				data.show();
				data.focus();
			};
			
			var save = function(){
				var msg = data.get('value').trim();
				text.set('text', msg);
				if (msg == '') {
					none.show('inline');
				}
				display.show();
				data.hide();
				new Request({
					url: '/settings/announce',
					data: Object.toQueryString({announce:msg})
				}).send();
			};
			
			display.addEvent('click', edit);
			data.addEvents({
				'blur': save,
				'keyup': function(e){
					if (e.key == 'enter') {
						save();
					}
				}
			});
		}
	};
	
	window.addEvent('domready',function(){
		App.fadeFlashMessages();
		if ($(document.body).hasClass('admin')) {
			App.editAnnounce();
		}
	});
	
})();

