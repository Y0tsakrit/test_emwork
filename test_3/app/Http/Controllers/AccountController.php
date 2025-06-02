<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::query(); 

        if ($request->has('month')) {
            $monthYear = $request->input('month'); 
            // Validate the format to prevent unexpected behavior
            if (preg_match('/^\d{4}-\d{2}$/', $monthYear)) {
                $year = substr($monthYear, 0, 4);
                $month = substr($monthYear, 5, 2);


                $query->whereYear('accDate', $year)
                      ->whereMonth('accDate', $month);
            } else {
                return response()->json([
                    'message' => 'Invalid month format. Expected format: YYYY-MM',
                    'error' => $monthYear,
                ], 400);
            }
        }


        $accounts = $query->orderBy('accDate', 'desc')->get();

        return response()->json($accounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'accType' => 'required|string|in:รายรับ,รายจ่าย',
                'accName' => 'required|string|max:255',
                'accAmount' => 'required|numeric',
                'accDate' => 'required|date',
            ]);

            $account = Account::create($validated);

            return response()->json([
                'message' => 'Account created successfully',
                'account' => $account,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $account = Account::findOrFail($id);

            return response()->json($account, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Account not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $account = Account::findOrFail($id);

            $validated = $request->validate([
                'accType' => 'required|string|in:รายรับ,รายจ่าย',
                'accName' => 'required|string|max:255',
                'accAmount' => 'required|numeric',
                'accDate' => 'required|date',
            ]);

            $account->update($validated);

            return response()->json([
                'message' => 'Account updated successfully',
                'account' => $account,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $account = Account::findOrFail($id);
            $account->delete();

            return response()->json([
                'message' => 'Account deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting account',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}