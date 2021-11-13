<?php namespace App\Currency\NowPayments;

class BNB extends NowPaymentsSupport {

    function id(): string {
        return "np_bnb";
    }

    public function walletId(): string {
        return "bnb";
    }

    function name(): string {
        return "BNB";
    }

    public function alias(): string {
        return 'bnb';
    }
    public function conversionID(): string {
        return 'binancecoin';
    }

    public function displayName(): string {
        return "BNB";
    }

    public function style(): string {
        return "#bfbbbb";
    }
	
	public function icon(): string {
        return "bnb";
    }
	
	public function nowpayments(): string {
        return 'BNBBSC';
    }

}
