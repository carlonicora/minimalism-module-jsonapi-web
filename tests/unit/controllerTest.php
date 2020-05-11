<?php
namespace carlonicora\minimalism\modules\jsonapi\web\tests\unit;

use carlonicora\minimalism\modules\jsonapi\web\tests\abstracts\abstractTestCase;
use Exception;

class controllerTest extends abstractTestCase
{
    public function testControllerViewInitialisation() : void
    {
        $controller = $this->generateMockController();
        $this->invokeMethod($controller, 'initialiseView');
    }
}