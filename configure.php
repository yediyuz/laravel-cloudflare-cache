#!/usr/bin/env php
<?php

function ask(string $question, string $default = ''): string
{
    $answer = readline($question . ($default ? " ({$default})" : null) . ': ');

    if (! $answer) {
        return $default;
    }

    return $answer;
}

function array_forget(&$array, $keys): void
{
    $original = &$array;

    $keys = (array) $keys;

    if (count($keys) === 0) {
        return;
    }

    foreach ($keys as $key) {
        if (array_key_exists($key, $array)) {
            unset($array[$key]);

            continue;
        }

        $parts = explode('.', $key);

        $array = &$original;

        while (count($parts) > 1) {
            $part = array_shift($parts);

            if (isset($array[$part]) && is_array($array[$part])) {
                $array = &$array[$part];
            } else {
                continue 2;
            }
        }

        unset($array[array_shift($parts)]);
    }
}

/** @noinspection JsonEncodingApiUsageInspection */
function composer_forget(string|array $paths): void
{
    $data = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);

    if (! is_array($paths)) {
        $paths = [$paths];
    }

    foreach ($paths as $path) {
        array_forget($data, $path);
    }

    file_put_contents(__DIR__ . '/composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function confirm(string $question, bool $default = false): bool
{
    $answer = ask($question . ' (' . ($default ? 'Y/n' : 'y/N') . ')');

    if (! $answer) {
        return $default;
    }

    return strtolower($answer) === 'y';
}

function writeln(string $line): void
{
    echo $line . PHP_EOL;
}

function runTests(string $command): void
{
    try {
        $result = [];
        exec($command . ' 2>&1', $result);
        foreach ($result as $line) {
            writeln($line);
        }
    } catch (\Exception $e) {
        writeln($e->getMessage());
    }
}

function run(string $command): string
{
    return trim(shell_exec($command));
}

function str_afterLast(string $subject, string $search): string
{
    if ($search === '') {
        return $subject;
    }

    $position = strrpos($subject, (string) $search);

    if ($position === false) {
        return $subject;
    }

    return substr($subject, $position + strlen($search));
}

function slugify(string $subject): string
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $subject), '-'));
}

function studly_case(string $subject): string
{
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $subject)));
}

function title_case(string $subject): string
{
    return ucwords(str_replace(['-', '_'], ' ', $subject));
}

function title_snake(string $subject, string $replace = '_'): string
{
    return str_replace(['-', '_'], $replace, $subject);
}

function safeUnlink(string $filename): void
{
    if (file_exists($filename) && is_file($filename)) {
        unlink($filename);
    }
}

function safeDeleteDirectory(string $filename): void
{
    if (file_exists($filename) && is_dir($filename)) {
        run('rm -rf ' . $filename);
    }
}

function replace_in_file(string $file, array $replacements): void
{
    $contents = file_get_contents($file);

    file_put_contents(
        $file,
        str_replace(
            array_keys($replacements),
            array_values($replacements),
            $contents
        )
    );
}

