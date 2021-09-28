<?php


namespace App\Services;


use App\Models\Merchant;
use App\Models\Transaction;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;
use Throwable;

class Log {

    public const DEPOSIT = 'deposit';
    public const REVERSE = 'reverse';
    public const REFUND = 'refund';
    public const RECEIPT = 'getReceiptStatus';

    protected function log(): LoggerInterface {
        return \Illuminate\Support\Facades\Log::channel('payment');
    }

    public function reserveRequest(string $uuid, bool $canReserve): void {
        $this->log()
            ->info('Reserve car request from ' . Merchant::current()->name,
                [
                    'Car uuid' => $uuid,
                    'Can reserve' => $canReserve
                ]);
    }

    public function getPaymentFormUrl(Request $request): void {
        $this->log()
            ->info('Get payment form url request ', $request->all());
    }

    public function paymentError(Throwable $e, array $context): void {
        $this->log()
            ->error($e->getMessage(), $context);
    }

    public function paymentSuccess(string $formUrl): void {
        $this->log()
            ->info('Successfully transacted ', [$formUrl]);
    }

    public function dalionRequest($action, Transaction $transaction): void {
        $this->log()
            ->info("Dalion $action request", ['transaction_id' => $transaction->id]);
    }

    public function sberResponseError($action): void {
        $this->log()->error("Sber failed $action");
    }

    public function sberCallback(Request $request): void {
        $this->log()
            ->info("Sber Callback request", $request->all());
    }

    public function dalionPaymentApproveStatus(Transaction $transaction, bool $handled): void {
        if ($handled) {
            $this->log()->info("Dalion successfully handled payment approve for {$transaction->id} order");
        } else {
            $this->log()->warning("Dalion can't handle payment approve for {$transaction->id} order");
        }
    }

    public function merchantPaymentHandlingStatus(Response $response , Transaction $transaction): void {
        if ($response->status() === 200) {
            $this->log()->info("Merchant {$transaction->merchant->name} successfully handle payment for {$transaction->id} order", [
                'headers' => $response->headers(),
                'body' => $response->body(),
            ]);
        } else {
            $this->log()->warning("Merchant {$transaction->merchant->name} can't handle payment for {$transaction->id} order", [
                'url' => $transaction->merchant->payment_approve_url,
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
            ]);
        }
    }

    public function orderStatus(Transaction $transaction) {
        $this->log()
            ->info("Order status request", ['transaction_id' => $transaction->id]);
    }

    public function requestToSber(string $route, array $data, array $response): void {
        $this->log()
            ->info('Request to Sber', compact('route', 'data', 'response'));
    }

    public function warning(string $message): void {
        $this->log()->warning($message);
    }

    public function opensslError(\Exception $e): void {
        $this->log()->error('openssl() error ' . $e->getMessage());
    }

    public function receiptStatus(array $receiptStatus): void {
        $this->log()->info('Receipt status', $receiptStatus);
    }
}
