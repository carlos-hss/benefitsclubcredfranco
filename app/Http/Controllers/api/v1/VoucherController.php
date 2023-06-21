<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Voucher;
use App\Http\Controllers\Controller;
use Error;
use Exception;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    private function generateVoucherCode()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        for ($i = 0; $i < 5; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    private function generateUniqueCode()
    {
        $code = $this->generateVoucherCode(); 

        $existingCode = Voucher::where('code', $code)->exists();

        while ($existingCode) {
            $code = $this->generateVoucherCode();
            $existingCode = Voucher::where('code', $code)->exists();
        }

        return $code;
    }

    public function getAllVouchers()
    {
        $vouchers = Voucher::all();

        if (empty($vouchers)) {
            return response()->json(['message' => 'No vouchers registered'], 200);
        }

        return response()->json($vouchers, 200);
    }

    public function getAllActiveVouchers()
    {
        $vouchers = Voucher::where('status', 'C')->get();

        if (empty($vouchers)) {
            return response()->json(['message' => 'No vouchers registered'], 200);
        }

        return response()->json($vouchers, 200);
    }

    public function getAllUsedVouchers()
    {
        $vouchers = Voucher::where('status', 'U')->get();

        if (empty($vouchers)) {
            return response()->json(['message' => 'No vouchers registered'], 200);
        }

        return response()->json($vouchers, 200);
    }

    public function generateVoucher(Request $request)
    {
        try {
            $user = $request->user;
            $product = $request->product;

            if (!$user || !$product) {
                return throw new Error();
            }

            $code = $this->generateUniqueCode();

            $voucher = Voucher::create([
                'code' => $code,
                'user_id' => $user->id,
                'product_id' => $product->id,
                'used_date' => null,
                'status' => 'C'
            ]);

            return response()->json(['data' => ['voucher' => $voucher]], 200);
        } catch (Error $e) {
            return response()->json(['message' => 'Bad Request'], 400);
        }
    }

    public function getUserVouchers($id)
    {
        $vouchers = Voucher::where('user_id', $id)->get();

        if(!$vouchers){
            response()->json(['message' => 'Vouchers not found'], 404);
        }

        return response()->json($vouchers, 200);
    }

    public function useVoucher(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required',
        ], [
            'code.required' => 'The name field is required.',
        ]);

        $voucher = Voucher::where('code', $validatedData['code']);
        
        if(!$voucher){
            response()->json(['message' => 'Voucher not found'], 404);
        }

        $voucher->status = 'U';
        $voucher->used_date = date('Y-m-d H:i:s');

        $voucher->save();

        return response()->json(['data' => ['voucher' => $voucher], 'message' => 'Voucher successfully updated!'], 200);
    }
}
