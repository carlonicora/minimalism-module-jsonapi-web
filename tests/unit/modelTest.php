<?php
namespace carlonicora\minimalism\modules\jsonapi\web\tests\unit;

use carlonicora\minimalism\modules\jsonapi\web\abstracts\abstractModel;
use carlonicora\minimalism\modules\jsonapi\web\tests\abstracts\abstractTestCase;
use carlonicora\minimalism\modules\jsonapi\web\tests\traits\arraysTrait;
use PHPUnit\Framework\MockObject\MockObject;

class modelTest extends abstractTestCase
{
    use arraysTrait;

    public function testModelInitialisation(): MockObject
    {
        $model = $this->getMockForAbstractClass(
            abstractModel::class,
            [$this->servicesFactory, [], null]
        );

        $this->assertNull($model->redirectPage);

        return $model;
    }

    /**
     * @param MockObject|abstractModel $model
     * @depends testModelInitialisation
     */
    public function testValidateJsonapiParameterSimpleObject(MockObject $model): void
    {
        $object = $model->validateJsonapiParameter($this->jsonApiDocumentSimple);

        $this->assertEquals('carlo', $object->data->attributes['name']);
    }

    /**
     * @param MockObject|abstractModel $model
     * @depends testModelInitialisation
     */
    public function testValidateParameterDecryptionSimpleObject(MockObject $model): void
    {
        $object = $model->validateJsonapiParameter($this->jsonApiDocumentSimple);

        $this->assertEquals(1, $model->decryptParameter($object->data->id));
    }

    /**
     * @param MockObject|abstractModel $model
     * @depends testModelInitialisation
     */
    public function testNullPreRender(MockObject $model): void
    {
        $this->assertNull($model->preRender());
    }
}