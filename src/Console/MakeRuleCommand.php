<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Console;

final class MakeRuleCommand
{
    private const array RESERVED = [
        'abstract', 'and', 'array', 'as', 'bool', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const',
        'continue', 'declare', 'default', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor',
        'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'false', 'final', 'finally',
        'float', 'fn', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include',
        'include_once', 'instanceof', 'insteadof', 'int', 'interface', 'isset', 'iterable', 'list', 'match',
        'mixed', 'namespace', 'never', 'new', 'null', 'object', 'or', 'parent', 'print', 'private', 'protected',
        'public', 'readonly', 'require', 'require_once', 'return', 'self', 'static', 'string', 'switch', 'throw',
        'trait', 'true', 'try', 'unset', 'use', 'var', 'void', 'while', 'xor', 'yield',
    ];

    /**
     * Create a new instance of this command.
     *
     * @param resource|null $output
     * @param resource|null $error
     */
    public function __construct(
        private readonly string $cwd,
        private $output = null,
        private $error = null,
    ) {
        $this->output ??= STDOUT;
        $this->error ??= STDERR;
    }

    /**
     * The command's usage text.
     */
    public static function usage(): string
    {
        return <<<TXT
            Usage:
              vanguard make:rule <name> [--date]

            Creates a custom rule class. By default it is placed in the `Rules` folder of the first
            PSR-4 entry in your composer.json, e.g. `App\Rules\<name>` in `src/Rules/<name>.php`.
            A name that starts with a PSR-4 namespace is used as given, e.g. `App/Validation/Uppercase`.

            Arguments:
              name           The rule's class name. Use `/` to separate namespaces, e.g. `Text/Uppercase`.

            Options:
              --date         Use the PreparesDate trait, so date strings are converted to dates
              -h, --help     Show this help
        TXT;
    }

    /**
     * Run the command, returning its exit code.
     *
     * @param list<string> $arguments
     */
    public function run(array $arguments): int
    {
        try {
            return $this->handle($arguments);
        } catch (\RuntimeException $e) {
            fwrite($this->error, $e->getMessage() . PHP_EOL);

            return 1;
        }
    }

    /**
     * Parse the arguments and write the rule file.
     *
     * @param list<string> $arguments
     */
    private function handle(array $arguments): int
    {
        $name = null;
        $date = false;

        foreach ($arguments as $argument) {
            if ($argument === '--help' || $argument === '-h') {
                fwrite($this->output, self::usage());

                return 0;
            }

            if ($argument === '--date') {
                $date = true;
            } elseif (str_starts_with($argument, '-')) {
                throw new \RuntimeException("Unknown option [{$argument}].");
            } elseif ($name === null) {
                $name = $argument;
            } else {
                throw new \RuntimeException("Unexpected argument [{$argument}].");
            }
        }

        if ($name === null || $name === '') {
            throw new \RuntimeException('Please provide a rule name, e.g. `vanguard make:rule Uppercase`.');
        }

        $segments = $this->segments($name);
        $class = array_pop($segments);

        if (class_exists("Mewtonium\\Vanguard\\Rules\\{$class}")) {
            throw new \RuntimeException("[{$class}] is already a built-in Vanguard rule. Please choose another name.");
        }

        $root = $this->projectRoot();

        [$namespace, $directory] = $this->resolve($root, $segments);

        $relativePath = ($directory === '' ? '' : "{$directory}/") . "{$class}.php";
        $file = "{$root}/{$relativePath}";

        if (file_exists($file)) {
            throw new \RuntimeException("Rule already exists at [{$relativePath}].");
        }

        if (! is_dir(dirname($file)) && ! mkdir(dirname($file), 0o755, true) && ! is_dir(dirname($file))) {
            throw new \RuntimeException('Unable to create directory [' . dirname($relativePath) . '].');
        }

        file_put_contents($file, $this->render($namespace, $class, $date));

        fwrite($this->output, "Rule [{$namespace}\\{$class}] created at [{$relativePath}]." . PHP_EOL);

        return 0;
    }

