<?php

declare(strict_types=1);

namespace Test\Testcontainers\Module;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Testcontainers\Module\RabbitMQ\RabbitMQContainer;

/**
 * @internal
 */
final class RabbitMQContainerTest extends TestCase
{
    private static RabbitMQContainer $container;

    private AMQPChannel $channel;

    public static function setUpBeforeClass(): void
    {
        self::$container = new RabbitMQContainer();
        self::$container->start();
    }

    public static function tearDownAfterClass(): void
    {
        self::$container->stop();
    }

    public function testCreateExchange(): void
    {
        $channel = $this->getChannel();
        self::assertNull($channel->exchange_declare('testExchange', AMQPExchangeType::DIRECT));
    }

    /**
     * @depends testCreateExchange
     */
    public function testCreateAndBindQueue(): void
    {
        $channel = $this->getChannel();

        self::assertEquals(['testQueue', 0, 0], $channel->queue_declare('testQueue'));
        self::assertNull($channel->queue_bind('testQueue', 'testExchange'));
    }

    /**
     * @depends testCreateExchange
     * @depends testCreateAndBindQueue
     */
    public function testPublishAndConsume(): void
    {
        $channel = $this->getChannel();
        $originalMessage = new AMQPMessage('test message', ['content_type' => 'text/plain']);

        $channel->basic_publish($originalMessage, 'testExchange');

        $receivedMessage = $channel->basic_get('testQueue');

        self::assertInstanceOf(AMQPMessage::class, $receivedMessage);
        self::assertSame($originalMessage->getBody(), $receivedMessage->getBody());
    }

    private function getChannel(): AMQPChannel
    {
        $connection = new AMQPStreamConnection(
            self::$container->getHost(),
            self::$container->getFirstMappedPort(),
            self::$container->user,
            self::$container->pass
        );

        return $connection->channel();
    }
}
