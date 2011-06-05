var currentWindow = null;

//booking form modal base obj
var modal = {
	width:760,
	height:400,
	onOpen: function(){
		this.mask = new Mask(document.body, {
			onHide: function(){
				this.destroy();
			}
		}).show();
		this.mask.addEvent('click', function(){
			this.close();
		}.bind(this));
	},
	onClose: function(){
		this.mask.hide();
		this.mask.destroy(500, this.mask);
		this.destroy.delay(500, this);
	},
	onSuccess: function(){
		initBookingForm.call(this);
	}
};

//booking autocomplete base obj
var autoComplete = {
		cacheLength:0,
    valueFilter: function(data){
        return data.id;
    },
	selectOnTab: false,
	onSelect: function(elements){
		elements.field.node.removeClass('invalid');
		elements.field.node.addClass('valid');
	},
	onDeselect: function(elements){
		elements.field.node.removeClass('valid');
		elements.field.node.addClass('invalid');
	},
	onNoItemToList: function(elements){
		elements.field.node.removeClass('valid');
		elements.field.node.addClass('invalid');
	},
	filter: {
		type: 'contains',
		path: 'user'
	}
};

//booking form init, must be bound to the currentWindow
var initBookingForm = function(){
	
	var form = this.contentBox.getElement('form');
	this.form = new Li3Form.Request(form, {}, this.messageBox);	
	
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
	
	var p1 = new Meio.Autocomplete.Select($('BookingsPlayer1'), users, Object.merge(autoComplete, {
		valueField: $('Users0')
	}));

	var p2 = new Meio.Autocomplete.Select($('BookingsPlayer2'), users, Object.merge(autoComplete, {
		valueField: $('Users1')
	}));
	
	this.addEvent('close', function(){
		$(document.body).getElements('div.ma-container').dispose();
		try {
			p1.destroy();
			p2.destroy();
		} catch (e) {

		}
	});
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
		currentWindow = new LightFace.Request(Object.merge(modal, {
			url: '/bookings/add',
			request: {
				data: JSON.parse(value),
				method: 'get',
				evalScripts: true
			}
		})).open();
	});

	//booking events for editing bookings
	$$('.booking').addEvent('click', function(e){
		e.stop();
		var id = this.getElement('input').get('value');
		currentWindow = new LightFace.Request(Object.merge(modal, {
			url: '/bookings/edit/' + id,
			request: {
				method: 'get',
				evalScripts: true
			}
		})).open();
	});

});
