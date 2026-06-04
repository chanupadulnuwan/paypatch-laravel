<?php

namespace Tests\Unit;

use App\Services\DebtCalculatorService;
use PHPUnit\Framework\TestCase;

class DebtCalculatorTest extends TestCase
{
    private DebtCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DebtCalculatorService();
    }

    /**
     * Test simplification with empty balances (everyone is already settled).
     */
    public function test_simplification_with_empty_balances(): void
    {
        $balances = [];
        $result = $this->service->simplify($balances);

        $this->assertEmpty($result);
    }

    /**
     * Test simplification where everyone has exactly zero balance.
     */
    public function test_simplification_with_zero_balances(): void
    {
        $balances = [
            1 => 0.00,
            2 => 0.00,
            3 => 0.00,
        ];
        $result = $this->service->simplify($balances);

        $this->assertEmpty($result);
    }

    /**
     * Test simple 2-person debt (User 2 owes User 1).
     */
    public function test_simple_two_person_debt(): void
    {
        $balances = [
            1 => 50.00,  // Owed LKR 50
            2 => -50.00, // Owes LKR 50
        ];
        $result = $this->service->simplify($balances);

        $this->assertCount(1, $result);
        $this->assertEquals(2, $result[0]['from']);
        $this->assertEquals(1, $result[0]['to']);
        $this->assertEquals(50.00, $result[0]['amount']);
    }

    /**
     * Test 3-person direct offset debt.
     */
    public function test_three_person_offset_debt(): void
    {
        $balances = [
            1 => 100.00,  // Owed LKR 100
            2 => -60.00,  // Owes LKR 60
            3 => -40.00,  // Owes LKR 40
        ];
        $result = $this->service->simplify($balances);

        // Should return exactly 2 transactions:
        // 2 pays 1 (60.00)
        // 3 pays 1 (40.00)
        $this->assertCount(2, $result);

        // Find transaction from User 2
        $txFrom2 = collect($result)->firstWhere('from', 2);
        $this->assertNotNull($txFrom2);
        $this->assertEquals(1, $txFrom2['to']);
        $this->assertEquals(60.00, $txFrom2['amount']);

        // Find transaction from User 3
        $txFrom3 = collect($result)->firstWhere('from', 3);
        $this->assertNotNull($txFrom3);
        $this->assertEquals(1, $txFrom3['to']);
        $this->assertEquals(40.00, $txFrom3['amount']);
    }

    /**
     * Test complex 4-person debt cycles.
     */
    public function test_complex_four_person_cyclic_debt(): void
    {
        $balances = [
            1 => 80.00,   // Owed LKR 80
            2 => 20.00,   // Owed LKR 20
            3 => -70.00,  // Owes LKR 70
            4 => -30.00,  // Owes LKR 30
        ];
        $result = $this->service->simplify($balances);

        // The greedy algorithm resolves this by matching biggest debtor with biggest creditor:
        // Big debtor 3 (70.00) -> Big creditor 1 (80.00) gets matched first.
        // User 3 pays User 1 LKR 70.00.
        // Remaining creditor 1 (10.00), remaining debtor 3 (0.00).
        // Then match next: Debtor 4 (30.00) -> Creditor 2 (20.00).
        // User 4 pays User 2 LKR 20.00.
        // Remaining debtor 4 (10.00), remaining creditor 2 (0.00).
        // Finally, remaining Debtor 4 (10.00) -> remaining Creditor 1 (10.00).
        // User 4 pays User 1 LKR 10.00.
        // Total transactions should be 3.
        $this->assertCount(3, $result);

        // Verify total money transferred is equal to absolute sum of debts
        $totalTransfer = collect($result)->sum('amount');
        $this->assertEquals(100.00, $totalTransfer);
    }
}
