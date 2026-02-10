<?php

namespace Services;

use GuzzleHttp\Client;

class AiService {
    private $client;
    private $apiKey;

    public function __construct() {
        // In a real app, use dotenv. For now, checking env or fallback.
        $this->apiKey = getenv('OPENAI_API_KEY') ?: 'YOUR_OPENAI_KEY';
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'timeout'  => 30.0,
            'headers'  => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ]
        ]);
    }

    public function generateContent(string $topic): array {
        // Fallback to mock if key is missing or default
        if ($this->apiKey === 'YOUR_OPENAI_KEY' && getenv('APP_ENV') !== 'production') {
            return $this->getMockData($topic);
        }

        try {
            $prompt = $this->buildPrompt($topic);
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a professional presentation generator. Return ONLY a valid JSON array. No markdown formatting.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            $content = $body['choices'][0]['message']['content'];

            // Cleanup potential markdown fences
            $content = trim($content);
            if (strpos($content, '```json') === 0) {
                $content = str_replace('```json', '', $content);
                $content = str_replace('```', '', $content);
            }

            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // If parsing fails, fallback to mock (or log error)
                return $this->getMockData($topic);
            }

            return $data;

        } catch (\Exception $e) {
            // Log error
            return $this->getMockData($topic);
        }
    }

    private function buildPrompt($topic) {
        return "Generate a 3-slide presentation about '{$topic}'.
        Return a JSON array where each item is a slide.
        Structure:
        [
          {
            \"slide_number\": 1,
            \"layout\": \"title_slide\",
            \"elements\": {
                \"title\": \"Main Title\",
                \"subtitle\": \"Subtitle here\",
                \"background_image_keyword\": \"keyword for unsplash\",
                \"icon\": \"icon-name\" (e.g. 'fa-lightbulb', 'fa-cogs', just the name without fa- prefix usually, but user said 'fa-robot')
            }
          },
          {
            \"slide_number\": 2,
            \"layout\": \"content_slide\",
            \"elements\": {
                \"title\": \"Slide Title\",
                \"bullets\": [\"Bullet 1\", \"Bullet 2\"],
                \"background_image_keyword\": \"keyword\",
                \"icon\": \"fa-check\"
            }
          }
        ]
        Ensure the first slide is title_slide. Use 'fa-' prefix for icons.
        ";
    }

    private function getMockData($topic) {
         return [
            [
                "slide_number" => 1,
                "layout" => "title_slide",
                "elements" => [
                    "title" => "The Future of $topic",
                    "subtitle" => "An In-depth Analysis",
                    "background_image_keyword" => "technology",
                    "icon" => "fa-robot"
                ]
            ],
            [
                "slide_number" => 2,
                "layout" => "content_slide",
                "elements" => [
                    "title" => "Key Concepts",
                    "bullets" => [
                        "Understanding the basics of $topic",
                        "Why $topic matters in 2024",
                        "Future projections"
                    ],
                    "background_image_keyword" => "meeting",
                    "icon" => "fa-lightbulb"
                ]
            ],
            [
                "slide_number" => 3,
                "layout" => "content_slide",
                "elements" => [
                    "title" => "Conclusion",
                    "bullets" => [
                        "Summary of key points",
                        "Actionable takeaways",
                        "Q&A Session"
                    ],
                    "background_image_keyword" => "success",
                    "icon" => "fa-check-circle"
                ]
            ]
        ];
    }
}
