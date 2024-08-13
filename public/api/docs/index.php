<!DOCTYPE html>
<html>
<head>
  <title>API COMPRAS - Swagger UI</title>
  <link rel="stylesheet" type="text/css" href="../css/swagger-ui.css">
</head>
<body>
  <div id="swagger-ui"></div>
  <script src="../js/swagger-ui-bundle.js"></script>
  <script src="../js/swagger-ui-standalone-preset.js"></script>
  <script>
    window.onload = function() {
      const ui = SwaggerUIBundle({
        url: "./docs.yaml",
        dom_id: '#swagger-ui',
        presets: [
          SwaggerUIBundle.presets.apis,
          SwaggerUIStandalonePreset
        ],
        layout: "StandaloneLayout"
      });
    }
  </script>
</body>
</html>
