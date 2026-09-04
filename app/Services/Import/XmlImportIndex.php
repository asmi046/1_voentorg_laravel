<?php

namespace App\Services\Import;

class XmlImportIndex
{
    public array $byNormBase = [];

    public array $byBarcode = [];

    public array $byCategory = [];

    public array $byExternalId = [];

    public int $total = 0;

    public int $unparsed = 0;

    public function __construct(
        private ModifierExtractor $extractor,
        private NameNormalizer $normalizer,
    ) {}

    public function build(string $path, ?string $onlyCategoryExternalId = null, ?\Closure $onProgress = null): void
    {
        $reader = new \XMLReader;
        $reader->open($path);

        $read = 0;
        while ($reader->read()) {
            if ($reader->nodeType !== \XMLReader::ELEMENT || $reader->localName !== 'Товар') {
                continue;
            }
            $xml = $reader->readOuterXml();
            if ($xml === '') {
                continue;
            }
            $node = @simplexml_load_string($xml);
            if (! $node) {
                continue;
            }

            $title = trim((string) ($node->Наименование ?? ''));
            foreach ($node->ЗначенияРеквизитов->ЗначениеРеквизита ?? [] as $req) {
                if ((string) $req->Наименование === 'Полное наименование') {
                    $fullName = trim((string) $req->Значение);
                    if ($fullName !== '') {
                        $title = $fullName;
                    }
                }
            }

            $barcode = trim((string) ($node->Штрихкод ?? ''));
            $extId = trim((string) ($node->Ид ?? ''));
            $catId = trim((string) ($node->Группы->Ид ?? ''));

            if ($onlyCategoryExternalId !== null && $catId !== $onlyCategoryExternalId) {
                continue;
            }

            $this->total++;
            $read++;
            if ($onProgress && $read % 100 === 0) {
                $onProgress($read);
            }

            $split = $this->extractor->split($title);
            if ($split['base_name'] === '' || $split['base_name'] === $title && $split['modifier'] === '') {
                $this->unparsed++;

                continue;
            }

            $normBase = $this->normalizer->normalize($split['base_name']);

            $offer = [
                'barcode' => $barcode,
                'modifier' => $split['modifier'],
                'ext_id' => $extId,
                'category_id' => $catId,
                'base_name' => $split['base_name'],
                'base_norm' => $normBase,
                'raw_title' => $title,
            ];

            if ($normBase !== '') {
                $this->byNormBase[$normBase][] = $offer;
            }
            if ($barcode !== '') {
                $this->byBarcode[$barcode] = $offer;
            }
            if ($catId !== '') {
                $this->byCategory[$catId][] = $offer;
            }
            if ($extId !== '') {
                $this->byExternalId[$extId] = $offer;
            }
        }

        if ($onProgress) {
            $onProgress($read);
        }

        $reader->close();
    }
}
