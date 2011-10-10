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
	<h2>My <?php echo Inflector::humanize($status);?> Jobs</h2>

	<nav id="jobNav" class="navBar">
		<ul>
			<li><?php echo $this->html->link($t('{:action} {:entity}', array('action' => $t('Add'), 'entity' => $t($singular))), array('action' => 'add'), array('class' => 'button'));?></li>
			<?php foreach($statuses as $_status): $bClass = $status == $_status ? ' active' : ''?>
			<li class="right"><?php echo $this->html->link($t(Inflector::humanize($_status)), array('action' => 'index', 'status' => $_status), array('class' => 'button' . $bClass));?></li>
			<?php endforeach;?>
		</ul>
		<div class="clear"></div>
	</nav>

	<div class="index">
		<table>
			<tr>
				<th><?php echo $t('Job');?></th>
			</tr>
		<?php foreach ($recordSet as $record):?>
			<tr>
				<td>
					<div class="actions">
					<?php
						echo $this->html->link($t('Complete'), array('action' => 'complete', 'args' => $record->key()), array('class' => 'button button-small button-complete'));
						if ($active == $record->id):
							echo $this->html->link($t('Stop'), array('action' =>  'stop', 'args' => $record->key()), array('class' => 'button button-small button-red button-stop'));
						else:
							echo $this->html->link($t('Start'), array('action' => 'start', 'args' => $record->key()), array('class' => 'button button-small button-green button-start'));
						endif;
					?>
					<div class="clear"></div>
					<?php
						echo $this->html->link($t('Delete'), array('action' => 'delete', 'args' => $record->key()), array('class' => 'button button-small button-delete'));
						echo $this->html->link($t('Edit'), array('action' => 'edit', 'args' => $record->key()), array('class' => 'button button-small button-edit'));
					?>
					</div>

					<strong>
					<?php echo $this->html->link("#{$record->id}: " . $record->title, array(
						'action' => 'edit',
						'args' => $record->key()
					));
					?></strong>
					<br>
					<em>
						<strong>Status:</strong>
						<?php echo $t(Inflector::humanize($record->status())); ?>,
						<?php if($record->completed):?>
						<strong>Completed:</strong>
						<?php
							$date->setTimestamp($record->completed);
							echo $date->format($format);
						?>,
						<?php endif;?>
						<strong>Due:</strong>
						<?php
							$date->setTimestamp($record->__due);
							$user = $date->format($format);
							echo $user;
							if ($user != $record->due):
								echo " [{$record->due} {$record->timezone}]";
							endif;
						?>
						<br>
						<strong>Time:</strong>
						<?php if($active == $record->id):?>
						<span class="timed" data-time="<?php echo time() - $record->time();?>"><?php echo $record->timeString();?></span>,
						<?php else:?>
						<?php echo $record->timeString();?>,
						<?php endif;?>
						<strong>Fee:</strong> <?php echo $record->fees();?>
						<?php if($record->started):?>
						,
						<strong>Rate:</strong> <?php echo $record->rate();?>
						<?php endif;?>
					</em>
				</td>
			</tr>
		<?php endforeach;?>
		</table>
	</div>
</div>