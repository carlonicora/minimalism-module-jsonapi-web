<?php
namespace CarloNicora\Minimalism\Modules\JsonApi\Web\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\Error;
use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractWebModel;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Modules\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Modules\JsonApi\Traits\JsonApiModelTrait;
use CarloNicora\Minimalism\Services\Encrypter\Encrypter;
use CarloNicora\Minimalism\Services\Encrypter\ParameterValidator\Decrypter;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\DecrypterInterface;
use Exception;
use RuntimeException;
use Twig\Extension\ExtensionInterface;

abstract class AbstractModel extends AbstractWebModel {
    use JsonApiModelTrait;

    /** @var Document  */
    protected Document $document;

    /** @var Encrypter */
    protected Encrypter $encrypter;

    /** @var array  */
    private array $twigExtensions = [];

    /** @var Error|null  */
    private ?Error $error=null;

    /**
     * AbstractModel constructor.
     * @param ServicesFactory $services
     * @throws Exception
     */
    public function __construct(ServicesFactory $services)
    {
        parent::__construct($services);

        $this->document = new document();
        try {
            $this->document->meta->add('url', $this->services->paths()->getUrl());
        } catch (Exception $e) {}
    }

    /**
     * @param array $passedParameters
     * @param array|null $file
     * @throws Exception
     */
    public function initialise(array $passedParameters, array $file = null): void
    {
        $this->encrypter = $this->services->service(Encrypter::class);

        parent::initialise($passedParameters, $file);
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
     * @return DecrypterInterface
     */
    public function decrypter(): DecrypterInterface
    {
        return new Decrypter($this->encrypter);
    }

    /**
     * @return responseInterface
     */
    public function generateData() : ResponseInterface{
        return new JsonApiResponse();
    }

    /**
     * @throws Exception
     */
    public function preRender() : void {
        if ($this->error !== null) {
            $errorArray = $this->error->prepare();
            throw new RuntimeException($errorArray['detail'], $errorArray['status']);
        }
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