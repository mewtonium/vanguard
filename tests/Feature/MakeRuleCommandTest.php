<?php

declare(strict_types=1);

use Mewtonium\Vanguard\Console\MakeRuleCommand;

/**
 * Create a throwaway project with the given composer.json autoload section.
 */
function makeProject(?array $autoload = ['psr-4' => ['App\\' => 'src/']]): string
{
    $root = sys_get_temp_dir() . '/vanguard-make-rule-' . bin2hex(random_bytes(6));

    mkdir($root, 0o755, true);

    file_put_contents("{$root}/composer.json", json_encode(array_filter(['name' => 'acme/app', 'autoload' => $autoload])));

    return $root;
}

/**
 * Run the command from the given directory.
 *
 * @return array{int, string, string}
 */
function makeRule(string $cwd, string ...$arguments): array
{
    $output = fopen('php://memory', 'w+');
    $error = fopen('php://memory', 'w+');

    $code = (new MakeRuleCommand($cwd, $output, $error))->run(array_values($arguments));

    rewind($output);
    rewind($error);

    return [$code, stream_get_contents($output), stream_get_contents($error)];
}

/**
 * Recursively delete a directory.
 */
function removeDirectory(string $directory): void
{
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST,
    );

    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }

    rmdir($directory);
}

beforeEach(function (): void {
    $this->root = makeProject();
});

afterEach(function (): void {
    removeDirectory($this->root);
});

test('it creates a rule in the Rules folder of the first PSR-4 entry', function (): void {
    [$code, $output] = makeRule($this->root, 'Uppercase');

    $file = "{$this->root}/src/Rules/Uppercase.php";

    expect($code)->toBe(0);
    expect($output)->toContain('Rule [App\Rules\Uppercase] created at [src/Rules/Uppercase.php].');
    expect(file_get_contents($file))
        ->toContain('namespace App\Rules;')
        ->toContain('final class Uppercase extends Rule')
        ->not->toContain('{{');
});

test('the date option adds the PreparesDate trait', function (): void {
    [$code] = makeRule($this->root, 'Before', '--date');

    expect($code)->toBe(0);
    expect(file_get_contents("{$this->root}/src/Rules/Before.php"))
        ->toContain('use Mewtonium\\Vanguard\\Concerns\\PreparesDate;')
        ->toContain('    use PreparesDate;')
        ->not->toContain('{{');
});

test('rules are created without the PreparesDate trait by default', function (): void {
    makeRule($this->root, 'Uppercase');

    expect(file_get_contents("{$this->root}/src/Rules/Uppercase.php"))->not->toContain('PreparesDate');
});

test('a slash in the name creates a sub-namespace', function (): void {
    [$code] = makeRule($this->root, 'Text/Slug');

    expect($code)->toBe(0);
    expect(file_get_contents("{$this->root}/src/Rules/Text/Slug.php"))->toContain('namespace App\Rules\Text;');
});

test('it finds the project root from a nested directory', function (): void {
    mkdir("{$this->root}/src/Deep/Er", 0o755, true);

    [$code] = makeRule("{$this->root}/src/Deep/Er", 'Uppercase');

    expect($code)->toBe(0);
    expect(file_exists("{$this->root}/src/Rules/Uppercase.php"))->toBeTrue();
});

test('a name starting with a PSR-4 namespace is used as given', function (string $name, string $class, string $file): void {
    file_put_contents("{$this->root}/composer.json", json_encode([
        'autoload' => ['psr-4' => ['App\\' => 'src/', 'App\\Domain\\' => ['modules/domain/', 'lib/']]],
        'autoload-dev' => ['psr-4' => ['Tests\\' => 'tests/']],
    ]));

    [$code, $output] = makeRule($this->root, $name);

    expect($code)->toBe(0);
    expect($output)->toContain("Rule [{$class}] created at [{$file}].");
    expect(file_get_contents("{$this->root}/{$file}"))->toContain('namespace ' . mb_substr($class, 0, mb_strrpos($class, '\\')) . ';');
})->with([
    'relative' => ['Uppercase', 'App\Rules\Uppercase', 'src/Rules/Uppercase.php'],
    'relative sub-namespace' => ['Text/Uppercase', 'App\Rules\Text\Uppercase', 'src/Rules/Text/Uppercase.php'],
    'fully qualified' => ['App/Validation/Rules/Uppercase', 'App\Validation\Rules\Uppercase', 'src/Validation/Rules/Uppercase.php'],
    'directly under the prefix' => ['App/Uppercase', 'App\Uppercase', 'src/Uppercase.php'],
    'longest prefix wins' => ['App/Domain/Rules/Uppercase', 'App\Domain\Rules\Uppercase', 'modules/domain/Rules/Uppercase.php'],
    'autoload-dev prefix' => ['Tests/Rules/Uppercase', 'Tests\Rules\Uppercase', 'tests/Rules/Uppercase.php'],
    'prefix must match whole segments' => ['Application/Uppercase', 'App\Rules\Application\Uppercase', 'src/Rules/Application/Uppercase.php'],
    'prefix is case-sensitive' => ['app/Uppercase', 'App\Rules\app\Uppercase', 'src/Rules/app/Uppercase.php'],
]);

