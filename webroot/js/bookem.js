var BookingForm = new Class({
	window: null,
	autocomplete: null,
	elements: {},
	initialize: function(modal) {
		this.window = modal;
		$('Attending').getElements('a.remove').addEvent('click', this.removeUser.bind(this));
		var addUser = this.addUser.bind(this);
		var attending = new Meio.Autocomplete.Select($('BookingsAddSomeone'), users, {
			cacheLength:0,
		    valueFilter: function(data){
		        return data.id;
		    },
			selectOnTab: false,
			onSelect: function(elements, value){
				elements.field.node.removeClass('ma-invalid');
				//add user to list
				addUser(value);
				//reset value
				elements.field.node.set('value', '');
				//reposition
				elements.list.positionNextTo(elements.field.node);
				
			},
			onDeselect: function(elements){
				elements.field.node.removeClass('ma-valid');
				elements.field.node.addClass('ma-invalid');
			},
			filter: {
				type: 'contains',
				path: 'user'
			}
		});
		this.autocomplete = attending;
		this.window.addEvent('close', function(){
			$(document.body).getElements('div.ma-container').dispose();
			try {
				attending.destroy();
			} catch (e) {

			}
		});
	},

	addUser: function(userData) {
		if ($('Attending').getElement('.attendee input[value='+userData.id+']')) {
			return;
		}
		var user = Element.from(userTmpl.insert(userData));
		user.getElement('a.remove').addEvent('click', this.removeUser.bind(this));
		$('Attending').grab(user);
	},

	removeUser: function(e) {
		e.stop();
		var ele = $(e.target) || false;
		if(ele && ele.match('#Attending a.remove')) {
			ele.getParent('.attendee').dispose();
			var elements = this.autocomplete.elements;
			elements.list.positionNextTo(elements.field.node);
			return true;
		}
	}
});

var Booking = {
	add: function() {},
	edit: function() {},
	move: function() {},
	remove: function() {}
};

//booking form init, must be bound to the currentWindow
var initBookingForm = function(){
	var resize = this._resize.bind(this);
	var fade = this.fade.bind(this);
	var unfade = this.unfade.bind(this);
	var form = this.contentBox.getElement('form');
	this.form = new Li3Form.Request(form, {
		onSend: function() {
			fade();
		},
		onSuccess: function(){
			resize();
			unfade(1);
		}
	}, this.messageBox);	
	
	if ($('BookingId')) {
		this.addButton('Update', function(){
			this.form.submit();
		}.bind(this), 'green');
		this.addButton('Delete',function(){
			this.form.request.options.extraData.action = 'delete';
			this.form.submit();
		}.bind(this), 'red');
	} else {
		this.addButton('Add', function(){
			this.form.submit();
		}.bind(this), 'green');
	}
	
	this.addButton('Cancel',function(){
		this.close();
	}.bind(this));
	
	resize();
}

window.addEvent('domready',function(){
	//booking index date selector
	new DatePicker($('DateSelect'), {
		toggle: $('date-select'),
		positionOffset: {x: 5, y: 0},
		pickerClass: 'datepicker_jqui',
		useFadeInOut: !Browser.ie,
		onSelect: function(date){
			var location = new URI();
			location.setData({today:date.format('%Y%m%d')});
			window.location = location.toString();
		}
	});
	
	//interval events for adding new bookings
	$$('.interval').addEvent('click', function(e){
		var value = this.getElement('input').get('value');
		currentWindow = new LightFace.Request(Object.merge(Object.clone(modal), {
			url: '/bookings/add',
			request: {
				data: JSON.parse(value),
				method: 'get',
				evalScripts: true
			},
			onSuccess: function(){
				initBookingForm.call(this);
				new BookingForm(this);
			}
		})).open();
	});

	//booking events for editing bookings
	$$('.booking').addEvent('click', function(e){
		e.stop();
		var input = this.getElement('input');
		if (input) {
			var id = input.get('value');
			currentWindow = new LightFace.Request(Object.merge(Object.clone(modal), {
				url: '/bookings/edit/' + id,
				request: {
					method: 'get',
					evalScripts: true
				},
				onSuccess: function(){
					initBookingForm.call(this);
					new BookingForm(this);
				}
			})).open();
		}
	});
});
