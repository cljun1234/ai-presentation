<?php

namespace Services;

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\Paragraph;
use PhpOffice\PhpPresentation\Slide\Background\Image;
use PhpOffice\PhpPresentation\Style\Bullet;
use GuzzleHttp\Client;

class SlideGenerator {
    private $aiService;
    private $unsplashService;
    private $iconService;
    private $templatePath;
    private $outputPath;
    private $httpClient;

    public function __construct() {
        $this->aiService = new AiService();
        $this->unsplashService = new UnsplashService();
        $this->iconService = new IconService();
        $this->templatePath = __DIR__ . '/../../templates/template.pptx';
        $this->outputPath = __DIR__ . '/../../storage/generated/';
        $this->httpClient = new Client();
    }

    public function generate(string $topic): array {
        // 1. Get AI Data
        $slidesData = $this->aiService->generateContent($topic);

        // 2. Load Template
        if (!file_exists($this->templatePath)) {
            throw new \Exception("Template not found at " . $this->templatePath);
        }
        $ppt = IOFactory::load($this->templatePath);

        // 3. Identify Templates
        // We assume template has: Index 0 = Title Slide, Index 1 = Content Slide
        $tmplTitle = $ppt->getSlide(0);
        $tmplContent = $ppt->getSlideCount() > 1 ? $ppt->getSlide(1) : $tmplTitle;

        // 4. Generate Slides
        foreach ($slidesData as $slideData) {
            $layoutType = $slideData['layout'] ?? 'content_slide';

            // Determine base template
            if ($layoutType === 'title_slide') {
                $base = $tmplTitle;
            } else {
                $base = $tmplContent;
            }

            // Clone and add
            $newSlide = clone $base;
            $ppt->addSlide($newSlide);

            // Process
            $this->processSlide($newSlide, $slideData);
        }

        // 5. Cleanup Template Slides
        // We remove the first N slides that were the templates.
        // If we had 2 template slides, we remove index 0 twice.
        $templateCount = ($ppt->getSlideCount() > 1 && $tmplContent !== $tmplTitle) ? 2 : 1;

        for ($i = 0; $i < $templateCount; $i++) {
            $ppt->removeSlideByIndex(0);
        }

        // 6. Save
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0777, true);
        }
        $filename = 'presentation_' . time() . '.pptx';
        $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        $writer->save($this->outputPath . $filename);

        return [
            'filename' => $filename,
            'topic' => $topic
        ];
    }

    private function processSlide($slide, $data) {
        // Background Image
        if (!empty($data['elements']['background_image_keyword'])) {
            $imageUrl = $this->unsplashService->getImage($data['elements']['background_image_keyword']);
            if ($imageUrl) {
                try {
                    $tempImg = sys_get_temp_dir() . '/bg_' . uniqid() . '.jpg';
                    $this->httpClient->get($imageUrl, ['sink' => $tempImg]);

                    $bg = new Image();
                    $bg->setPath($tempImg);
                    $slide->setBackground($bg);
                } catch (\Exception $e) {
                    // Ignore image download errors
                }
            }
        }

        // Collect shapes to modify/remove
        $shapes = $slide->getShapeCollection();
        $toRemoveIndices = []; // Store by index
        $toAddShapes = [];

        foreach ($shapes as $index => $shape) {
            if ($shape instanceof RichText) {
                $shapeName = $shape->getName();

                // Title
                if ($shapeName === 'TITLE' || $this->hasPlaceholder($shape, '{{TITLE}}')) {
                    $this->replaceText($shape, '{{TITLE}}', $data['elements']['title'] ?? '');
                }

                // Subtitle
                if ($shapeName === 'SUBTITLE' || $this->hasPlaceholder($shape, '{{SUBTITLE}}')) {
                     $text = $data['elements']['subtitle'] ?? '';
                     $this->replaceText($shape, '{{SUBTITLE}}', $text);
                }

                // Bullets
                if ($shapeName === 'BULLETS' || $this->hasPlaceholder($shape, '{{BULLETS}}')) {
                    $bullets = $data['elements']['bullets'] ?? [];
                    if (is_array($bullets)) {
                        $this->replaceWithList($shape, $bullets);
                    }
                }

                // Icon Placeholder
                if ($shapeName === 'ICON_PLACEHOLDER' || $this->hasPlaceholder($shape, '{{ICON}}')) {
                     $iconName = $data['elements']['icon'] ?? '';
                     if ($iconName) {
                         $svgShape = $this->iconService->getIcon($iconName);
                         if ($svgShape) {
                             $svgShape->setOffsetX($shape->getOffsetX());
                             $svgShape->setOffsetY($shape->getOffsetY());
                             $svgShape->setWidth($shape->getWidth());
                             $svgShape->setHeight($shape->getHeight());

                             $toAddShapes[] = $svgShape;
                             $toRemoveIndices[] = $index;
                         }
                     }
                }
            }
        }

        // Remove replaced placeholders
        foreach ($toRemoveIndices as $idx) {
            $slide->unsetShape($idx);
        }

        // Add new shapes
        foreach ($toAddShapes as $s) {
            $slide->addShape($s);
        }
    }

    private function hasPlaceholder(RichText $shape, string $placeholder): bool {
        return strpos($shape->getPlainText(), $placeholder) !== false;
    }

    private function replaceText(RichText $shape, string $placeholder, string $replacement) {
        foreach ($shape->getParagraphs() as $paragraph) {
            foreach ($paragraph->getRichTextElements() as $element) {
                if ($element instanceof \PhpOffice\PhpPresentation\Shape\RichText\Run) {
                    $text = $element->getText();
                    if (strpos($text, $placeholder) !== false) {
                        $element->setText(str_replace($placeholder, $replacement, $text));
                    }
                }
            }
        }
    }

    private function replaceWithList(RichText $shape, array $bullets) {
        $newParagraphs = [];
        foreach ($bullets as $bulletText) {
            $p = new Paragraph();
            $p->getBulletStyle()->setBulletType(Bullet::TYPE_BULLET);
            $textRun = $p->createTextRun($bulletText);
            $textRun->getFont()->setSize(18); // Default size
            $newParagraphs[] = $p;
        }
        $shape->setParagraphs($newParagraphs);
    }
}
