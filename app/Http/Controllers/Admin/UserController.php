<?php
namespace App\Http\Controllers\Admin;

use App\User;
use App\Game;
use App\Invoice;
use App\Withdraw;
use App\Transaction;
use App\Currency\Currency;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use MongoDB\BSON\Decimal128;
use App\Http\Controllers\Controller;
use App\TransactionStatistics;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
	
    public function user(Request $request)
    {
		$user = User::where('_id', $request->id)->first();

		$currencies = [];
		foreach (Currency::all() as $currency) {
			$currencies = array_merge($currencies, [
				$currency->id() => [
					'games' => Game::where('demo', '!=', true)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->where('user', $user->_id)->where('currency', $currency->id())->count(),
					'wins' => Game::where('demo', '!=', true)->where('status', 'win')->where('user', $user->_id)->where('currency', $currency->id())->count(),
					'losses' => Game::where('demo', '!=', true)->where('status', 'lose')->where('user', $user->_id)->where('currency', $currency->id())->count(),
					'wagered' => Game::where('demo', '!=', true)->where('status', '!=', 'cancelled')->where('user', $user->_id)->where('currency', $currency->id())->sum('wager'),
					'deposited' => Invoice::where('user', $user->_id)->where('currency', $currency->id())->sum('sum'),
					'balance' => $user->balance($currency)->get()
				]
			]);
		}

		return APIResponse::success([
			'user' => $user->makeVisible($user->hidden)->toArray(),
			'games' => Game::where('user', $user->_id)->where('demo', '!=', true)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->count(),
			'wins' => Game::where('demo', '!=', true)->where('status', 'win')->where('user', $user->_id)->count(),
			'losses' => Game::where('demo', '!=', true)->where('status', 'lose')->where('user', $user->_id)->count(),
			'transactions' => Transaction::where('user', $user->_id)->where('demo', '!=', true)->get()->toArray(),
			'freespins' => $user->makeVisible($user->hidden)->freespins ?? 0,
			'currencies' => $currencies,
			'gamesArray' => Game::where('demo', '!=', true)->where('user', $user->_id)->get()->toArray()]);
    }


    public function userTxStats(Request $request)
    {
		$user = User::where('_id', $request->id)->first();
		$TransactionStats = TransactionStatistics::statsGet($user->_id);
		$TransactionStats = $TransactionStats[0];
		return APIResponse::success([
			'promocode' => $TransactionStats['promocode'],
			'weeklybonus' => $TransactionStats['weeklybonus'],
			'freespins_amount' => $TransactionStats['freespins_amount'],
			'faucet' => $TransactionStats['faucet'],
			'challenges' => $TransactionStats['challenges'],
			'depositbonus' => $TransactionStats['depositbonus'],
			'deposit_total' => $TransactionStats['deposit_total'],
			'deposit_count' => $TransactionStats['deposit_count'],
			'withdraw_count' => $TransactionStats['withdraw_count'],
			'withdraw_total' => $TransactionStats['withdraw_total'],
			'vip_progress' => $TransactionStats['vip_progress']
		]);
    }
	
	public function users(Request $request)
    {
		$draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value'] ?? null; // Search value

        // Total records
        $totalRecords = User::where('bot', '!=', true)->select('count(*) as allcount')->count();

        // Fetch records
		if(!$searchValue){
			$records = User::where('bot', '!=', true)->orderBy($columnName,$columnSortOrder)
				->skip(intval($start))
				->take(intval($rowperpage))
				->get();
			$totalRecordswithFilter = User::where('bot', '!=', true)->select('count(*) as allcount')->count();
		} else {
			$records = User::where('bot', '!=', true)->orderBy($columnName,$columnSortOrder)
				->where('name', 'like', '%' .$searchValue . '%')
				->skip(intval($start))
				->take(intval($rowperpage))
				->get();
			$totalRecordswithFilter = User::where('bot', '!=', true)->select('count(*) as allcount')->where('name', 'like', '%' .$searchValue . '%')->count();
		}

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $data_arr[] = array(
				"_id" => $record->_id,
				"avatar" => $record->avatar,
                "name" => $record->name,
                "created_at" => $record->created_at,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        ); 

		return APIResponse::success($response);
    }
	
	public function checkDuplicates(Request $request)
    {
		$user = User::where('_id', $request->id)->first();
		if($user->bot) return APIResponse::reject(1, 'Can\'t verify bots');

		return APIResponse::success([
			'user' => $user->makeVisible('register_multiaccount_hash')->makeVisible('login_multiaccount_hash')->toArray(),
			'same_register_hash' => User::where('register_multiaccount_hash', $user->register_multiaccount_hash)->get()->toArray(),
			'same_login_hash' => User::where('login_multiaccount_hash', $user->login_multiaccount_hash)->get()->toArray(),
			'same_register_ip' => User::where('register_ip', $user->register_ip)->get()->toArray(),
			'same_login_ip' => User::where('login_ip', $user->login_ip)->get()->toArray()
		]);
    }
	
	public function ban(Request $request)
    {
		$user = User::where('_id', request('id'))->first();
		(new \App\ActivityLog\BanUnbanLog())->insert(['type' => $user->ban ? 'unban' : 'ban', 'id' => $user->_id]);
		$user->update([
			'ban' => $user->ban ? false : true
		]);
		return APIResponse::success();
    }
	
	public function role(Request $request)
    {
		User::where('_id', request('id'))->update([
			'access' => request('role')
		]);
		return APIResponse::success();
    }
	
	public function balance(Request $request)
    {
		User::where('_id', request('id'))->update([
			request('currency') => new Decimal128(strval(request('balance')))
		]);

		(new \App\ActivityLog\BalanceChangeActivity())->insert(['currency' => request('currency'), 'balance' => request('balance'), 'id' => request('id')]);
		return APIResponse::success();
    }
	
}