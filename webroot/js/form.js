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
})();