/** @noinspection PhpDuplicateMatchArmBodyInspection */
function removeConditionalCodeBlocks(string $file, string $conditionKey, bool $condition, bool $test = false): ?string
{
    $contents = file_get_contents($file);

    if (str_contains($file, 'phpstan.neon')) {
        $extOrFilename = 'neon';
    } elseif (str_contains($file, 'phpunit.xml')) {
        $extOrFilename = 'xml';
    } else {
        $extOrFilename = str_afterLast(str_afterLast($file, '.'), DIRECTORY_SEPARATOR);
    }

    $patterns = [
        [
            // #<key># ... #</key>#
            // # <key> # ... # </key> #
            // /**<key>*/ ... /**</key>*/
            // /** <key> */ ... /** </key> */
            '/\n?{O}[[:blank:]]?\<{K}\>[[:blank:]]?{C}([\s\S]*?){O}[[:blank:]]?\<\/{K}\>[[:blank:]]?{C}|{O}[[:blank:]]?\<\/{K}\>[[:blank:]]?{C}/s',
            // #<key>#
            // #</key>#
            // # <key> #
            // # </key> #
            '/\n?{O}[[:blank:]]?\<\/?{K}\>[[:blank:]]?{C}/',
        ],
        [
            // <key--> xml contents </key-->
            '/(\n?<{K}-->([\s\S]*?)(?:<\/{K}-->|)<\/{K}-->)/',
            // <key-->
            // </key-->
            '/\n?<\/?{K}-->/',
        ],
        [
            // <!--key--> md contents <!--/key-->
            '/(\n?<\!--{K}-->([\s\S]*?)(?:<\!--\/{K}-->|)<\!--\/{K}-->)/',
            // <!--key-->
            // <!--/key-->
            '/\n?<\!--\/?{K}-->/',
        ],
    ];

    /** @formatter:off */
    $pattern = match ($extOrFilename) {
        'xml'               => str_replace('{K}', $conditionKey, $condition ? $patterns[1][1] : $patterns[1][0]),
        'php'               => str_replace(['{O}', '{C}', '{K}'], ['\/\*\*', '\*\/', $conditionKey], $condition ? $patterns[0][1] : $patterns[0][0]),
        'md', 'MD'          => str_replace('{K}', $conditionKey, $condition ? $patterns[2][1] : $patterns[2][0]),
        'gitignore',
        'gitattributes',
        'editorconfig',
        'yml',
        'yaml',
        'neon',
        'LICENSE'           => str_replace(['{O}', '{C}', '{K}'], ['\#', '\#', $conditionKey], $condition ? $patterns[0][1] : $patterns[0][0]),
        default             => str_replace(['{O}', '{C}', '{K}'], ['\#', '\#', $conditionKey], $condition ? $patterns[0][1] : $patterns[0][0]),
    };
    /** @formatter:on */

    if (! $test) {
        file_put_contents(
            $file,
            preg_replace($pattern, '', $contents) ?: $contents
        );
        return null;
    }

    return preg_replace($pattern, '', $contents) ?: $contents;
}

function remove_prefix(string $prefix, string $content): string
{
    if (str_starts_with($content, $prefix)) {
        return substr($content, strlen($prefix));
    }

    return $content;
}

function determineSeparator(string $path): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function replaceForWindows(): array
{
    return preg_split('/\\r\\n|\\r|\\n/', run('dir /S /B * | findstr /v /i .git\ | findstr /v /i vendor | findstr /v /i ' . basename(__FILE__) . ' | findstr /r /i /M /F:/ ":author :vendor :package VendorName skeleton migration_table_name vendor_name vendor_slug author@domain.com :coverage_value :default_branch :codecov_token"'));
}

function replaceForAllOtherOSes(): array
{
    return explode(PHP_EOL, run('grep -E -r -l -i "<\w+-->|\# <\w+> \#|\#<\w+>\#|\/\*\* <\w+> \*\/|\/\*\*<\w+>\*\/|:author|:vendor|:package|VendorName|skeleton|migration_table_name|vendor_name|vendor_slug|author@domain.com|:coverage_value|:default_branch|:codecov_token" --exclude-dir=vendor ./* ./.php-cs-fixer.dist.php ./.github/* | grep -v ' . basename(__FILE__)));
}

$gitName = run('git config user.name');
$authorName = ask('Author name', $gitName);

$gitEmail = run('git config user.email');
$authorEmail = ask('Author email', $gitEmail);

$usernameGuess = explode(':', run('git config remote.origin.url'))[1] ?? null;

if (! is_null($usernameGuess)) {
    $usernameGuess = dirname($usernameGuess);
    $usernameGuess = basename($usernameGuess);
    $authorUsername = ask('Author username', $usernameGuess);
} else {
    $authorUsername = ask('Author username (required)');
    if (empty($authorUsername)) {
        exit(1);
    }
}

$vendorName = ask('Vendor name', $authorUsername);
$vendorSlug = slugify($vendorName);
$vendorNamespace = ucwords($vendorName);
$vendorNamespace = ask('Vendor namespace', $vendorNamespace);
$defaultBranch = ask('Default branch', 'master');

$currentDirectory = getcwd();
$folderName = basename($currentDirectory);

$packageName = ask('Package name', $folderName);
$packageSlug = slugify($packageName);
$packageSlugWithoutPrefix = remove_prefix('laravel-', $packageSlug);

