<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Report #{{ $patient->id }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 24px;
            color: #111827;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .meta {
            font-size: 12px;
            color: #6b7280;
        }
        .grid {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 10px 14px;
            margin-bottom: 22px;
        }
        .label {
            font-weight: 700;
            color: #374151;
        }
        .value {
            color: #111827;
        }
        .section {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 14px;
        }
        .section h2 {
            margin: 0 0 10px;
            font-size: 16px;
        }
        .toolbar {
            margin-bottom: 14px;
        }
        .btn {
            border: 0;
            background: #111827;
            color: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
        }
        @media print {
            .toolbar { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <div class="header">
        <h1>Patient Data Report</h1>
        <div class="meta">
            Generated: {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>

    <div class="section">
        <h2>Basic Information</h2>
        <div class="grid">
            <div class="label">ID</div><div class="value">{{ $patient->id }}</div>
            <div class="label">Name</div><div class="value">{{ $patient->name }}</div>
            <div class="label">Email</div><div class="value">{{ $patient->email }}</div>
            <div class="label">Phone</div><div class="value">{{ $patient->phone ?: '-' }}</div>
            <div class="label">City</div><div class="value">{{ $patient->city ?: '-' }}</div>
            <div class="label">Age</div><div class="value">{{ $patient->age ?: '-' }}</div>
            <div class="label">Gender</div><div class="value">{{ $patient->gender ?: '-' }}</div>
            <div class="label">Status</div><div class="value">{{ $patient->approval_status ?: 'approved' }}</div>
        </div>
    </div>

    <div class="section">
        <h2>Medical Profile</h2>
        <div class="grid">
            <div class="label">Blood Type</div><div class="value">{{ $patient->blood_type ?: '-' }}</div>
            <div class="label">Address</div><div class="value">{{ $patient->address ?: '-' }}</div>
            <div class="label">Emergency Contact</div><div class="value">{{ $patient->emergency_contact ?: '-' }}</div>
            <div class="label">Created At</div><div class="value">{{ optional($patient->created_at)->format('Y-m-d H:i') ?: '-' }}</div>
        </div>
    </div>
</body>
</html>

