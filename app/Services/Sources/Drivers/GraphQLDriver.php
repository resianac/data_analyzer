<?php

namespace App\Services\Sources\Drivers;

use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Enums\SourceDriverType;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class GraphQLDriver extends BaseDriver
{
    protected SourceDriverType $name = SourceDriverType::GRAPHQL;
    private ?PendingRequest $client = null;

    private function initializeClient(): void
    {
        if ($this->client === null) {
            $this->client = Http::timeout($this->config->get('timeout'))
                ->withHeaders($this->config->get('headers') ?? []);

            $this->client->baseUrl($this->config['base_url']);
        }
    }

    /**
     * @throws Exception
     */
    public function query(string $query, array $variables = []): array
    {
        return $this->execute('POST', '', ['query' => $query, 'variables' => $variables]);
    }

    public function execute(string $method, string $endpoint, array $data = []): mixed
    {
        $this->initializeClient();

        $query = $data['query'] ?? '';
        $variables = $data['variables'] ?? [];
        $operationName = $data['operationName'] ?? null;

        $payload = [
            'query' => $query,
            'variables' => $variables,
        ];

        if ($operationName) {
            $payload['operationName'] = $operationName;
        }

        try {
            $response = $this->client->post($endpoint, $payload);

            if ($response->failed()) {
                throw new Exception("GraphQL request failed: " . $response->status());
            }

            $data = $response->json();

            if (isset($data['errors'])) {
                throw new Exception("GraphQL errors: " . json_encode($data['errors']));
            }

            return $data['data'] ?? $data;

        } catch (Exception $e) {
            for ($i = 0; $i < 3; $i++) {
                try {
                    usleep(1000 * 1000);
                    $response = $this->client->post($endpoint, $payload);
                    return $response->json()['data'] ?? $response->json();
                } catch (Exception $retryException) {
                    continue;
                }
            }

            throw $e;
        }
    }

}
