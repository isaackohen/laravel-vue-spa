<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use \Cache;
use Carbon\Carbon;
use App\User;
use App\Currency\Currency;
use Illuminate\Support\Facades\Log;

class Challenges extends Model {

    protected $connection = 'mongodb';
    protected $collection = 'challenges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game', 'game_name', 'minbet', 'game_image', 'game_provider', 'game_category', 'multiplier', 'winners', 'currency', 'sum', 'maxwinners', 'expires', 'expired', 'vip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'winners' => 'json',
        'expires' => 'datetime'
    ];

    public static function generate() {
        return strtoupper(substr(str_shuffle(MD5(microtime())), 0, 8));
    }


    public static function check($gameid, $wager, $multi, $user) {

        $selectChallenge = Challenges::where('game', '=', $gameid)->first();
        $user = User::where('_id', $user)->first();
        //Log::warning('challenges info: '.$gameid.$wager.$multi.$user);
        if($selectChallenge) {
            if($wager < $selectChallenge->minbet) return;
            if($multi < $selectChallenge->multiplier) return;
            if($selectChallenge->maxwinners != -1 && $selectChallenge->expired >= $selectChallenge->maxwinners) return;
            if($selectChallenge->expires->timestamp != Carbon::minValue()->timestamp && $selectChallenge->expires->isPast()) return;
            if(in_array($user->_id, $selectChallenge->winners)) return;

            $winners = $selectChallenge->winners;
            array_push($winners, $user->_id);

        $selectChallenge->update([
            'expired' => $selectChallenge->expired + 1,
            'winners' => $winners
        ]);

        $currency = Currency::find($selectChallenge->currency);
        $amount = $currency->convertUSDToToken($selectChallenge->sum);
        $user->balance(Currency::find($selectChallenge->currency))->add($amount, Transaction::builder()->message('Challenge Winner')->get());
        TransactionStatistics::statsUpdate($user->_id, 'promocode', $selectChallenge->sum);
        

        }



    }

}
