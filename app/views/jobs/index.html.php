<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
use sli_util\storage\Registry;
use lithium\util\Inflector;

$tz = new \DateTimeZone($user->timezone);
$date = new \DateTime(null, $tz);
$format = Registry::get('app.date.long');
$active = false;
if ($job = $user->job()) {
	$active = $job->job->id;
}

$this->title('My Jobs');
?>
<div class="<?php echo $plural;?>">
	<h2>My Jobs</h2>

	<nav id="jobNav" class="navBar">
		<ul>
			<li><?php echo $this->html->link($t('{:action} {:entity}', array('action' => $t('Add'), 'entity' => $t($singular))), array('action' => 'add'));?></li>
			<?php foreach($statuses as $status):?>
			<li class="right"><?php echo $this->html->link($t(Inflector::humanize($status)), array('action' => 'index', 'status' => $status));?></li>
			<?php endforeach;?>
		</ul>
		<div class="clear"></div>
	</nav>

	<div class="index">
		<table>
			<tr>
				<th width="1%"></th>
				<th width="60%"><?php echo $t('Job');?></th>
				<th class="actions"><?php echo $t('Actions');?></th>
			</tr>
		<?php foreach ($recordSet as $record):?>
			<tr>
				<td class="app indicated">
					<?php
						if ($active == $record->id):
							echo $this->html->link('', array(
								'action' => 'stop',
								'args' => $record->key()
							), array(
								'title' => $t('{:action} {:entity}', array('action' => $t('Stop'), 'entity' => $t('Work'))),
								'class' => 'disabled'
							));
						else:
							echo $this->html->link('', array(
								'action' => 'start',
								'args' => $record->key()
							), array(
								'title' => $t('{:action} {:entity}', array('action' => $t('Start'), 'entity' => $t('Work'))),
								'class' => 'enabled'
							));
						endif;
					?>
				</td>
				<td>
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
						<strong>Fee:</strong> <?php echo $record->fees();?>
						<?php if($record->started):?>
						, <strong>Time:</strong> <?php echo $record->timeString();?>,
						<strong>Rate:</strong> <?php echo $record->rate();?>
						<?php endif;?>
					</em>
				</td>
				<td class="actions">
					<?php
						$_actions = array('edit', 'complete', 'delete');
						foreach ($_actions as $action):
							echo $this->html->link($t(ucfirst($action)), array('action' => $action, 'args' => $record->key()));
						endforeach;
					?>
				</td>
			</tr>
		<?php endforeach;?>
		</table>
	</div>
</div>