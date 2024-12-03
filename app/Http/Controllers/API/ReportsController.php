<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Invoice;
use App\Trait\ApiResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    use ApiResponse;


    public function LastYearEarnings()
    {
        try {
            $endYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;
            $startYear = $endYear - 1;
            $startMonth = $currentMonth;
            $results = [];
            while ($startMonth != 12) {
                $total = 0;
                if (Invoice::whereMonth('created_at', $startMonth)->whereYear('created_at', $startYear)->first() != null) {
                    $query = Invoice::whereMonth('created_at', $startMonth)->whereYear('created_at', $startYear)->select(DB::raw('SUM(total_net_price) as tnp'), DB::raw('SUM(total_sell_price) as tsp'), DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'))
                        ->groupBy('year', 'month')
                        ->get();
                    $total = $query->first()->tsp - $query->first()->tnp;
                }
                $results[] = [
                    'year' => $startYear,
                    'month' => $startMonth,
                    'total' =>  $total
                ];
                $startMonth++;
            }
            $startMonth = 1;
            while ($startMonth != $currentMonth + 1) {
                $total = 0;
                if (Invoice::whereMonth('created_at', $startMonth)->whereYear('created_at', $endYear)->first() != null) {
                    $query = Invoice::whereMonth('created_at', $startMonth)->whereYear('created_at', $endYear)->select(DB::raw('SUM(total_net_price) as tnp'), DB::raw('SUM(total_sell_price) as tsp'), DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'))
                        ->groupBy('year', 'month')
                        ->get();
                    $total = $query->first()->tsp - $query->first()->tnp;
                }
                $results[] = [
                    'year' => $endYear,
                    'month' => $startMonth,
                    'total' =>  $total
                ];
                $startMonth++;
            }
            return $this->SuccessOne($results, null, 'Success');
        } catch (Exception $e) {
            return $this->Error(null, $e->getMessage());
        }
    }


    public function MonthlyReport($month, $year)
    {
        try {
            $total_net_price = 0;
            $total_sell_price = 0;
            $total_expenses = 0;
            if (Invoice::whereMonth('created_at', $month)->whereYear('created_at', $year)->first() != null) {
                $query = Invoice::whereMonth('created_at', $month)->whereYear('created_at', $year)->select(DB::raw('SUM(total_net_price) as tnp'), DB::raw('SUM(total_sell_price) as tsp'))
                    ->get();
                $total_net_price = $query->first()->tnp;
                $total_sell_price = $query->first()->tsp;
            }
            if (Expense::whereMonth('created_at', $month)->whereYear('created_at', $year)->first() != null) {
                $query = Expense::whereMonth('created_at', $month)->whereYear('created_at', $year)->select(DB::raw('SUM(cost) as cost'))->get();
                $total_expenses = $query->first()->cost;
            }
            $data =  [
                'total_net_price' => $total_net_price,
                'total_sell_price' => $total_sell_price,
                'total_expenses' => $total_expenses,
                'earnings' => $total_sell_price - $total_net_price,
                'earnings_with_expense' => $total_sell_price - $total_net_price - $total_expenses
            ];
            return $this->SuccessOne($data, null, 'Success');
        } catch (Exception $e) {
            return $this->Error(null, $e->getMessage());
        }
    }
}