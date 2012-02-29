<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
use lithium\util\Inflector;

$this->title('My Jobs');
?>
<div class="<?php echo $plural;?>">
	<h2>My <?php echo Inflector::humanize($status) . ' ' . $t($plural);?></h2>

	<nav id="jobNav" class="navBar">
		<ul>
			<li><?php echo $this->html->link($t('{:action} {:entity}', array('action' => $t('Add'), 'entity' => $t($singular))), array('action' => 'add'), array('class' => 'button'));?></li>
			<?php if($pages > 1):?>
			<li class="right">
				<?php echo $this->_render('element', 'work_units/paging');?>
			</li>
			<?php endif;?>
			<?php foreach($statuses as $_status): $bClass = $status == $_status ? ' active' : ''?>
			<li class="right"><?php echo $this->html->link($t(Inflector::humanize($_status)), array('action' => 'index', 'status' => $_status), array('class' => 'button' . $bClass));?></li>
			<?php endforeach;?>
		</ul>
		<div class="clear"></div>
	</nav>

	<div class="index">
		<table>
			<tr>
				<th><?php echo $t($singular);?></th>
			</tr>
		<?php if(!$recordSet->count()):?>
			<tr>
				<td>
					<p><br><strong>No <?php echo $t(Inflector::humanize($status)) . ' ' . $t($plural);?>.
					<br><br>
					<?php echo $this->html->link($t('{:action} {:entity}', array('action' => $t('Add'), 'entity' => $t($singular))), array('action' => 'add'), array('class' => 'button'));?>
					</strong></p>
				</td>
			</tr>
		<?php endif;?>
		<?php foreach ($recordSet as $record):?>
			<tr>
				<td>
					<div class="actions">
					<?php
						if ($status != 'completed'):
							echo $this->html->link($t('Complete'), array(
								'controller' => 'work_units',
								'action' => 'complete',
								'job_id' => $record->id
							), array('class' => 'button button-small button-complete'));
						endif;
						if ($active && $active->job_id == $record->id):
							echo $this->html->link($t('Stop'), array(
								'controller' => 'work_units',
								'action' =>  'stop',
								'job_id' => $record->id
							), array('class' => 'button button-small button-red button-stop'));
						else:
							echo $this->html->link($t('Start'), array(
								'controller' => 'work_units',
								'action' => 'start',
								'job_id' => $record->id
							), array('class' => 'button button-small button-green button-start'));
						endif;
					?>
					<div class="clear"></div>
					<?php
						echo $this->html->link($t('Delete'), array('action' => 'delete', 'args' => $record->key()), array('class' => 'button button-small button-delete'));
						echo $this->html->link($t('Edit'), array('action' => 'edit', 'args' => $record->key()), array('class' => 'button button-small button-edit'));
						if ($record->tasks()):
							echo $this->html->link($t('Tasks') . " ({$record->tasks()})", array(
								'controller' => 'tasks',
								'action' => 'active_job',
								'args' => $record->key()
							), array('class' => 'button button-small'));
						endif;
					?>
					</div>

					<strong>
					<?php echo $this->html->link("#{$record->id} " . $record->title, array(
						'action' => 'edit',
						'args' => $record->key()
					));
					?></strong>
					<br>
					<em>
						<strong>Status:</strong>
						<?php echo $t(Inflector::humanize($record->status())); ?>
						<?php if($record->completed):?>,
						<strong>Completed:</strong>
						<?php
							$date->setTimestamp($record->completed);
							echo $date->format($format);
						?>
						<?php endif;?>
						<?php if($record->due()):?>,
						<strong>Due:</strong>
						<?php
							$date->setTimestamp($record->due(true));
							$user = $date->format($format);
							echo $user;
							if ($user != $record->due()):
								echo " [{$record->due()} {$record->timezone}]";
							endif;
						?>
						<?php endif;?>
						<br>
						<?php if($time = $record->time()):?>
						<strong>Time:</strong>
						<?php if($active == $record->id):?>
						<span class="timed" data-time="<?php echo time() - $time;?>"><?php echo $record->time(true);?></span>
						<?php else:?>
						<?php echo $record->time(true);?>
						<?php endif;?>
						<?php endif;?>
						<?php if((float) $record->fee > 0):?><?php if($time):?>,<?php endif;?>
						<strong>Fee:</strong> <?php echo $record->fees();?>
						<?php if($record->started):?>,
						<strong>Rate:</strong> <?php echo $record->rate();?>
						<?php endif;?>
						<?php endif;?>
						&nbsp;
					</em>
				</td>
			</tr>
		<?php endforeach;?>
		</table>
	</div>
</div>