<?php
namespace carlonicora\minimalism\modules\jsonapi\web\tests\unit;

use carlonicora\jsonapi\document;
use carlonicora\minimalism\modules\jsonapi\web\extensions\twigExtensions;
use carlonicora\minimalism\modules\jsonapi\web\tests\abstracts\abstractTestCase;
use carlonicora\minimalism\modules\jsonapi\web\tests\traits\arraysTrait;
use Exception;

class twigExtensionsTest extends abstractTestCase
{
    use arraysTrait;

    public function testInitialiseTwigExtensions() : void
    {
        $object = new twigExtensions();

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
        $object = new twigExtensions();

        $this->assertEquals($this->objectUserWithRelationship, $object->included($documentArray['included'], $this->objectUserWithRelationship));
    }

    public function testIncludedNoFound() : void
    {
        $object = new twigExtensions();

        $this->assertEquals([], $object->included($this->jsonApiDocumentComplete['included'], $this->objectUserNonExisting));
    }

    /**
     * @throws Exception
     */
    public function testIncludedTypeId() : void
    {
        $document = new document($this->jsonApiDocumentComplete);
        $object = new twigExtensions();

        $this->assertEquals($this->objectUserWithRelationship, $object->includedTypeId($this->jsonApiDocumentComplete['included'], 'user', '10'));
    }

    public function testIncludedTypeIdNotFound() : void
    {
        $object = new twigExtensions();

        $this->assertEquals([], $object->includedTypeId($this->jsonApiDocumentComplete['included'], 'nonExistingType', '0'));

    }
}