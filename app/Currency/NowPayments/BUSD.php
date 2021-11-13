<?php namespace App\Currency\NowPayments;

class BUSD extends NowPaymentsSupport {

    function id(): string {
        return "np_busd";
    }

    public function walletId(): string {
        return "busd";
    }

    function name(): string {
        return "BUSD";
    }

    public function alias(): string {
        return 'busd';
    }
    public function conversionID(): string {
        return 'binance-usd';
    }

    public function displayName(): string {
        return "BUSD";
    }

    public function style(): string {
        return "#bfbbbb";
    }
	
	public function icon(): string {
        return "busd";
    }
	
	public function nowpayments(): string {
        return 'BUSDBSC';
    }

}
