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
<?php
	$_actionLinks = array();
	if (!empty($displayUrlToComment)) {
		$_actionLinks[] = sprintf('<a href="%s">%s</a>', $urlToComment . '/' . $comment['Comment']['slug'], __d('comments', 'View', true));
	}

	if (!empty($isAuthorized)) {
		#$_actionLinks[] = $this->CommentWidget->link(__d('comments', 'Reply', true), array_merge($url, array('comment' => $comment['Comment']['id'], '#' => 'comment' . $comment['Comment']['id'])));
		if (!empty($isAdmin)) {
			if (empty($comment['Comment']['approved'])) {
				$_actionLinks[] = $this->CommentWidget->link(__d('comments', 'Publish', true), array_merge($url, array('comment' => $comment['Comment']['id'], 'comment_action' => 'toggleApprove', '#' => 'comment' . $comment['id'])));
			} else {
				$_actionLinks[] = $this->CommentWidget->link(__d('comments', 'Unpublish', true), array_merge($url, array('comment' => $comment['Comment']['id'], 'comment_action' => 'toggleApprove', '#' => 'comment' . $comment['Comment']['id'])));
			}
		}
	}

	#$_userLink = $comment[$userModel]['username'];
	$_userLink = $this->element('snpsht', array('useGallery' => true, 'userId' => $comment[$userModel]['id'], 'thumbSize' => 'small', 'thumbLink' => '/users/users/view/'.$comment[$userModel]['id']), array('plugin' => 'users'));

?>

<div class="comment index"> <a name="comment<?php echo $comment['Comment']['id'];?>"></a>
  <div class="indexRow">
    <div class="indexCell imageCell"> <?php echo $_userLink; ?> </div>
    <div class="indexCell metaCell">
      <ul class="metaData">
        <li><?php echo $this->Html->link($comment[$userModel]['full_name'], array('plugin' => 'users', 'controller' => 'users', 'action' => 'view', $comment[$userModel]['id'])); ?> </li>
        <li> <?php echo __d('comments', 'posted'); ?> &nbsp; <?php echo $this->Time->timeAgoInWords($comment['Comment']['created']); ?></li>
      </ul>
    </div>
    <div class="indexCell indexData">
      <div class="indexCell descriptionCell">
        <div class="truncate"><strong><a name="comment<?php echo $comment['Comment']['id'];?>"><?php echo $comment['Comment']['title'];?></a></strong> : <?php echo $this->Cleaner->bbcode2js($comment['Comment']['body']);?></div>
      </div>
      <div class="indexCell actionCell">
        <div class="drop-holder indexDrop actions">
          <ul class="drop">
            <li> <?php echo join('&nbsp;', $_actionLinks);?> </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
