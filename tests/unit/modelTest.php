<?php
namespace CarloNicora\Minimalism\Modules\JsonApi\Web\tests\unit;

use carlonicora\JsonApi\Document;
use CarloNicora\Minimalism\Modules\JsonApi\Web\AAbstracts\AbstractModel;
use CarloNicora\Minimalism\Modules\JsonApi\Web\tests\Abstracts\AbstractTestCase;
use CarloNicora\Minimalism\Modules\JsonApi\Web\tests\traits\arraysTrait;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Twig\Extension\ExtensionInterface;

class modelTest extends abstractTestCase
{
    use arraysTrait;

    public function testModelInitialisation(): MockObject
    {
        $model = $this->getMockForAbstractClass(
            AbstractModel::class,
            [$this->servicesFactory, [], null]
        );

        $this->assertNull($model->redirectPage);

        return $model;
    }

    /**
     * @param MockObject|AbstractModel $model
     * @depends testModelInitialisation
     * @throws Exception
     */
    public function testValidateJsonapiParameterSimpleObject(MockObject $model): void
    {
        $object = $model->validateJsonapiParameter($this->jsonApiDocumentSimple);

        $this->assertEquals('carlo', $object->resources[0]->attributes->get('name'));
    }

    /**
     * @param MockObject|AbstractModel $model
     * @depends testModelInitialisation
     * @throws Exception
     */
    public function testValidateParameterDecryptionSimpleObject(MockObject $model): void
    {
        $object = $model->validateJsonapiParameter($this->jsonApiDocumentSimple);


        $this->assertEquals(1, $model->decryptParameter($object->resources[0]->id));
    }

    /**
     * @param MockObject|AbstractModel $model
     * @depends testModelInitialisation
     */
    public function testNullPreRender(MockObject $model): void
    {
        $this->assertNull($model->preRender());
    }

    /**
     * @param MockObject|AbstractModel $model
     * @depends testModelInitialisation
     */
    public function testAddTwigExtension(MockObject $model): void {
        /** @var ExtensionInterface $extension */
        $extension = $this->extension;
        $model->addTwigExtension($extension);

        $this->assertCount(1, $model->getTwigExtensions());
    }

    /**
     * @param MockObject|AbstractModel $model
     * @depends testModelInitialisation
     * @throws Exception
     */
    public function testGenerateEmptyData(MockObject $model): void {
        $object = new document();
        $object->meta->add('url', '');

        $this->assertEquals($object, $model->generateData());
    }
}