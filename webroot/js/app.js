var d = function(_var_){
	console.log(_var_);
};

window.addEvent('domready', function(){
	//icon buttons

	var buttons = $$('.button-start, .button-stop, .button-complete, .button-edit, .button-delete');
	if (buttons.length > 0) {
		buttons.each(function(button){
			var text = new Element('span', {'class': 'button-ico-text', 'text': button.get('text')});
			button.empty();
			text.inject(button);
			new Element('span', {'class': 'button-ico-img'}).inject(button, 'top');
		});
	}

	var buttons = $$('.button-select');
	if (buttons.length > 0) {
		buttons.each(function(button){
			var text = new Element('span', {'class': 'button-ico-text', 'text': button.get('text')});
			button.empty();
			text.inject(button);
			new Element('span', {'class': 'button-ico-img right'}).inject(button, 'top');
		});
	}


	//datepickers
	var dateInputs = $$('input.date-picker');
	if (dateInputs.length > 0) {
		dateInputs.set('readonly', true);
		dateInputs.each(function(di){
			var dobj = {
			    timePicker: true,
			    positionOffset: {x: 5, y: 0},
			    pickerClass: 'datepicker_dashboard',
			    useFadeInOut: !Browser.ie
			};
			var format = di.get('data-format');
			if (format) {
				dobj.format = format;
			}
			new Picker.Date(di, dobj);
		});
	}

	//flash message fade
	var flashMessages = $$('.flash-message:not(.nofade)');
	if (flashMessages) {
		flashMessages.set('morph', {duration: 'long', onComplete: function(){
			this.element.dispose();
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

	//timers
	var updateTime = function(element){
		var minute = 1;
		var hour = 60;
		var day = 1440;
		var ts = parseInt(element.get('data-time')) * 1000;
		var started = new Date(ts);
		var now = new Date();
		var minutes = started.diff(now, 'minute');
		var timeString = '';
		while (minutes > day) {
			var days = Math.floor(minutes/day);
			timeString += days + 'd ';
			minutes -= (days*day);
		}
		while (minutes > hour) {
			var hours = Math.floor(minutes/hour);
			timeString += hours + 'h ';
			minutes -= (hours*hour);
		}
		if (minutes > 0) {
			timeString += minutes + 'm';
		}
		if (timeString.length == 0) {
			timeString = '1m'
		}
		element.set('text', timeString);
	};

	var timed = $$('.timed');
	if (timed) {
		timed.each(function(element){
			updateTime(element);
			updateTime.periodical(30 * 1000, window, [element]);
		});
	}

	//select buttons
	var selectButtons = $$('.button-select');
	if (selectButtons.length > 0) {
		selectButtons.each(function(bs){
			var options = bs.getNext('.button-select-options');
			if (options) {

				var show = function(){
					bs.addClass('active');
					options.setStyle('display', 'block');
				}

				var hide = function() {
					bs.removeClass('active');
					options.setStyle('display', 'none');
				}

				bs.addEvent('click', function(e){
					e.preventDefault();
					this.hasClass('active') ? hide() : show();
				});

				options.addEvent('mouseleave', hide);
			}
		});
	}

});