<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
use lithium\util\Inflector;

$this->title('Reports');
?>
<div class="reports">

<h2><?php echo $t('Reports');?></h2>

<nav id="reportNav" class="navBar">
	<ul>
		<?php foreach($reports as $type => $report):?>
		<li><?php echo $this->html->link($t($report), array('action' => 'index', 'report' => $type), array('class' => 'button'));?></li>
		<?php endforeach;?>
	</ul>
	<div class="clear"></div>
</nav>

<?php if(!empty($reportData['results'])):?>

<?php foreach($reportData['results'] as $name => $result):?>

<h4><?php echo $t($name);?></h4>

<table>
	<thead>
		<tr>
		<?php foreach($result['fields'] as $key => $field):?>
			<th class="<?php echo $key;?>"><?php echo $t($field);?></th>
		<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($result['data'] as $row):?>
		<tr>
		<?php foreach($result['fields'] as $key => $field):?>
			<td><?php echo isset($row[$key]) ? is_numeric($row[$key]) ? $row[$key] : $t($row[$key]) : '--';?></td>
		<?php endforeach; ?>
		</tr>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<?php foreach($result['totals'] as $row):?>
		<tr>
		<?php foreach($result['fields'] as $key => $field):?>
			<td><?php echo isset($row[$key]) ? is_numeric($row[$key]) ? $row[$key] : $t($row[$key]) : '';?></td>
		<?php endforeach; ?>
		</tr>
		<?php endforeach;?>
	</tfoot>
</table>

<?php endforeach;?>

<?php else:?>

<p><?php echo $t('No data found for this report.');?></p>

<?php endif;?>

</div>