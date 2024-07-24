<?php

declare(strict_types=1);

namespace Test\Testcontainers\Module;

use MongoDB\Client;
use PHPUnit\Framework\TestCase;
use Testcontainers\Module\MongoDb\MongoDbContainer;

/**
 * @internal
 */
final class MongoDbContainerTest extends TestCase
{
    private static MongoDbContainer $container;

    public static function setUpBeforeClass(): void
    {
        if (extension_loaded('mongodb') === false) {
            self::markTestSkipped('The ext-mongodb extension is not installed/enabled.');
        }

        self::$container = new MongoDbContainer();
        self::$container->start();
    }

    protected function tearDown(): void
    {
        self::$container->stop();
    }

    public function testCRUD()
    {
        $client = new Client(self::$container->getUri());
        $collection = $client->selectDatabase(self::$container->database)->selectCollection('test');

        $result = $collection->insertOne(['_id' => 1, 'foo' => 1]);
        self::assertSame(1, $result->getInsertedCount());

        $document = $collection->findOne(['_id' => 1]);
        self::assertSame(1, $document['foo']);

        $result = $collection->updateOne(['_id' => 1], ['$set' => ['foo' => 2]]);
        self::assertSame(1, $result->getModifiedCount());

        $document = $collection->findOne(['_id' => 1]);
        self::assertSame(2, $document['foo']);

        $result = $collection->deleteOne(['_id' => 1]);
        self::assertSame(1, $result->getDeletedCount());

        $document = $collection->findOne(['_id' => 1]);
        self::assertNull($document);
    }
}
