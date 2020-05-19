<?php
namespace CarloNicora\Minimalism\Modules\JsonApi\Web\tests\unit;

use CarloNicora\Minimalism\Modules\JsonApi\Web\tests\Abstracts\AbstractTestCase;

class controllerTest extends abstractTestCase
{
    public function testControllerViewInitialisation() : void
    {
        $controller = $this->generateMockController();
        $this->invokeMethod($controller, 'initialiseView');
    }
}