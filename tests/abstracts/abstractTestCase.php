<?php
namespace CarloNicora\Minimalism\Modules\JsonApi\Web\tests\Abstracts;

use CarloNicora\Minimalism\core\Modules\Abstracts\Models\AbstractModel;
use CarloNicora\Minimalism\core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Modules\JsonApi\Web\Controller;
use CarloNicora\Minimalism\Services\Encrypter\Encrypter;
use CarloNicora\Minimalism\Services\logger\logger;
use CarloNicora\Minimalism\Services\paths\paths;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Twig\Extension\ExtensionInterface;

abstract class abstractTestCase extends TestCase
{
    /** @var MockObject */
    protected MockObject $servicesFactory;

    /** @var MockObject */
    protected MockObject $encrypterService;

    /** @var MockObject */
    protected MockObject $loggerService;

    /** @var MockObject */
    protected MockObject $pathsService;

    /** @var MockObject  */
    protected MockObject $extension;

    /**
     * abstractTestCase constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->servicesFactory = $this->getMockBuilder(servicesFactory::class)
            ->getMock();

        $this->initialiseEncrypterService();

        $this->loggerService = $this->getMockBuilder(logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pathsService = $this->getMockBuilder(paths::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->servicesFactory->method('service')
            ->with($this->logicalOr(
                encrypter::class,
                logger::class,
                paths::class
            ))
            ->willReturn($this->returnCallback([$this, 'returnService']));

        $this->extension = $this->getMockBuilder(ExtensionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject
     */
    protected function generateMockController() : MockObject {
        /** @var Controller $controller */
        $controller = $this->getMockBuilder(Controller::class)
            ->disableOriginalConstructor()
            ->getMock();

        $model = $this->getMockBuilder(abstractModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setAttribute($controller, 'model', $model);

        /** @var MockObject $controller */
        return $controller;
    }

    /**
     * @param string $serviceName
     * @return MockObject
     */
    public function returnService(string $serviceName): MockObject
    {
        switch ($serviceName) {
            case encrypter::class:
                return $this->encrypterService;
                break;
            case logger::class:
                return $this->loggerService;
                break;
            case paths::class:
            default:
                return $this->pathsService;
                break;
        }
    }

    private function initialiseEncrypterService(): void
    {
        $this->encrypterService = $this->getMockBuilder(encrypter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->encrypterService->method('decryptId')->willReturn(1);
    }

    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        try {
            $reflection = new ReflectionClass(get_class($object));
            $method = $reflection->getMethod($methodName);
            $method->setAccessible(true);
            return $method->invokeArgs($object, $parameters);
        } catch (ReflectionException $e) {
            return null;
        }
    }

    protected function setAttribute(&$object, $attributeName, $value) : void
    {
        try {
            $reflection = new ReflectionClass(get_class($object));
            $attribute = $reflection->getProperty($attributeName);
            $attribute->setAccessible(true);
            $attribute->setValue($reflection, $value);
        } catch (ReflectionException $e) {
        }
    }
}