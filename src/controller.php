<?php
namespace carlonicora\minimalism\modules\jsonapi\web;

use carlonicora\minimalism\core\modules\abstracts\controllers\abstractWebController;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\modules\jsonapi\web\abstracts\abstractModel;
use carlonicora\minimalism\services\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\services\jsonapi\jsonApiDocument;
use carlonicora\minimalism\services\jsonapi\resources\errorObject;
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
        $jsonApiDocument = new jsonApiDocument();

        $response = null;

        /** @var errorObject $error  */
        if (($error = $this->model->preRender()) !== null){
            $jsonApiDocument->addError($error);
        } else {
            $this->logger->addSystemEvent(null, 'Pre-render completed');

            $this->preRender();

            /** @var jsonApiDocument $jsonApiDocument */
            $jsonApiDocument = $this->model->generateData();

            $this->logger->addSystemEvent(null, 'Data generated');

            if ($this->model->getViewName() !== '') {
                try {
                    foreach ($this->model->getTwigExtensions() ?? [] as $twigExtension){
                        $this->view->addExtension($twigExtension);
                    }
                    $response = $this->view->render($this->model->getViewName() . '.twig', $jsonApiDocument->toArray());

                    $this->logger->addSystemEvent(null, 'Data merged with view');
                } catch (Exception $e) {
                    $jsonApiDocument = new jsonApiDocument();
                    $jsonApiDocument->addError(new errorObject(responseInterface::HTTP_STATUS_500, 'Failed to render the view'));
                }
            }
        }

        if ($response === null){
            $response = $jsonApiDocument->toJson();
        }

        $GLOBALS['http_response_code'] = $jsonApiDocument->getStatus();
        header(jsonApiDocument::generateProtocol() . ' ' . $jsonApiDocument->getStatus() . ' ' . $jsonApiDocument->generateText());

        $this->completeRender($jsonApiDocument->getStatus(), $response);

        $this->logger->addSystemEvent(null, 'Render completed');

        return $jsonApiDocument->toJson();
    }

    /**
     * @param Throwable $e
     * @param string $httpStatusCode
     * @return void
     * @throws JsonException
     */
    public function writeException(Throwable $e, string $httpStatusCode = '500'): void {
        $response = new jsonApiDocument();
        $response->addError(new errorObject($httpStatusCode, $e->getMessage(), $e->getCode()));

        $GLOBALS['http_response_code'] = $response->getStatus();

        header(jsonApiDocument::generateProtocol() . ' ' . $response->getStatus() . ' ' . $response->generateText());

        echo $response->toJson();
    }
}