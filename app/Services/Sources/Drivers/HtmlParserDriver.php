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
use JetBrains\PhpStorm\NoReturn;
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
            ->withCookies([
                'PHPSESSID' => '1039a25202e793d1bc0c6b069ec9b52a',
                'customer_cart_id' => 'dWdtVWN1T3BOUVZWblNYcUt5bG1XUT09',
            ], 'bomba.md')
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
                    "HTTP error: {$response->status()} \n URL: {$response->effectiveUri()}"
                );
            }

            return $this->parseHtml(
                $response->body(),
                $selectors
            );

        } catch (Throwable $e) {
            $message = strlen($e->getMessage()) > 500
                ? substr($e->getMessage(), 0, 500) . '... (truncated)'
                : $e->getMessage();

            throw new RuntimeException(
                "Parser call failed for operation [{$operationName}]: " . $message,
                0,
                $e
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

    protected function parseElement(array|string|null $config, Crawler $crawler = null): array|string|null
    {
        if (!$crawler) {
            $crawler = $this->crawler;
        }

        if (is_null($config)) {
            return null;
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
                $node = $nodes->first();
                $value = null;

                if (isset($config['attributes'])) {
                    foreach ($config['attributes'] as $attribute) {
                        $value = $node->attr($attribute);

                        if ($value) {
                            break;
                        }
                    }
                } else if (isset($config['attribute'])) {
                    $value = $node->attr($config['attribute']);
                } else {
                    $value = trim($node->text());
                }

                if (isset($config['cast']) && $config['cast'] === 'array') {
                    $value = json_decode($value, true);
                }

                return $value;
            }

            return null;
        }

        throw new InvalidArgumentException(sprintf(
            'Invalid selector config in parseElement. Given: %s',
            is_array($config) ? json_encode($config) : gettype($config)
        ));
    }

    #[NoReturn] private function ddImages(): void
    {
        dd(
            $this->crawler
                ->filter('img')
                ->each(fn (Crawler $node) => [
                    'src' => $node->attr('src'),
                    'data-src' => $node->attr('data-src'),
                    'srcset' => $node->attr('srcset'),
                    'alt' => $node->attr('alt'),
                    'class' => $node->attr('class'),
                ])
        );
    }

    #[NoReturn] private function ddE(): void
    {
        dd(
            $this->crawler
                ->filter('.catalog__pill')
                ->each(fn (Crawler $node) => $node->outerHtml())
        );
    }
}
