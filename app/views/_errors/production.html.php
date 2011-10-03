<h3>How embarrassing, we can't find that page!</h3>

<?php if ($from = $this->_request->env('HTTP_REFERER')):?>

<p>It looks like you clicked through from a broken link, click below to go back:</p>

<p><a href="javascript:history.back()"><?php echo $from;?></a></p>

<?php endif;?>

<p>Take me back to the home page: <a href="/">Home</a></p>