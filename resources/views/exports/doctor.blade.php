<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Report #{{ $doctor->id }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 24px; color: #111827; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #e5e7eb; padding-bottom: 12px; margin-bottom: 20px; }
        h1 { margin: 0; font-size: 24px; }
        .meta { font-size: 12px; color: #6b7280; }
        .grid { display: grid; grid-template-columns: 180px 1fr; gap: 10px 14px; }
        .label { font-weight: 700; color: #374151; }
        .section { border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; margin-bottom: 14px; }
        .photo { width: 100px; height: 100px; border-radius: 10px; object-fit: cover; border: 1px solid #e5e7eb; }
        .toolbar { margin-bottom: 14px; }
        .btn { border: 0; background: #111827; color: #fff; padding: 8px 14px; border-radius: 6px; cursor: pointer; }
        @media print { .toolbar { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <div class="header">
        <h1>Doctor Data Report</h1>
        <div class="meta">Generated: {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <div class="section">
        <div class="grid">
            <div class="label">Photo</div>
            <div>
                @if($doctor->photo_path)
                    <img class="photo" src="{{ asset('storage/' . $doctor->photo_path) }}" alt="Doctor photo">
                @else
                    -
                @endif
            </div>
            <div class="label">ID</div><div>{{ $doctor->id }}</div>
            <div class="label">Name</div><div>{{ $doctor->name }}</div>
            <div class="label">Email</div><div>{{ $doctor->email }}</div>
            <div class="label">Phone</div><div>{{ $doctor->phone ?: '-' }}</div>
            <div class="label">License Number</div><div>{{ $doctor->license_number }}</div>
            <div class="label">Specialization</div><div>{{ $doctor->specialization }}</div>
            <div class="label">Hospital</div><div>{{ $doctor->hospital_name ?: '-' }}</div>
            <div class="label">Experience</div><div>{{ $doctor->years_experience ?? 0 }} years</div>
            <div class="label">Rating</div><div>{{ $doctor->rating ?? 0 }}</div>
            <div class="label">Verified</div><div>{{ $doctor->verified ? 'Yes' : 'No' }}</div>
            <div class="label">Created At</div><div>{{ optional($doctor->created_at)->format('Y-m-d H:i') ?: '-' }}</div>
        </div>
    </div>

    <div class="section">
        <strong>Bio</strong>
        <p>{{ $doctor->bio ?: '-' }}</p>
    </div>
</body>
</html>
