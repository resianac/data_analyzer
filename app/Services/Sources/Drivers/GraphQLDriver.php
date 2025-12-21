<?php

namespace App\Services\Sources\Drivers;

use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Enums\SourceDriverType;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class GraphQLDriver extends BaseDriver
{
    protected SourceDriverType $name = SourceDriverType::GRAPHQL;

    protected function initializeClient(): void
    {
        if ($this->client !== null) {
            return;
        }

        $this->client = Http::timeout($this->config->get('timeout'))
            ->withHeaders($this->config->get('headers') ?? [])
            ->baseUrl($this->config->get('base_url'));
    }

    public function call(...$params): Collection
    {
        try {
            $this->initializeClient();

            [$schema, $variables] = $params;

            $query = is_file($schema)
                ? file_get_contents($schema)
                : $schema;

            if ($query === false || $query === '') {
                throw new RuntimeException('GraphQL query is empty or unreadable');
            }

            $response = $this->client->post('', [
                'query'     => $query,
                'variables' => $variables,
            ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    'HTTP error: ' . $response->status() . ' ' . $response->body()
                );
            }

            $json = $response->json();

            if ($json === null) {
                throw new RuntimeException('Invalid JSON response from GraphQL');
            }

            if (!empty($json['errors'])) {
                throw new RuntimeException(
                    'GraphQL errors: ' . json_encode($json['errors'], JSON_UNESCAPED_UNICODE)
                );
            }

            return collect($json);


        } catch (Throwable $e) {
            throw new RuntimeException(
            'GraphQL call failed: ' . $e->getMessage(),
            previous: $e
            );
        }
    }

    /**
     * Выполнить запрос по указанной схеме
     *
     * @param string $schemaName имя .graphql файла
     * @param array $variables variables для GraphQL
     */
    public function executeQuery(string $schemaName, array $variables): Collection
    {
        $path = base_path("graphql/{$schemaName}.graphql");

        return $this->call(
            $path,
            $variables,
            Str::of($path)
                ->beforeLast('/')
                ->afterLast('/')
                ->value()
        );
    }
}
