<?php

use PHPUnit\Framework\TestCase;
use App\QueueManager; 

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../QueueManager.php';

class QueueManagerTest extends TestCase
{
    public function testIsQueueManagerCanBeCreated()
    {
        $manager = new QueueManager();
        $this->assertInstanceOf(QueueManager::class, $manager);
    }

    public function testPublishWithMock()
    {
        $mock = $this->getMockBuilder(\App\QueueManager::class)
                     ->disableOriginalConstructor() // Не подключаемся к реальному RabbitMQ
                     ->onlyMethods(['publish'])     // Мокаем метод publish
                     ->getMock();

        $mock->expects($this->once())
             ->method('publish')
             ->with($this->equalTo(['test' => 'data']));

        $mock->publish(['test' => 'data']);
    }
}
