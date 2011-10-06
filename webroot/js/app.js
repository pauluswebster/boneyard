var d = function(_var_){
	console.log(_var_);
};

window.addEvent('domready', function(){
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

	if ($('activeJob')) {
		var dateOutput = $('activeJob').getElement('.dateFormat');
		var ts = parseInt(dateOutput.get('data-time')) * 1000;
		var started = new Date(ts);
		var now = new Date();

		var minute = 1;
		var hour = 60;
		var day = 1440;

		var updateTime = function(){
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
			dateOutput.set('text', timeString);
		};
		updateTime();
		updateTime.periodical(60 * 1000);
	}
});