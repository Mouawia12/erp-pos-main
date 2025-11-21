<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Company;

class ClientMoneyController
{
    public function syncMoney($clientId,$oldMoney,$newMoney){
        $client = Company::find($clientId);
        $deposit_amount = $client->deposit_amount + $newMoney - $oldMoney;

        $client->update([
           'deposit_amount' => $deposit_amount
        ]);
    }

    public function getClientMoney($clientId){
        return Company::find($clientId)->deposit_amount;
    }
}
