<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomerServiceException;
use App\Exceptions\DalionException;
use App\Exceptions\SberException;
use App\Exceptions\TransactionServiceException;
use App\Http\Requests\RegisterOrder as RegisterOrderRequest;
use App\Services\CustomerService;
use App\Services\Dalion\DalionApi;
use App\Services\Log;
use App\Services\TransactionService;
use Exception;
use Illuminate\Http\JsonResponse;

class RegisterOrder extends Controller {

    private Log $log;
    public function __construct(Log $log) {
        $this->log = $log;
    }

    public function canReserveCar(string $uuid, DalionApi $dalion): JsonResponse {
        $canReserve = $dalion->carAvailableForReserve($uuid);
//        $this->log->reserveRequest($uuid, $canReserve); // чтоб не спамил логи
        return response()->json($canReserve);
    }

    public function getPaymentFormUrl(
        RegisterOrderRequest $request,
        DalionApi $dalion,
        CustomerService $customerService,
        TransactionService $transactionService
    ): JsonResponse {
        $this->log->getPaymentFormUrl($request);

        try {
            $transaction = $transactionService->registerNewTransaction($request);
        } catch (TransactionServiceException $e) {
            return $this->error($e);
        }

        try {
            $customerService->checkCustomer($transaction->customer);
        } catch (CustomerServiceException $e) {
            $transactionService->setStatus($transaction, $e->getMessage());
            return $this->error($e, $request->query('customer'));
        }

        try {
            $dalionResponse = $dalion->holdCarAndCreateContract(
                $transaction,
                $request->query('product')['price']
            );
        } catch (DalionException $e) {
            $transactionService->setStatus($transaction, $e->getMessage());
            return $this->error($e, [$request->query('product')['uuid']]);
        }

        try {
            $formUrl = $transactionService->transact($transaction, $request, $dalionResponse->receiptData);
        } catch (SberException $e) {
            $transactionService->setStatus($transaction, $e->getMessage());
            $dalion->transactionFailed($transaction);
            return $this->error($e, $request->all());
        }

        $transactionService->setStatus($transaction, 'registered');
        return $this->success($formUrl);
    }

    protected function error(Exception $e, array $context = []): JsonResponse {
        $this->log->paymentError($e, $context);

        return response()->json([
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ], 400);
    }

    protected function success(string $formUrl): JsonResponse {
        $this->log->paymentSuccess($formUrl);

        return response()->json([
            'formUrl' => $formUrl
        ]);
    }
}
