<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $full_name }} — CV | {{ config('app.name') }}</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.45;
            color: #1e293b;
            background: #ffffff;
        }
        table.t1-layout { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.t1-layout td { vertical-align: top; }
        td.t1-sidebar {
            width: 30%;
            background-color: #0f2744;
            color: #ffffff;
            padding: 26px 20px;
        }
        td.t1-main { width: 70%; padding: 26px 28px; }
        .t1-photo { text-align: center; margin-bottom: 16px; }
        .t1-photo img {
            width: 100px; height: 100px;
            border-radius: 50px;
            border: 3px solid #3b82f6;
        }
        .t1-name { font-size: 15pt; font-weight: bold; text-align: center; line-height: 1.2; }
        .t1-role { font-size: 9pt; color: #94a3b8; text-align: center; margin: 4px 0 18px; }
        .t1-side-title {
            font-size: 8pt; font-weight: bold; text-transform: uppercase;
            letter-spacing: 1.2px; border-bottom: 1px solid #334155;
            padding-bottom: 4px; margin-bottom: 8px;
        }
        .t1-side-block { margin-bottom: 16px; page-break-inside: avoid; }
        .t1-contact { font-size: 8.5pt; margin-bottom: 6px; line-height: 1.4; word-wrap: break-word; }
        .t1-icon { color: #60a5fa; display: inline-block; width: 14px; }
        .t1-skill {
            display: inline-block; background: #1e3a5f; color: #e2e8f0;
            font-size: 7.5pt; padding: 3px 7px; margin: 0 3px 4px 0; border-radius: 2px;
        }
        .t1-lang { font-size: 8.5pt; margin-bottom: 5px; }
        .t1-lang span { color: #94a3b8; }
        .t1-heading {
            font-size: 11pt; font-weight: bold; color: #0f2744;
            text-transform: uppercase; letter-spacing: 0.6px;
            border-bottom: 2px solid #dbeafe; padding-bottom: 3px; margin-bottom: 10px;
        }
        .t1-section { margin-bottom: 16px; page-break-inside: avoid; }
        .t1-summary { font-size: 9.5pt; line-height: 1.55; text-align: justify; }
        .t1-item { margin-bottom: 11px; page-break-inside: avoid; }
        .t1-item-title { font-size: 10pt; font-weight: bold; }
        .t1-item-org { font-size: 9pt; color: #2563eb; font-weight: bold; }
        .t1-item-meta { font-size: 8.5pt; color: #64748b; margin: 2px 0 4px; }
        .t1-item-note { font-size: 8.5pt; color: #64748b; font-style: italic; }
        ul.t1-bullets { margin: 3px 0 0 14px; padding: 0; }
        ul.t1-bullets li { font-size: 9pt; margin-bottom: 2px; line-height: 1.4; }
        .t1-cert-name { font-weight: bold; font-size: 9.5pt; }
        .t1-cert-meta { font-size: 8.5pt; color: #64748b; }
        .t1-proj-url { font-size: 8.5pt; color: #2563eb; }
        .t1-proj-desc { font-size: 9pt; margin-top: 2px; }
    </style>
</head>
<body>
<table class="t1-layout" cellpadding="0" cellspacing="0">
<tr>
    <td class="t1-sidebar">
        <div class="t1-photo"><img src="{{ $photo }}" alt="{{ $full_name }}"></div>
        <div class="t1-name">{{ $full_name }}</div>
        <div class="t1-role">{{ $job_title }}</div>

        <div class="t1-side-block">
            <div class="t1-side-title">Contact</div>
            <div class="t1-contact"><span class="t1-icon">&#9993;</span> {{ $contact['email'] }}</div>
            <div class="t1-contact"><span class="t1-icon">&#9742;</span> {{ $contact['phone'] }}</div>
            <div class="t1-contact"><span class="t1-icon">&#8962;</span> {{ $contact['address'] }}</div>
            <div class="t1-contact"><span class="t1-icon">&#9741;</span> {{ $contact['website'] }}</div>
            <div class="t1-contact"><span class="t1-icon">in</span> {{ $contact['linkedin'] }}</div>
        </div>

        <div class="t1-side-block">
            <div class="t1-side-title">Skills</div>
            @foreach ($skills as $skill)
                <span class="t1-skill">{{ $skill }}</span>
            @endforeach
        </div>

        <div class="t1-side-block">
            <div class="t1-side-title">Languages</div>
            @foreach ($languages as $lang)
                <div class="t1-lang"><strong>{{ $lang['name'] }}</strong> <span>— {{ $lang['level'] }}</span></div>
            @endforeach
        </div>
    </td>

    <td class="t1-main">
        <div class="t1-section">
            <div class="t1-heading">Professional Summary</div>
            <div class="t1-summary">{{ $summary }}</div>
        </div>

        <div class="t1-section">
            <div class="t1-heading">Work Experience</div>
            @foreach ($experience as $job)
                <div class="t1-item">
                    <div class="t1-item-title">{{ $job['title'] }}</div>
                    <div class="t1-item-org">{{ $job['company'] }}</div>
                    <div class="t1-item-meta">{{ $job['location'] }} | {{ $job['start'] }} — {{ $job['end'] }}</div>
                    @if (!empty($job['bullets']))
                        <ul class="t1-bullets">
                            @foreach ($job['bullets'] as $b)<li>{{ $b }}</li>@endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="t1-section">
            <div class="t1-heading">Education</div>
            @foreach ($education as $edu)
                <div class="t1-item">
                    <div class="t1-item-title">{{ $edu['degree'] }}</div>
                    <div class="t1-item-org">{{ $edu['institution'] }}</div>
                    <div class="t1-item-meta">{{ $edu['location'] }} | {{ $edu['start'] }} — {{ $edu['end'] }}</div>
                    @if (!empty($edu['details']))<div class="t1-item-note">{{ $edu['details'] }}</div>@endif
                </div>
            @endforeach
        </div>

        <div class="t1-section">
            <div class="t1-heading">Certifications</div>
            @foreach ($certifications as $cert)
                <div class="t1-item">
                    <div class="t1-cert-name">{{ $cert['name'] }}</div>
                    <div class="t1-cert-meta">{{ $cert['issuer'] }} | {{ $cert['year'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="t1-section">
            <div class="t1-heading">Projects</div>
            @foreach ($projects as $proj)
                <div class="t1-item">
                    <div class="t1-item-title">{{ $proj['name'] }}</div>
                    @if (!empty($proj['url']))<div class="t1-proj-url">{{ $proj['url'] }}</div>@endif
                    <div class="t1-proj-desc">{{ $proj['description'] }}</div>
                </div>
            @endforeach
        </div>
    </td>
</tr>
</table>
</body>
</html>
