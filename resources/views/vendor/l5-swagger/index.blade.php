<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{config('l5-swagger.documentations.'.$documentation.'.api.title')}}</title>
    <link rel="stylesheet" type="text/css" href="https://api-inventario.onrender.com/swagger-api/swagger-ui.css">
    {{-- <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}"> --}}
    <link rel="icon" type="image/png" href="https://api-inventario.onrender.com/swagger-api/favicon-32x32.png" sizes="32x32"/>
    {{-- <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-32x32.png') }}" sizes="32x32"/> --}}
    <link rel="icon" type="image/png" href="https://api-inventario.onrender.com/swagger-api/favicon-16x16.png" sizes="16x16"/>
    {{-- <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-16x16.png') }}" sizes="16x16"/> --}}
    <style>
        /* CLIENT-SPECIFIC STYLES */

::-webkit-scrollbar {
    width: 8px;
}


/* Track */

::-webkit-scrollbar-track {
    background: #f1f1f1;
}


/* Handle */

::-webkit-scrollbar-thumb {
    background: #888;
}


/* Handle on hover */

::-webkit-scrollbar-thumb:hover {
    background: #555;
}


    html
    {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
    }
    *,
    *:before,
    *:after
    {
        box-sizing: inherit;
    }

    body {
      margin:0;
      background: #fafafa;
    }

    .swagger-ui .scheme-container {
    background: transparent !important;
     box-shadow: none !important;
    margin: 0 0 20px;
    padding: 30px 0;
}

.filter{
    display: none !important;
}
    </style>
</head>

<body>
<div id="swagger-ui"></div>

<script src="https://api-inventario.onrender.com/swagger-api/swagger-ui-bundle.js"></script>
{{-- <script src="{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}"></script> --}}
<script src="https://api-inventario.onrender.com/swagger-api/swagger-ui-standalone-preset.js"></script>
{{-- <script src="{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}"></script> --}}
<script>
    window.onload = function() {
        // Build a system
        const ui = SwaggerUIBundle({
            dom_id: '#swagger-ui',
            url: "{!! $urlToDocs !!}",
            operationsSorter: {!! isset($operationsSorter) ? '"' . $operationsSorter . '"' : 'null' !!},
            configUrl: {!! isset($configUrl) ? '"' . $configUrl . '"' : 'null' !!},
            validatorUrl: {!! isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null' !!},
            oauth2RedirectUrl: "{{ route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath) }}",

            requestInterceptor: function(request) {
                request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                return request;
            },

            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],

            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],

            layout: "StandaloneLayout",
            docExpansion : "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
            deepLinking: true,
            filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
            persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",

        })

        window.ui = ui

        @if(in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type')))
        ui.initOAuth({
            usePkceWithAuthorizationCodeGrant: "{!! (bool)config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant') !!}"
        })
        @endif
    }
</script>
</body>
</html>
