<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CurrencyFormRequest;
use Illuminate\Contracts\Support\ValidatedData;

class CurrencyController extends Controller
{
    function index()
    {
        return Currency::all();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_name' => 'required',
            'currency_symbol' => 'required',
            'currency_code' => 'required',
            'currency_exchange' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $currency = Currency::create([
                'currency_name' => $request->input('currency_name'),
                'currency_symbol' => $request->input('currency_symbol'),
                'currency_code' => $request->input('currency_code'),
                'currency_exchange' => $request->input('currency_exchange'),

            ]);

            if ($currency) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Currency created successfully',
                    'data' => $currency,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create currency',
                ], 500);
            }
        }
    }

    public function show(Currency $currency)
    {
        return $currency;
    }

    public function update(Request $request, Currency $currency)
    {
        $validator = Validator::make($request->all(), [
            'currency_name' => 'required',
            'currency_symbol' => 'required',
            'currency_code' => 'required',
            'currency_exchange' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $currency->currency_name = $request->currency_name;
            $currency->currency_symbol = $request->currency_symbol;
            $currency->currency_code = $request->currency_code;
            $currency->currency_exchange = $request->currency_exchange;
            $currency->update();
            if ($currency) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Currency edit successfully',
                    'data' => $currency,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to edit currency',
                ], 500);
            }
        }
    }

    public function destroy(Currency $currency)
    {

        $currency->delete();
        if ($currency) {
            return response()->json([
                'status' => 200,
                'message' => 'Currency delete successfully',
                'data' => $currency,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete currency',
            ], 500);
        }
    }

    public function getCurrencyList(Request $request)
    {
        $start = $request->gridState['skip'];
        $take = $request->gridState['take'];
        $DisplayStart = 1;
        if (isset($start))
            $DisplayStart = intval($start);

        $DisplayLength = 10;
        if (isset($take))
            $DisplayLength = intval($take);

        $sortingName = '';
        $sortBy = '';
        $sortField = '';

        if (isset($request->gridState['sort'][0]['dir'])) {
            if (count($request->gridState['sort']) > 0) {
                $sort = $request->gridState['sort'][0];
                $sortBy = $sort['dir'] == null ? $sortBy : $sort['dir'];
                $sortField = $sort['field'];
            }
            //echo $sortField;exit();
            if (in_array($sortField, array('currency_name', 'currency_symbol', 'currency_code', 'currency_exchange'))) {
                if ($sortBy == 'desc') {
                    $sortingName = $sortField;
                    $sortBy = $sortBy;
                } else {
                    $sortingName = $sortField;
                    $sortBy = $sortBy;
                }
            }
        } else //default sorting
        {
            $sortingName = 'currency_name';
            $sortBy = 'asc';
        }

        $filterOptions = $request->gridState['filter']['filters'];
        if (count($filterOptions) > 0) {
            for ($i = 0; $i < count($filterOptions); $i++) {
                $fieldName = $filterOptions[$i]['field'];
                $fieldvalue = $filterOptions[$i]['value'];
                $FilterBy = '';

                if ($fieldName == 'currency_name') {
                    $fieldName = $fieldName;
                    $fieldvalue = '%' . $fieldvalue . '%';
                    $FilterBy = 'LIKE';
                }
                if ($fieldName == 'currency_code' || $fieldName == 'currency_exchange') {
                    $fieldName = $fieldName;
                    $fieldvalue = $fieldvalue;
                    $FilterBy = '=';
                }
            }
        }

        $qryResult = null;

        if (isset($fieldName)) {
            $qryResult = Currency::where($fieldName, $FilterBy, $fieldvalue)
                ->orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        } else {
            $qryResult = Currency::orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        }

        $currencies = Currency::all();
        $dataRowsCount = count($currencies);
        return response()->json(['data' => $qryResult, 'dataFoundRowsCount' => $dataRowsCount]);
    }

    public function saveCurrency(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_name' => 'required',
            'currency_code' => 'required',
            'currency_symbol' => 'required',
            'currency_exchange' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $currency = Currency::create([
                'currency_name' => $request->input('currency_name'),
                'currency_symbol' => $request->input('currency_symbol'),
                'currency_code' => $request->input('currency_code'),
                'currency_exchange' => $request->input('currency_exchange')
            ]);

            if ($currency) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Currency created successfully',
                    'data' => $currency,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Currency',
                ], 500);
            }
        }
    }

    public function updateCurrency(Request $request, Currency $currency)
    {
        $validator = Validator::make($request->all(), [
            'currency_name' => 'required',
            'currency_code' => 'required',
            'currency_symbol' => 'required',
            'currency_exchange' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }

        $currency = Currency::find($request->id);

        $currency->currency_name = $request->input('currency_name');
        $currency->currency_code = $request->input('currency_code');
        $currency->currency_symbol = $request->input('currency_symbol');
        $currency->currency_exchange = $request->input('currency_exchange');

        $updated = $currency->save();

        if ($updated) {
            return response()->json([
                'status' => 200,
                'message' => 'Currency updated successfully',
                'data' => $currency,
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update Currency',
            ]);
        }
    }

    public function removeCurrency(Request $request)
    {
        $currency_id = $request->id;
        $result = Currency::where('id', $currency_id)->delete();

        return response()->json(['data' => $result]);
    }
    public function getCurrencies()
    {
        $res_currency = Currency::all();
        return response()->json(['data' => $res_currency]);
    }
}
