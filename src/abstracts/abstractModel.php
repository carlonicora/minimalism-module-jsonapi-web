<?php
namespace carlonicora\minimalism\modules\jsonapi\web\abstracts;

use carlonicora\jsonapi\document;
use carlonicora\jsonapi\objects\error;
use carlonicora\minimalism\core\modules\abstracts\models\abstractWebModel;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\encrypter\encrypter;
use carlonicora\minimalism\services\paths\paths;
use Exception;
use Twig\Extension\ExtensionInterface;

abstract class abstractModel extends abstractWebModel {
    /** @var document  */
    protected document $document;

    /** @var error|null  */
    protected ?error $error=null;

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

        $this->document = new document();

        /** @var paths $paths */
        $paths = $this->services->service(paths::class);
        $this->document->meta->add('url', $paths->getUrl());
    }

    /**
     * @param ExtensionInterface $extension
     */
    public function addTwigExtension(ExtensionInterface $extension): void {
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

    /*
     * @return responseInterface
     */
    public function generateData(): document{
        return $this->document;
    }

    /**
     * @return error|null
     */
    public function preRender() : ?error {
        return $this->error;
    }

    /**
     * @param $parameter
     * @return document
     * @throws Exception
     */
    public function validateJsonapiParameter($parameter): document{
        return new document($parameter);
    }
}