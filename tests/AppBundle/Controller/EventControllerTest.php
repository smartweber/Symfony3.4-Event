<?php

namespace Tests\AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();

        // enables the profiler for the next request (it does nothing if the profiler is not available)
        $client->enableProfiler();
        $crawler = $client->request('GET', '/event');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Edit List', $crawler->filter('h1')->text());
    }
}
