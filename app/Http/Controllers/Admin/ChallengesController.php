<?php
namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Challenges;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Gameslist;

class ChallengesController extends Controller
{
	
    public function get()
    {
        return APIResponse::success(Challenges::get()->toArray());
	}
	
	public function create(Request $request)
    {
		request()->validate([
            'game' => 'required',
            'maxwinners' => 'required',
            'expires' => 'required',
            'minbet' => 'required',
            'multiplier' => 'required',
            'sum' => 'required',
            'currency' => 'required'
        ]);

        $selectGame = Gameslist::where('id', request('game'))->first();

        Challenges::create([
            'game' => request('game'),
            'game_name' => $selectGame->name,
            'game_image' => $selectGame->image,
            'game_provider' => $selectGame->provider,
            'game_category' => $selectGame->category,
            'multiplier' => request('multiplier'),
            'currency' => request('currency'),
            'winners' => [],
            'minbet' => request('minbet'),
            'sum' => floatval(request('sum')),
            'maxwinners' => request('maxwinners'),
            'expired' => 0,
            'expires' => request('expires') === '%unlimited%' ? Carbon::minValue() : Carbon::createFromFormat('d-m-Y H:i', request()->get('expires'))
        ]);
        return APIResponse::success();
	}
	
	public function remove()
    {
		Challenges::where('_id', request()->get('id'))->delete();
        return APIResponse::success();
	}
	
	public function removeInactive(Request $request)
    {
		foreach(Challenges::get() as $challenge) {
            if(($challenge->expires->timestamp != Carbon::minValue()->timestamp && $challenge->expires->isPast())
                || ($challenge->maxwinners != -1 && $challenge->expired >= $challenge->maxwinners)) $challenge->delete();
        }
        return APIResponse::success();
	}
	
}