<?php
namespace carlonicora\minimalism\modules\jsonapi\web\tests\unit;

use carlonicora\jsonapi\document;
use carlonicora\minimalism\modules\jsonapi\web\abstracts\abstractModel;
use carlonicora\minimalism\modules\jsonapi\web\tests\abstracts\abstractTestCase;
use carlonicora\minimalism\modules\jsonapi\web\tests\traits\arraysTrait;
use carlonicora\minimalism\services\jsonapi\jsonApiDocument;
use carlonicora\minimalism\services\jsonapi\resources\errorObject;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Test\Extension;
use Twig\Extension\ExtensionInterface;

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
     * @throws Exception
     */
    public function testValidateJsonapiParameterSimpleObject(MockObject $model): void
    {
        $object = $model->validateJsonapiParameter($this->jsonApiDocumentSimple);

        $this->assertEquals('carlo', $object->resources[0]->attributes->get('name'));
    }

    /**
     * @param MockObject|abstractModel $model
     * @depends testModelInitialisation
     * @throws Exception
     */
    public function testValidateParameterDecryptionSimpleObject(MockObject $model): void
    {
        $object = $model->validateJsonapiParameter($this->jsonApiDocumentSimple);


        $this->assertEquals(1, $model->decryptParameter($object->resources[0]->id));
    }

    /**
     * @param MockObject|abstractModel $model
     * @depends testModelInitialisation
     */
    public function testNullPreRender(MockObject $model): void
    {
        $this->assertNull($model->preRender());
    }

    /**
     * @param MockObject|abstractModel $model
     * @depends testModelInitialisation
     */
    public function testAddTwigExtension(MockObject $model): void {
        /** @var ExtensionInterface $extension */
        $extension = $this->extension;
        $model->addTwigExtension($extension);

        $this->assertCount(1, $model->getTwigExtensions());
    }

    /**
     * @param MockObject|abstractModel $model
     * @depends testModelInitialisation
     * @throws Exception
     */
    public function testGenerateEmptyData(MockObject $model): void {
        $object = new document();
        $object->meta->add('url', '');

        $this->assertEquals($object, $model->generateData());
    }
}