<?php namespace App\Currency\NowPayments;

class Cake extends NowPaymentsSupport {

    function id(): string {
        return "np_cake";
    }

    public function walletId(): string {
        return "cake";
    }

    function name(): string {
        return "CAKE";
    }

    public function alias(): string {
        return 'cake';
    }
    public function conversionID(): string {
        return 'pancakeswap-token';
    }

    public function displayName(): string {
        return "CAKE";
    }

    public function style(): string {
        return "#bfbbbb";
    }
	
	public function icon(): string {
        return "cake";
    }
	
	public function nowpayments(): string {
        return 'CAKE';
    }

}
