<?php
namespace carlonicora\minimalism\modules\jsonapi\web;

use carlonicora\jsonapi\document;
use carlonicora\jsonapi\objects\error;
use carlonicora\jsonapi\response;
use carlonicora\minimalism\core\modules\abstracts\controllers\abstractWebController;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\modules\jsonapi\web\abstracts\abstractModel;
use carlonicora\minimalism\services\paths\paths;
use JsonException;
use RuntimeException;
use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Exception;

class controller extends abstractWebController {
    /** @var Environment */
    private Environment $view;

    /**
     *
     * @throws serviceNotFoundException
     */
    protected function initialiseView(): void {
        /** @var paths $paths */
        $paths = $this->services->service(paths::class);

        /** @var abstractModel $model */
        $model = $this->model;
        if ($model->getViewName() !== '') {
            try {
                $twigLoader = new FilesystemLoader($paths->getRoot() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'views');
                $this->view = new Environment($twigLoader);
            } catch (Exception $e) {
                throw new RuntimeException('View failure: ' . $e->getMessage(), 404);
            }
        }
        $this->logger->addSystemEvent(null, 'Twig engine initialised');
    }

    /**
     * @return string
     * @throws JsonException
     */
    public function render(): string{
        $document = new document();

        $response = null;

        if (($error = $this->model->preRender()) !== null){
            $document->addError($error);
        } else {
            $this->logger->addSystemEvent(null, 'Pre-render completed');

            $this->preRender();

            /** @var document $document */
            $document = $this->model->generateData();

            $this->logger->addSystemEvent(null, 'Data generated');

            if ($this->model->getViewName() !== '') {
                try {
                    foreach ($this->model->getTwigExtensions() ?? [] as $twigExtension){
                        $this->view->addExtension($twigExtension);
                    }
                    $response = $this->view->render($this->model->getViewName() . '.twig', $document->prepare());

                    $this->logger->addSystemEvent(null, 'Data merged with view');
                } catch (Exception $e) {
                    $document = new document();
                    $document->addError(new error(responseInterface::HTTP_STATUS_500, 'Failed to render the view'));
                }
            }
        }

        if ($response === null){
            $response = $document->export();
        }

        $responseObject = new response();
        $responseObject->document = $document;

        $responseObject->renderHeaders();

        $this->completeRender($responseObject->httpStatus, $response);

        $this->logger->addSystemEvent(null, 'Render completed');

        return $response;
    }

    /**
     * @param Throwable $e
     * @param string $httpStatusCode
     * @return void
     * @throws JsonException
     */
    public function writeException(Throwable $e, string $httpStatusCode = '500'): void {
        $response = new response();
        $response->document->addError(new error($httpStatusCode, $e->getMessage(), $e->getCode()));

        $response->renderHeaders();

        echo $response->document->export();
    }
}