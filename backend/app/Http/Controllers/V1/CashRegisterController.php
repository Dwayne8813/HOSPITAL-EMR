<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Billing\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    public function __construct(
        private readonly BillingService $billingService,
    ) {
    }

    public function open(Request $request): JsonResponse
    {
        $request->validate([
            'initial_amount' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $register = $this->billingService->openCashRegister(
                $request->user()->institution,
                auth()->id(),
                $request->input('initial_amount')
            );

            return response()->json([
                'success' => true,
                'data' => $register,
                'message' => 'Cash register opened successfully.',
            ], 201);
        } catch (\App\Exceptions\BusinessLogicException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to open cash register.',
            ], 500);
        }
    }

    public function close(Request $request): JsonResponse
    {
        $request->validate([
            'register_id' => ['required', 'integer', 'exists:cash_registers,id'],
            'actual_amount' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $register = $this->billingService->closeCashRegister(
                $request->input('register_id'),
                auth()->id(),
                $request->input('actual_amount')
            );

            return response()->json([
                'success' => true,
                'data' => $register,
                'message' => 'Cash register closed successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Cash register not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to close cash register.',
            ], 500);
        }
    }

    public function movements(Request $request): JsonResponse
    {
        $request->validate([
            'register_id' => ['required', 'integer', 'exists:cash_registers,id'],
            'type' => ['required', 'string', 'in:inflow,outflow'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:500'],
        ]);

        try {
            $movement = $this->billingService->addCashMovement(
                $request->input('register_id'),
                $request->input('type'),
                $request->input('amount'),
                $request->input('description')
            );

            return response()->json([
                'success' => true,
                'data' => $movement,
                'message' => 'Cash movement recorded successfully.',
            ], 201);
        } catch (\App\Exceptions\BusinessLogicException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to record cash movement.',
            ], 500);
        }
    }

    public function arrears(Request $request): JsonResponse
    {
        $request->validate([
            'register_id' => ['required', 'integer', 'exists:cash_registers,id'],
        ]);

        try {
            $arrears = $this->billingService->getArrears($request->input('register_id'));

            return response()->json([
                'success' => true,
                'data' => $arrears,
                'message' => 'Arrears calculated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to calculate arrears.',
            ], 500);
        }
    }
}
