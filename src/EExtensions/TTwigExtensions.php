<?php
namespace CarloNicora\Minimalism\Modules\JsonApi\Web\EExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TTwigExtensions extends AbstractExtension {
    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array {
        return [
            new TwigFunction('getIncluded', [$this, 'included']),
            new TwigFunction('getIncludedByTypeId', [$this, 'includedTypeId']),
        ];
    }

    /**
     * @param array $included
     * @param array $object
     * @return array
     */
    public function included(array $included, array $object): array {
        return $this->includedTypeId($included, $object['type'], $object['id']);
    }

    /**
     * @param array $included
     * @param string $type
     * @param string $id
     * @return mixed
     */
    public function includedTypeId(array $included, string $type, string $id){
        foreach ($included as $element) {
            if ($element['id'] === $id && $element['type'] === $type){
                return $element;
            }
        }

        return[];
    }
}