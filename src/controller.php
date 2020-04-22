<?php
namespace carlonicora\minimalism\modules\jsonapi\web;

use carlonicora\minimalism\core\modules\abstracts\controllers\abstractWebController;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\modules\jsonapi\web\abstracts\abstractModel;
use carlonicora\minimalism\services\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\services\jsonapi\responses\dataResponse;
use carlonicora\minimalism\services\jsonapi\responses\errorResponse;
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
        /** @var errorResponse $error  */
        if (($error = $this->model->preRender()) !== null){
            return $error->toJson();
        }

        $this->logger->addSystemEvent(null, 'Pre-render completed');

        $this->preRender();

        $response = null;

        /** @var responseInterface $data */
        $data = $this->model->generateData();

        $this->logger->addSystemEvent(null, 'Data generated');

        /**
        if (array_key_exists('forceRedirect', $data)) {
        header('Location:' . $data['forceRedirect']);
        exit;
        }
         */

        if ($this->model->getViewName() !== '') {
            try {
                /** @var abstractModel $model */
                $model = $this->model;
                foreach ($model->getTwigExtensions() ?? [] as $twigExtension){
                    $this->view->addExtension($twigExtension);
                }
                $response = $this->view->render($this->model->getViewName() . '.twig', $data->toArray());

                $this->logger->addSystemEvent(null, 'Data merged with view');
            } catch (Exception $e) {
                $data = new errorResponse(errorResponse::HTTP_STATUS_500, 'Failed to render the view');
            }
        }

        if ($response === null) {
            $response = $data->toJson();
        }

        $code = $data->getStatus();
        $GLOBALS['http_response_code'] = $code;
        header(dataResponse::generateProtocol() . ' ' . $code . ' ' . $data->generateText());

        $this->completeRender($code, $response);

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
        $error = new errorResponse($httpStatusCode, $e->getMessage(), $e->getCode());

        $code = $error->getStatus();
        $GLOBALS['http_response_code'] = $code;

        header(dataResponse::generateProtocol() . ' ' . $code . ' ' . $error->generateText());

        echo $error->toJson();
    }
}