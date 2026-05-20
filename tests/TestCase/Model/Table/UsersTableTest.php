<?php

declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersTable
     */
    protected $Users;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
        $this->Users = $this->getTableLocator()->get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\UsersTable::validationDefault()
     */
    public function testValidation_dataIsValid_createdWithoutErrors(): void
    {
        $validData = [
            'username' => 'newuser',
            'password' => 'pass',
        ];

        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $validData);

        $this->assertFalse($user->hasErrors(), 'User entity has errors');
    }

    public function testValidation_usernameNotProvided_hasUsernameError(): void
    {
        $data = [
            'password' => 'pass',
        ];

        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $data);

        $this->assertArrayHasKey('username', $user->getErrors(), 'Username is required');
    }

    public function testValidation_passwordNotProvided_hasPasswordError(): void
    {
        $data = [
            'username' => 'newuser',
        ];

        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $data);

        $this->assertArrayHasKey('password', $user->getErrors(), 'Password is required');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\UsersTable::buildRules()
     */
    public function testBuildRules_usernameIsUnique_hasNoErrors(): void
    {
        $data = [
            'username' => 'newuser',
            'password' => 'pass',
        ];

        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $data);

        $this->assertEmpty($user->hasErrors(), 'User entity has errors');
    }

    public function testBuildRules_usernameAlreadyExists_hasUsernameError(): void
    {
        $data = [
            'username' => 'Alice',//from Fixture
            'password' => 'pass',
        ];

        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $data);

        $this->assertArrayHasKey('username', $user->getErrors(), 'Username must be unique');
    }
}
