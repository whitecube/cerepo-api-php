<?php

namespace Whitecube\Cerepo;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

class Source
{
    public ?string $unf_id = null;
    public ?string $host_ref = null;

    public array $prod_ids = [];
    public array $val_ids = [];
    public array $aud_ids = [];
    public array $prod_refs = [];
    public array $cross_refs = [];
    public array $codes = [];
    public array $synonyms = [];

    public string $lang;
    public string $title;
    public string $url;
    public mixed $content;

    public ?string $pub_date = null;
    public ?string $upd_date = null;
    public ?string $rvw_date = null;
    public ?string $obs_date = null;

    public function __construct(array $data)
    {
        $this->unf_id = $this->castNullableString($data, 'unf_id');
        $this->host_ref = $this->castNullableString($data, 'host_ref');

        $this->lang = $this->castRequireString($data, 'lang');
        $this->title = $this->castRequireString($data, 'title');
        $this->url = $this->castRequireString($data, 'url');

        $this->content = $this->castNullableString($data, 'content');

        $this->prod_ids = $this->castStringArray($data, 'prod_ids', false);
        $this->val_ids = $this->castStringArray($data, 'val_ids');
        $this->aud_ids = $this->castStringArray($data, 'aud_ids');
        $this->prod_refs = $this->castStringArray($data, 'prod_refs');
        $this->synonyms = $this->castStringArray($data, 'synonyms', false);

        $this->codes = $this->castCodesArray($data);
        $this->cross_refs = $this->castCrossRefsArray($data);

        $this->pub_date = $this->formatDate($data, 'pub_date');
        $this->upd_date = $this->formatDate($data, 'upd_date');
        $this->rvw_date = $this->formatDate($data, 'rvw_date');
        $this->obs_date = $this->formatDate($data, 'obs_date');
    }

    protected function formatDate(array $data, string $key): ?string
    {
        if (! array_key_exists($key, $data) || $data[$key] === null) {
            return null;
        }

        $date = $data[$key];

        if (is_string($data[$key])) {
            $date = new DateTimeImmutable($data[$key]);
        }

        if (! $date instanceof DateTimeInterface) {
            throw new InvalidArgumentException(sprintf(
                'Invalid type for "%s"; expected DateTimeInterface or string, got %s.',
                $key,
                get_debug_type($date)
            ));
        }

        return $date->format('Y-m-d');
    }

    protected function castRequireString(array $data, string $key): string
    {
        if (! array_key_exists($key, $data)) {
            throw new InvalidArgumentException(sprintf('Missing required field "%s".', $key));
        }

        if (! is_string($data[$key]) || trim($data[$key]) === '') {
            throw new InvalidArgumentException(sprintf('Field "%s" must be a non-empty string.', $key));
        }

        return $data[$key];
    }

    protected function castNullableString(array $data, string $key): ?string
    {
        if (! array_key_exists($key, $data) || $data[$key] === null) {
            return null;
        }

        if (! is_string($data[$key])) {
            throw new InvalidArgumentException(sprintf(
                'Field "%s" must be a string or null, %s given.',
                $key,
                get_debug_type($data[$key])
            ));
        }

        return $data[$key];
    }

    protected function castStringArray(array $data, string $key, bool $required = true): array
    {
        if (! isset($data[$key]) && $required) {
            throw new InvalidArgumentException(sprintf('Missing required field "%s".', $key));
        }

        if (! isset($data[$key]) && ! $required) {
            return [];
        }

        if (! is_array($data[$key])) {
            throw new InvalidArgumentException(sprintf(
                'Field "%s" must be an array of strings, %s given.',
                $key,
                get_debug_type($data[$key])
            ));
        }

        foreach ($data[$key] as $item) {
            if (! is_string($item)) {
                throw new InvalidArgumentException(sprintf(
                    'Each element of "%s" must be a string, %s given.',
                    $key,
                    get_debug_type($item)
                ));
            }
        }

        return array_values($data[$key]);
    }

    protected function castCodesArray(array $data): array
    {
        if (! isset($data['codes'])) {
            return [];
        }

        if (! is_array($data['codes'])) {
            throw new InvalidArgumentException(sprintf(
                'Field "codes" must be an array, %s given.',
                get_debug_type($data['codes'])
            ));
        }

        $normalized = [];

        foreach ($data['codes'] as $index => $code) {
            if (! is_array($code)) {
                throw new InvalidArgumentException(sprintf(
                    'Each item in "codes" must be an array, %s given at index %s.',
                    get_debug_type($code),
                    $index
                ));
            }

            if (! isset($code['class']) || ! is_string($code['class']) || trim($code['class']) === '') {
                throw new InvalidArgumentException(sprintf(
                    'Item "codes[%s].class" must be a non-empty string.',
                    $index
                ));
            }

            if (! isset($code['list'])) {
                throw new InvalidArgumentException(sprintf(
                    'Missing "list" for codes[%s].',
                    $index
                ));
            }

            $list = $this->castStringArray($code, 'list');

            $normalized[] = [
                'class' => $code['class'],
                'list'  => $list,
            ];
        }

        return $normalized;
    }

    protected function castCrossRefsArray(array $data): array
    {
        if (! isset($data['cross_refs'])) {
            return [];
        }

        if (! is_array($data['cross_refs'])) {
            throw new InvalidArgumentException(sprintf(
                'Field "cross_refs" must be an array, %s given.',
                get_debug_type($data['cross_refs'])
            ));
        }

        $normalized = [];

        foreach ($data['cross_refs'] as $index => $ref) {
            if (! is_array($ref)) {
                throw new InvalidArgumentException(sprintf(
                    'Each item in "cross_refs" must be an array, %s given at index %s.',
                    get_debug_type($ref),
                    $index
                ));
            }

            if (! isset($ref['host_id']) || ! is_string($ref['host_id']) || trim($ref['host_id']) === '') {
                throw new InvalidArgumentException(sprintf(
                    'Item "cross_refs[%s].host_id" must be a non-empty string.',
                    $index
                ));
            }

            if (! isset($ref['host_refs'])) {
                throw new InvalidArgumentException(sprintf(
                    'Missing "host_refs" for cross_refs[%s].',
                    $index
                ));
            }

            $hostRefs = $this->castStringArray($ref, 'host_refs');

            $normalized[] = [
                'host_id'   => $ref['host_id'],
                'host_refs' => $hostRefs,
            ];
        }

        return $normalized;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
