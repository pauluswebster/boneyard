<?php
$this->title($t($plural));
?>
<div class="scaffold <?php echo $plural;?> index<?php echo $singular;?>">
	<h2><?php echo $t($plural);?></h2>
	<hr>
	<div class="row">
		<div class="span9 summary">
	  	<?php if(!count($recordSet)): ?>
		<div class="hero-unit">
			  <h1>Eeeek, no projects!</h1>
			  <p>Best you start creating some...</p>
			  <p>
			    <?php 
			    	echo $this->html->link($t('{:action} {:entity}', array('action' => $t('Add'), 'entity' => $t($singular))), array('action' => 'add'), array('class' => 'btn btn-primary btn-large'));
			    ?>
			  </p>
		</div>
	  	<?php else: ?>
	  	<?php foreach ($recordSet as $record): ?>
	  	<div class="project">
			<div class="alert alert-info title">
				<h3><?php echo $this->html->link($record->title, array('action' => 'view', 'args' => $record->key()));?> <small class="client reference"><?php echo $record->client_id . ' - ' . $h($record->reference);?></small></h3>
      			<div class="actions">
      				<?php
					$_actions = array(
						'edit' => 'icon-pencil', 
						'delete' => 'icon-minus-sign icon-white'
					);
					$_links = array();
					$options = array('class' => 'btn btn-mini', 'escape' => false);
					foreach ($_actions as $action => $icon):
						if(in_array($action, $actions)):
							if ($action == 'delete'):
								$options['class'] .= ' btn-danger btn-delete';
							endif;
							$icon = $icon ? '<i class="'.$icon.'"></i>&nbsp;' : '';
							$_links[] = $this->html->link($icon . $t(ucfirst($action)), array('action' => $action, 'args' => $record->key()), $options);
						endif;
					endforeach;
					echo join('&nbsp;', $_links);
				?>
      			</div>
      		</div>
      		<div class="details">
      			<table class="table table-striped table-bordered table-condensed">
      				<tr>
      					<th>Staff</th>
      					<td>
      						<a class="btn btn-mini btn-info actions"><i class="icon-cog"></i> Permissions</a>
      						<?php 
      							foreach ($record->staff as $staff):
      								echo "<a class=\"btn btn-mini\">{$staff->first_name} {$staff->last_name}</a>";
      							endforeach;
      						?>
      						
      						
      						<!--a class="btn btn-mini btn-success" title="Project Manager">Paul Webster</a>
      						<a class="btn btn-mini btn-info" title="Manager">Pauk Rebsta</a>
      						<a class="btn btn-mini">Paul Webster</a>
      						<a class="btn btn-mini">Paul Webster</a>
      						<a class="btn btn-mini">Paul Webster</a-->
      					</td>
      				</tr>
      				<tr>
      					<th>Timing</th>
      					<td>
      						<span class="label label-success" title="On Time">Due: 1st August 2012</span> 
      						<span class="label">Created:  1st June 2012</span>
      					</td>
      				</tr>
      				<tr>
      					<th>Pogress</th>
      					<td>
      						<span class="label label-warning" title="Progress Behind">1 / 4 Milestones</span> 
      						<span class="label label-warning" title="Progress Behind">17 / 84 Tasks</span>
      						<span class="label">13h 12m / 100h</span>
      					</td>
      				</tr>
      				<!--tr>
      					<th>Description</th>
      					<td>
      						<div class="collapse in" id="project-description-<?php echo $record->id;?>"><p><?php echo nl2br($h($record->description))?></p></div>
      						<a data-toggle="collapse" data-target="#project-description-<?php echo $record->id;?>">View...</a>
      					</td>
      				</tr-->
      			</table>
      		</div>
	  	</div>
	  	<hr>
	  	<?php endforeach;?>
	  	<?php endif;?>
	  </div>
	  <div class="span3">
	  	<ul class="actions nav nav-tabs nav-stacked">
	  		<li><a><strong>Actions</strong></a></li>
			<?php if(in_array('add', $actions)):?>
			<li><?php 
				$link = '<i class="icon-plus-sign"></i>&nbsp;';
				$link.= $t('{:action} {:entity}', array('action' => $t('Add'), 'entity' => $t($singular)));
				echo $this->html->link($link, array('action' => 'add'), array('escape' => false));
			?></li>
			<?php endif;?>
		</ul>
	</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$(".collapse").collapse();
		$("a.btn-delete").popover({
			title: function() {
				return '<i class=" icon-exclamation-sign right"></i> ' + $(this).text() + ', really?';
			},
			content: function(){			
				return '<a class="btn btn-success btn-cancel">No, take me back.</a>' +
				'<a class="btn btn-danger right" href="'+$(this).attr('href')+'">'+$(this).text()+'</a>';
			},
			trigger: 'manual',
			placement: 'bottom'
		});
		$("body").delegate(".popover .btn-cancel", "click", function() {
			$(this).closest("div.popover").addClass('out').removeClass('in');				
		});
		$("a.btn-delete").click(function(e){
			e.preventDefault();
			$(this).popover('show');
		});
	});
</script>