    /**
     * The nearest directory at or above the working directory containing a composer.json.
     */
    private function projectRoot(): string
    {
        $directory = mb_rtrim($this->cwd, '/');

        while (true) {
            if (is_file("{$directory}/composer.json")) {
                return $directory;
            }

            $parent = dirname($directory);

            if ($parent === $directory) {
                return mb_rtrim($this->cwd, '/');
            }

            $directory = $parent;
        }
    }

    /**
     * The rule's namespace and directory, treating names that start with a PSR-4 prefix as fully qualified.
     *
     * @param list<string> $segments
     * @return array{string, string}
     */
    private function resolve(string $root, array $segments): array
    {
        $composer = null;

        if (is_file("{$root}/composer.json")) {
            try {
                $composer = json_decode((string) file_get_contents("{$root}/composer.json"), true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new \RuntimeException("Unable to read [{$root}/composer.json]: {$e->getMessage()}.");
            }
        }

        $autoload = $this->psr4(is_array($composer) ? ($composer['autoload']['psr-4'] ?? null) : null);
        $autoloadDev = $this->psr4(is_array($composer) ? ($composer['autoload-dev']['psr-4'] ?? null) : null);

        $match = null;

        foreach ([...$autoload, ...$autoloadDev] as [$prefix, $directory]) {
            $prefixSegments = $prefix === '' ? [] : explode('\\', $prefix);
            $length = count($prefixSegments);

            if ($length > 0 && array_slice($segments, 0, $length) === $prefixSegments && ($match === null || $length > $match[2])) {
                $match = [$prefix, $directory, $length];
            }
        }

        if ($match !== null) {
            [$prefix, $directory, $length] = $match;
            $relative = array_slice($segments, $length);
        } elseif ($autoload !== []) {
            [$prefix, $directory] = $autoload[0];
            $relative = ['Rules', ...$segments];
        } else {
            throw new \RuntimeException('No PSR-4 autoload entry found in composer.json, so the rule couldn\'t be autoloaded.');
        }

        return [
            implode('\\', array_filter([$prefix, ...$relative], fn (string $s): bool => $s !== '')),
            implode('/', array_filter([$directory, ...$relative], fn (string $s): bool => $s !== '')),
        ];
    }

    /**
     * Normalise a PSR-4 map into a list of namespace prefix and directory pairs.
     *
     * @return list<array{string, string}>
     */
    private function psr4(mixed $map): array
    {
        if (! is_array($map)) {
            return [];
        }

        $entries = [];

        foreach ($map as $prefix => $directories) {
            foreach ((array) $directories as $directory) {
                $entries[] = [
                    mb_trim((string) $prefix, '\\'),
                    mb_trim(preg_replace('#^(\./)+#', '', (string) $directory), '/'),
                ];
            }
        }

        return $entries;
    }

    /**
     * Ensure a segment is a valid, non-reserved PHP identifier.
     */
    private function validateSegment(string $segment): void
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $segment) !== 1) {
            throw new \RuntimeException("[{$segment}] is not a valid class or namespace name.");
        }

        if (in_array(mb_strtolower($segment), self::RESERVED, true)) {
            throw new \RuntimeException("[{$segment}] is a reserved word and can't be used as a class or namespace name.");
        }
    }

    /**
     * Split the rule name into validated namespace segments, the last being the class.
     *
     * @return non-empty-list<string>
     */
    private function segments(string $name): array
    {
        if (str_contains($name, '\\')) {
            throw new \RuntimeException(
                'Rule names may not contain `\\`. Use `/` to separate namespaces, e.g. `App/Validation/Uppercase`.',
            );
        }

        $segments = explode('/', mb_trim($name, '/'));

        foreach ($segments as $segment) {
            $this->validateSegment($segment);
        }

        return $segments;
    }

    /**
     * Fill the rule stub with the namespace and class.
     */
    private function render(string $namespace, string $class, bool $date): string
    {
        $stub = (string) file_get_contents(dirname(__DIR__, 2) . ($date ? '/stubs/rule.date.stub' : '/stubs/rule.stub'));

        return str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $class], $stub);
    }
}
