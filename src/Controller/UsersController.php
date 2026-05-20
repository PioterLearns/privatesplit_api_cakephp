<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    /**
     * Me method
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function me()
    {
        $id = 1;//todo replace with session authenticated id
        $this->set('user', $this->Users->get($id, contain: ['PrimaryBuckets', 'SecondaryBuckets', 'Droplets']));
        $this->viewBuilder()->setOption('serialize', 'user');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        //todo I don't get why this is created if !post, but I'll think about it later
        //todo validation
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->set('user', $user);
                $this->viewBuilder()->setOption('serialize', 'user');
            }
            //todo error handling needs custom views?
        }
    }
}
