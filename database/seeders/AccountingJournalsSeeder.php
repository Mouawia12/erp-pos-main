<?php

namespace Database\Seeders;

use App\Http\Controllers\Admin\SystemController;
use App\Models\CatchRecipt;
use App\Models\Expenses;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sales;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class AccountingJournalsSeeder extends Seeder
{
    public function run(): void
    {
        $controller = new SystemController();
        $subscribers = Subscriber::all();
        if ($subscribers->isEmpty()) {
            $subscribers = collect([(object) ['id' => null]]);
        }

        foreach ($subscribers as $subscriber) {
            $subscriberId = $subscriber->id ?? null;
            $user = User::query()
                ->when($subscriberId !== null, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->first() ?? User::first();

            if ($user) {
                Auth::login($user);
            }

            $sales = Sales::withoutGlobalScope('subscriber')
                ->when($subscriberId !== null, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->get();
            foreach ($sales as $sale) {
                $controller->saleJournals($sale->id);
            }

            $purchases = Purchase::withoutGlobalScope('subscriber')
                ->when($subscriberId !== null, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->get();
            foreach ($purchases as $purchase) {
                $controller->purchaseJournals($purchase->id);
            }

            $payments = Payment::withoutGlobalScope('subscriber')
                ->when($subscriberId !== null, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->get();
            foreach ($payments as $payment) {
                if ($payment->sale_id) {
                    $controller->EnterMoneyAccounting($payment->id);
                } elseif ($payment->purchase_id) {
                    $controller->ExitMoneyAccounting($payment->id);
                }
            }

            $expenses = Expenses::withoutGlobalScope('subscriber')
                ->when($subscriberId !== null, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->get();
            foreach ($expenses as $expense) {
                $controller->ExpenseAccounting($expense->id);
            }

            $catchRecipts = CatchRecipt::withoutGlobalScope('subscriber')
                ->when($subscriberId !== null, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->get();
            foreach ($catchRecipts as $catch) {
                $controller->CatchAccounting($catch->id);
            }

            if ($user) {
                Auth::logout();
            }
        }
    }
}
