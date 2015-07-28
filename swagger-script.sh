java -jar ~/code/swagger-codegen/modules/swagger-codegen-cli/target/swagger-codegen-cli.jar generate -i spec/swagger.yaml -l silex -o server -t templates

mv server/SwaggerServer server/api

gulp apidoc