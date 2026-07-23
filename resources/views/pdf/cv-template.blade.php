<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $full_name }} — CV | {{ config('app.name') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.45;
            color: {{ $colors['text'] }};
            background: #ffffff;
        }

        table.cv-layout {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.cv-layout td {
            vertical-align: top;
        }

        td.cv-sidebar {
            width: 32%;
            background-color: {{ $colors['sidebar_bg'] }};
            color: {{ $colors['sidebar_text'] }};
            padding: 28px 22px;
        }

        td.cv-main {
            width: 68%;
            padding: 28px 30px;
            background-color: #ffffff;
        }

        .photo-wrap {
            text-align: center;
            margin-bottom: 18px;
        }

        .photo-wrap img {
            width: 110px;
            height: 110px;
            border-radius: 55px;
            border: 3px solid {{ $colors['accent'] }};
            object-fit: cover;
        }

        .sidebar-name {
            font-size: 16pt;
            font-weight: bold;
            line-height: 1.2;
            text-align: center;
            margin-bottom: 4px;
        }

        .sidebar-title {
            font-size: 9.5pt;
            color: {{ $colors['sidebar_muted'] }};
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.35;
        }

        .sidebar-section {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }

        .sidebar-heading {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.25);
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .contact-row {
            margin-bottom: 7px;
            font-size: 8.5pt;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .contact-icon {
            display: inline-block;
            width: 16px;
            font-size: 9pt;
            color: {{ $colors['sidebar_muted'] }};
        }

        .skill-tag {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            font-size: 8pt;
            padding: 3px 8px;
            margin: 0 4px 5px 0;
            border-radius: 3px;
            line-height: 1.3;
        }

        .lang-row {
            margin-bottom: 6px;
            font-size: 8.5pt;
        }

        .lang-name {
            font-weight: bold;
        }

        .lang-level {
            color: {{ $colors['sidebar_muted'] }};
        }

        .main-section {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }

        .main-heading {
            font-size: 11pt;
            font-weight: bold;
            color: {{ $colors['accent'] }};
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 2px solid {{ $colors['border'] }};
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        .summary-text {
            font-size: 9.5pt;
            line-height: 1.55;
            color: {{ $colors['text'] }};
            text-align: justify;
        }

        .entry {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .entry:last-child {
            margin-bottom: 0;
        }

        .entry-head {
            margin-bottom: 3px;
        }

        .entry-title {
            font-size: 10pt;
            font-weight: bold;
            color: {{ $colors['text'] }};
        }

        .entry-sub {
            font-size: 9pt;
            color: {{ $colors['accent'] }};
            font-weight: bold;
        }

        .entry-meta {
            font-size: 8.5pt;
            color: {{ $colors['muted'] }};
            margin-bottom: 4px;
        }

        .entry-details {
            font-size: 8.5pt;
            color: {{ $colors['muted'] }};
            font-style: italic;
            margin-bottom: 4px;
        }

        ul.bullets {
            margin: 4px 0 0 16px;
            padding: 0;
        }

        ul.bullets li {
            font-size: 9pt;
            line-height: 1.45;
            margin-bottom: 3px;
            color: {{ $colors['text'] }};
        }

        .cert-row {
            margin-bottom: 6px;
            page-break-inside: avoid;
        }

        .cert-name {
            font-weight: bold;
            font-size: 9.5pt;
        }

        .cert-meta {
            font-size: 8.5pt;
            color: {{ $colors['muted'] }};
        }

        .project-name {
            font-weight: bold;
            font-size: 9.5pt;
        }

        .project-url {
            font-size: 8.5pt;
            color: {{ $colors['accent'] }};
        }

        .project-desc {
            font-size: 9pt;
            margin-top: 2px;
            line-height: 1.45;
        }

        .ref-name {
            font-weight: bold;
            font-size: 9.5pt;
        }

        .ref-meta {
            font-size: 8.5pt;
            color: {{ $colors['muted'] }};
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <table class="cv-layout" cellpadding="0" cellspacing="0">
        <tr>
            <td class="cv-sidebar">
                <div class="photo-wrap">
                    <img src="{{ $photo }}" alt="{{ $full_name }}">
                </div>

                <div class="sidebar-name">{{ $full_name }}</div>
                <div class="sidebar-title">{{ $job_title }}</div>

                <div class="sidebar-section">
                    <div class="sidebar-heading">Contact</div>
                    <div class="contact-row">
                        <span class="contact-icon">&#9993;</span>
                        {{ $contact['email'] }}
                    </div>
                    <div class="contact-row">
                        <span class="contact-icon">&#9742;</span>
                        {{ $contact['phone'] }}
                    </div>
                    <div class="contact-row">
                        <span class="contact-icon">&#8962;</span>
                        {{ $contact['address'] }}
                    </div>
                    <div class="contact-row">
                        <span class="contact-icon">&#9741;</span>
                        {{ $contact['website'] }}
                    </div>
                    <div class="contact-row">
                        <span class="contact-icon">in</span>
                        {{ $contact['linkedin'] }}
                    </div>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-heading">Skills</div>
                    @foreach ($skills as $skill)
                        <span class="skill-tag">{{ $skill }}</span>
                    @endforeach
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-heading">Languages</div>
                    @foreach ($languages as $language)
                        <div class="lang-row">
                            <span class="lang-name">{{ $language['name'] }}</span>
                            <span class="lang-level"> — {{ $language['level'] }}</span>
                        </div>
                    @endforeach
                </div>
            </td>

            <td class="cv-main">
                <div class="main-section">
                    <div class="main-heading">Professional Summary</div>
                    <div class="summary-text">{{ $summary }}</div>
                </div>

                <div class="main-section">
                    <div class="main-heading">Work Experience</div>
                    @foreach ($experience as $job)
                        <div class="entry">
                            <div class="entry-head">
                                <span class="entry-title">{{ $job['title'] }}</span>
                            </div>
                            <div class="entry-sub">{{ $job['company'] }}</div>
                            <div class="entry-meta">{{ $job['location'] }} &nbsp;|&nbsp; {{ $job['start'] }} — {{ $job['end'] }}</div>
                            @if (! empty($job['bullets']))
                                <ul class="bullets">
                                    @foreach ($job['bullets'] as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="main-section">
                    <div class="main-heading">Education</div>
                    @foreach ($education as $edu)
                        <div class="entry">
                            <div class="entry-title">{{ $edu['degree'] }}</div>
                            <div class="entry-sub">{{ $edu['institution'] }}</div>
                            <div class="entry-meta">{{ $edu['location'] }} &nbsp;|&nbsp; {{ $edu['start'] }} — {{ $edu['end'] }}</div>
                            @if (! empty($edu['details']))
                                <div class="entry-details">{{ $edu['details'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="main-section">
                    <div class="main-heading">Certifications</div>
                    @foreach ($certifications as $cert)
                        <div class="cert-row">
                            <div class="cert-name">{{ $cert['name'] }}</div>
                            <div class="cert-meta">{{ $cert['issuer'] }} &nbsp;|&nbsp; {{ $cert['year'] }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="main-section">
                    <div class="main-heading">Projects</div>
                    @foreach ($projects as $project)
                        <div class="entry">
                            <div class="project-name">{{ $project['name'] }}</div>
                            @if (! empty($project['url']))
                                <div class="project-url">{{ $project['url'] }}</div>
                            @endif
                            <div class="project-desc">{{ $project['description'] }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="main-section">
                    <div class="main-heading">References</div>
                    @foreach ($references as $reference)
                        <div class="entry">
                            <div class="ref-name">{{ $reference['name'] }}</div>
                            <div class="ref-meta">{{ $reference['title'] }}</div>
                            <div class="ref-meta">{{ $reference['contact'] }}</div>
                        </div>
                    @endforeach
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
