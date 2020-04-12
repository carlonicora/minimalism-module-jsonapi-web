<?php
namespace carlonicora\minimalism\modules\jsonapi\web\abstracts;

use carlonicora\minimalism\core\modules\abstracts\models\abstractWebModel;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\services\jsonapi\responses\dataResponse;
use carlonicora\minimalism\services\jsonapi\responses\errorResponse;
use carlonicora\minimalism\services\paths\paths;

abstract class abstractModel extends abstractWebModel {
    /** @var dataResponse  */
    protected dataResponse $response;

    /** @var errorResponse|null  */
    protected ?errorResponse $error=null;
    /**
     * abstractWebModel constructor.
     * @param servicesFactory $services
     * @param array $passedParameters
     * @param array $file
     * @throws serviceNotFoundException
     */
    public function __construct(servicesFactory $services, array $passedParameters, array $file=null){
        parent::__construct($services, $passedParameters, $file);

        $this->response = new dataResponse();

        /** @var paths $paths */
        $paths = $this->services->service(paths::class);
        $this->response->addMeta('url', $paths->getUrl());
    }

    /**
     * @return responseInterface
     */
    public function generateData(): responseInterface{
        return $this->response;
    }

    /**
     * @return errorResponse|null
     */
    public function preRender() : ?errorResponse {
        return $this->error;
    }
}