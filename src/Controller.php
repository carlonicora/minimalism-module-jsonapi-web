<?php
namespace CarloNicora\Minimalism\Modules\JsonApi\Web;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractWebController;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ApiModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Modules\JsonApi\Events\JsonApiInfoEvents;
use CarloNicora\Minimalism\Modules\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Modules\JsonApi\Web\Abstracts\AbstractModel;
use RuntimeException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Exception;

class Controller extends AbstractWebController {
    /** @var Environment */
    private Environment $view;

    /** @var ModelInterface|ApiModelInterface|AbstractModel */
    protected ModelInterface $model;

    /**
     *
     * @throws serviceNotFoundException
     */
    protected function initialiseView(): void {
        /** @var AbstractModel $model */
        $model = $this->model;
        if ($model->getViewName() !== '') {
            try {
                $twigLoader = new FilesystemLoader($this->services->paths()->getRoot()
                    . DIRECTORY_SEPARATOR . 'src'
                    . DIRECTORY_SEPARATOR . 'views');
                $this->view = new Environment($twigLoader);
            } catch (Exception $e) {
                throw new RuntimeException('View failure: ' . $e->getMessage(), 404);
            }
        }
        $this->services->logger()->info()->log(JsonApiInfoEvents::TWIG_ENGINE_INITIALISED());
    }

    /**
     * @return ControllerInterface
     */
    public function postInitialise(): ControllerInterface
    {
        return $this;
    }

    /**
     * @return Response
     */
    public function render() : ResponseInterface
    {
        try {
            $this->preRender();

            $this->services->logger()->info()->log(JsonApiInfoEvents::PRE_RENDER_COMPLETED());

            /** @var JsonApiResponse $response */
            $response = $this->model->generateData();

            $this->services->logger()->info()->log(JsonApiInfoEvents::DATA_GENERATED());

            if ($this->model->getViewName() !== '') {
                try {
                    foreach ($this->model->getTwigExtensions() ?? [] as $twigExtension){
                        $this->view->addExtension($twigExtension);
                    }
                    $response->setData($this->view->render($this->model->getViewName() . '.twig', $response->getDocument()->prepare()));


                    $this->services->logger()->info()->log(JsonApiInfoEvents::DATA_MERGED($this->model->getViewName()));
                } catch (Exception $e) {
                    $response = $this->model->generateResponseFromError(new Exception(ResponseInterface::HTTP_STATUS_500, 'Failed to render the view'));
                }
            }
        } catch (Exception $e) {
            $response = $this->model->generateResponseFromError($e);
        }

        $response->setContentType('text/html');

        $this->completeRender($response->getStatus(), $response->getData());

        $this->services->logger()->info()->log(JsonApiInfoEvents::RENDER_COMPLETE());

        return $response;
    }
}