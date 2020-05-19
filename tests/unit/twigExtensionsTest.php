<?php
namespace CarloNicora\Minimalism\Modules\JsonApi\Web\tests\unit;

use carlonicora\JsonApi\Document;
use CarloNicora\Minimalism\Modules\JsonApi\Web\EExtensions\TTwigExtensions;
use CarloNicora\Minimalism\Modules\JsonApi\Web\tests\Abstracts\AbstractTestCase;
use CarloNicora\Minimalism\Modules\JsonApi\Web\tests\traits\arraysTrait;
use Exception;

class twigExtensionsTest extends abstractTestCase
{
    use arraysTrait;

    public function testInitialiseTwigExtensions() : void
    {
        $object = new TTwigExtensions();

        $response = $object->getFunctions();

        $this->assertCount(2, $response);
    }

    /**
     * @throws Exception
     */
    public function testIncluded() : void
    {
        $document = new document($this->jsonApiDocumentComplete);
        $documentArray = $document->prepare();
        $object = new TTwigExtensions();

        $this->assertEquals($this->objectUserWithRelationship, $object->included($documentArray['included'], $this->objectUserWithRelationship));
    }

    public function testIncludedNoFound() : void
    {
        $object = new TTwigExtensions();

        $this->assertEquals([], $object->included($this->jsonApiDocumentComplete['included'], $this->objectUserNonExisting));
    }

    /**
     * @throws Exception
     */
    public function testIncludedTypeId() : void
    {
        $object = new TTwigExtensions();

        $this->assertEquals($this->objectUserWithRelationship, $object->includedTypeId($this->jsonApiDocumentComplete['included'], 'user', '10'));
    }

    public function testIncludedTypeIdNotFound() : void
    {
        $object = new TTwigExtensions();

        $this->assertEquals([], $object->includedTypeId($this->jsonApiDocumentComplete['included'], 'nonExistingType', '0'));

    }
}