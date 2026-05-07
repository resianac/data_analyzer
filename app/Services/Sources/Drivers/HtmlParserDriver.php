<?php

namespace App\Services\Sources\Drivers;

use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Enums\SourceDriverType;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class HtmlParserDriver extends BaseDriver
{
    protected SourceDriverType $name = SourceDriverType::HTML_PARSER;

    protected ?Crawler $crawler = null;

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

            [$operationName, $selectors] = $params;

            $response = $this->client->get($operationName);

            if ($response->failed()) {
                throw new RuntimeException(
                    'HTTP error: ' . $response->status() . ' ' . $response->body()
                );
            }

            return $this->parseHtml(
                $response->body(),
                $selectors
            );

        } catch (Throwable $e) {
            throw new RuntimeException(
                'HTML fetch failed: ' . $e->getMessage(),
            );
        }
    }

    protected function parseHtml(string $html, array $selectors): Collection
    {
        try {
            $this->crawler = new Crawler($html);

            return collect(
                $this->extractData($selectors)
            );
        } catch (Throwable $e) {
            throw new RuntimeException(
                'HTML parsing failed: ' . $e->getMessage());
        }
    }

    protected function extractData(array $selectorsConfig): Collection
    {
        $result = [];

        foreach ($selectorsConfig as $key => $config) {
            if (isset($config['fields'])) {
                $result[$key] = $this->parseList($config);
                continue;
            }

            $result[$key] = $this->parseElement($config);
        }

        return collect($result);
    }

    protected function parseList(array $config): Collection
    {
        try {
            $containers = $this->crawler->filter($config['selector']);
            $items = [];

            foreach ($containers as $container) {
                $itemCrawler = new Crawler($container);
                $itemData = [];

                foreach ($config['fields'] as $fieldName => $fieldConfig) {
                    $itemData[$fieldName] = $this->parseElement($fieldConfig, $itemCrawler);
                }

                $items[] = $itemData;
            }

            return collect($items);

        } catch (Throwable $e) {
            throw new RuntimeException(
                'Failed to parse list: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    protected function parseElement(array|string $config, Crawler $crawler = null): ?string
    {
        if (!$crawler) {
            $crawler = $this->crawler;
        }

        if (is_string($config)) {
            $nodes = $crawler->filter($config);

            return $nodes->count() > 0
                ? trim($nodes->first()->text())
                : null;
        }

        if (is_array($config) && isset($config['selector'])) {
            $nodes = $crawler->filter($config['selector']);

            if ($nodes->count() > 0) {
                if (isset($config['attribute'])) {
                    return $nodes->first()->attr($config['attribute']);
                }

                return trim($nodes->first()->text());
            }

            return null;
        }

        throw new InvalidArgumentException(sprintf(
            'Invalid selector config in parseElement. Given: %s',
            is_array($config) ? json_encode($config) : gettype($config)
        ));
    }
}
