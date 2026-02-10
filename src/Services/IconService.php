<?php

namespace Services;

use PhpOffice\PhpPresentation\Shape\Drawing\File;

class IconService {
    private $iconDir;

    public function __construct() {
        $this->iconDir = __DIR__ . '/../../storage/icons/';
    }

    public function getIcon(string $iconName): ?File {
        // Normalize icon name
        $iconName = strtolower(trim($iconName));
        if (strpos($iconName, 'fa-') !== 0) {
            $iconName = 'fa-' . $iconName;
        }

        $path = $this->iconDir . $iconName . '.svg';

        if (!file_exists($path)) {
            // Check if we have a fallback or try to find without prefix if user provided full name
            return null;
        }

        $shape = new File();
        $shape->setPath($path);
        $shape->setName('Icon ' . $iconName);
        $shape->setWidth(64);
        $shape->setHeight(64);

        return $shape;
    }
}
