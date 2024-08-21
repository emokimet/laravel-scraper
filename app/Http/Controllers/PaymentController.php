<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
class PaymentController extends Controller
{
    
    public function checkBlockchainForDeposit(Request $request)
    {
        $address = $request->query('address');;
        $currency = $request->query('currency');
        switch ($currency) {
            case 'ETH':
                return $this->checkEthereumDeposit($address);
            case 'BTC':
                return $this->checkBitcoinDeposit($address);
            case 'TRON':
                return $this->checkTronDeposit($address);
            case 'BNB':
                return $this->checkBnbDeposit($address);
            default:
                return false;
        }
    }

    private function checkEthereumDeposit($address)
    {
        $url = "https://api.etherscan.io/api?module=account&action=txlist&address={$address}&sort=desc&apikey=" . env('ETHERSCAN_API_KEY');
        
        $response = Http::get($url);
        $data = $response->json();
   
        // Check if there are any transactions
        if ($data['status'] == '1' && !empty($data['result'])) {
            foreach ($data['result'] as $transaction) {
                
                $time1 = Carbon::createFromTimestamp($transaction['timeStamp']);
                $time2 = Carbon::now();
                $diff_sec=$time2->diffInSeconds($time1);
                $address=Str::lower($address);
                if ($transaction['to']==$address && $transaction['value'] > 0 && $diff_sec<360) {
                    return true; // Deposit found
                } 
            }
        }

        return false; // No deposit found
    }

    private function checkBitcoinDeposit($address)
    {
        $url = "https://api.blockcypher.com/v1/btc/main/addrs/{$address}/full?token=" . env('BLOCKCYPHER_API_KEY');
        
        $response = Http::get($url);
        $data = $response->json();

        // Check if there are any transactions
        if (isset($data['txs']) && !empty($data['txs'])) {
            foreach ($data['txs'] as $transaction) {
                foreach ($transaction['outputs'] as $output) {
                    if ($output['addresses'][0] === $address && $output['value'] > 0) {
                        return true; // Deposit found
                    }
                }
            }
        }

        return false; // No deposit found
    }

    private function checkTronDeposit($address)
    {
        $url = "https://api.tronscan.org/api/transaction?address={$address}&limit=3&sort=-timestamp";

        $response = Http::get($url);
        $data = $response->json();
        
        // Check if there are any transactions
        if (!empty($data['data'])) {
            
            foreach ($data['data'] as $transaction) {
                $time1 = Carbon::createFromTimestamp($transaction['timestamp']/1000);
                $time2 = Carbon::now();
                $diff_sec=$time2->diffInSeconds($time1);
                if($transaction['toAddress']==$address && ($transaction['amount']/1000000)>0.001 && $diff_sec<360 && $transaction['confirmed'])
                {
                    return true;
                }
                
            }
        }

        return false; // No deposit found
    }

    private function checkBnbDeposit($address)
    {
        $url = "https://api.bscscan.com/api?module=account&action=txlist&address={$address}&sort=desc&apikey=7XBK9IJFTIR78YSKN36JC1VXQFX251YVYI";
        
        $response = Http::get($url);
        $data = $response->json();
   
        // Check if there are any transactions
        if ($data['status'] == '1' && !empty($data['result'])) {
            foreach ($data['result'] as $transaction) {
                
                $time1 = Carbon::createFromTimestamp($transaction['timeStamp']);
                $time2 = Carbon::now();
                $diff_sec=$time2->diffInSeconds($time1);
                $address=Str::lower($address);
                if ($transaction['to']==$address && $transaction['value'] > 0 && $diff_sec<360) {
                    return true; // Deposit found
                } 
            }
        }

        return false; // No deposit found
    }
}
