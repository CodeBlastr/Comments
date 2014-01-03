<?php
	$_url = array_merge($url, array('action' => str_replace(Configure::read('Routing.admin') . '_', '', $this->action)));
	foreach (array('page', 'order', 'sort', 'direction') as $named) {
		if (isset($this->passedArgs[$named])) {
			$_url[$named] = $this->passedArgs[$named];
		}
	}
	if ($target) {
		$_url['action'] = str_replace(Configure::read('Routing.admin') . '_', '', 'comments');
		$ajaxUrl = $this->CommentWidget->prepareUrl(array_merge($_url, array('comment' => $comment, '#' => 'comment' . $comment)));
		echo $this->Form->create(null, array('url' => $ajaxUrl, 'target' => $target));
	} else {
		echo $this->Form->create(null, array('url' => array_merge($_url, array('comment' => $comment, '#' => 'comment' . $comment))));
	}
	echo $this->CommentWidget->globalParams['titleOptions'] === false ? null : $this->Form->input('Comment.title', $this->CommentWidget->globalParams['titleOptions']);
	echo $this->Form->input('Comment.body', $this->CommentWidget->globalParams['bodyOptions']);
	// Bots will very likely fill this fields
	echo $this->Form->input('Other.title', array('type' => 'hidden'));
	echo $this->Form->input('Other.comment', array('type' => 'hidden'));
	echo $this->Form->input('Other.submit', array('type' => 'hidden'));
	if ($target) {
		echo $js->submit(__('Submit', true), array_merge(array('url' => $ajaxUrl), $this->CommentWidget->globalParams['ajaxOptions']));
	} else {
		echo $this->Form->submit(__('Submit', true));
	}
	echo $this->Form->end();
