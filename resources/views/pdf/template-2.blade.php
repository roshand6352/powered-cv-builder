<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $full_name }} — CV</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.45;
            color: #1a2332;
            background: #ffffff;
        }
        table.t2-header {
            width: 100%; border-collapse: collapse;
            background-color: #ffffff;
            border-bottom: 3px solid #1d4ed8;
            padding: 0;
        }
        td.t2-header-cell { padding: 28px 32px 22px; }
        .t2-name { font-size: 22pt; font-weight: bold; color: #0f172a; letter-spacing: -0.5px; }
        .t2-title { font-size: 11pt; color: #1d4ed8; font-weight: bold; margin-top: 2px; }
        table.t2-contact { width: 100%; margin-top: 12px; border-collapse: collapse; }
        table.t2-contact td {
            font-size: 8.5pt; color: #475569; padding: 2px 12px 2px 0;
            white-space: nowrap;
        }
        table.t2-contact td.t2-sep { color: #cbd5e1; padding: 2px 8px; width: 1%; }
        table.t2-body { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.t2-body td { vertical-align: top; padding: 22px 28px; }
        td.t2-left { width: 38%; background-color: #f8fafc; border-right: 1px solid #e2e8f0; }
        td.t2-right { width: 62%; }
        .t2-heading {
            font-size: 9.5pt; font-weight: bold; color: #1d4ed8;
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 8px; padding-bottom: 3px;
            border-bottom: 1px solid #bfdbfe;
        }
        .t2-section { margin-bottom: 18px; page-break-inside: avoid; }
        .t2-summary { font-size: 9.5pt; line-height: 1.55; color: #334155; text-align: justify; }
        .t2-skill-row { margin-bottom: 6px; page-break-inside: avoid; }
        .t2-skill-name { font-size: 9pt; font-weight: bold; color: #0f172a; }
        table.t2-skill-bar { width: 100%; border-collapse: collapse; margin-top: 2px; }
        table.t2-skill-bar td {
            height: 4px; background-color: #e2e8f0; font-size: 1px; line-height: 1px;
        }
        table.t2-skill-bar td.t2-fill { background-color: #1d4ed8; }
        .t2-item { margin-bottom: 12px; page-break-inside: avoid; }
        .t2-item-head { margin-bottom: 2px; }
        .t2-item-title { font-size: 10pt; font-weight: bold; color: #0f172a; }
        .t2-item-company { font-size: 9pt; color: #1d4ed8; font-weight: bold; }
        .t2-item-meta { font-size: 8.5pt; color: #64748b; margin-bottom: 3px; }
        .t2-item-note { font-size: 8.5pt; color: #64748b; font-style: italic; }
        ul.t2-bullets { margin: 2px 0 0 14px; padding: 0; }
        ul.t2-bullets li { font-size: 9pt; line-height: 1.42; margin-bottom: 2px; color: #334155; }
        .t2-proj-name { font-weight: bold; font-size: 9.5pt; }
        .t2-proj-link { font-size: 8.5pt; color: #1d4ed8; }
        .t2-proj-text { font-size: 9pt; margin-top: 2px; color: #334155; }
    </style>
</head>
<body>

<table class="t2-header" cellpadding="0" cellspacing="0">
<tr><td class="t2-header-cell">
    <div class="t2-name">{{ $full_name }}</div>
    <div class="t2-title">{{ $job_title }}</div>
    <table class="t2-contact" cellpadding="0" cellspacing="0">
        <tr>
            <td>&#9993; {{ $contact['email'] }}</td>
            <td class="t2-sep">|</td>
            <td>&#9742; {{ $contact['phone'] }}</td>
            <td class="t2-sep">|</td>
            <td>&#8962; {{ $contact['address'] }}</td>
        </tr>
        <tr>
            <td colspan="5" style="padding-top:4px;">
                &#9741; {{ $contact['website'] }} &nbsp;&nbsp; in {{ $contact['linkedin'] }}
            </td>
        </tr>
    </table>
</td></tr>
</table>

<table class="t2-body" cellpadding="0" cellspacing="0">
<tr>
    <td class="t2-left">
        <div class="t2-section">
            <div class="t2-heading">Executive Summary</div>
            <div class="t2-summary">{{ $summary }}</div>
        </div>

        <div class="t2-section">
            <div class="t2-heading">Core Skills</div>
            @foreach ($skills as $index => $skill)
                <div class="t2-skill-row">
                    <div class="t2-skill-name">{{ $skill }}</div>
                    <table class="t2-skill-bar" cellpadding="0" cellspacing="0">
                        <tr>
                            @php $fill = min(5, max(3, 5 - ($index % 3))); @endphp
                            @for ($i = 0; $i < 5; $i++)
                                <td style="width:20%;" class="{{ $i < $fill ? 't2-fill' : '' }}">&nbsp;</td>
                            @endfor
                        </tr>
                    </table>
                </div>
            @endforeach
        </div>
    </td>

    <td class="t2-right">
        <div class="t2-section">
            <div class="t2-heading">Professional Experience</div>
            @foreach ($experience as $job)
                <div class="t2-item">
                    <div class="t2-item-head">
                        <span class="t2-item-title">{{ $job['title'] }}</span>
                    </div>
                    <div class="t2-item-company">{{ $job['company'] }}</div>
                    <div class="t2-item-meta">{{ $job['location'] }} &bull; {{ $job['start'] }} — {{ $job['end'] }}</div>
                    @if (!empty($job['bullets']))
                        <ul class="t2-bullets">
                            @foreach ($job['bullets'] as $b)<li>{{ $b }}</li>@endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="t2-section">
            <div class="t2-heading">Education</div>
            @foreach ($education as $edu)
                <div class="t2-item">
                    <div class="t2-item-title">{{ $edu['degree'] }}</div>
                    <div class="t2-item-company">{{ $edu['institution'] }}</div>
                    <div class="t2-item-meta">{{ $edu['location'] }} &bull; {{ $edu['start'] }} — {{ $edu['end'] }}</div>
                    @if (!empty($edu['details']))<div class="t2-item-note">{{ $edu['details'] }}</div>@endif
                </div>
            @endforeach
        </div>

        <div class="t2-section">
            <div class="t2-heading">Key Projects</div>
            @foreach ($projects as $proj)
                <div class="t2-item">
                    <div class="t2-proj-name">{{ $proj['name'] }}</div>
                    @if (!empty($proj['url']))<div class="t2-proj-link">{{ $proj['url'] }}</div>@endif
                    <div class="t2-proj-text">{{ $proj['description'] }}</div>
                </div>
            @endforeach
        </div>
    </td>
</tr>
</table>

</body>
</html>
