<?php
	echo $this->html->link($t('Page') . ": {$page}", array(
		'action' => 'index',
		'status' => $status,
		'page' => $page
	), array(
		'title' => $t('Page') . ": {$page}",
		'class' => 'button button-select'
	));
?>
<ul class="button-select-options">
	<?php 
		$_page = 0; 
		while ($pages--): 
			$_page++;
			if ($_page == $page) {
				$_page++;
			}
	?>
		<li>
			<?php
				echo $this->html->link($t('Page') . ": {$_page}", array(
					'action' => 'index',
					'status' => $status,
					'page' => $_page
				), array(
					'title' => $t('Page') . ": {$_page}",
				));
			?>
		</li>
	<?php endwhile;?>
</ul>