<aside class='page-sidebar'>
<?php 
	if (isset($sidebar)):
		foreach ((array) $sidebar as $sb):
			echo $this->_render('element', 'layout/sidebar/' . $sb);
		endforeach;
	endif;
?>          
</aside>