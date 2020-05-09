<?php
namespace carlonicora\minimalism\modules\jsonapi\web\abstracts;

use carlonicora\minimalism\core\modules\abstracts\models\abstractWebModel;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\modules\jsonapi\web\extensions\twigExtensions;
use carlonicora\minimalism\services\encrypter\encrypter;
use carlonicora\minimalism\services\jsonapi\interfaces\jsonapiModelInterface;
use carlonicora\minimalism\services\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\services\jsonapi\jsonApiDocument;
use carlonicora\minimalism\services\jsonapi\resources\errorObject;
use carlonicora\minimalism\services\jsonapi\traits\modelTrait;
use carlonicora\minimalism\services\paths\paths;
use Exception;
use Twig\Extension\ExtensionInterface;

abstract class abstractModel extends abstractWebModel implements jsonapiModelInterface {
    use modelTrait;

    /** @var jsonApiDocument  */
    protected jsonApiDocument $response;

    /** @var errorObject|null  */
    protected ?errorObject $error=null;

    /** @var array  */
    private array $twigExtensions = [];

    /**
     * abstractWebModel constructor.
     * @param servicesFactory $services
     * @param array $passedParameters
     * @param array $file
     * @throws serviceNotFoundException
     * @throws Exception
     */
    public function __construct(servicesFactory $services, array $passedParameters, array $file=null){
        parent::__construct($services, $passedParameters, $file);

        $this->response = new jsonApiDocument();

        /** @var paths $paths */
        $paths = $this->services->service(paths::class);
        $this->response->addMeta('url', $paths->getUrl());

        $this->twigExtensions[] = new twigExtensions();
    }

    /**
     * @param ExtensionInterface $extension
     */
    protected function addTwigExtension(ExtensionInterface $extension): void {
        $this->twigExtensions[] = $extension;
    }

    /**
     * @return array
     */
    public function getTwigExtensions(): array {
        return $this->twigExtensions;
    }

    /**
     * @param string $parameter
     * @return string
     * @throws serviceNotFoundException
     */
    public function decryptParameter(string $parameter) : string {
        /** @var encrypter $encrypter */
        $encrypter = $this->services->service(encrypter::class);

        return $encrypter->decryptId($parameter);
    }

    /**
     * @return responseInterface
     */
    public function generateData(): responseInterface{
        return $this->response;
    }

    /**
     * @return errorObject|null
     */
    public function preRender() : ?errorObject {
        return $this->error;
    }

    /**
     * @param $parameter
     * @return jsonApiDocument
     */
    public function validateJsonapiParameter($parameter): jsonApiDocument{
        return new jsonApiDocument($parameter);
    }
}