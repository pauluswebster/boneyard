<div class="row">
	<div class="span3">
		<?php echo $this->element->render('layout/sidebar', array('sidebar' => array('account')));?>
	</div>
	<div class="span9">
		<ul class="breadcrumb">
			<li><a href="/pages/account">MyAuto</a><span class="divider">/</span></li>
			<li class="active">Favourites</li>
		</ul>
		<hr>		
		<div class="tabbable">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#1" data-toggle="tab">Listing Shortlist</a></li>
				<li><a href="#2" data-toggle="tab">Saved Searches</a></li>
				<li><a href="#3" data-toggle="tab">Watched Dealers</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="1">
					<p>Listing Shortlist.</p>
				</div>
				<div class="tab-pane" id="2">
					<p>Saved Searches.</p>
				</div>
				<div class="tab-pane" id="3">
					<p>Watched Dealers.</p>
				</div>
			</div>
		</div>
	</div>
</div>