test('a fully qualified name works without an autoload entry', function (): void {
    file_put_contents("{$this->root}/composer.json", json_encode([
        'autoload-dev' => ['psr-4' => ['Tests\\' => 'tests/']],
    ]));

    [$code] = makeRule($this->root, 'Tests/Rules/Uppercase');

    expect($code)->toBe(0);
    expect(file_exists("{$this->root}/tests/Rules/Uppercase.php"))->toBeTrue();
});

test('it does not overwrite an existing rule', function (): void {
    makeRule($this->root, 'Uppercase');
    file_put_contents("{$this->root}/src/Rules/Uppercase.php", 'custom');

    [$code, , $error] = makeRule($this->root, 'Uppercase');

    expect($code)->toBe(1);
    expect($error)->toContain('Rule already exists at [src/Rules/Uppercase.php].');
    expect(file_get_contents("{$this->root}/src/Rules/Uppercase.php"))->toBe('custom');
});

test('it rejects invalid names', function (string $name, string $message): void {
    [$code, , $error] = makeRule($this->root, $name);

    expect($code)->toBe(1);
    expect($error)->toContain($message);
    expect(is_dir("{$this->root}/src"))->toBeFalse();
})->with([
    'backslash' => ['App\Rules\Uppercase', 'Rule names may not contain `\`. Use `/` to separate namespaces'],
    'leading digit' => ['1Rule', '[1Rule] is not a valid class or namespace name.'],
    'hyphen' => ['Upper-case', '[Upper-case] is not a valid class or namespace name.'],
    'empty segment' => ['Text//Slug', '[] is not a valid class or namespace name.'],
    'reserved word' => ['Match', '[Match] is a reserved word'],
    'built-in rule' => ['Email', '[Email] is already a built-in Vanguard rule.'],
]);

test('it requires a name', function (): void {
    [$code, , $error] = makeRule($this->root);

    expect($code)->toBe(1);
    expect($error)->toContain('Please provide a rule name');
});

test('it rejects unknown options and extra arguments', function (string ...$arguments): void {
    [$code] = makeRule($this->root, ...$arguments);

    expect($code)->toBe(1);
})->with([
    'unknown option' => ['Uppercase', '--force'],
    'extra argument' => ['Uppercase', 'Lowercase'],
]);

test('it errors when there is no PSR-4 entry to infer from', function (): void {
    $root = makeProject(null);

    try {
        [$code, , $error] = makeRule($root, 'Uppercase');

        expect($code)->toBe(1);
        expect($error)->toContain('No PSR-4 autoload entry found in composer.json');
    } finally {
        removeDirectory($root);
    }
});

test('it reports an invalid composer.json', function (): void {
    file_put_contents("{$this->root}/composer.json", '{"autoload": {"psr-4": {"App\": "src/"}}}');

    [$code, , $error] = makeRule($this->root, 'Uppercase');

    expect($code)->toBe(1);
    // PHP 8.6 appends the error's location, e.g. "Syntax error near location 1:34".
    expect($error)->toContain("Unable to read [{$this->root}/composer.json]: Syntax error");
    expect(is_dir("{$this->root}/src"))->toBeFalse();
});

test('it prints usage with --help', function (): void {
    [$code, $output] = makeRule($this->root, '--help');

    expect($code)->toBe(0);
    expect($output)->toContain('vanguard make:rule <name> [--date]');
});

test('the generated rule is valid PHP and works as a rule', function (): void {
    $prefix = 'Generated' . bin2hex(random_bytes(4));
    $namespace = "{$prefix}\\Rules";

    file_put_contents("{$this->root}/composer.json", json_encode(['autoload' => ['psr-4' => ["{$prefix}\\" => 'src/']]]));

    makeRule($this->root, 'Uppercase');

    $file = "{$this->root}/src/Rules/Uppercase.php";

    exec(sprintf('%s -l %s', escapeshellarg(PHP_BINARY), escapeshellarg($file)), result_code: $lint);
    expect($lint)->toBe(0);

    require $file;

    eval(<<<PHP
        namespace {$namespace};

        final class Form
        {
            use \\Mewtonium\\Vanguard\\Vanguard;

            public function __construct(
                #[Uppercase]
                protected string \$code,
            ) {
            }
        }
        PHP);

    $form = new ("{$namespace}\\Form")('abc');
    $form->validate();

    expect($form->invalid())->toBeTrue();
    expect($form->errors()->first('code'))->toBe('The code field is invalid.');
});

test('the generated date rule converts date strings', function (): void {
    $prefix = 'Generated' . bin2hex(random_bytes(4));
    $namespace = "{$prefix}\\Rules";

    file_put_contents("{$this->root}/composer.json", json_encode(['autoload' => ['psr-4' => ["{$prefix}\\" => 'src/']]]));

    makeRule($this->root, 'Before', '--date');

    $file = "{$this->root}/src/Rules/Before.php";

    exec(sprintf('%s -l %s', escapeshellarg(PHP_BINARY), escapeshellarg($file)), result_code: $lint);
    expect($lint)->toBe(0);

    require $file;

    $rule = new ("{$namespace}\\Before")();

    expect($rule->prepare('2026-01-01'))->toBeInstanceOf(\DateTimeInterface::class);
    expect($rule->prepare(5))->toBe(5);
});
