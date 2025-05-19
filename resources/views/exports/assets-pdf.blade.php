<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            font-size: 12px;
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <!-- Dynamic headers based on selected fields -->
                @foreach($fields as $field)
                    <th>{{ ucfirst(str_replace('_', ' ', $field)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <!-- For inventory report -->
            @if(strpos($title, 'Inventory') !== false)
                @foreach($data as $asset)
                    <tr>
                        @foreach($fields as $field)
                            <td>
                                @switch($field)
                                    @case('property_tag_number')
                                        {{ $asset->property_tag_number ?? 'N/A' }}
                                        @break
                                    @case('brand')
                                        {{ $asset->brand ?? 'N/A' }}
                                        @break
                                    @case('model')
                                        {{ $asset->model ?? 'N/A' }}
                                        @break
                                    @case('serial_number')
                                        {{ $asset->serial_number ?? 'N/A' }}
                                        @break
                                    @case('po_number')
                                        {{ $asset->po_number ?? 'N/A' }}
                                        @break
                                    @case('category')
                                        {{ $asset->category->name ?? 'N/A' }}
                                        @break
                                    @case('location')
                                        {{ $asset->location->name ?? 'N/A' }}
                                        @break
                                    @case('status')
                                        {{ ucfirst($asset->status ?? 'N/A') }}
                                        @break
                                    @case('purchase_date')
                                        {{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'N/A' }}
                                        @break
                                    @case('purchase_value')
                                        {{ number_format($asset->purchase_value, 2) ?? 'N/A' }}
                                        @break
                                    @case('assigned_to')
                                        {{ $asset->assignedUser->name ?? 'Unassigned' }}
                                        @break
                                    @case('description')
                                        {{ $asset->description ?? 'N/A' }}
                                        @break
                                    @default
                                        {{ $asset->{$field} ?? 'N/A' }}
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @else
                <!-- For movement report -->
                @foreach($data as $movement)
                    <tr>
                        @foreach($fields as $field)
                            <td>
                                @switch($field)
                                    @case('asset')
                                        {{ $movement->asset->brand ?? 'N/A' }} {{ $movement->asset->model ?? '' }}
                                        @break
                                    @case('property_tag_number')
                                        {{ $movement->asset->property_tag_number ?? 'N/A' }}
                                        @break
                                    @case('serial_number')
                                        {{ $movement->asset->serial_number ?? 'N/A' }}
                                        @break
                                    @case('from_location')
                                        {{ $movement->fromLocation->name ?? 'N/A' }}
                                        @break
                                    @case('to_location')
                                        {{ $movement->toLocation->name ?? 'N/A' }}
                                        @break
                                    @case('assigned_by')
                                        {{ $movement->assignedByUser->name ?? 'N/A' }}
                                        @break
                                    @case('assigned_to')
                                        {{ $movement->assignedToUser->name ?? 'Unassigned' }}
                                        @break
                                    @case('movement_date')
                                        {{ $movement->movement_date ? $movement->movement_date->format('M d, Y') : 'N/A' }}
                                        @break
                                    @case('movement_type')
                                        {{ ucfirst($movement->movement_type ?? 'N/A') }}
                                        @break
                                    @default
                                        {{ $movement->{$field} ?? 'N/A' }}
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>ITSSMO-Tool PAMO Asset Management System | Page 1</p>
    </div>
</body>
</html>
