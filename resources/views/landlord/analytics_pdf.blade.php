<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Performance Analysis Statement</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2D3748; line-height: 1.4; font-size: 13px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .header-title { font-size: 24px; font-weight: bold; color: #1A202C; margin: 0; }
        .header-meta { text-align: right; font-size: 11px; color: #718096; font-family: monospace; }
        
        .metric-container { width: 100%; margin-bottom: 25px; }
        .metric-card { width: 31%; display: inline-block; background-color: #F7FAFC; border: 1px solid #E2E8F0; border-radius: 4px; padding: 12px; margin-right: 1.5%; vertical-align: top; }
        .metric-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #4A5568; margin-bottom: 5px; font-weight: bold; }
        .metric-value { font-size: 18px; font-weight: bold; color: #2B6CB0; }
        .metric-sub { font-size: 10px; color: #718096; margin-top: 3px; }
        
        .section-title { font-size: 14px; font-weight: bold; color: #2D3748; border-bottom: 2px solid #E2E8F0; padding-bottom: 6px; margin-bottom: 12px; margin-top: 20px; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th { background-color: #EDF2F7; text-align: left; padding: 8px 10px; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #4A5568; border-bottom: 1px solid #CBD5E0; }
        table.data-table td { padding: 8px 10px; border-bottom: 1px solid #E2E8F0; font-size: 12px; }
        table.data-table tr:nth-child(even) { background-color: #F7FAFC; }
        
        .summary-box { width: 45%; margin-left: auto; background-color: #EDF2F7; padding: 12px; border-radius: 4px; margin-top: 15px; }
        .summary-row { font-size: 12px; padding: 3px 0; }
        .summary-label { color: #4A5568; display: inline-block; width: 60%; }
        .summary-val { font-weight: bold; text-align: right; display: inline-block; width: 35%; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #A0AEC0; border-top: 1px solid #E2E8F0; padding-top: 6px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <h1 class="header-title">DavaoDormConnect</h1>
                <div style="color: #4A5568; font-size: 12px; margin-top: 3px;">Housing Portfolio Yield Statement</div>
            </td>
            <td class="header-meta">
                Report Run: {{ now()->format('M d, Y h:i A') }}<br>
                Manager Reference Code: LL-{{ Auth::id() }}
            </td>
        </tr>
    </table>

    <div class="section-title">EXECUTIVE PERFORMANCE OVERVIEW</div>
    
    <div class="metric-container">
        <div class="metric-card">
            <div class="metric-label">Active Collected Revenue</div>
            <div class="metric-value" style="color: #2B6CB0;">PHP {{ number_format($currentActiveRevenue, 2) }}</div>
            <div class="metric-sub">Out of {{ number_format($monthlyGrossPotential, 2) }} max capacity</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Vacancy Loss Margin</div>
            <div class="metric-value" style="color: #DD6B20;">PHP {{ number_format($vacancyLoss, 2) }}</div>
            <div class="metric-sub">Unrented broadcasting space drain</div>
        </div>
        <div class="metric-card" style="margin-right: 0;">
            <div class="metric-label">Portfolio Occupancy</div>
            <div class="metric-value" style="color: #38A169;">{{ $occupancyRate }}%</div>
            <div class="metric-sub">Allocation Yield across total structures</div>
        </div>
    </div>

    <div class="section-title">ACTIVE RENTAL OCCUPANCY LEDGER</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Room ID</th>
                <th style="width: 45%;">Verified Resident Student</th>
                <th style="width: 20%;">Lease Commitment</th>
                <th style="width: 20%; text-align: right;">Monthly Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activeTenants as $tenant)
                <tr>
                    <td style="font-weight: bold;">#{{ $tenant['room_number'] }}</td>
                    <td>{{ $tenant['tenant_name'] }}</td>
                    <td style="color: #4A5568;">{{ $tenant['started_at'] }}</td>
                    <td style="text-align: right; font-weight: bold; color: #2B6CB0;">PHP {{ number_format($tenant['rate'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #718096; padding: 20px;">No registered active student tenants mapped to assets.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">INVENTORY BREAKDOWN METRICS</div>
    <table class="data-table" style="width: 50%;">
        <thead>
            <tr>
                <th>Inventory Classification Metric</th>
                <th style="text-align: right; width: 30%;">Count Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Configured Dormitory Rooms</td>
                <td style="text-align: right; font-weight: bold;">{{ $totalRooms }}</td>
            </tr>
            <tr>
                <td>Occupied Locked Rooms</td>
                <td style="text-align: right; font-weight: bold; color: #E53E3E;">{{ $occupiedRooms }}</td>
            </tr>
            <tr>
                <td>Vacant Public Broadcasting Assets</td>
                <td style="text-align: right; font-weight: bold; color: #38A169;">{{ $vacantRooms }}</td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row" style="border-bottom: 1px solid #CBD5E0; padding-bottom: 4px; margin-bottom: 4px; font-weight: bold;">
            <span class="summary-label">Financial Parameter</span>
            <span class="summary-val">Monthly Projection</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Gross Capacity Yield:</span>
            <span class="summary-val">PHP {{ number_format($monthlyGrossPotential, 2) }}</span>
        </div>
        <div class="summary-row" style="color: #E53E3E;">
            <span class="summary-label">Less Vacancy Loss:</span>
            <span class="summary-val">- PHP {{ number_format($vacancyLoss, 2) }}</span>
        </div>
        <div class="summary-row" style="border-top: 1px solid #CBD5E0; margin-top: 4px; padding-top: 4px; font-weight: bold; font-size: 13px; color: #2B6CB0;">
            <span class="summary-label">Net Projected Cashflow:</span>
            <span class="summary-val">PHP {{ number_format($currentActiveRevenue, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        DavaoDormConnect Platform Automated Statement Service • Confidential Management Information Log
    </div>

</body>
</html>