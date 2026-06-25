<?php

namespace App\Services\Pdf;

use App\Services\BaseService;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BrowsershotEnvironment extends BaseService
{
    public function applyToBrowsershot(\Spatie\Browsershot\Browsershot $browsershot): void
    {
        if ($node = $this->nodeBinary()) {
            $browsershot->setNodeBinary($node);
        }

        if ($npm = $this->npmBinary()) {
            $browsershot->setNpmBinary($npm);
        }

        if ($chrome = $this->chromePath()) {
            $browsershot->setChromePath($chrome);
        }

        $browsershot->setNodeModulePath(base_path('node_modules'));

        if (PHP_OS_FAMILY === 'Windows' || config('browsershot.no_sandbox', false)) {
            $browsershot->noSandbox();
        }
    }

    public function nodeBinary(): ?string
    {
        if ($configured = config('browsershot.node_binary')) {
            return $this->normalizePath($configured);
        }

        return $this->firstExistingPath([
            'C:\\Program Files\\nodejs\\node.exe',
            'C:\\Program Files (x86)\\nodejs\\node.exe',
            getenv('LOCALAPPDATA') ? getenv('LOCALAPPDATA').'\\Programs\\node\\node.exe' : null,
            $this->which('node'),
        ]);
    }

    public function npmBinary(): ?string
    {
        if ($configured = config('browsershot.npm_binary')) {
            return $this->normalizePath($configured);
        }

        return $this->firstExistingPath([
            'C:\\Program Files\\nodejs\\npm.cmd',
            'C:\\Program Files (x86)\\nodejs\\npm.cmd',
            $this->which('npm'),
        ]);
    }

    public function chromePath(): ?string
    {
        if ($configured = config('browsershot.chrome_path')) {
            return $this->normalizePath($configured);
        }

        $cached = $this->latestChromeFromDirectory(getenv('USERPROFILE').'/.cache/puppeteer')
            ?? $this->latestChromeFromDirectory(base_path('node_modules/puppeteer/.cache/puppeteer'))
            ?? $this->latestChromeFromGlob([
                (getenv('LOCALAPPDATA') ?: '').'/Google/Chrome/Application/chrome.exe',
                'C:/Program Files/Google/Chrome/Application/chrome.exe',
                'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
            ]);

        if ($cached) {
            return $cached;
        }

        return $this->resolveChromeViaNode();
    }

    public function isConfigured(): bool
    {
        return $this->nodeBinary() !== null && $this->chromePath() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function diagnostics(): array
    {
        $node = $this->nodeBinary();
        $npm = $this->npmBinary();
        $chrome = $this->chromePath();

        return [
            'configured' => $this->isConfigured(),
            'node_binary' => $node,
            'node_exists' => $node ? File::exists($node) : false,
            'npm_binary' => $npm,
            'npm_exists' => $npm ? File::exists($npm) : false,
            'chrome_path' => $chrome,
            'chrome_exists' => $chrome ? File::exists($chrome) : false,
            'node_module_path' => base_path('node_modules'),
            'node_modules_exists' => File::isDirectory(base_path('node_modules')),
            'puppeteer_installed' => File::isDirectory(base_path('node_modules/puppeteer')),
            'os_family' => PHP_OS_FAMILY,
            'no_sandbox' => PHP_OS_FAMILY === 'Windows' || (bool) config('browsershot.no_sandbox', false),
        ];
    }

    /**
     * @param  list<string|null>  $paths
     */
    protected function firstExistingPath(array $paths): ?string
    {
        foreach ($paths as $path) {
            if (! is_string($path) || $path === '') {
                continue;
            }

            $normalized = $this->normalizePath($path);

            if ($normalized && File::exists($normalized)) {
                return $normalized;
            }
        }

        return null;
    }

    /**
     * @param  list<string>  $patterns
     */
    protected function latestChromeFromGlob(array $patterns): ?string
    {
        $matches = [];

        foreach ($patterns as $pattern) {
            if ($pattern === '') {
                continue;
            }

            if (File::exists($pattern)) {
                $matches[] = $this->normalizePath($pattern);
            }
        }

        if ($matches === []) {
            return null;
        }

        usort($matches, static fn (string $a, string $b): int => filemtime($b) <=> filemtime($a));

        return $matches[0];
    }

    protected function latestChromeFromDirectory(?string $directory): ?string
    {
        if (! is_string($directory) || $directory === '' || ! File::isDirectory($directory)) {
            return null;
        }

        $matches = [];

        foreach (File::allFiles($directory) as $file) {
            if (strtolower($file->getFilename()) === 'chrome.exe') {
                $matches[] = $file->getPathname();
            }
        }

        if ($matches === []) {
            return null;
        }

        usort($matches, static fn (string $a, string $b): int => filemtime($b) <=> filemtime($a));

        return $this->normalizePath($matches[0]);
    }

    protected function resolveChromeViaNode(): ?string
    {
        $node = $this->nodeBinary();

        if (! $node) {
            return null;
        }

        $process = new Process([
            $node,
            '-e',
            "import('puppeteer').then(async (module) => { console.log(module.default.executablePath()); }).catch(() => process.exit(1));",
        ], base_path(), null, null, 15);

        try {
            $process->run();

            if (! $process->isSuccessful()) {
                return null;
            }

            $path = trim($process->getOutput());

            return $path !== '' && File::exists($path) ? $this->normalizePath($path) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function which(string $command): ?string
    {
        $finder = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $process = new Process([$finder, $command]);
        $process->run();

        if (! $process->isSuccessful()) {
            return null;
        }

        $line = trim(strtok($process->getOutput(), PHP_EOL));

        return $line !== '' ? $this->normalizePath($line) : null;
    }

    protected function normalizePath(?string $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
}
