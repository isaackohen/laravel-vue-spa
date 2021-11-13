<?php

namespace App\Http\Controllers\Api;

use \Cache;
use Carbon\Carbon;
use App\Invoice;
use App\Settings;
use App\Withdraw;
use App\Transaction;
use App\Utils\APIResponse;
use App\Currency\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WalletController
{
	
	public function deposit(Request $request) 
	{
        $minimal = floatval(Currency::find('local_rub')->option('min_deposit'));
        if(auth('sanctum')->user()->vipLevel() >= 4) $minimal /= 2;

        if(floatval($request->sum) < $minimal) return APIResponse::reject(1, 'Invalid deposit value');
        $aggregator = Aggregator::find($request->aggregator);
        if($aggregator == null) return APIResponse::reject(2, 'Invalid aggregator');

        $invoice = Invoice::create([
            'method' => $request->type,
            'sum' => floatval($request->sum),
            'user' => auth('sanctum')->user()->_id,
            'aggregator' => $aggregator->id(),
            'currency' => 'local_rub',
            'status' => 0
        ]);

        return APIResponse::success([
            'url' => $aggregator->invoice($invoice)
        ]);
	}
	
	public function historyDeposits(Request $request) 
	{	
		return APIResponse::success(Invoice::where('user', auth('sanctum')->user()->_id)->where('status', '>=', 1)->latest()->get()->toArray());
	}
	
	public function historyWithdraws(Request $request) 
	{
		return APIResponse::success(Withdraw::where('user', auth('sanctum')->user()->_id)->latest()->get()->toArray());
	}
	
	public function getDepositWallet(Request $request) 
	{
		$currency = Currency::find($request->currency);
        $wallet = auth('sanctum')->user()->depositWallet($currency);
		if($currency == null || !$currency->isRunning() || $wallet === 'Error') return APIResponse::reject(1);
		$invoice = Invoice::where('ledger', $wallet)->where('updated_at', '>=', Carbon::parse(Carbon::today()->toDateTimeString()))->where('status', 0)->first();
		if(!$invoice) return APIResponse::reject(1);
		$payid = $invoice->payid;
		$min_deposit = $invoice->min_deposit;
		$min_deposit_usd = $invoice->min_deposit_usd;
		return APIResponse::success([
            'currency' => $request->currency,
            'wallet' => $wallet,
			'payid' => $payid,
			'min_deposit' => $min_deposit,
			'min_deposit_usd' => $min_deposit_usd,
        ]);
	}
	
	public function withdraw(Request $request) 
	{
		if(!auth('sanctum')->user()->validate2FA(false)) return APIResponse::invalid2FASession();
        auth('sanctum')->user()->reset2FAOneTimeToken();
        $currency = Currency::find($request->currency);
		$manualTrigger = floatval($currency->option('withdraw_manual_trigger'));
        $manualTrigger = number_format(($manualTrigger), 8, '.', '');
        if($request->sum < floatval($currency->option('withdraw')) + floatval($currency->option('fee'))) return APIResponse::reject(1, 'Invalid withdraw value');
        if(auth('sanctum')->user()->balance($currency)->get() < $request->sum + floatval($currency->option('fee'))) return APIResponse::reject(2, 'Not enough balance');
        if(Withdraw::where('user', auth('sanctum')->user()->_id)->where('status', 0)->count() > 0) return APIResponse::reject(3, 'Moderation is still in process');
		if(auth('sanctum')->user()->access == 'moderator') return APIResponse::reject(1, 'Not available');

        auth('sanctum')->user()->balance($currency)->subtract($request->sum + floatval($currency->option('fee')), Transaction::builder()->message('Withdraw')->get());

        if(auth('sanctum')->user()->balance($currency)->get() + Withdraw::where('status', 0)->where('user', auth('sanctum')->user()->_id)->where('currency', $currency->id())->sum('sum') > $manualTrigger)
        {
			$withdraw = Withdraw::create([
				'user' => auth('sanctum')->user()->_id,
				'sum' => $request->sum,
				'currency' => $currency->id(),
				'address' => $request->wallet,
				'status' => 0,
				'auto' => false
			]);
		} else {
			
			$withdraw = Withdraw::create([
				'user' => auth('sanctum')->user()->_id,
				'sum' => $request->sum,
				'currency' => $currency->id(),
				'address' => $request->wallet,
				'status' => 0,
				'auto' => true
			]);
			
			if($currency->withdrawmethod() === 'chaingateway') {

				if($currency->option('contract_address') === '0') {
				   $url = 'https://eu.bsc.chaingateway.io/v1/sendBinancecoin';
				} else {
				   $url = 'https://eu.bsc.chaingateway.io/v1/sendToken';
				}
				$apikey = env('CHAINGATEWAY_APIKEY'); // API Key in your account panel
				$password = env('CHAINGATEWAY_PASSWORD'); // Chaingateway password


				# Define function endpoint
				$ch = curl_init($url);

				# Setup request to send json via POST. This is where all parameters should be entered.

				if($currency->option('contract_address')  === '0') {
				$payload = json_encode( array("from" => $currency->option('withdraw_address'), "to" => $request->wallet, "password" => $password, "amount" => $requestAmount));
				} else {
				$payload = json_encode( array("contractaddress" => $currency->option('contract_address'), "from" => $currency->option('withdraw_address'), "to" => $request->wallet, "password" => $password, "amount" => $requestAmount));
				}

				curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: " . $apikey));

				# Return response instead of printing.
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

				# Send request.
				$result = curl_exec($ch);
				curl_close($ch);

				# Decode the received JSON string
				$resultdecoded = json_decode($result, true);

				$okresponse = $resultdecoded["ok"];
					if($okresponse === true) {
						$withdraw->update(['status' => (int) 1]);
						$withdraw->update(['hash' => $resultdecoded["txid"]]);
						return APIResponse::success();
					}  
						} else {


	       			 try {
				            $url = "https://api.nowpayments.io/v1/auth";
				            $curljwt = curl_init($url);
				            curl_setopt($curljwt, CURLOPT_URL, $url);
				            curl_setopt($curljwt, CURLOPT_POST, true);
				            curl_setopt($curljwt, CURLOPT_RETURNTRANSFER, true);
				            $headers = array(
				               "Content-Type: application/json",
				            );
				            curl_setopt($curljwt, CURLOPT_HTTPHEADER, $headers);
$data = <<<DATA
{
            "email": "captain@treasurekey.bet",
            "password": "fw6pA2Adwzz3TczqS99VQQTRW5572" 
        }
DATA;
				                curl_setopt($curljwt, CURLOPT_POSTFIELDS, $data);
				                //for debug only!
				                curl_setopt($curljwt, CURLOPT_SSL_VERIFYHOST, false);
				                curl_setopt($curljwt, CURLOPT_SSL_VERIFYPEER, false);
				                $response = curl_exec($curljwt);
				                curl_close($curljwt);
				                } catch (\Exception $exception) {
				            return reject(2, 'Could not process request');
				        }
				        $responseResult = json_decode($response);
				        Log::notice(json_encode($response));

						 if($responseResult !== null) {

						Log::notice($request->wallet);
						Log::notice($currency->nowpayments());
						Log::notice($request->sum);
						$usercurrenccy = $currency->nowpayments();
						$appUrls = env('APP_URL');
						$sumformat = number_format(floatval($request->sum), 6, '.', '');
						$ipnwithdr = $appUrls.'api/callback/nowpayments/withdrawals';
						try {
						$curl = curl_init();
						curl_setopt_array($curl, array(
						  CURLOPT_URL => 'https://api.nowpayments.io/v1/payout',
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => '',
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => 'POST',
						  CURLOPT_POSTFIELDS =>'{
						    "withdrawals": [
						        {
						            "address": "'.$request->wallet.'",
						            "currency": "'.$usercurrenccy.'",
						            "ipn_callback_url": "'.$ipnwithdr.'",
						            "amount": '.$sumformat.'
						        }
						    ]
						}',
						  CURLOPT_HTTPHEADER => array(
						    'x-api-key: V68WSXK-8GQMEJG-GQFEYHR-HT02EYS',
						    'Content-Type: application/json',
						    'Authorization: Bearer '.$responseResult->token.''
						  ),
						));
						$responsewithdraw = curl_exec($curl);
						curl_close($curl);
						Log::notice(json_encode($responsewithdraw));

						 } catch (\Exception $exception) {
						            Log::notice($exception);
						            return reject(2, 'Could not process request');
						        }
						}

		}

		return APIResponse::success();
	}
}
	
	public function withdrawBnb(Request $request) 
	{


	       			 try {
				            $url = "https://api.nowpayments.io/v1/auth";
				            $curljwt = curl_init($url);
				            curl_setopt($curljwt, CURLOPT_URL, $url);
				            curl_setopt($curljwt, CURLOPT_POST, true);
				            curl_setopt($curljwt, CURLOPT_RETURNTRANSFER, true);
				            $headers = array(
				               "Content-Type: application/json",
				            );
				            curl_setopt($curljwt, CURLOPT_HTTPHEADER, $headers);
$data = <<<DATA
{
            "email": "captain@treasurekey.bet",
            "password": "fw6pA2Adwzz3TczqS99VQQTRW5572" 
        }
DATA;
				                curl_setopt($curljwt, CURLOPT_POSTFIELDS, $data);
				                //for debug only!
				                curl_setopt($curljwt, CURLOPT_SSL_VERIFYHOST, false);
				                curl_setopt($curljwt, CURLOPT_SSL_VERIFYPEER, false);
				                $response = curl_exec($curljwt);
				                curl_close($curljwt);
				                } catch (\Exception $exception) {
				            return reject(2, 'Could not process request');
				        }
				        $responseResult = json_decode($response);
						echo json_encode($response);
						$sumformat = number_format(floatval('1'), 6, '.', '');
						$rwallet = "0x1485928aD8bf0649ef697973565eFdCc00dA68cE";
						$usercurrenccy = 'bnbbsc';
						 if($responseResult !== null) {
		return[];

						$appUrls = env('APP_URL');
						$ipnwithdr = $appUrls.'api/callback/nowpayments/withdrawals';

						try {
						$curl = curl_init();
						curl_setopt_array($curl, array(
						  CURLOPT_URL => $nowpaymentsUrl,
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => '',
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => 'POST',
						  CURLOPT_POSTFIELDS =>'{
						    "withdrawals": [
						        {
						            "address": "'.$rwallet.'",
						            "currency": "'.$usercurrenccy.'",
						            "ipn_callback_url": "'.$ipnwithdr.'",
						            "amount": "'.$sumformat.'"
						        }
						    ]
						}',
							  CURLOPT_HTTPHEADER => array(
						    'x-api-key: K7G74DH-3RV4WBN-N7DCTHV-GTFEKRH',
						    'Content-Type: application/json',
						    'Authorization: Bearer '.$responseResult->token.''
						  ),
						));
						$responsewithdraw = curl_exec($curl);
						curl_close($curl);
						echo json_encode($responseResult->token);

						echo json_encode($responsewithdraw);

						 } catch (\Exception $exception) {
						            Log::notice($exception);
						            return reject(2, 'Could not process request');
						        }
						}
}

	public function cancelWithdraw(Request $request) 
	{
        $withdraw = Withdraw::where('_id', $request->id)->where('user', auth('sanctum')->user()->_id)->where('status', 0)->first();
        if($withdraw == null) return APIResponse::reject(1, 'Hacking attempt');
        if($withdraw->auto) return APIResponse::reject(2, 'Auto-withdrawals cannot be cancelled');
        $withdraw->update([
            'status' => 4
        ]);
        auth('sanctum')->user()->balance(Currency::find($withdraw->currency))->add($withdraw->sum, Transaction::builder()->message('Withdraw cancellation')->get());
        return APIResponse::success();
	}
	
	public function exchange(Request $request) 
	{
		//Disabled
		return;

		if(!auth('sanctum')->user()->validate2FA(false)) return APIResponse::invalid2FASession();
        auth('sanctum')->user()->reset2FAOneTimeToken();
        $currencyFrom = Currency::find($request->from);
        $currencyTo = Currency::find($request->to);
		if($currencyFrom == $currencyTo) APIResponse::reject(2, 'Abuse both currencies');
        if(!$currencyFrom || !$currencyTo) return APIResponse::reject(2, 'Invalid currency');
        $amount = floatval($request->amount);
		if($amount == 0) return APIResponse::reject(1, 'Invalid amount');
        if(auth()->user()->balance($currencyFrom)->get() < $amount) return APIResponse::reject(1, 'Invalid amount');
        auth()->user()->balance($currencyFrom)->subtract($amount, Transaction::builder()->message('Exchange')->get());
        auth()->user()->balance($currencyTo)->add($currencyTo->convertUSDToToken($currencyFrom->convertTokenToUSD($amount)), Transaction::builder()->message('Exchange')->get());
        return APIResponse::success();
	}
	
}
