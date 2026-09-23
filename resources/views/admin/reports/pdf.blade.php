@php
    $typeDisplay = match (strtolower((string) $type)) {
        'all'       => __('messages.reports_all_reports'),
        'booking'   => __('messages.reports_booking_report'),
        'user'      => __('messages.reports_user_report'),
        'complaint' => __('messages.reports_complaint_report'),
        default     => ucfirst((string) $type),
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.report_pdf_page_title', ['type' => $typeDisplay]) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, 'Segoe UI', Roboto, Arial, sans-serif;
            color: #1B3B36;
            background: #f5f5f5;
            padding: 16px;
            font-size: 12px;
        }

        .toolbar {
            max-width: 1100px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-print { background: #F07A3A; color: #fff; }
        .btn-back  { background: #e5e7eb; color: #333; }

        .sheet {
            max-width: 1100px;
            margin: 0 auto;
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .header {
            border-bottom: 3px solid #1B3B36;
            padding-bottom: 16px;
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .header h1 { font-size: 20px; color: #1B3B36; }
        .header .meta {
            font-size: 11px;
            color: #666;
            line-height: 1.6;
        }

        .section { margin-bottom: 32px; page-break-inside: auto; }
        .section-title {
            font-size: 13px;
            font-weight: 800;
            color: #1B3B36;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding-bottom: 6px;
            border-bottom: 2px solid #F07A3A;
            margin-bottom: 12px;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #eaeaea;
            border-radius: 8px;
        }

        table {
            width: 100%;
            min-width: 640px;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #eaeaea;
            white-space: nowrap;
        }
        th {
            background: #f9fafb;
            font-weight: 800;
            color: #555;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.04em;
            position: sticky;
            top: 0;
        }
        tbody tr:nth-child(even) { background: #fafafa; }
        tbody tr:last-child td { border-bottom: none; }

        .empty { color: #999; font-style: italic; padding: 12px 0; }
        .footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #eaeaea;
            text-align: center;
            color: #888;
            font-size: 10px;
        }

        @media (max-width: 640px) {
            body { padding: 8px; font-size: 11px; }
            .sheet { padding: 16px; border-radius: 8px; }
            .header h1 { font-size: 17px; }
            .header .meta { font-size: 10px; }
            .section-title { font-size: 12px; }
            .toolbar { justify-content: center; }

            .table-wrap::before {
                content: '← Scroll horizontally to see more →';
                display: block;
                font-size: 9px;
                color: #999;
                padding: 6px 8px;
                background: #fafafa;
                border-bottom: 1px solid #eaeaea;
                text-align: center;
            }
        }

        @media print {
            body { background: #fff; padding: 0; font-size: 11px; }
            .toolbar { display: none; }
            .sheet {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
                border-radius: 0;
            }
            .table-wrap {
                border: 1px solid #ddd;
                border-radius: 0;
            }
            table { min-width: 0; }
            th, td { padding: 6px 8px; font-size: 10px; }
            .header { flex-direction: row; justify-content: space-between; align-items: flex-end; }
            .header .meta { text-align: right; }
            .table-wrap::before { display: none; }
            @page { margin: 12mm; size: A4 landscape; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <a href="{{ route('admin.reports') }}" class="btn btn-back">{{ __('messages.report_pdf_back') }}</a>
        <button onclick="window.print()" class="btn btn-print">{{ __('messages.report_pdf_print') }}</button>
    </div>

    <div class="sheet">
        <div class="header">
            <div>
                <h1>{{ __('messages.report_pdf_heading', ['type' => $typeDisplay]) }}</h1>
                <p style="margin-top:4px; color:#666;">
                    {{ strtolower((string) $type) === 'all'
                        ? __('messages.report_pdf_full_system')
                        : __('messages.report_pdf_type_report', ['type' => $typeDisplay]) }}
                </p>
            </div>
            <div class="meta">
                <div><strong>{{ __('messages.report_pdf_from') }}</strong> {{ $from ?: '—' }}</div>
                <div><strong>{{ __('messages.report_pdf_to') }}</strong> {{ $to ?: '—' }}</div>
                <div><strong>{{ __('messages.report_pdf_generated') }}</strong> {{ now()->format('F j, Y g:i A') }}</div>
            </div>
        </div>

        {{-- USERS --}}
        @if ($users->isNotEmpty())
            <div class="section">
                <h2 class="section-title">{{ __('messages.report_pdf_section_users', ['count' => $users->count()]) }}</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('messages.report_pdf_col_id') }}</th>
                                <th>{{ __('messages.report_pdf_col_name') }}</th>
                                <th>{{ __('messages.report_pdf_col_email') }}</th>
                                <th>{{ __('messages.report_pdf_col_role') }}</th>
                                <th>{{ __('messages.report_pdf_col_verified') }}</th>
                                <th>{{ __('messages.report_pdf_col_status') }}</th>
                                <th>{{ __('messages.report_pdf_col_joined') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $u)
                                <tr>
                                    <td>{{ $u->id }}</td>
                                    <td>{{ trim(($u->f_name ?? '') . ' ' . ($u->l_name ?? '')) }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ $u->is_sitter ? __('messages.report_pdf_role_sitter') : __('messages.role_owner') }}</td>
                                    <td>{{ $u->id_validation_status === 'verified' ? __('messages.report_pdf_yes') : __('messages.report_pdf_no') }}</td>
                                    <td>{{ ucfirst($u->status) }}</td>
                                    <td>{{ optional($u->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- BOOKINGS --}}
        @if ($bookings->isNotEmpty())
            <div class="section">
                <h2 class="section-title">{{ __('messages.report_pdf_section_bookings', ['count' => $bookings->count()]) }}</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('messages.report_pdf_col_id') }}</th>
                                <th>{{ __('messages.report_pdf_col_owner') }}</th>
                                <th>{{ __('messages.report_pdf_col_sitter') }}</th>
                                <th>{{ __('messages.report_pdf_col_pet') }}</th>
                                <th>{{ __('messages.report_pdf_col_status') }}</th>
                                <th>{{ __('messages.report_pdf_col_total') }}</th>
                                <th>{{ __('messages.report_pdf_col_created') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $b)
                                <tr>
                                    <td>{{ $b->id }}</td>
                                    <td>{{ trim(($b->owner->f_name ?? '') . ' ' . ($b->owner->l_name ?? '')) ?: '—' }}</td>
                                    <td>{{ trim(($b->sitter->f_name ?? '') . ' ' . ($b->sitter->l_name ?? '')) ?: '—' }}</td>
                                    <td>{{ $b->pet->name ?? '—' }}</td>
                                    <td>{{ ucfirst($b->status) }}</td>
                                    <td>₱{{ number_format($b->subtotal ?? $b->total_amount ?? 0, 2) }}</td>
                                    <td>{{ optional($b->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- COMPLAINTS --}}
        @if ($complaints->isNotEmpty())
            <div class="section">
                <h2 class="section-title">{{ __('messages.report_pdf_section_complaints', ['count' => $complaints->count()]) }}</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('messages.report_pdf_col_id') }}</th>
                                <th>{{ __('messages.report_pdf_col_description') }}</th>
                                <th>{{ __('messages.report_pdf_col_status') }}</th>
                                <th>{{ __('messages.report_pdf_col_created') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($complaints as $c)
                                <tr>
                                    <td>{{ $c->id }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($c->description ?? $c->subject ?? '—', 80) }}</td>
                                    <td>{{ ucfirst($c->status) }}</td>
                                    <td>{{ optional($c->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($users->isEmpty() && $bookings->isEmpty() && $complaints->isEmpty())
            <p class="empty">{{ __('messages.report_pdf_empty') }}</p>
        @endif

        <div class="footer">
            {{ __('messages.report_pdf_footer', ['date' => now()->format('F j, Y \a\t g:i A')]) }}
        </div>
    </div>

</body>
</html>