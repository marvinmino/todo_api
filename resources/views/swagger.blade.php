<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo API - Swagger Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui.css" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        body {
            margin: 0;
            background: #fafafa;
        }
        .swagger-ui .topbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 10px 0;
        }
        .swagger-ui .topbar .download-url-wrapper {
            display: none;
        }
        .swagger-ui .info {
            margin: 50px 0;
        }
        .swagger-ui .info .title {
            color: #3b4151;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            // Get current host and port (e.g., http://192.168.1.50:8080)
            const currentHost = window.location.protocol + '//' + window.location.host;
            
            const ui = SwaggerUIBundle({
                url: "{{ asset('swagger.yaml') }}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                tryItOutEnabled: true,
                supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
                // Intercept requests to replace server URLs
                requestInterceptor: function(request) {
                    // Replace localhost or production URL with current host
                    if (request.url) {
                        request.url = request.url.replace(/http:\/\/localhost:8000/g, currentHost);
                        request.url = request.url.replace(/https:\/\/api\.todoapi\.com/g, currentHost);
                    }
                    return request;
                },
                onComplete: function() {
                    console.log("Swagger UI loaded successfully");
                    console.log("Current host:", currentHost);
                },
                onFailure: function(data) {
                    console.error("Failed to load Swagger UI:", data);
                }
            });
        };
    </script>
</body>
</html>

