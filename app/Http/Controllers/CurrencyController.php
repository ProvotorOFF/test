<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\JsonResponse;

class CurrencyController extends Controller
{
    public function rates(Request $request): JsonResponse
    {
        $res = [];
        $blockChainInfo = $this->getBlockChainInfo();
        if (!$blockChainInfo) return response()->json([
            "status" => "error",
            "code" => 500,
            "message" => "Error occurred in receiving data"
        ], 500);
        usort($blockChainInfo, function ($a, $b) {
            return $a['buy'] - $b['buy'];
        });
        foreach ($blockChainInfo as $data) {
            $res[$data['symbol']] = [
                'buy' => $data['buy'] * 1.02,
                'sell' => $data['sell'] * 0.98
            ];
        }
        if (isset($request['currency'])) {
            if (!isset($res[strtoupper($request['currency'])])) return response()->json([
                "status" => "error",
                "code" => 404,
                "message" => "No currency with this code was found"
            ], 404);
            return response()->json([
                "status" => "success",
                "code" => 200,
                "data" => $res[strtoupper($request['currency'])]
            ]);
        }
        return response()->json([
            "status" => "success",
            "code" => 200,
            "data" => $res
        ]);
    }

    public function convert(Request $request)
    {
        $validator = validator($request->input(), [
            "currency_from" => "required|string",
            "currency_to" => "required|string",
            "value" => "required|numeric|min:0.01"
        ]);
        if (sizeof($validator->errors())) return response()->json([
            "status" => "error",
            "code" => 400,
            "message" => implode(" ", $validator->messages()->all())
        ], 400);

        if ($request["currency_to"] != "BTC" && $request["currency_from"] != "BTC") {
            return response()->json([
                "status" => "error",
                "code" => 400,
                "message" => "From or to must equals BTC"
            ], 400);
        }

        $blockChainInfo = $this->getBlockChainInfo();
        if (!$blockChainInfo) return response()->json([
            "status" => "error",
            "code" => 500,
            "message" => "Error occurred in receiving data"
        ], 500);
        $res = [
            "currency_from" => strtoupper($request["currency_from"]),
            "currency_to" => strtoupper($request["currency_to"]),
            "value" => (double)$request['value'],
        ];

        if ($request["currency_to"] == "BTC") {
            if (!isset($blockChainInfo[strtoupper($request["currency_from"])])) return response()->json([
                "status" => "error",
                "code" => 400,
                "message" => "Incorrect from currency"
            ], 400);
            $from = $blockChainInfo[strtoupper($request["currency_from"])];
            $res['converted_value'] = number_format($request['value'] / ($from['buy'] * 1.02), 10, '.', '');
            $res['rate'] = number_format(1 / ($from['buy'] * 1.02), 10, '.', '');
        } else {
            if (!isset($blockChainInfo[strtoupper($request["currency_to"])])) return response()->json([
                "status" => "error",
                "code" => 400,
                "message" => "Incorrect to currency"
            ], 400);
            $to = $blockChainInfo[strtoupper($request["currency_to"])];
            $res['converted_value'] = number_format($request['value'] * ($to['sell'] * 0.98), 2, '.', '');
            $res['rate'] = number_format($to['sell'] * 0.98, 2, '.', '');
        }
        return response()->json([
            "status" => "success",
            "code" => 200,
            "data" => $res
        ]);
    }

    private function getBlockChainInfo()
    {
        try {
            return Http::retry(3, 1000)->get("https://blockchain.info/ticker")->json();
        } catch (Exception $e) {
            return null;
        }
    }
}
