<?php
namespace carlonicora\minimalism\modules\jsonapi\web\abstracts;

use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\modules\jsonapi\abstracts\abstractJsonApiModel;
use carlonicora\minimalism\modules\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\services\paths\factories\serviceFactory;
use carlonicora\minimalism\services\paths\paths;

abstract class abstractModel extends abstractJsonApiModel {
    /** @var string */
    protected string $viewName='';

    /**
     * abstractWebModel constructor.
     * @param servicesFactory $services
     * @param array $passedParameters
     * @param array $file
     * @throws serviceNotFoundException
     */
    public function __construct(servicesFactory $services, array $passedParameters, array $file=null){
        parent::__construct($services, $passedParameters, $file);

        /** @var paths $paths */
        $paths = $this->services->service(serviceFactory::class);
        $this->response->addMeta('url', $paths->getUrl());
    }

    /**
     * @return responseInterface
     */
    public function generateData(): responseInterface{
        return $this->response;
    }

    /**
     * @return string
     */
    public function getViewName(): string {
        return $this->viewName;
    }
}