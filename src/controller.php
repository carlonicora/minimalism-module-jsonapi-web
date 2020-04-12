<?php
namespace carlonicora\minimalism\modules\jsonapi\web;

use carlonicora\minimalism\core\modules\abstracts\controllers\abstractWebController;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\modules\jsonapi\web\abstracts\abstractModel;
use carlonicora\minimalism\services\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\services\jsonapi\responses\dataResponse;
use carlonicora\minimalism\services\jsonapi\responses\errorResponse;
use carlonicora\minimalism\services\paths\paths;
use RuntimeException;
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
    }

    /**
     * @return string
     */
    public function render(): string{
        /** @var errorResponse $error  */
        if (($error = $this->model->preRender()) !== null){
            return $error->toJson();
        }

        $response = null;

        /** @var responseInterface $data */
        $data = $this->model->generateData();

        /**
        if (array_key_exists('forceRedirect', $data)) {
        header('Location:' . $data['forceRedirect']);
        exit;
        }
         */

        if ($this->model->getViewName() !== '') {
            try {
                $response = $this->view->render($this->model->getViewName() . '.twig', $data->toArray());
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

        $this->completeRender();

        return $response;
    }

    /**
     * @param Exception $e
     * @return void
     */
    public function writeException(Exception $e): void {
        $error = new errorResponse($e->getCode() ?? 500, $e->getMessage());

        $code = $error->getStatus();
        $GLOBALS['http_response_code'] = $code;

        header(dataResponse::generateProtocol() . ' ' . $code . ' ' . $error->generateText());

        echo $error->toJson();
    }
}