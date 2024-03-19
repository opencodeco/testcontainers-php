<?php

declare(strict_types=1);

namespace Test\Testcontainers\Module;

use AMQPChannel;
use AMQPEnvelope;
use AMQPExchange;
use AMQPQueue;
use PHPUnit\Framework\TestCase;
use Testcontainers\Module\RabbitMQ\RabbitMQContainer;

/**
 * @internal
 */
final class RabbitMQContainerTest extends TestCase
{
    private static RabbitMQContainer $container;

    private AMQPExchange $exchange;

    private AMQPQueue $queue;

    public static function setUpBeforeClass(): void
    {
        if (extension_loaded('amqp') === false) {
            self::markTestSkipped('The amqp extension is not installed/enabled.');
        }

        self::$container = new RabbitMQContainer();
        self::$container->start();
    }

    protected function tearDown(): void
    {
        self::$container->stop();
    }

    public function testCreateExchange(): void
    {
        $this->exchange = new AMQPExchange($this->getChannel());
        $this->exchange->setName('testExchange');
        $this->exchange->setFlags(AMQP_DURABLE);
        $this->exchange->setType(AMQP_EX_TYPE_DIRECT);

        self::assertTrue($this->exchange->declareExchange());
    }

    /**
     * @depends testCreateExchange
     */
    public function testCreateAndBindQueue(): void
    {
        $this->queue = new AMQPQueue($channel);
        $this->queue->setName('testQueue');
        $this->queue->setFlags(AMQP_DURABLE);

        self::assertSame(0, $this->queue->declareQueue());
    }

    /**
     * @depends testCreateExchange
     * @depends testCreateAndBindQueue
     */
    public function testPublishAndConsume(): void
    {
        self::assertTrue($this->exchange->publish('testMessage', 'routingKey'));

        $message = $this->queue->get();
        self::assertInstanceOf(AMQPEnvelope::class, $message);
        self::assertSame('testMessage', $message->getBody());
        self::assertSame('routingKey', $message->getRoutingKey());
    }

    private function getChannel(): AMQPChannel
    {
        $amqp = self::$container->createAmqp();
        if ($amqp->isConnected() === false) {
            $amqp->connect();
        }

        return new AMQPChannel($amqp);
    }
}
