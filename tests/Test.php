<?php

namespace App\Tests;



use PHPUnit\Framework\TestCase;
use CadotInfo\testTools;


class Test  extends TestCase
{
    use testTools;
    public function testOptNotRecognized(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ($this->getLinks('<!DOCTYPE html><html><a href="https://github.com/cadot-info/testTools">github</a></html>', 0, ['test' => 'test']));
        throw new \InvalidArgumentException();
    }
    public function testUrl(): void
    {
        $this->assertGreaterThan(1, $this->getLinks('https://github.com'));
    }
    public function testOne(): void
    {
        $this->assertCount(1, $this->getLinks('<!DOCTYPE html><html><a href="https://github.com/cadot-info/testTools">github</a></html>', 0));
    }
    public function testRecursivity(): void
    {
        $this->assertGreaterThan(2, $this->getLinks('<!DOCTYPE html><html><a href="https://github.com/cadot-info/testTools">github</a></html>', 1));
    }
    public function testcontrolhttps(): void
    {
        //control https exists
        $this->assertCount(1, $this->getLinks('<!DOCTYPE html><html><a href="https://github.com/cadot-info/testTools">github</a></html>', 0, ['2points' => []]));
    }
    public function testcontrolnohttps(): void
    {
        //control remove this links
        $this->assertCount(0, $this->getLinks('<!DOCTYPE html><html><a href="https://github.com/cadot-info/testTools">github</a></html>', 1, ['2points' => ['https']]));
    }
    public function testcontrolsublink(): void
    {
        //control refuse sub links with tests
        $this->assertGreaterThan(2, $this->getLinks('<!DOCTYPE html><html><a href="https://github.com/cadot-info/testTools">github</a></html>', 1, ['2points' => ['https'], 'pass' => true]));
    }
    public function testpoint(): void
    {
        $this->assertCount(0, $this->getLinks('<!DOCTYPE html><html><a href="https://github.com/cadot-info/testTools">github</a></html>', 1, ['point' => ['https://github']]));
    }
    public function testclass(): void
    {
        $this->assertCount(0, $this->getLinks('<!DOCTYPE html><html><a href="https://github.com/cadot-info/testTools" class="test">github</a></html>', 0, ['class' => ['test']]));
    }
    public function testempty(): void
    {
        $this->assertCount(0, $this->getLinks('<!DOCTYPE html><html><a href="" >github</a></html>', 0));
    }
    public function testiholdlink(): void
    {
        $this->assertCount(1, $this->getLinks('<!DOCTYPE html><html></html>', 0, [], ['https://github.com' => 'github']));
    }
    public function testihavelink(): void
    {
        $this->assertCount(2, $this->getLinks('<!DOCTYPE html><html><a href="https://github.com" >github</a><a href="toto">toto</a></html>', 0, [], ['https://github.com' => 'github']));
    }
    public function testbegin(): void
    {
        $this->assertCount(0, $this->getLinks('<!DOCTYPE html><html><a href="https://github.com/cadot-info/testTools" class="test">github</a></html>', 0, ['begin' => ['https://github']]));
    }
    public function testfinish(): void
    {
        $this->assertCount(0, $this->getLinks('<!DOCTYPE html><html><a href="https://github.com/cadot-info/testTools" class="test">github</a></html>', 0, ['finish' => ['/testTools']]));
    }
}