$className = studly_case($packageName);
$className = ask('Class name', $className);
$variableName = lcfirst($className);

$packageTitle = title_case($packageName);
$packageTitle = ask('Package title', $packageTitle);
$description = ask('Package description', $packageSlug);

$hasDatabase = confirm('Has database', false);
$hasCommand = confirm('Has Artisan command', false);
$hasFacade = confirm('Has Facade', true);
$hasConfig = confirm('Has Config', true);
$hasViews = confirm('Has Views', false);

$usePhpStan = confirm('Enable PhpStan?', true);
$usePhpCsFixer = confirm('Enable PhpCsFixer?', true);
$useUpdateChangelogWorkflow = confirm('Use automatic changelog updater workflow?', true);
$coverage = 'none';
$useCoverageInWorkflow = confirm('Use coverage in workflow?', true);
$useCodecovInWorkflow = false;
$codecovBadgeToken = '';

if ($useCoverageInWorkflow) {
    $coverage = 'xdebug';
    if ($useCodecovInWorkflow = confirm('Enable Codecov in workflow?', true)) {
        $codecovBadgeToken = ask('Codecov Badge Token');
    }
}

writeln('------');
writeln("Author          : {$authorName} ({$authorUsername}, {$authorEmail})");
writeln("Vendor          : {$vendorName} ({$vendorSlug})");
writeln("Package         : {$packageSlug} <{$description}>");
writeln("Package Title   : {$packageTitle}");
writeln("Default Branch  : {$defaultBranch}");
writeln("Namespace       : {$vendorNamespace}\\{$className}");
writeln("Class name      : {$className}");
writeln("---");
writeln("Laravel");
writeln("Has Config           : " . ($hasConfig ? 'yes' : 'no'));
writeln("Has Views            : " . ($hasViews ? 'yes' : 'no'));
writeln("Has Database         : " . ($hasDatabase ? 'yes' : 'no'));
writeln("Has Facade           : " . ($hasFacade ? 'yes' : 'no'));
writeln("Has Artisan Command  : " . ($hasCommand ? 'yes' : 'no'));
writeln("---");
writeln("Packages & Utilities");
writeln("Use PhpCsFixer       : " . ($usePhpCsFixer ? 'yes' : 'no'));
writeln("Use Larastan/PhpStan : " . ($usePhpStan ? 'yes' : 'no'));
writeln("Use Auto-Changelog   : " . ($useUpdateChangelogWorkflow ? 'yes' : 'no'));
writeln("Use Coverage         : " . ($useCoverageInWorkflow ? 'yes' : 'none'));
writeln("Use Codecov          : " . ($useCodecovInWorkflow ? 'yes' : 'no'));
writeln("Codecov Badge Token  : " . ($useCodecovInWorkflow ? $codecovBadgeToken : '-'));
writeln('------');

writeln('This script will replace the above values in all relevant files in the project directory.');

if (! confirm('Modify files?', true)) {
    exit(1);
}

$files = (str_starts_with(strtoupper(PHP_OS), 'WIN') ? replaceForWindows() : replaceForAllOtherOSes());

