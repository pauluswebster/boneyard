<?php
	use lithium\core\Environment;
?>
<!doctype html>
<html lang="<?= str_replace('_', '-', Environment::get('locale')); ?>">
<head>
	<?=$this->html->charset();?>
	<title><?=$t($settings['siteName']);?> &gt; <?=$this->title();?></title>
	<?php 
		echo $this->html->style(array(
			'lithium', 
			'debug', 
			'bookem'
		)); 
		echo $this->html->script(array(
			'https://ajax.googleapis.com/ajax/libs/mootools/1.3.2/mootools.js',
			'mootools-more/Class/Class.Binds',
			'mootools-more/Class/Class.Refactor',
			'mootools-more/Class/Class.Occlude',
			'mootools-more/Class/Events.Pseudos',
			'mootools-more/Types/Object.Extras',
			'mootools-more/Types/String.Extras',
			'mootools-more/Types/String.QueryString',
			'mootools-more/Types/URI',
			'mootools-more/Locale/Locale',
			'mootools-more/Locale/Locale.en-US.Date',
			'mootools-more/Types/Date',
			'mootools-more/Element/Element.Forms',
			'mootools-more/Element/Element.Event.Pseudos',
			'mootools-more/Element/Element.Position',
			'mootools-more/Element/Element.Measure',
			'mootools-more/Element/Element.Delegation',
			'mootools-more/Utilities/IframeShim',
			'mootools-more/Interface/Mask',
			'mootools-more/Interface/Spinner',
			'mootools-more/Forms/Form.Request',
			'LightFace/LightFace',
			'LightFace/LightFace.Request',
			'LightFace/LightFace.IFrame',
			'mootools-meio-autocomplete/Meio.Autocomplete',
			'mootools-datepicker/Locale.en-US.DatePicker',
			'mootools-datepicker/Picker',
			'mootools-datepicker/Picker.Attach',
			'mootools-datepicker/Picker.Date',
			'form'
		));
		echo $this->scripts();
	?>
	<?=$this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="app bookings<?=$user->admin ? ' admin' :'' ?>">
	<?=$this->flashMessage->output();?>
	<?=$this->view()->render(array('element' => 'layout/header'), compact('settings', 'user'));?>
	<div id="container">
		<?=$this->content;?>
	</div>
</body>
</html>