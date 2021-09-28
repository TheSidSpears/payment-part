<?php


namespace App\Services;


use App\Exceptions\CustomerServiceException;
use App\Models\Customer;
use App\Models\Transaction;
use Carbon\Carbon;

class CustomerService {

    /**
     * @param Customer $customer
     * @return void
     * @throws CustomerServiceException
     */
    public function checkCustomer(Customer $customer): void {
        $this->checkHoldLimit($customer);
        $this->checkCustomerApproves($customer);
    }

    /**
     * Один пользователь не может холдить больше трех машин в сутки
     *
     * @param Customer $customer
     * @throws CustomerServiceException
     */
    protected function checkHoldLimit(Customer $customer): void {
        $todayCustomerHolds = Transaction::join('customers', 'transactions.customer_id', '=', 'customers.id')
            ->where('customers.phone', $customer->phone)
            ->where('transactions.created_at', '>', Carbon::now()->subDay())
            ->whereNotNull('transactions.sber_order_id')
            ->count();

        if ($todayCustomerHolds >= config('payment.hold_limit')) {
            throw new CustomerServiceException('Вы достигли суточного лимита по бронированию авто');
        }
    }

    /**
     * У одного пользователя может быть только одна машина на резерве
     *
     * @param Customer $customer
     * @throws CustomerServiceException
     */
    protected function checkCustomerApproves(Customer $customer): void {
        $hasApprovedTransaction = Customer::query()
            ->join('transactions','transactions.customer_id','=','customers.id')
            ->where('customers.phone', $customer->phone)
            ->where('customers.id', '!=', $customer->id)
            ->where('transactions.created_at', '>', Carbon::now()->subWeek())
            ->pluck('status')
            ->search('approved');

        if ($hasApprovedTransaction) {
            throw new CustomerServiceException('У Вас уже есть забронированный автомобиль, обратитесь в отдел продаж по телефону +7 (812) 333-33-44');
        }
    }
}
