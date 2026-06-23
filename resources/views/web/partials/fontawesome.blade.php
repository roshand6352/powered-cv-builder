@php
    use App\Support\FontAwesome\FontAwesomeAssets;
@endphp
<meta name="document-fontawesome-url" content="{{ FontAwesomeAssets::cssUrl() }}">
{!! FontAwesomeAssets::stylesheetLinkTag() !!}
