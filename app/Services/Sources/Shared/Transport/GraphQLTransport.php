<?php

namespace App\Services\Sources\Shared\Transport;

use App\Services\Sources\Shared\Enums\SourceDriverType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class GraphQLTransport extends BaseTransport
{
    protected SourceDriverType $name = SourceDriverType::GRAPHQL;

    protected function initializeClient(): void
    {
        if ($this->client !== null) {
            return;
        }

        $this->client = Http::timeout($this->config->get('timeout'))
            ->withHeaders($this->config->get('headers') ?? [])
            ->baseUrl($this->config->get('base_api_url'));
    }

    public function call(string $operation, array $parameters): Collection
    {
        try {
            $this->initializeClient();

            $schema = $operation;

            $query = is_file($schema)
                ? file_get_contents($schema)
                : $schema;

            if ($query === false || $query === '') {
                throw new RuntimeException('GraphQL query is empty or unreadable');
            }

            $response = $this->client->post('', [
                'query' => $query,
                'variables' => $parameters,
            ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    'HTTP error: '.$response->status().' '.$response->body()
                );
            }

            $json = $response->json();

            if ($json === null) {
                throw new RuntimeException('Invalid JSON response from GraphQL');
            }

            if (! empty($json['errors'])) {
                throw new RuntimeException(
                    'GraphQL errors: '.json_encode($json['errors'], JSON_UNESCAPED_UNICODE)
                );
            }

            return collect($json);
        } catch (Throwable $e) {
            throw new RuntimeException(
                'GraphQL call failed: '.$e->getMessage(),
                previous: $e
            );
        }
    }

    /**
     * Выполнить запрос по указанной схеме
     *
     * @param  string  $schemaName  имя .graphql файла
     * @param  array  $variables  variables для GraphQL
     */
    public function executeQuery(string $schemaName, array $variables): Collection
    {
        $path = base_path("graphql/{$schemaName}.graphql");

        return $this->call($path, $variables);
    }
}
