<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Utility\Security;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['register']);
        $this->Authorization->skipAuthorization();
    }

    /**
     * Me method
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function me()
    {
        $user = $this->request->getAttribute('identity');
        $this->set('user', $this->Users->get($user->id, contain: ['PrimaryBuckets', 'SecondaryBuckets', 'Droplets']));
        $this->viewBuilder()->setOption('serialize', 'user');
    }

    /**
     * Register method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function register()
    {
        //todo I don't get why this is created if !post, but I'll think about it later
        //todo validation
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->set('user', $user);
                $this->viewBuilder()->setOption('serialize', 'user');
            }
            //todo error handling needs custom views?
        }
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function login()
    {
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $user = $this->request->getAttribute('identity');
            $session = $this->Users->Sessions->newEmptyEntity();
            $session->setAccess('user_id', true);
            $this->Users->Sessions->patchEntity($session, [
                'token' => Security::randomString(),
                'user_id' => $user->id
            ]);
            if ($this->Users->Sessions->save($session)) {
                $this->set('session', $session);
                $this->viewBuilder()->setOption('serialize', 'session');
            }
        }
    }
}
