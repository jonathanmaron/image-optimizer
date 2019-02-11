<?php
declare(strict_types=1);

namespace ApplicationTest\History;

use Application\History\Entity;

class EntityTest extends AbstractTestCase
{
    protected $entity;

    protected function setUp(): void
    {
        $filename = $this->getTestAssetFilename();

        $this->entity = new Entity($filename);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->entity);

        parent::tearDown();
    }

    public function testGetId(): void
    {
        $expected = '0a645e6aff6887af8a2bdeb877e5304a4815340e743cb2d52a134acdd57a8c57';
        $actual   = $this->entity->getId();

        $this->assertEquals($expected, $actual);
    }

    public function testGetFilesize(): void
    {
        $expected = 2495270;
        $actual   = $this->entity->getFilesize();

        $this->assertEquals($expected, $actual);
    }

    public function testGetFilemtime(): void
    {
        $expected = 1549259198;
        $actual   = $this->entity->getFilemtime();

        $this->assertEquals($expected, $actual);
    }

    public function testGetFilename(): void
    {
        $expected = $this->getTestAssetFilename();;
        $actual   = $this->entity->getFilename();

        $this->assertEquals($expected, $actual);
    }
}
