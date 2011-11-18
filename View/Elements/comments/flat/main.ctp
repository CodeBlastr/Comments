<?php
/**
 * Copyright 2009 - 2010, Cake Development Corporation
 *                        1785 E. Sahara Avenue, Suite 490-423
 *                        Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */
?>
<div class="comments indexContainer">
<?php
if ($allowAddByAuth):
	if ($isAddMode && $allowAddByAuth): ?>
		<h3><?php echo __d('comments', 'Add New Comment'); ?></h3>
		<?php
		echo $this->CommentWidget->element('form', array('comment' => (!empty($comment) ? $comment : 0)));
	else:
		if (empty($this->request->params[$adminRoute]) && $allowAddByAuth):
			echo $this->CommentWidget->link(__d('comments', 'Add comment', true), am($url, array('comment' => 0)), array('class' => 'button'));
		endif;
	endif;
else: ?>
	<h3><?php echo __d('comments', 'Comments'); ?></h3>
	<?php
		echo sprintf(__d('comments', 'If you want to post comments, you need to login first.', true), $this->Html->link(__d('comments', 'login', true), array('controller' => 'users', 'action' => 'login', 'prefix' => $adminRoute, $adminRoute => false)));
endif;

if (!$isAddMode || $isAddMode):
	echo $this->CommentWidget->element('paginator');
	foreach (${$viewComments} as $comment):
		echo $this->CommentWidget->element('item', array('comment' => $comment));
	endforeach;
endif;

?>
</div>
<?php echo $this->Html->image('/comments/img/indicator.gif', array('id' => 'busy-indicator',
 'style' => 'display:none;')); ?>
