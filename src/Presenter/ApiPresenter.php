<?php

namespace Kelemen\ApiNette\Presenter;

use Kelemen\ApiNette\Api;
use Kelemen\ApiNette\Exception\ApiNetteException;
use Kelemen\ApiNette\Exception\UnresolvedRouteException;
use Kelemen\ApiNette\Exception\ValidationFailedException;
use Kelemen\ApiNette\Logger\Logger;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;
use Kelemen\ApiNette\Response\JsonApiResponse;
use Tracy\Debugger;
use Exception;

class ApiPresenter extends Presenter
{
    /** @var Api */
    private $api;

    /** @var Logger */
    private $logger;

    /**
     * @param Api $api
     * @param Logger $logger
     */
    public function __construct(Api $api, Logger $logger)
    {
        parent::__construct();
        $this->api = $api;
        $this->logger = $logger;
    }

    /**
     * Run api handling
     * @param $params
     */
    public function actionDefault($params)
    {
        $this->logger->start();

        try {
            $response = $this->api->run($params, $this->logger);
        } catch (UnresolvedRouteException $e) {
            $response = new JsonApiResponse(IResponse::S400_BAD_REQUEST, ['error' => 'Unresolved api route']);
        } catch (ValidationFailedException $e) {
            $response = new JsonApiResponse(IResponse::S400_BAD_REQUEST, [
                'error' => 'Bad input parameter',
                'errors' => $e->getValidator()->getErrors()
            ]);
        } catch (ApiNetteException $e) { // UnresolvedHandlerException, UnresolvedMiddlewareException, ValidatorException
            Debugger::log($e, 'apiError');
            $response = new JsonApiResponse(IResponse::S500_INTERNAL_SERVER_ERROR, ['error' => 'Internal server error']);
        } catch (Exception $e) {
            Debugger::log($e, 'error');
            $response = new JsonApiResponse(IResponse::S500_INTERNAL_SERVER_ERROR, ['error' => 'Internal server error']);
        }
        $this->logger->finish($response);
        $this->sendResponse($response);
    }
}