foreach ($files as $file) {
    replace_in_file($file, [
        ':author_name'                 => $authorName,
        ':author_username'             => $authorUsername,
        'author@domain.com'            => $authorEmail,
        ':vendor_name'                 => $vendorName,
        ':vendor_slug'                 => $vendorSlug,
        'VendorName'                   => $vendorNamespace,
        ':package_name'                => $packageName,
        ':package_slug'                => $packageSlug,
        ':package_slug_without_prefix' => $packageSlugWithoutPrefix,
        'Skeleton'                     => $className,
        'skeleton'                     => $packageSlug,
        ':package_description'         => $description,
        ':coverage_value'              => $coverage,
        ':default_branch'              => $defaultBranch,
        ':codecov_token'               => $codecovBadgeToken,
        ':package_title'               => $packageTitle,
        'migration_table_name'         => title_snake($packageSlug),
        'variable'                     => $variableName,
    ]);

    removeConditionalCodeBlocks($file, 'deleteCoverage', $useCoverageInWorkflow);
    removeConditionalCodeBlocks($file, 'deleteCoverageElse', ! $useCoverageInWorkflow);
    removeConditionalCodeBlocks($file, 'deleteCodecov', $useCodecovInWorkflow);
    removeConditionalCodeBlocks($file, 'delete', false);
    removeConditionalCodeBlocks($file, 'hasDatabase', $hasDatabase);
    removeConditionalCodeBlocks($file, 'hasCommand', $hasCommand);
    removeConditionalCodeBlocks($file, 'hasFacade', $hasFacade);
    removeConditionalCodeBlocks($file, 'hasConfig', $hasConfig);
    removeConditionalCodeBlocks($file, 'hasConfigElse', ! $hasConfig);
    removeConditionalCodeBlocks($file, 'hasViews', $hasViews);

    match (true) {
        str_contains($file, determineSeparator('src/Skeleton.php'))                                                   => rename($file, determineSeparator('./src/' . $className . '.php')),
        str_contains($file, determineSeparator('src/SkeletonServiceProvider.php'))                                    => rename($file, determineSeparator('./src/' . $className . 'ServiceProvider.php')),
        $hasFacade && str_contains($file, determineSeparator('src/Facades/Skeleton.php'))                             => rename($file, determineSeparator('./src/Facades/' . $className . '.php')),
        $hasCommand && str_contains($file, determineSeparator('src/Commands/SkeletonCommand.php'))                    => rename($file, determineSeparator('./src/Commands/' . $className . 'Command.php')),
        $hasDatabase && str_contains($file, determineSeparator('database/migrations/create_skeleton_table.php.stub')) => rename($file, determineSeparator('./database/migrations/create_' . title_snake($packageSlugWithoutPrefix) . '_table.php.stub')),
        $hasConfig && str_contains($file, determineSeparator('config/skeleton.php'))                                  => rename($file, determineSeparator('./config/' . $packageSlugWithoutPrefix . '.php')),
        default                                                                                                       => [],
    };

}

if (! $hasConfig) {
    safeDeleteDirectory(__DIR__ . '/config');
}

if (! $hasFacade) {
    safeDeleteDirectory(__DIR__ . '/src/Facades');
    composer_forget('extra.laravel.aliases.' . $className);
}

if (! $hasCommand) {
    safeDeleteDirectory(__DIR__ . '/src/Commands');
}

if (! $hasDatabase) {
    safeDeleteDirectory(__DIR__ . '/database');
}

if (! $hasViews) {
    safeDeleteDirectory(__DIR__ . '/resources');
}

if (! $usePhpCsFixer) {
    safeUnlink(__DIR__ . '/.php-cs-fixer.dist.php');
    safeUnlink(__DIR__ . '/.github/workflows/php-cs-fixer.yml');

    composer_forget([
        'require-dev.friendsofphp/php-cs-fixer',
        'scripts.lint',
        'scripts.test:lint',
    ]);
}

if (! $usePhpStan) {
    safeUnlink(__DIR__ . '/phpstan.neon.dist');
    safeUnlink(__DIR__ . '/phpstan-baseline.neon');
    safeUnlink(__DIR__ . '/.github/workflows/phpstan.yml');

    composer_forget([
        'require-dev.phpstan/phpstan',
        'require-dev.phpstan/phpstan-phpunit',
        'require-dev.phpstan/extension-installer',
        'require-dev.phpstan/phpstan-deprecation-rules',
        'require-dev.nunomaduro/larastan',
        'scripts.test:styles',
        'scripts.test:styles:pro',
        'config.allow-plugins.phpstan/extension-installer',
    ]);
}
if (! $useCoverageInWorkflow) {
    composer_forget([
        'require-dev.phpunit/phpcov',
        'scripts.test:coverage',
        'scripts.test:coverage:html',
    ]);
}

if (! $useUpdateChangelogWorkflow) {
    safeUnlink(__DIR__ . '/.github/workflows/update-changelog.yml');
}

$runTests = false;

if ($composerInstall = confirm('Execute `composer install`?')) {
    run('composer install');

    $runTests = confirm('Run tests?');
}

$deleteItSelf = confirm('Let this script delete itself?', true);

if ($runTests) {
    runTests('composer test');
}

if ($deleteItSelf) {
    unlink(__FILE__);
}
