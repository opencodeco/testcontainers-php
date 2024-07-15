---
currentMenu: home
---
# Testcontainers for PHP

Testcontainers is a PHP library that supports PHPUnit tests, providing lightweight, throwaway instances of common databases, Selenium web browsers, or anything else that can run in a Docker container.

## Getting started

### Installation

```shell
composer require opencodeco/testcontainers
```

### Usage

You can create containers using PHP, from a variety of modules, or just using `GenericContainer` and use them as actual infrastructure components.

> [!NOTE]
> If you are using Docker Desktop and would like to run testcontainers inside a container, you need to configure the ```TESTCONTAINERS_HOST_OVERRIDE``` environment variable to use the special DNS name ```host.docker.internal``` for accessing the host from within a container, which is provided by Docker Desktop.

#### Example

##### Hello, World!

```php
$redis_container = (new \Testcontainers\GenericContainer('redis:alpine'))
    ->withExposedPorts('6379/tcp')
    ->start();

$redis_client = new \Predis\Client([
    'host' => $redis_container->getHost(),
    'port' => $redis_container->getFirstMappedPort(),
]);

$redis_client->set('greetings', 'Hello, World!');
echo $redis_client->get('greetings');
```

##### PHPUnit

Using the built-in Redis module.

```php
final class RedisContainerTest extends TestCase
{
    private static TestContainer $redisContainer;

    /**
     * @throws TestContainerException
     */
    public static function setUpBeforeClass(): void
    {
        self::$redisContainer = new RedisContainer();
        self::$redisContainer->start();
    }

    /**
     * @throws TestContainerException
     */
    public static function tearDownAfterClass(): void
    {
        self::$redisContainer->stop();
    }

    public function testConnectSetAndGet(): void
    {
        $redis_client = new Client([
            'host' => self::$redisContainer->getHost(),
            'port' => self::$redisContainer->getFirstMappedPort(),
        ]);

        $redis_client->set('testcontainers', 'php');

        $this->assertSame('php', $redis_client->get('testcontainers'));
    }
}
```