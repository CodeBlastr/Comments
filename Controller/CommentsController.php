<?php

/**
 * CakePHP Comments
 *
 * Copyright 2009 - 2010, Cake Development Corporation
 *                        1785 E. Sahara Avenue, Suite 490-423
 *                        Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2009 - 2010, Cake Development Corporation
 * @link      http://github.com/CakeDC/Comments
 * @link          http://zuha.com Zuha� Project
 * @package       zuha
 * @subpackage    zuha.app.plugins.comments
 * @since         Zuha(tm) v 0.0045
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class AppCommentsController extends CommentsAppController {

/**
 * @var string
 * @access public
 */
	public $name = 'Comments';

/**
 * @var array
 * @access public
 */
	public $components = array('RequestHandler');

/**
 * @var array
 * @access public
 */
	public $helpers = array('Text', 'Time');

/**
 * @var array
 * @access public
 */
	public $uses = array('Comments.Comment');

/**
 * Admin index action
 *
 * @TODO either hardcode spam/ham possible values (remove option from Commentable) or find a way to use these values here
 * @param string
 * @access public
 */
	public function index($type = 'spam') {
		$this->Comment->recursive = 0;
		$this->Comment->bindModel(array(
			'belongsTo' => array(
				'UserModel' => array(
					'className' => 'Users.User',
					'foreignKey' => 'user_id'))));
		$conditions = array();
		if (App::uses('Search.Prg', 'Controller/Component')) {
			$this->Prg = new PrgComponent();
			$this->Prg->initialize($this);
			$this->Comment->Behaviors->attach('Search.Searchable');
			$this->Comment->filterArgs = array(
				array('name' => 'is_spam', 'type' => 'value'),
				array('name' => 'approved', 'type' => 'value'),
			);
			$this->Prg->commonProcess();
			$this->Comment->parseCriteria($this->passedArgs);
		}

		$this->paginate['Comment'] = array(
			'conditions' => $conditions,
			'contain' => array('UserModel'),
			'order' => 'Comment.created DESC');
		if ($type == 'spam') {
			$this->paginate['Comment']['conditions'] = array('Comment.is_spam' => array('spam', 'spammanual'));
		} elseif ($type == 'clean') {
			$this->paginate['Comment']['conditions'] = array('Comment.is_spam' => array('ham', 'clean'));
		}
		$this->set('comments', $this->paginate('Comment'));
	}

/**
 * Processes mailbox folders
 *
 * @param string $folder Name of the folder to process
 * @access public
 */
	public function process($type = null) {
		$addInfo = '';
		if (!empty($this->request->data)) {
			try {
				$message = $this->Comment->process($this->request->data['Comment']['action'], $this->request->data);
			} catch (Exception $ex) {
				$message = $ex->getMessages();
			}
			$this->Session->setFlash($message);
		}
		$url = array('plugin' => 'comments', 'action' => 'index', 'admin' => true);
		$url = Set::merge($url, $this->request->params['pass']);
		$this->redirect(Set::merge($url, $this->request->params['named']));
	}

/**
 * Admin mark comment as spam
 *
 * @param string UUID
 * @access public
 */
	public function spam($id) {
		$this->Comment->id = $id;
		if (!$this->Comment->exists(true)) {
			$this->Session->setFlash(__d('comments', 'Wrong comment id', true));
		} elseif ($this->Comment->markAsSpam()) {
			$this->Session->setFlash(__d('comments', 'Antispam system informed about spam message.', true));
		} else {
			$this->Session->setFlash(__d('comments', 'Error appear during save.', true));
		}
		$this->redirect(array('action' => 'index'));
	}

/**
 * Admin mark comment as ham
 *
 * @param string UUID
 * @access public
 */
	public function ham($id) {
		$this->Comment->id = $id;
		if (!$this->Comment->exists(true)) {
			$this->Session->setFlash(__d('comments', 'Wrong comment id', true));
		} elseif ($this->Comment->markAsHam()) {
			$this->Session->setFlash(__d('comments', 'Antispam system informed about ham message.', true));
		} else {
			$this->Session->setFlash(__d('comments', 'Error appear during save.', true));
		}
		$this->redirect(array('action' => 'index'));
	}

/**
 * Admin View action
 *
 * @param string UUID
 * @access public
 */
	public function view($id = null) {
		$this->Comment->id = $id;
		$comment = $this->Comment->read(null, $id);
		if (empty($comment)) {
			$this->Session->setFlash(__d('comments', 'Invalid Comment.', true));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('comment', $comment);
	}

/**
 * Admin delete action
 *
 * @param string UUID
 * @access public
 */
	public function delete($id = null) {
		$this->Comment->id = $id;
		if (!$this->Comment->exists()) {
			$this->Session->setFlash(__d('comments', 'Invalid id for Comment', true));
		} elseif ($this->Comment->delete()) {
			$this->Session->setFlash(__d('comments', 'Comment deleted', true));
		} else {
			$this->Session->setFlash(__d('comments', 'Impossible to delete the Comment. Please try again.', true));
		}
		$this->redirect(array('action' => 'index'));
	}

/**
 * Request comments
 *
 * @todo Return only "clean" comments?
 * @todo Return also related models: find a way to automatically bind related models to comments
 * @param string user UUID
 * @return void
 * @access public
 */
	public function requestForUser($userId = null, $amount = 5) {
		if (!$this->RequestHandler->isAjax() && !$this->_isRequestedAction()) {
			return $this->cakeError('404');
		}

		$conditions = array('Comment.user_id' => $userId);
		if (!empty($this->request->params['named']['model'])) {
			$conditions['Comment.model'] = $this->request->params['named']['model'];
		}

		$this->paginate = array(
			'conditions' => $conditions,
			'order' => 'Comment.created DESC',
			'limit' => $amount);

		$this->set('comments', $this->paginate());
		$this->set('userId', $userId);

		$this->viewPath = 'elements/comments';
		$this->render('comment');
	}

/**
 * Returns true if the action was called with requestAction()
 *
 * @return boolean
 */
	protected function _isRequestedAction() {
		return array_key_exists('requested', $this->request->params);
	}

/* Adds the comment
 *
 * @return null
 */
	public function add() {
		if (!empty($this->request->data)) {
			if ($this->Comment->save($this->request->data)) {
				$this->Session->setFlash('Comment Added');
			} else {
				$this->Session->setFlash('Comment can not be saved');
			}
		}
		$this->redirect($this->referer());
	}

}

if (!isset($refuseInit)) {
	class CommentsController extends AppCommentsController {}
}