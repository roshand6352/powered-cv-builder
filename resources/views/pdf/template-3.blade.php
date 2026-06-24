<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $full_name }} — CV</title>
    <style>
        @page { size: A4 portrait; margin: 18mm 16mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.45;
            color: #111827;
            background: #ffffff;
        }
        table.t3-top {
            width: 100%; border-collapse: collapse;
            border-bottom: 2px solid #111827;
            margin-bottom: 14px;
        }
        td.t3-top-left { width: 72%; vertical-align: middle; padding-bottom: 10px; }
        td.t3-top-right { width: 28%; text-align: right; vertical-align: middle; padding-bottom: 10px; }
        .t3-name { font-size: 20pt; font-weight: bold; color: #111827; letter-spacing: 0.5px; }
        .t3-role { font-size: 10pt; color: #374151; margin-top: 2px; }
        .t3-photo img {
            width: 80px; height: 80px;
            border: 2px solid #111827;
        }
        table.t3-personal {
            width: 100%; border-collapse: collapse;
            border: 1px solid #d1d5db;
            margin-bottom: 14px;
        }
        table.t3-personal td {
            font-size: 8.5pt; padding: 6px 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            word-wrap: break-word;
        }
        table.t3-personal td.t3-label {
            width: 18%; font-weight: bold;
            background-color: #f9fafb; color: #374151;
        }
        .t3-section {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .t3-heading {
            font-size: 10pt; font-weight: bold;
            text-transform: uppercase; letter-spacing: 1.2px;
            color: #111827;
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-left: 4px solid #111827;
            padding: 5px 10px;
            margin-bottom: 8px;
        }
        .t3-text { font-size: 9.5pt; line-height: 1.55; text-align: justify; color: #1f2937; }
        table.t3-exp {
            width: 100%; border-collapse: collapse;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        table.t3-exp td { vertical-align: top; padding: 0; }
        td.t3-exp-date {
            width: 22%; font-size: 8.5pt; color: #6b7280;
            padding-right: 10px; line-height: 1.35;
        }
        td.t3-exp-body { width: 78%; }
        .t3-exp-title { font-size: 10pt; font-weight: bold; color: #111827; }
        .t3-exp-org { font-size: 9pt; color: #374151; font-weight: bold; }
        .t3-exp-loc { font-size: 8.5pt; color: #6b7280; margin-bottom: 3px; }
        ul.t3-bullets { margin: 2px 0 0 14px; padding: 0; }
        ul.t3-bullets li { font-size: 9pt; line-height: 1.42; margin-bottom: 2px; }
        table.t3-skills {
            width: 100%; border-collapse: collapse;
            border: 1px solid #e5e7eb;
        }
        table.t3-skills td {
            font-size: 9pt; padding: 5px 10px;
            border: 1px solid #e5e7eb;
            width: 50%;
            vertical-align: top;
        }
        table.t3-skills td.t3-skill-head {
            font-weight: bold; background-color: #f9fafb;
            width: 100%; border-bottom: 1px solid #d1d5db;
        }
        .t3-edu-item { margin-bottom: 8px; page-break-inside: avoid; }
        .t3-edu-degree { font-weight: bold; font-size: 9.5pt; }
        .t3-edu-meta { font-size: 8.5pt; color: #6b7280; }
        .t3-cert-row {
            border-bottom: 1px solid #e5e7eb;
            padding: 5px 0;
            page-break-inside: avoid;
        }
        .t3-cert-name { font-weight: bold; font-size: 9.5pt; }
        .t3-cert-detail { font-size: 8.5pt; color: #6b7280; }
        .t3-proj { margin-bottom: 8px; page-break-inside: avoid; }
        .t3-proj-name { font-weight: bold; font-size: 9.5pt; }
        .t3-proj-url { font-size: 8.5pt; color: #374151; }
        .t3-proj-desc { font-size: 9pt; margin-top: 1px; }
        table.t3-refs { width: 100%; border-collapse: collapse; }
        table.t3-refs td {
            width: 50%; vertical-align: top;
            padding: 0 10px 0 0;
            font-size: 9pt;
            page-break-inside: avoid;
        }
        .t3-ref-name { font-weight: bold; }
        .t3-ref-detail { font-size: 8.5pt; color: #6b7280; line-height: 1.4; }
    </style>
</head>
<body>

<table class="t3-top" cellpadding="0" cellspacing="0">
<tr>
    <td class="t3-top-left">
        <div class="t3-name">{{ $full_name }}</div>
        <div class="t3-role">{{ $job_title }}</div>
    </td>
    <td class="t3-top-right">
        <div class="t3-photo"><img src="{{ $photo }}" alt="{{ $full_name }}"></div>
    </td>
</tr>
</table>

<table class="t3-personal" cellpadding="0" cellspacing="0">
<tr>
    <td class="t3-label">Email</td>
    <td>{{ $contact['email'] }}</td>
    <td class="t3-label">Phone</td>
    <td>{{ $contact['phone'] }}</td>
</tr>
<tr>
    <td class="t3-label">Address</td>
    <td colspan="3">{{ $contact['address'] }}</td>
</tr>
<tr>
    <td class="t3-label">Website</td>
    <td>{{ $contact['website'] }}</td>
    <td class="t3-label">LinkedIn</td>
    <td>{{ $contact['linkedin'] }}</td>
</tr>
</table>

<div class="t3-section">
    <div class="t3-heading">Professional Summary</div>
    <div class="t3-text">{{ $summary }}</div>
</div>

<div class="t3-section">
    <div class="t3-heading">Work Experience</div>
    @foreach ($experience as $job)
        <table class="t3-exp" cellpadding="0" cellspacing="0">
        <tr>
            <td class="t3-exp-date">{{ $job['start'] }}<br>—<br>{{ $job['end'] }}</td>
            <td class="t3-exp-body">
                <div class="t3-exp-title">{{ $job['title'] }}</div>
                <div class="t3-exp-org">{{ $job['company'] }}</div>
                <div class="t3-exp-loc">{{ $job['location'] }}</div>
                @if (!empty($job['bullets']))
                    <ul class="t3-bullets">
                        @foreach ($job['bullets'] as $b)<li>{{ $b }}</li>@endforeach
                    </ul>
                @endif
            </td>
        </tr>
        </table>
    @endforeach
</div>

<div class="t3-section">
    <div class="t3-heading">Skills</div>
    <table class="t3-skills" cellpadding="0" cellspacing="0">
        <tr><td class="t3-skill-head" colspan="2">Technical &amp; Professional Competencies</td></tr>
        @foreach (array_chunk($skills, 2) as $pair)
            <tr>
                <td>{{ $pair[0] ?? '' }}</td>
                <td>{{ $pair[1] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
</div>

<div class="t3-section">
    <div class="t3-heading">Education</div>
    @foreach ($education as $edu)
        <div class="t3-edu-item">
            <div class="t3-edu-degree">{{ $edu['degree'] }}</div>
            <div class="t3-edu-meta">
                {{ $edu['institution'] }} | {{ $edu['location'] }} | {{ $edu['start'] }} — {{ $edu['end'] }}
                @if (!empty($edu['details'])) — {{ $edu['details'] }} @endif
            </div>
        </div>
    @endforeach
</div>

<div class="t3-section">
    <div class="t3-heading">Certifications</div>
    @foreach ($certifications as $cert)
        <div class="t3-cert-row">
            <div class="t3-cert-name">{{ $cert['name'] }}</div>
            <div class="t3-cert-detail">{{ $cert['issuer'] }} | {{ $cert['year'] }}</div>
        </div>
    @endforeach
</div>

<div class="t3-section">
    <div class="t3-heading">Projects</div>
    @foreach ($projects as $proj)
        <div class="t3-proj">
            <div class="t3-proj-name">{{ $proj['name'] }}</div>
            @if (!empty($proj['url']))<div class="t3-proj-url">{{ $proj['url'] }}</div>@endif
            <div class="t3-proj-desc">{{ $proj['description'] }}</div>
        </div>
    @endforeach
</div>

<div class="t3-section">
    <div class="t3-heading">References</div>
    <table class="t3-refs" cellpadding="0" cellspacing="0">
        <tr>
            @foreach ($references as $ref)
                <td>
                    <div class="t3-ref-name">{{ $ref['name'] }}</div>
                    <div class="t3-ref-detail">{{ $ref['title'] }}</div>
                    <div class="t3-ref-detail">{{ $ref['contact'] }}</div>
                </td>
            @endforeach
        </tr>
    </table>
</div>

</body>
</html>
