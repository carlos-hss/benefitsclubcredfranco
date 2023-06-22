<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Voucher;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
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
        $vouchers = Voucher::with('product')->get();

        if (empty($vouchers)) {
            return response()->json(['message' => 'No vouchers registered'], 200);
        }

        return response()->json(['vouchers' => $vouchers], 200);
    }

    public function getAllActiveVouchers()
    {
        $vouchers = Voucher::where('status', 'C')->with('product')->get();

        if (empty($vouchers)) {
            return response()->json(['message' => 'No vouchers registered'], 200);
        }

        return response()->json(['vouchers' => $vouchers], 200);
    }

    public function getAllUsedVouchers()
    {
        $vouchers = Voucher::where('status', 'U')->with('product')->get();

        if (empty($vouchers)) {
            return response()->json(['message' => 'No vouchers registered'], 200);
        }

        return response()->json(['vouchers' => $vouchers], 200);
    }

    public function generateVoucher(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'product_id' => 'required',
        ]);
    
        $code = $this->generateUniqueCode();

        $voucher = new Voucher();
        $voucher->code = $code;
        $voucher->user_id = $validatedData['user_id'];
        $voucher->product_id = $validatedData['product_id'];
        $voucher->used_date = null;
        $voucher->status = 'C';
        $voucher->save();
    
        return response()->json(['voucher' => $voucher, 'message' => 'Voucher generated'], 200);
    }

    public function getUserVouchers($id)
    {
        $user = User::find($id);
        $vouchers = $user->vouchers()->with('product')->get();

        if(!$vouchers){
            response()->json(['message' => 'Vouchers not found'], 404);
        }

        return response()->json(['vouchers' => $vouchers], 200);
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

        $user = User::find($voucher->user_id);
        $product = Product::find($voucher->product_id);

        $voucher->status = 'U';
        $voucher->used_date = date('Y-m-d H:i:s');

        $user->points -= $product->points_cost;

        $voucher->save();

        return response()->json(['voucher' => $voucher::with('product'), 'message' => 'Voucher successfully updated!'], 200);
    }
}
