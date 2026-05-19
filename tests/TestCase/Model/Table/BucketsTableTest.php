<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BucketsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BucketsTable Test Case
 */
class BucketsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BucketsTable
     */
    protected $Buckets;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Buckets',
        'app.Droplets',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Buckets') ? [] : ['className' => BucketsTable::class];
        $this->Buckets = $this->getTableLocator()->get('Buckets', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Buckets);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\BucketsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
